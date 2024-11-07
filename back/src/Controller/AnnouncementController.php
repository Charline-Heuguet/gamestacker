<?php

namespace App\Controller;

use App\Entity\Announcement;
use App\Form\AnnouncementType;
use App\Repository\UserRepository;
use App\Service\RoomIdGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AnnouncementRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('api/announcement', name: 'api_announcement_')]
class AnnouncementController extends AbstractController
{

    private $entityManager;
    private $announcementRepository;

    public function __construct(EntityManagerInterface $entityManager, AnnouncementRepository $announcementRepository)
    {
        $this->entityManager = $entityManager;
        $this->announcementRepository = $announcementRepository;
    }

    //VOIR TOUTES LES ANNONCES
    #[Route('/', name: 'announcement', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): JsonResponse
    {
        $searchTerm = $request->query->get('search', '');

        // Construire la requête en fonction de la présence du terme de recherche
        $query = $searchTerm
            ? $this->announcementRepository->findBySearchTerm($searchTerm)
            : $this->announcementRepository->findAllOrderedByDate();

        // Paginer les résultats
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->json([
            'items' => $pagination->getItems(),
            'totalItems' => $pagination->getTotalItemCount(),
        ], 200, [], ['groups' => 'announcement:read']);
    }

    //VOIR LES DERNIERES ANNONCES
    #[Route('/latest', name: 'latest', methods: ['GET'])]
    public function latestAnnouncements(): JsonResponse
    {
        $announcements = $this->announcementRepository->findLatestAnnouncements();
        return $this->json($announcements, 200, [], ['groups' => 'announcement:read']);
    }

    //VOIR UNE ANNONCES
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function viewAnnouncement($id): JsonResponse
    {
        $announcement = $this->announcementRepository->find($id);
        return $this->json($announcement, 200, [], ['groups' => 'announcement:details']);
    }
    #CREER UNE ANNONCE
    // CRÉER UNE ANNONCE
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function createAnnouncement(Request $request, RoomIdGeneratorService $roomIdGenerator): JsonResponse
    {
        $user = $this->getUser();  // Récupère l'utilisateur connecté

        if (!$user) {
            return new JsonResponse(['status' => 'Utilisateur non connecté'], 403);
        }

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['message' => 'No data sent'], 400);
        }

        $announcement = new Announcement();

        $form = $this->createForm(AnnouncementType::class, $announcement);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {

            $announcement->setUser($user);
            $announcement->setDate(new \DateTime());
            $announcement->addParticipant($user);
            $announcement->setRoomId($roomIdGenerator->generateUniqueId(14));

            $this->entityManager->persist($announcement);
            $this->entityManager->flush();

            return new JsonResponse(['status' => 'Annonce créée. Bon jeu !', 'id' => $announcement->getId()], 201);
        }

        return new JsonResponse([
            'status' => 'Invalid data',
            'errors' => (string) $form->getErrors(true, false)
        ], 400);
    }

    // MODIFIER UNE ANNONCE
    #[Route('/{id}/edit', name: 'edit', methods: ['PUT'])]
    public function editAnnouncement(int $id, Request $request): JsonResponse
    {
        $user = $this->getUser();  // Récupère l'utilisateur connecté

        if (!$user) {
            return new JsonResponse(['status' => 'Utilisateur non connecté'], 403);
        }

        $announcement = $this->announcementRepository->find($id);

        // Vérifie si l'utilisateur connecté est bien le créateur de l'annonce
        if (!$announcement || $announcement->getUser() !== $user) {
            return new JsonResponse(['status' => 'Vous n\'êtes pas autorisé à modifier cette annonce'], 403);
        }

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['message' => 'No data sent'], 400);
        }

        $form = $this->createForm(AnnouncementType::class, $announcement);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($announcement);
            $this->entityManager->flush();

            return new JsonResponse(['status' => 'Annonce modifiée. Bon jeu !'], 201);
        }

        return new JsonResponse([
            'status' => 'Invalid data',
            'errors' => (string) $form->getErrors(true, false)
        ], 400);
    }

    // SUPPRIMER UNE ANNONCE
    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'])]
    public function deleteAnnouncement($id): JsonResponse
    {
        $user = $this->getUser();  // Récupère l'utilisateur connecté

        if (!$user) {
            return new JsonResponse(['status' => 'Utilisateur non connecté'], 403);
        }

        $announcement = $this->announcementRepository->find($id);

        // Vérifie si l'utilisateur connecté est bien le créateur de l'annonce
        if ($announcement && $announcement->getUser() === $user) {
            $this->entityManager->remove($announcement);
            $this->entityManager->flush();

            return new JsonResponse(['status' => 'Annonce supprimée'], 200);
        } else {
            return new JsonResponse(['status' => 'Vous n\'êtes pas autorisé à supprimer cette annonce'], 403);
        }
    }

    // REJOINDRE UNE ANNONCE
    #[Route('/{id}/join', name: 'join', methods: ['POST'])]
    public function joinAnnouncement($id): JsonResponse
    {
        $user = $this->getUser();  // Récupère l'utilisateur connecté

        if (!$user) {
            return new JsonResponse(['status' => 'Utilisateur non connecté'], 403);
        }

        $announcement = $this->announcementRepository->find($id);

        if (!$announcement) {
            return new JsonResponse(['status' => 'Annonce non trouvée'], 404);
        }

        $maxPlayers = $announcement->getMaxNbPlayers();
        $currentPlayers = count($announcement->getParticipants());

        if ($currentPlayers >= $maxPlayers) {
            return new JsonResponse(['status' => 'Nombre de joueurs maximum atteint'], 403);
        }

        $announcement->addParticipant($user);
        $this->entityManager->persist($announcement);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Vous avez rejoint l\'annonce. Bon jeu'], 200);
    }


    //EXPULSER UN PARTICIPANT D'UNE ANNONCE
    #[Route('/{id}/kick/{participant_id}', name: 'kick', methods: ['POST'])]
    public function kickParticipant(int $id, int $participant_id, UserRepository $userRepository): JsonResponse
    {
        $user = $this->getUser();  // Récupère l'utilisateur connecté
        $announcement = $this->announcementRepository->find($id);
        $participant = $userRepository->find($participant_id);

        if (!$announcement) {
            return new JsonResponse(['status' => 'Annonce non trouvée'], 404);
        }

        if (!$participant) {
            return new JsonResponse(['status' => 'Participant non trouvé'], 404);
        }

        // Vérifie que l'utilisateur connecté est bien le créateur de l'annonce
        if ($announcement->getUser() !== $user) {
            return new JsonResponse(['status' => 'Vous n\'avez pas la permission d\'expulser ce participant'], 403);
        }

        // Empêcher l'expulsion de l'auteur lui-même
        if ($participant === $user) {
            return new JsonResponse(['status' => 'Vous ne pouvez pas vous expulser vous-même'], 403);
        }

        // Supprime le participant de l'annonce
        $announcement->removeParticipant($participant);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Vous avez expulsé le joueur ' . $participant->getPseudo()], 200);
    }
}
