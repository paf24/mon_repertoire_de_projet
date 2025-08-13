<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;


#[Route('/api/restaurant', name: 'app_api_restaurant_')]
class RestaurantController extends AbstractController
{
    private int $maxGuests;

    public function __construct(
        private EntityManagerInterface $manager,
        private RestaurantRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {
        $this->maxGuests = 10;
    }

    #[Route('', name: 'new', methods: ['POST'])]
    /**
     * @OA\Post(
     *     path="/api/restaurant",
     *     summary="Créer un nouveau restaurant",
     *     @OA\RequestBody(
     *        required=true,
     *        description="Données du restaurant à enregistrer",
     *        @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="name", type="string", example="nom du restaurant"),
     *          @OA\Property(property="description", type="string", example="description du restaurant"),
     *          @OA\Property(property="max_guest", type="integer", example=10)
     *        )
     *     ),
     *     @OA\Response(
     *        response=201,
     *        description="Restaurant créé avec succès",
     *        @OA\JsonContent(
     *           type="object",
     *           @OA\Property(property="id", type="integer", example=1),
     *           @OA\Property(property="name", type="string", example="NOM DU RESTAURANT"),
     *           @OA\Property(property="description", type="string", example="description du restaurant"),
     *           @OA\Property(property="createdAt", type="string", format="date-time")
     *        )
     *     )
     * )
     */
    
    public function new(Request $request): JsonResponse
    {
        $restaurant = $this->serializer->deserialize(
            $request->getContent(),
            Restaurant::class,
            'json'
        );

        $restaurant->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($restaurant);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($restaurant, 'json');

        $location = $this->urlGenerator->generate(
            'app_api_restaurant_show',
            ['id' => $restaurant->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    /**
     * @OA\Get(
     *     path="/api/restaurant/{id}",
     *     summary="Afficher un restaurant par ID",
     *     @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID du restaurant à afficher",
     *        @OA\Schema(type="integer", example=1)
     *      ),
     *     @OA\Response(
     *        response=200,
     *        description="Restaurant trouvée avec succès",
     *        @OA\JsonContent(
     *           type="object",
     *           @OA\Property(property="id", type="integer", example=1),
     *           @OA\Property(property="name", type="string", example="NOM DU RESTAURANT"),
     *           @OA\Property(property="description", type="string", example="description du restaurant"),
     *           @OA\Property(property="createdAt", type="string", format="date-time")
     *        )
     *     ),
     *     @OA\Response(
     *        response=404,
     *        description="Restaurant non trouvée"
     *     )
     * )
     */


    public function show(int $id): JsonResponse
    {
        $restaurant = $this->repository->find($id);

        if (!$restaurant) {
            throw $this->createNotFoundException("No Restaurant found for ID {$id}");
        }

        $data = $this->serializer->serialize($restaurant, 'json');

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(Request $request, int $id): JsonResponse
    {
        $restaurant = $this->repository->find($id);

        if (!$restaurant) {
            throw $this->createNotFoundException("No Restaurant found for ID {$id}");
        }

        $this->serializer->deserialize(
            $request->getContent(),
            Restaurant::class,
            'json',
            ['object_to_populate' => $restaurant]
        );

        $this->manager->flush();

        $data = $this->serializer->serialize($restaurant, 'json');

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    /**
     * @OA\Delete(
     *     path="/api/restaurant/{id}",
     *     summary="Supprimer un restaurant par ID",
     *     @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID du restaurant à supprimer",
     *        @OA\Schema(type="integer", example=1)
     *      ),
     *     @OA\Response(
     *        response=204,
     *        description="Restaurant supprimé avec succès, super"
     *     ),
     *     @OA\Response(
     *        response=404,
     *        description="Restaurant non trouvé"
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $restaurant = $this->repository->find($id);

        if (!$restaurant) {
            throw $this->createNotFoundException("No Restaurant found for ID {$id}");
        }

        $this->manager->remove($restaurant);
        $this->manager->flush();

        return $this->json(['message' => "Restaurant resource deleted"], Response::HTTP_NO_CONTENT);
    }
}
