<?php

namespace App\DataFixtures;

use App\Entity\Restaurant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use DateTimeImmutable;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RestaurantFixtures extends Fixture
{   
    public const RESTAURANT_NB_TUPPLE = 20;
    public const RESTAURANT_REFERENCE = 'restaurant';
    public function load(ObjectManager $manager): void
    {   
        for ($i = 1; $i <= self::RESTAURANT_NB_TUPPLE; $i++) {
            $restaurant = (new Restaurant())
                ->setName("Restaurant n°$i")
                ->setDescription("Description n°$i")
                ->setAmOpeningTime([])
                ->setPmOpeningTime([])
                ->setMaxGuest(random_int(10,50))
                ->setCreatedAt(new DateTimeImmutable());

            $manager->persist($restaurant);
            $this->addReference(self::RESTAURANT_REFERENCE . $i, $restaurant, Restaurant::class); // cette ligne permet de créer une référence pour ce restaurant
            //pour pouvoir l'utiliser dans d'autres fixtures
        }

        $manager->flush();
    }
}
