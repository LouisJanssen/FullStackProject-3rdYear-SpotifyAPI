<?php

namespace App\Services;

use App\Entity\User;
use App\Entity\Artist;
use App\Controller\DefaultController;
use App\Entity\Genre;
use App\Entity\Track;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArtistService
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

    public function getSpotifyArtist($userAccessToken): Artist
    {
        $spotifyBaseUrl = $this->parameterBag->get('spotify_base_url');
        $artistId = "6Ip8FS7vWT1uKkJSweANQK";

        $spotifySearchtUrl = sprintf('%s/artists/%s', $spotifyBaseUrl, $artistId);
        $authorizationHeader = sprintf('Bearer %s', $userAccessToken);
        
        $response = $this->httpClient->request('GET', $spotifySearchtUrl,[
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

        var_dump($jsonArtist['genres']);
    
        $artist
            ->setArtistId($jsonArtist['id'])
            ->setArtistName($jsonArtist['name'])
            ->setPopularity($jsonArtist['popularity'])
            ->setFollowers($jsonArtist['followers']['total'])
            ->setImage('test');
        
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
        return $artist;
    }
}