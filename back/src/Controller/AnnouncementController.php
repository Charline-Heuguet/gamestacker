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
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function createAnnouncement(Request $request, UserRepository $userRepository, RoomIdGeneratorService $roomIdGenerator): JsonResponse
    {
        $testUser = $userRepository->findBy(['id' => 31]);

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['message' => 'No data sent'], 400);
        }

        $announcement = new Announcement();

        $form = $this->createForm(AnnouncementType::class, $announcement);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {

            $announcement->setUser($testUser[0]);
            $announcement->setDate(new \DateTime());
            $announcement->addParticipant($testUser[0]);
            $announcement->setRoomId($roomIdGenerator->generateUniqueId(14));

            $this->entityManager->persist($announcement);
            $this->entityManager->flush();

            return new JsonResponse(['status' => 'Annonce créée. Bon jeu !'], 201);
        }

        return new JsonResponse([
            'status' => 'Invalid data',
            'errors' => (string) $form->getErrors(true, false)
        ], 400);
    }

    //MODIFIER UNE ANNONCE
    #[Route('/{id}/edit', name: 'edit', methods: ['PUT'])]
    public function editAnnouncement(int $id, Request $request, UserRepository $userRepository): JsonResponse
    {

        $testUser = $userRepository->findBy(['id' => 212]);

        $announcement = $this->announcementRepository->find($id);

        //Permet que seul le créateur de l'annonce puisse la modifiée
        if ($announcement->getUser() !== $testUser[0]) {
            return new JsonResponse(['status' => 'Vous n\'êtes pas autorisé à modifier cette annonce'], 403);
        }

        if (!$announcement) {
            return new JsonResponse(['status' => 'Annonce non trouvée'], 404);
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

    //SUPPRIMER UNE ANNONCE
    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'])]
    public function deleteAnnouncement($id, UserRepository $userRepository): JsonResponse
    {

        $userTest = $userRepository->findBy(['id' => 211]);

        $announcement = $this->announcementRepository->find($id);

        if ($announcement && $announcement->getUser() === $userTest[0]) {
            $this->entityManager->remove($announcement);
            $this->entityManager->flush();

            return new JsonResponse(['status' => 'Annonce supprimée'], 200);
        } else {
            return new JsonResponse(['status' => 'Vous n\'êtes pas autorisé à supprimer cette annonce'], 403);
        }
    }

    //REJOINDRE UNE ANNONCE
    #[Route('/{id}/join', name: 'join', methods: ['POST'])]
    public function joinAnnouncement($id, Announcement $announcement, UserRepository $userRepository): JsonResponse
    {
        $userTest = $userRepository->findBy(['id' => 31]);

        $announcement = $this->announcementRepository->find($id);

        $getMaxPlayers = $announcement->getMaxNbPlayers();
        $diff = $getMaxPlayers - count($announcement->getParticipants());

        if ($diff <= 0) {
            return new JsonResponse(['status' => 'Nombre de joueurs maximum atteint'], 403);
        } else {
            $announcement->addParticipant($userTest[0]);
            $this->entityManager->persist($announcement);
            $this->entityManager->flush();
            return new JsonResponse(['status' => 'Vous avez rejoint l\'annonce. Bon jeu'], 200);
        }
    }

    //EXPULSER UN PARTICPANT D'UNE ANNONCE
    #[Route('/{id}/kick/{participant_id}', name: 'kick', methods: ['POST'])]
    public function kickParticipant(int $id, int $participant_id, UserRepository $userRepository, AnnouncementRepository $announcementRepository): JsonResponse
    {
        $userTest = $userRepository->findBy(['id' => 423]);
        $announcement = $announcementRepository->find($id);
        $participant = $userRepository->find($participant_id);

        if (!$announcement) {
            return new JsonResponse(['status' => 'Annonce non trouvée'], 404);
        }

        if (!$participant) {
            return new JsonResponse(['status' => 'Participant non trouvé'], 404);
        }

        if ($participant == $userTest[0]) {
            return new JsonResponse(['status' => 'Vous ne pouvez pas vous expulser vous-même'], 403);
        }

        if ($announcement->getUser() !== $userTest[0]) {
            return new JsonResponse(['status' => 'Vous n\'avez pas la permission d\'expulser ce participant'], 403);
        }

        $announcement->removeParticipant($participant);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Vous avez expulsé le joueur ' . $participant->getPseudo()], 200);
    }
}
