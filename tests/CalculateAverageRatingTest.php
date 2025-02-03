<?php

namespace App\Tests;

use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\TestCase;

class CalculateAverageRatingTest extends TestCase
{
    /**
     * @dataProvider provideVideoGame
     */
    public function testShouldCalculateAverageRating(VideoGame $videoGame, ?int $expectedAverageRating): void
    {
        $ratingHandler = new RatingHandler();
        $ratingHandler->calculateAverage($videoGame);

        self::assertSame($expectedAverageRating, $videoGame->getAverageRating());
    }

    /**
     * @return iterable<array{VideoGame, ?int}>
     */
    public static function provideVideoGame(): iterable
    {
        yield 'O review' => [new VideoGame(), null,];

        yield '1 review' => [self::createVideoGame(5), 5,];

        yield 'Several reviews' => [
            self::createVideoGame(3, 2, 5, 5, 1, 4, 5, 2),
            4,
        ];
    }

    private static function createVideoGame(int ...$ratings): VideoGame
    {
        $videoGame = new VideoGame();

        foreach ($ratings as $rating) {
            $videoGame->getReviews()->add((new Review())->setRating($rating));
        }

        return $videoGame;
    }
}
