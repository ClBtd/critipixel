<?php

namespace App\Tests;

use App\Model\Entity\NumberOfRatingPerValue;
use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\TestCase;

class CountRatingsPerValueTest extends TestCase
{
    /**
     * @dataProvider provideVideoGame
     */
    public function testShouldCountRatingsPerValue(VideoGame $videoGame, NumberOfRatingPerValue $expectedNumberOfRatingPerValue): void
    {
        $ratingHandler = new RatingHandler();
        $ratingHandler->countRatingsPerValue($videoGame);

        self::assertEquals($expectedNumberOfRatingPerValue, $videoGame->getNumberOfRatingsPerValue());
    }

    /**
     * @return iterable<array{VideoGame, NumberOfRatingPerValue}>
     */
    public static function provideVideoGame(): iterable
    {
        yield 'O review' => [new VideoGame(), new NumberOfRatingPerValue()];

        yield '1 review' => [self::createVideoGame(5), self::createExpectedState(five:1)];

        yield 'several reviews' => [self::createVideoGame(1, 1, 2, 3, 3, 3, 4, 4, 5), self::createExpectedState(2, 1, 3, 2, 1)];
    }

    private static function createVideoGame(int ...$ratings): VideoGame
    {
        $videoGame = new VideoGame();

        foreach ($ratings as $rating) {
            $videoGame->getReviews()->add((new Review())->setRating($rating));
        }

        return $videoGame;
    }

    private static function createExpectedState(int $one = 0, int $two = 0, int $three = 0, int $four = 0, int $five = 0): NumberOfRatingPerValue
    {
        $state = new NumberOfRatingPerValue();

        for($i=0; $i<$one; $i++) {
            $state->increaseOne();
        }

        for($i=0; $i<$two; $i++) {
            $state->increaseTwo();
        }

        for($i=0; $i<$three; $i++) {
            $state->increaseThree();
        }

        for($i=0; $i<$four; $i++) {
            $state->increaseFour();
        }

        for($i=0; $i<$five; $i++) {
            $state->increaseFive();
        }

        return $state;
    }
}
