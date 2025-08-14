 $client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'toto',
                'password' => 'toto',
            ], JSON_THROW_ON_ERROR) // encode les données en JSON et lance une exception en cas d'erreur
        );