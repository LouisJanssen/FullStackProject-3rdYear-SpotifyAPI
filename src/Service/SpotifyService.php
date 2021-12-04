<?php

namespace App\Service;

use App\Entity\Artist;
use App\Entity\Genre;
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
            $userImageUrl = $userInfo['images'][0]['url'] ?? 'https://redcdn.net/nimo/monthly_2019_09/small.quokka-3.jpg.1f1fba9c647d47bc0644b04f689dae47.jpg';

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

    public function getSpotifyArtist($userAccessToken, $artistId): array
    {
        $spotifyBaseUrl = $this->parameterBag->get('spotify_base_url');

        $spotifySearchUrl = sprintf('%s/artists/%s', $spotifyBaseUrl, $artistId);
        $authorizationHeader = sprintf('Bearer %s', $userAccessToken);

        $response = $this->httpClient->request('GET', $spotifySearchUrl,[
            'body' => [],
            'headers' => [
                'Authorization' => $authorizationHeader,
                'Content-Type' => 'application/json',
            ]
        ]);
        $jsonArtist = json_decode($response->getContent(), true);

        $artist = $this->entityManager->getRepository(Artist::class)->findOneByArtistId($jsonArtist['id']);

        if (null === $artist) {
            $artist = new Artist();
        }

        $artistImageUrl = $jsonArtist['images'][0]['url'] ?? 'https://redcdn.net/nimo/monthly_2019_09/small.quokka-3.jpg.1f1fba9c647d47bc0644b04f689dae47.jpg';

        $artist
            ->setArtistId($jsonArtist['id'])
            ->setArtistName($jsonArtist['name'])
            ->setPopularity($jsonArtist['popularity'])
            ->setFollowers($jsonArtist['followers']['total'])
            ->setImage($artistImageUrl);

        foreach ($jsonArtist['genres'] as $jsonGenre) {

            $genre = $this->entityManager->getRepository(Genre::class)->findOneByName($jsonGenre);

            if (null === $genre) {
                $genre = new Genre();
            }

            $genre
                ->setName($jsonGenre);

            $artist->addGenre($genre);
            $genre->addArtist($artist);
            $this->entityManager->persist($genre);

        }

        $this->entityManager->persist($artist);
        $this->entityManager->flush();

        $returnArray[] = [
            'id' => $artist->getId(),
            'artistId' => $artist->getArtistId(),
            'artistName' => $artist->getArtistName(),
            'artistImageUrl' => $artist->getImage(),
            'artistFollowers' => $artist->getFollowers(),
            'artistPopularity' => $artist->getPopularity(),
        ];

        return $returnArray;
    }
}