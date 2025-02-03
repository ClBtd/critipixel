<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Model\Entity\VideoGame;
use App\Tests\Functional\FunctionalTestCase;

final class FilterTest extends FunctionalTestCase
{
    /**
    * @return array<string, array<int|string, list<string>>>
    */
    public function filterProvider(): array
    {
        return [
            'Aucun filtre'       => [[]], 
            'Un seul tag'        => ['tags' => ['1']],
            'Deux tags'          => ['tags' => ['1', '2']]
        ];
    }


    public function testShouldListTenVideoGames(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->clickLink('2');
        self::assertResponseIsSuccessful();
    }

    public function testShouldFilterVideoGamesBySearch(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->submitForm('Filtrer', ['filter[search]' => 'Jeu vidéo 49'], 'GET');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(1, 'article.game-card');
    }

    /**
    * @dataProvider filterProvider
    * @param array<string> $tags
    */
    public function testShouldFilterVideoGamesByTags(array $tags): void
    {

        $this->get('/');
        self::assertResponseIsSuccessful();
        $this->client->submitForm('Filtrer', ['filter[tags]' => $tags], 'GET');
        self::assertResponseIsSuccessful();
        $filteredGames = $this->client->getCrawler()->filter('article.game-card')->count();
        $tagsNumber = count($tags);
                switch($tagsNumber) {
            case 0: 
                $expectedCount = 10;
                break;
            case 1: 
                $expectedCount = $this->countVideoGamesWithTag((int)$tags[0]);
                break;
            case 2:
                $expectedCount = $this->countVideoGamesWithTwoTags((int)$tags[0],(int)$tags[1]);
                break;
            default :
                $expectedCount = 0;
                break;
        }  
        self::assertSame($expectedCount, $filteredGames, "Le filtrage par étiquette(s) ne retourne pas le bon nombre de jeux.");
    }

    public function countVideoGamesWithTag(int $tagId): int
    {
        $em = $this->getEntityManager();
        $videoGameRepository = $em->getRepository(VideoGame::class);

        $query = $videoGameRepository->createQueryBuilder('v')
            ->innerJoin('v.tags', 't')
            ->where('t.id = :tagId')
            ->setParameter('tagId', $tagId)
            ->select('COUNT(v.id)')
            ->getQuery();
    
        return (int) $query->getSingleScalarResult();
    }

    public function countVideoGamesWithTwoTags(int $tagId1, int $tagId2): int
    {
        $videoGameRepository = $this->getContainer()->get('doctrine')->getRepository(VideoGame::class);
    
        $query = $videoGameRepository->createQueryBuilder('v')
            ->select('COUNT(DISTINCT v.id) as count')
            ->innerJoin('v.tags', 't')
            ->where('t.id IN (:tagIds)')
            ->setParameter('tagIds', [$tagId1, $tagId2])
            ->groupBy('v.id')
            ->having('COUNT(DISTINCT t.id) = 2')
            ->getQuery();

        $result = $query->getOneOrNullResult();
        return $result ? (int) $result['count'] : 0;
    }


}
