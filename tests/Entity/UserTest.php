<?php

namespace App\Tests\Entity;

use App\Entity\User;// 
use PHPUnit\Framework\TestCase;// ce fichier de test vérifie que l'entité User fonctionne comme prévu


// ce fichier de test vérifie que les entités fonctionnent comme prévu
// entité User vérifie que l'utilisateur a un token API automatiquement généré et au moins un rôle 'ROLE_USER'
class UserTest extends TestCase
{   
    // test que l'entité User a un token API généré automatiquement
    public function testTheAutomaticApiTokenSettingWhenAnUserIsCreated(): void
    {
        $user = new User();// création d'un nouvel utilisateur
        $this->assertNotNull($user->getApiToken()); // vérification que le token API n'est pas nul
    }
    public function testThanAnUserHasAtLeastOneRoleUser(): void // test que l'utilisateur a au moins un rôle 'ROLE_USER'
    {
        $user = new User();
        $this->assertContains($needle = 'ROLE_USER', $user->getRoles());// vérification que le rôle 'ROLE_USER' est présent dans les rôles de l'utilisateur
    }

    
        
    
}