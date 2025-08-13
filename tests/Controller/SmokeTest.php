<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

// ce fichier de test vérifie que la page d'accueil de l'API est accessible
// api doc est la documentation de l'API générée par NelmioApiDocBundle
class SmokeTest extends WebTestCase
{
    public function testApiDocUrlIsSecure(): void// test que la page d'accueil de l'API est accessible
    {
        $client = self::createClient();
        $client->request(method: 'get', uri: 'api/doc'); // envoie une requête POST à l'URL 'api/doc'
        self::assertResponseIsSuccessful(); // vérifie que la réponse est réussie (code HTTP 200)
    }

      public function testLoginRouteCanConnectAvalidUser(): void// test que la page d'accueil de l'API est accessible
    {
       $client = self::createClient(); // création d'un client pour simuler une requête HTTP
       $client ->followRedirects(followRedirects: false); // suit les redirections pour obtenir la réponse finale
        $client->request(
            'POST',
            '/api/registration',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
                json_encode([
                'email' => 'toto@example.com',
                'password' => 'toto123',
                'roles' => ['ROLE_USER'],
                'api_token' => 'un_token_unique_ici'
            ], JSON_THROW_ON_ERROR)
        );

      


       
       
       $statusCode = $client->getResponse()->getStatusCode(); // récupère le code de statut de la réponse
       dd($statusCode); // affiche le code de statut pour le débogage
            
            
    }


   
    

    
    
       
    
}