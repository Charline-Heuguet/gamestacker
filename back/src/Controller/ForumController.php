<?php 

namespace App\Controller;

use App\Entity\Forum;
use App\Form\ForumType;
use App\Repository\UserRepository;
use App\Repository\ForumRepository;
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

    #[Route('/forum', name: 'forum', methods: ['GET'])]
    public function index(): Response
    {
        
        $forums = $this->forumRepository->findAll();
        return $this->json($forums, 200, [], ['groups' => 'forum:read']);
    }

    #[Route('/forum/{id}', name: 'view_forum', methods: ['GET'])]
    public function viewForum($id): Response
    {
        $forum = $this->forumRepository->find($id);

    
        return $this->json(['forum' => $forum], 200, [], ['groups' => 'forum:details', 'comment:details']);
    }

    #[Route('/forum-add', name: 'add_forum', methods: ['POST'])]
public function forumAdd(Request $request, UserRepository $userRepository): JsonResponse
{
    // remplacer par $user = $this->getUser(); pour récupérer l'utilisateur connecté
    // quand le login sera en place
    $testUser = $userRepository->findOneBy(['id' => 361]);
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

    

    

    
}