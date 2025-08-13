<?php

namespace App\DataFixtures;

use App\Entity\{Picture, Restaurant};
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PictureFixtures extends Fixture implements DependentFixtureInterface
{   
    /** @throws Exception   */
    public function load(ObjectManager $manager): void
    {    for ($i = 1; $i <= 20; $i++) {
            /** @var Restaurant $restaurant */
            $restaurant = $this->getReference(RestaurantFixtures::RESTAURANT_REFERENCE . random_int(1, 20), Restaurant::class);

            

            $picture = (new Picture())
                ->setTitle(title: "image n°$i")
                ->setSlug(slug: "slug-article-title")
                ->setRestaurant($restaurant)
                ->setCreatedAt(new DateTimeImmutable());

            $manager->persist($picture);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [RestaurantFixtures::class];
    }
}
