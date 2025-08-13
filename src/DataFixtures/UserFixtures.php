<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface

{   
    public const USER_NB_TUPPLE = 20;
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
        
    }

    public function load(ObjectManager $manager): void
    {   
        for ($i = 1; $i <= self::USER_NB_TUPPLE; $i++) {
            $user = (new User())
                ->setFirstname("Firstname $i")
                ->setLastname("Lastname $i")
                ->setGuestNumber(random_int(0, 10))
                ->setEmail("email.$i@studi.fr")
                ->setCreatedAt(new DateTimeImmutable()); 
            
            $user->setPassword($this->passwordHasher->hashPassword($user, "password$i"));
            $manager->persist($user);
        }

        $manager->flush();
    }
    public static function getGroups(): array
    {
        return ['independant', 'user'];
    }
}
