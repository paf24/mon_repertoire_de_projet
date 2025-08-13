<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiTokenAuthentificationAuthenticator extends AbstractAuthenticator
{
    public function __construct(private UserRepository $repository)
    {
        
    }
    public function supports(Request $request): ?bool // cette méthode vérifie si l'authentification est supportée
    {
        // On vérifie si l'en-tête X-API-TOKEN est présent dans la requête
        return $request->headers->has('X-API-TOKEN');
    }

    public function authenticate(Request $request): Passport // cette méthode est appelée pour authentifier l'utilisateur
    {   
        $apiToken = $request->headers->get('X-API-TOKEN'); // on récupère le token API depuis l'en-tête de la requête
        if (null === $apiToken) {
            throw new CustomUserMessageAuthenticationException(message:'No API token provided'); // si le token n'est pas fourni, on lance une exception
        }

        $user = $this->repository->findOneBy(['apiToken' => $apiToken]); // on cherche l'utilisateur avec le token API
        if (null === $user) {
            throw new UserNotFoundException(); // si l'utilisateur n'est pas trouvé, on lance une exception
        }

        return new SelfValidatingPassport(new UserBadge($user->getUserIdentifier())); // on crée un passeport avec le token API
        // le UserBadge est une classe qui représente l'utilisateur authentifié
        // il est utilisé pour charger l'utilisateur depuis la base de données
        // et pour vérifier si le token est valide)
        // TODO: Implement authenticate() method.
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response // cette méthode est appelée si l'authentification réussit
    {
       return null; // si l'authentification réussit, on retourne null pour ne pas interrompre le flux de la requête
        // ou on peut retourner une réponse personnalisée si nécessaire
        // par exemple, on peut retourner une réponse JSON avec les informations de l'utilisateur
        // return new JsonResponse(['message' => 'Authentication successful'], Response::HTTP_OK);
        // mais dans ce cas, on ne le fait pas car on utilise l'authentification stateless
        // donc on ne retourne pas de réponse, on laisse Symfony gérer la réponse
        // car on utilise l'authentification stateless (sans état)
        // TODO: Implement onAuthenticationSuccess() method.
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response // cette méthode est appelée si l'authentification échoue
    {   
        return new JsonResponse(
            ['message' => $exception->getMessageKey(), 'data' => $exception->getMessageData()],
            Response::HTTP_UNAUTHORIZED
        ); // retourne une réponse JSON avec le message d'erreur et le code HTTP 401 Unauthorized
        
        // TODO: Implement onAuthenticationFailure() method.
    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }
}
