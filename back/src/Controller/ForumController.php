<?php

namespace App\Controller;

use App\Entity\Forum;
use App\Entity\Comment;
use App\Form\ForumType;
use App\Form\CommentType;
use App\Repository\UserRepository;
use App\Repository\ForumRepository;
use App\Repository\CommentRepository;
use App\Service\CommentFilterService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/api/forum', name: 'api_forum_')]
class ForumController extends AbstractController
{
    private $entityManager;
    private $forumRepository;
    private $commentRepository;

    public function __construct(EntityManagerInterface $entityManager, ForumRepository $forumRepository, CommentRepository $commentRepository)
    {
        $this->entityManager = $entityManager;
        $this->forumRepository = $forumRepository;
        $this->commentRepository = $commentRepository;
    }

    /// VISUALISER TOUS LES TICKETS
    #[Route('/', name: 'forum', methods: ['GET', 'OPTIONS'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $searchTerm = $request->query->get('search', '');

        // Récupérer la requête en fonction de la présence du terme de recherche
        $query = $searchTerm
            ? $this->forumRepository->findBySearchTerm($searchTerm)
            : $this->forumRepository->findAllOrderedByDate();

        // Paginer les résultats
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->json([
            'items' => $pagination->getItems(),
            'totalItems' => $pagination->getTotalItemCount(),
        ], 200, [], ['groups' => 'forum:read']);
    }

    /// VISUALISER LES 5 DERNIERS TICKETS
    #[Route('/last-five', name: 'last_five', methods: ['GET'])]
    public function lastFive(): Response
    {
        $forums = $this->forumRepository->findLastFive();
        return $this->json($forums, 200, [], ['groups' => 'forum:read']);
    }

    /// VISUALISER UN TICKET 
    #[Route('/{id}', name: 'view', methods: ['GET'])]
    public function viewForum($id): Response
    {
        $forum = $this->forumRepository->find($id);
        return $this->json($forum, 200, [], ['groups' => 'forum:details', 'comment:details']);
    }


    /// AJOUTE UN TICKET ///
    #[Route('/add', name: 'add_forum', methods: ['POST'])]
    public function forumAdd(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            throw new NotFoundHttpException('Utilisateur non connecté');
        }

        // Décodage des données JSON envoyées dans la requête
        $data = json_decode($request->getContent(), true);

        // Vérification que les données ont été reçues
        if (!$data) {
            return new JsonResponse(['status' => 'No data received'], 400);
        }

        // Création de l'entité Forum
        $forum = new Forum();

        // Création du formulaire et soumission des données
        $form = $this->createForm(ForumType::class, $forum);
        $form->submit($data);

        // Vérification de la validité du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $forum->setUser($user);  // Attribution de l'utilisateur connecté
            $forum->setDate(new \DateTime());  // Attribution de la date actuelle
            $this->entityManager->persist($forum);
            $this->entityManager->flush();

            return new JsonResponse(['status' => 'Forum post created'], 201);
        }

        // Retour des erreurs si le formulaire n'est pas valide
        return new JsonResponse([
            'status' => 'Invalid data',
            'errors' => (string) $form->getErrors(true, false)
        ], 400);
    }


    /// MODIFIER UN TICKET DANS LES 10 PREMIERES MINUTES ///
    #[Route('/forum-edit/{id}', name: 'edit_forum', methods: ['PUT'])]
    public function editForum(int $id, Request $request, ForumRepository $forumRepository): JsonResponse
    {
        $forum = $forumRepository->find($id);

        if (!$forum) {
            return new JsonResponse(['status' => 'Ticket not found'], 404);
        }

        $currentDateTime = new \DateTime();
        $createdAt = $forum->getDate();

        // Calculer la différence en minutes entre la date actuelle et la date de création
        $interval = $createdAt->diff($currentDateTime);
        $minutesDifference = $interval->i + ($interval->h * 60); // Ajouter les heures converties en minutes

        // Vérifier si plus de 10 minutes se sont écoulées
        if ($minutesDifference > 10) {
            return new JsonResponse(['message' => 'Désolé, vous ne pouvez modifier votre ticket que 10 minutes après l\'avoir creer.'], 403);
        }

        // Si la modification est encore autorisée, traiter la mise à jour
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(ForumType::class, $forum);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $forum->setUpdatedAt(new \DateTime());
            $this->entityManager->flush();

            return new JsonResponse(['message' => 'Ticket modifié.'], 200);
        }

        return new JsonResponse(['message' => 'Données Invalides.'], 400);
    }

    /// SUPPRIME UN TICKET ///
    #[Route('/{id}/delete', name: 'delete_forum', methods: ['DELETE'])]
    public function deleteForum($id): JsonResponse
    {
        $forum = $this->forumRepository->find($id);

        if ($forum) {
            $this->entityManager->remove($forum);
            $this->entityManager->flush();

            return new JsonResponse(['message' => 'Vous avez supprimé un votre ticket.'], 200);
        }

        return new JsonResponse(['status' => 'Le ticket n\'existe pas.'], 404);
    }
}
