<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DefaultController extends AbstractController
{
    private $logger;

    public function __construct(
        LoggerInterface $logger,
        HttpClientInterface $httpClient)
    {
        $this->logger = $logger;
        $this->httpClient = $httpClient;
    }

    /**
     * @Route("/", name="default")
     */
    public function index(): Response
    {
        return $this->json('Hello world');
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
            'https://accounts.spotify.com/authorize?client_id=%s&response_type=code&redirect_uri=http://127.0.0.1:8080/exchange_token&scope=user-read-private',
            $this->getParameter('spotify_client_id')
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

        return $this->json($json_response);
    }
}
