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

class TrackService
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

    public function getTopTracksForArtist($userAccessToken, Artist $artist): array
    {
        $spotifyBaseUrl = $this->parameterBag->get('spotify_base_url');

        $spotifySearchtUrl = sprintf('%s/artists/%s/top-tracks?market=FR', $spotifyBaseUrl, $artist->getArtistId());
        $authorizationHeader = sprintf('Bearer %s', $userAccessToken);
        
        $response = $this->httpClient->request('GET', $spotifySearchtUrl,[
            'body' => [],
            'headers' => [
                'Authorization' => $authorizationHeader,
                'Content-Type' => 'application/json',
            ]
        ]);

        $jsonTracks = json_decode($response->getContent(), true);
        $tracks = [];

        foreach($jsonTracks['tracks'] as $jsonTrack) {
            $track = new Track();

            $track
                ->setTrackId($jsonTrack['id'])
                ->setTrackName($jsonTrack['name'])
                ->setPreviewUrl($jsonTrack['preview_url'])
                ->setArtist($artist);
            
            $this->entityManager->persist($track);
        }
        $this->entityManager->persist($artist);
        $this->entityManager->flush();
        return $tracks;
    }
}