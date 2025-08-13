<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use DateTimeImmutable;

#[Route('/api', name: 'app_api_')]
class SecurityController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private SerializerInterface $serializer)
    {

    }

    
    #[Route("/registration", name: 'registration', methods: "POST")] // Route pour l'inscription

    // Documentation OpenAPI pour l'inscription d'un nouvel utilisateur, verifier avec http://localhost:8000/api/doc
        // On utilise l'annotation @OA\Post pour documenter la route

    /**
     * @OA\Post(
     *     path="/api/registration",
     *     summary="Inscription d'un nouvel utilisateur",
     *     @OA\RequestBody(
     *        required=true,
     *        description="Données de l'utilisateur à enregistrer",
     *        @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="email", type="string", example="adresse@email.com"),
     *          @OA\Property(property="password", type="string", example="motdepasse123")
     *        )
     *     ),
     *     @OA\Response(
     *        response=201,
     *        description="Utilisateur créé avec succès",
     *        @OA\JsonContent(
     *           type="object",
     *           @OA\Property(property="user", type="string", example="adresse@email.com"),
     *           @OA\Property(property="apiToken", type="string", example="841cf7407cd7c750c6d06ada738c36f327f87c45"),
     *           @OA\Property(property="roles", type="array", @OA\Items(type="string", example="ROLE_USER"))
     *        )
     *     )
     * )
     */





    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher): JsonResponse// On utilise l'interface UserPasswordHasherInterface pour hasher le mot de passe
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');   // serialieer
        $user->setPassword($userPasswordHasher->hashPassword($user, $user->getPassword())); // recuperer le mot de passe et le hasher
        $user->setCreatedAt(new DateTimeImmutable()); // set la date de creation

        $this->manager->persist($user); // persister l'utilisateur
        $this->manager->flush(); // sauvegarder l'utilisateur
         
        // retour information client 
        return new JsonResponse(
            [   'user' => $user ->getUserIdentifier(), 
                'apiToken' => $user->getApiToken(),
                'roles' => $user ->getRoles()],

            Response::HTTP_CREATED
        );
    }

    // Route pour la connexion
    // On utilise l'authentification JSON
    // On utilise l'annotation #[CurrentUser] pour recuperer l'utilisateur authentifié
    // On retourne les informations de l'utilisateur et son token API
    // Si l'utilisateur n'est pas authentifié, on retourne une erreur 401

   #[Route('/login', name: 'login', methods: 'POST')]
    /** @OA\Post(
     *     path="/api/login",
     *     summary="Connecter un utilisateur",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données de l’utilisateur pour se connecter",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="username", type="string", example="adresse@email.com"),
     *             @OA\Property(property="password", type="string", example="Mot de passe")
     *         )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Connexion réussie",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user", type="string", example="Nom d'utilisateur"),
     *             @OA\Property(property="apiToken", type="string", example="31a023e212f116124a36af14ea0c1c3806eb9378"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string", example="ROLE_USER"))
     *          )
     *      )
     *   )
     */
     
    public function login(#[CurrentUser] ?User $user): JsonResponse
    {
        // si l'utilisateur n'est pas authentifié, on retourne une erreur
        if (null === $user) {
           return new JsonResponse(['message' => 'missing credentials'], Response::HTTP_UNAUTHORIZED);
        }
         
        // si l'utilisateur est authentifié, on retourne les informations de l'utilisateur
        return new JsonResponse(
            [
                'user' => $user ->getUserIdentifier(), 
                'apiToken' => $user->getApiToken(),
                'roles' => $user ->getRoles()],

            Response::HTTP_CREATED
            

           
        );
    }

}


