<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\SpotifyService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DefaultController extends AbstractController
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /*
    * @var SpotifyService
    */
    private $spotifyService;

    /*
    * @var $userService
    */
    private $userService;

    public function __construct(
        LoggerInterface $logger,
        HttpClientInterface $httpClient,
        SpotifyService $spotifyService,
        UserService $userService)
    {
        $this->logger = $logger;
        $this->httpClient = $httpClient;
        $this->spotifyService = $spotifyService;
        $this->userService = $userService;
    }

    /**
     * @Route("/", name="default")
     */
    public function index(): Response
    {
        return $this->json('Welcome !');
    }

    /**
     * @Route("/error", name="error")
     */
    public function error(): Response
    {
        return $this->json('Error');
    }

    /**
     * @Route("/oauth", name="oauth")
     */
    public function oauth(): Response
    {
        $oauth_string = sprintf(
            'https://accounts.spotify.com/authorize?client_id=%s&response_type=code&redirect_uri=%s&scope=user-read-private user-read-email user-follow-modify playlist-modify-public playlist-modify-private',
            $this->getParameter('spotify_client_id'),
            $this->getParameter('spotify_redirect_uri')
        );

        return $this->redirect($oauth_string);
    }

    /**
     * @Route("/exchange_token", name="exchange_token")
     */
    public function token(Request $request): Response
    {
        $authorization_code = $request->get('code');

        try {

            $body = [
                'redirect_uri' => $this->getParameter('spotify_redirect_uri'),
                'code' => $authorization_code,
                'grant_type' => 'authorization_code'
            ];

            $basicAuth = base64_encode(sprintf('%s:%s', $this->getParameter('spotify_client_id'), $this->getParameter('spotify_client_secret')));

            $header = [
                'Authorization' => sprintf('Basic %s', $basicAuth),
                'Content-Type' => 'application/x-www-form-urlencoded'
            ];

            $response = $this->httpClient->request(
                'POST',
                'https://accounts.spotify.com/api/token',
                [
                    'body' => $body,
                    'headers' => $header,
                ]

            );

            $json_response = json_decode($response->getContent(), true);
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(
                    'Error : %s',
                    $e->getMessage()
                )
            );
            return $this->json($e->getMessage());
        }

        $userAccessToken = $json_response['access_token'];

        $user = $this->spotifyService->storeSpotifyUser($userAccessToken);

        //return $this->json($json_response);
        return $this->json($user);
    }

    /**
     * @Route("/followArtist", name="followArtist")
     */
    public function followArtist(Request $request): Response
    {
        /** @var User $user */
        $user = $this->userService->getUserFromRequest($request);
        if (null === $user) {
            return new Response('Unauthorized', 401);
        }

        $accessToken = $user->getAccessToken();
        $artistContent = json_decode($request->getContent(), true);
        var_dump($artistContent);
        $artistId = $artistContent['id'];
        $this->spotifyService->followSpotifyArtist($accessToken, $artistId);
        return $this->json('Artist followed');
    }

    /**
     * @Route("/getUserInfos", name="getUserInfos")
     */
    public function getUserInfos(Request $request): Response
    {
        /** @var User $user */
        $user = $this->userService->getUserFromRequest($request);
        if (null === $user) {
            return new Response('Unauthorized', 401);
        }

        $returnArray[] = [
            'id' => $user->getId(),
            'userId' => $user->getUserId(),
            'username' => $user->getUsername(),
            'userImageUrl' => $user->getUserImageUrl(),
        ];
        return $this->json($returnArray);
    }
}
