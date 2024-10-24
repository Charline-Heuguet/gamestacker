<?php 

namespace App\Controller;

use App\Entity\Forum;
use App\Entity\Comment;
use App\Form\ForumType;
use App\Form\CommentType;
use App\Repository\UserRepository;
use App\Repository\ForumRepository;
use App\Service\CommentFilterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/api', name: 'api_forum_')]
class ForumController extends AbstractController
{
    private $entityManager;
    private $forumRepository;

    public function __construct(EntityManagerInterface $entityManager, ForumRepository $forumRepository)
    {
        $this->entityManager = $entityManager;
        $this->forumRepository = $forumRepository;
    }

    /// VISUALISER TOUS LES TICKETS ///
    #[Route('/forum', name: 'forum', methods: ['GET'])]
    public function index(): Response
    {
        
        $forums = $this->forumRepository->findAll();
        return $this->json($forums, 200, [], ['groups' => 'forum:read']);
    }

    /// VISUALISER UN TICKKET ///
    #[Route('/forum/{id}', name: 'view_forum', methods: ['GET'])]
    public function viewForum($id): Response
    {
        $forum = $this->forumRepository->find($id);

    
        return $this->json(['forum' => $forum], 200, [], ['groups' => 'forum:details', 'comment:details']);
    }


    /// AJOUTE UN TICKET ///
    #[Route('/forum-add', name: 'add_forum', methods: ['POST'])]
    public function forumAdd(Request $request, UserRepository $userRepository): JsonResponse
    {
        // remplacer par $user = $this->getUser(); pour récupérer l'utilisateur connecté
        // quand le login sera en place
        $testUser = $userRepository->findOneBy(['id' => 31]);
        if (!$testUser) {
            throw new NotFoundHttpException('User "testUser" not found');
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
            $forum->setUser($testUser);  // A changer lors du login
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

    #[Route('/forum/{id}/add-comment', name: 'add_forum_comment', methods: ['POST'])]
    public function addForumComment(Request $request, Forum $forum, UserRepository $userRepository, CommentFilterService $commentFilterService): JsonResponse
    {
        // Créer un utilisateur test
        // A remplacer par $user = $this->getUser() lorsque le login sera actif
        //Verifier que le User a l'id 39  existe dans la base de données
        $userTest = $userRepository->findOneBy(['id'=>39]);
        // Récupérer les données du commentaire depuis la requête
        $data = json_decode($request->getContent(), true);


        // Créer une nouvelle entité Comment
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->submit($data);
        

        if($form->isSubmitted() && $form->isValid()) {
            $filteredContent = $commentFilterService->filterProhibitedContent($comment->getContent());
            $comment->setContent($filteredContent);
            $comment->setDate(new \DateTime()); // Attribuer la date actuelle au commentaire
            $comment->setUser($userTest); // A remplacer par $user lorsque le login sera actif
        } else {
            return new JsonResponse(['status' => 'Invalid data'], 400);
        }

        // Ajouter le commentaire au forum en utilisant la méthode addComment
        $forum->addComment($comment);

        // Persister les changements
        $this->entityManager->persist($comment); // Persister le commentaire
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Comment added successfully'], 201);
    }

    

    /// MODIFIER UN TICKET DANS LES 10 PREMIERE MINUTE ///
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
            return new JsonResponse(['status' => 'You cannot edit the ticket anymore. The 10 minutes window has passed.'], 403);
        }

        // Si la modification est encore autorisée, traiter la mise à jour
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(ForumType::class, $forum);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $forum->setUpdatedAt(new \DateTime()); 
            $this->entityManager->flush();

            return new JsonResponse(['status' => 'Ticket updated successfully'], 200);
        }

        return new JsonResponse(['status' => 'Invalid data'], 400);
    }

    /// SUPPRIME UN TICKET ///
    #[Route('/forum/{id}/delete', name: 'delete_forum', methods: ['DELETE'])]
    public function deleteForum($id): JsonResponse
    {
        $forum = $this->forumRepository->find($id);

        if ($forum) {
            $this->entityManager->remove($forum);
            $this->entityManager->flush();

            return new JsonResponse(['status' => 'Forum post deleted'], 200);
        }

        return new JsonResponse(['status' => 'Forum post not found'], 404);
    }
}