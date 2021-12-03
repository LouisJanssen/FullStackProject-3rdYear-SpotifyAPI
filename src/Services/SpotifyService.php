<?php

namespace App\Services;

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

        $user = new User();

        $user
            ->setUserId($userInfo['id'])
            ->setUsername($userInfo['display_name'])
            ->setUserImageUrl('test')
            ->setAccessToken($userAccessToken);
        $this->entityManager->persist($user);
        $userInfo[] = $user;

        $this->entityManager->flush();
        return $userInfo;
    }

    public function storeNotionPages(): array
    {
        $pages = $this->getNotionPages();

        $notionPages = [];
        foreach ($pages['results'] as $page) {

            $existingNotionPage = $this->entityManager->getRepository(NotionPage::class)->findOneByNotionId($page['id']);

            if (null !== $existingNotionPage) {
                continue;
            }

            $notionPage = new NotionPage();
            if (isset($page['properties']['title'])) {
                $title = substr($page['properties']['title']['title'][0]['plain_text'], 0, 255);
            } else {
                $title = 'No title';
            }

            $date = new \DateTime($page['created_time']);
            $notionPage
                ->setNotionId($page['id'])
                ->setCreationDate($date)
                ->setTitle($title);
            $this->entityManager->persist($notionPage);
            $notionPages[] = $notionPage;
        }

        $this->entityManager->flush();
        return $notionPages;
    }

}