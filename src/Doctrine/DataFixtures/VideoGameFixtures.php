<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Review;
use App\Model\Entity\Tag;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use App\Rating\CalculateAverageRating;
use App\Rating\CountRatingsPerValue;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

final class VideoGameFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly Generator $faker,
        private readonly CalculateAverageRating $calculateAverageRating,
        private readonly CountRatingsPerValue $countRatingsPerValue
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $users = $manager->getRepository(User::class)->findAll();

        $tags = array_map(fn (): Tag => (new Tag())
            ->setName($this->faker->word()), range(0, 19));
        foreach ($tags as $tag) {
            $manager->persist($tag);
        }

        $videoGames = array_map(fn (int $index): VideoGame => (new VideoGame())
            ->setTitle(sprintf('Jeu vidéo %d', $index))
            ->setDescription($this->faker->paragraphs(10, true))
            ->setReleaseDate(new DateTimeImmutable())
            ->setTest($this->faker->paragraphs(6, true))
            ->setRating(($index % 5) + 1)
            ->setImageName(sprintf('video_game_%d.png', $index))
            ->setImageSize(2_098_872), range(0, 49));

        foreach($videoGames as $videoGame) {

            $selectedTags = $this->faker->randomElements($tags, $this->faker->numberBetween(1, 3));
            foreach ($selectedTags as $tag) {
                $videoGame->getTags()->add($tag);
            }

            $reviews = array_map(fn (): Review => (new Review())
            ->setUser($this->faker->randomElement($users))
            ->setComment($this->faker->paragraphs(2, true))
            ->setVideoGame($videoGame)
            ->setRating($this->faker->numberBetween(1, 5)), range(0, 2));

            foreach ($reviews as $review) {
                $videoGame->getReviews()->add($review);
                $manager->persist($review);
            }

            $this->calculateAverageRating->calculateAverage($videoGame);
            $this->countRatingsPerValue->countRatingsPerValue($videoGame);

            $manager->persist($videoGame);
        }

        $manager->flush();

    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
