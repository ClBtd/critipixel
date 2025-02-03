<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Model\Entity\User;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ShowTest extends FunctionalTestCase
{
    public function testShouldShowVideoGame(): void
    {
        $this->get('/jeu-video-0');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Jeu vidéo 0');
    }

    /*public function testShouldPostReview(): void
    {
        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $entityManager->beginTransaction();

        $user = new User();
        $user->setEmail('testuser@example.com');
        $user->setPlainPassword('password');
        $user->setUsername('test');

        $entityManager->persist($user);
        $entityManager->flush();

        $this->login($user->getEmail());
        $this->get('/jeu-video-49');
        self::assertResponseIsSuccessful();
        $this->submit(
            'Poster',
            [
                'review[rating]' => 4,
                'review[comment]' => 'Mon commentaire',
            ]
        );
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $crawler = $this->client->followRedirect();
        
        self::assertSelectorTextContains('div.list-group-item:last-child h3', 'test');
        self::assertSelectorTextContains('div.list-group-item:last-child p', 'Mon commentaire');
        self::assertSelectorTextContains('div.list-group-item:last-child span.value', '4');

        $entityManager->rollback();
    }*/
}
