<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SpotifyService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(
        EntityManagerInterface $entityManager,
        HttpClientInterface $httpClient,
        ParameterBagInterface $parameterBag)
    {
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;
        $this->parameterBag = $parameterBag;
    }

    public function getSpotifyUser($userAccessToken): array
    {
        $spotifyBaseUrl = $this->parameterBag->get('spotify_base_url');

        $spotifyCurrentUserUrl = sprintf('%s/me', $spotifyBaseUrl);
        $authorizationHeader = sprintf('Bearer %s', $userAccessToken);

        $userInfo = $this->httpClient->request('GET', $spotifyCurrentUserUrl,[
            'body' => [],
            'headers' => [
                'Authorization' => $authorizationHeader,
                'Content-Type' => 'application/json'
            ]
        ])  ;
        return json_decode($userInfo->getContent(), true);
    }

    public function storeSpotifyUser($userAccessToken): array
    {
        $userInfo = $this->getSpotifyUser($userAccessToken);

        $existingUserId = $this->entityManager->getRepository(User::class)->findOneByUserId($userInfo['id']);

        if (null !== $existingUserId) {
            $existingUserId->setAccessToken($userAccessToken);
            $userFrontToken = $existingUserId->getFrontToken();
        }
        else{
            $user = new User();
            $userImageUrl = $userInfo['images']['url'] ?? 'https://redcdn.net/nimo/monthly_2019_09/small.quokka-3.jpg.1f1fba9c647d47bc0644b04f689dae47.jpg';

            $frontTokenGenerate = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');

            $user
                ->setUserId($userInfo['id'])
                ->setUsername($userInfo['display_name'])
                ->setUserImageUrl($userImageUrl)
                ->setAccessToken($userAccessToken)
                ->setFrontToken($frontTokenGenerate);
            $this->entityManager->persist($user);
            $userInfo[] = $user;
            $userFrontToken = $user->getFrontToken();
        }
        $this->entityManager->flush();
        return [$userFrontToken];
    }

    public function followSpotifyArtist($accessToken, $artistId): array
    {
        $spotifyBaseUrl = $this->parameterBag->get('spotify_base_url');

        $spotifyCurrentUserUrl = sprintf('%s/me/following?type=artist', $spotifyBaseUrl);
        $authorizationHeader = sprintf('Bearer %s', $accessToken);

        $artistFollowed = $this->httpClient->request('PUT', $spotifyCurrentUserUrl,[
            'json' => [
                'ids'=> [$artistId]
            ],
            'headers' => [
                'Authorization' => $authorizationHeader,
                'Content-Type' => 'application/json'
            ]
        ])  ;
        var_dump($artistFollowed->getStatusCode());
        return ['Artist Followed'];
    }
}