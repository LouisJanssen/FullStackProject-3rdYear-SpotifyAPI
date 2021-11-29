<?php

namespace App\Controller;

use App\Entity\NotionPage;
use App\Entity\User;
use App\Service\NotionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /*
     * @var NotionService
     */
    private $notionService;

    public function __construct(NotionService $notionService)
    {
        $this->notionService = $notionService;
    }

    /**
     * @Route("/", name="default")
     */
    public function index(): Response
    {
        return $this->json('Hello world');
    }

    /**
     * @Route("/savePages", name="savePages")
     */
    public function savePages(): Response
    {
        $pages = $this->notionService->storeNotionPages();

        return $this->json('Notion pages saved');
    }

    /**
     * @Route("/notionPages", name="notionPages")
     */
    public function getNotionPages(): Response
    {
        $pages = $this->getDoctrine()->getRepository(NotionPage::class)->findAll();

        $returnArray = [];

        /** @var NotionPage $page */
        foreach ($pages as $page){
            $returnArray[] = [
                'id' => $page->getId(),
                'notionId' => $page->getNotionId(),
                'title' => $page->getTitle(),
                'creationDate' => $page->getCreationDate()->format(DATE_ATOM),
            ];
        }

        return $this->json($returnArray);
    }

    /**
     * @Route("/error", name="error")
     */
    public function error(): Response
    {
        return $this->json('Error');
    }

     /**
     * @Route("/login", name="login")
     */
    public function login(Request $request): Response
    {
        $params = json_decode($request->getContent(), true);

        if (!isset($params['username']) || empty($params['username'])) {
            throw new HttpException(400, 'Missing username parameter.');
        }

        if (!isset($params['email']) || empty($params['email'])) {
            throw new HttpException(400, 'Missing email parameter.');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $user = $entityManager->getRepository(User::class)->findOneByEmail($params['email']);

        if (null === $user) {
            $user = new User();
        }

        $user->setUsername($params['username'])
            ->setEmail($params['email'])
        ;

        $entityManager->persist($user);
        $entityManager->flush();

        $returnArray = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
        ];

        return $this->json($returnArray);
    }
}
