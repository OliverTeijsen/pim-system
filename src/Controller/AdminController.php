<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[\AllowDynamicProperties]
#[Route('/api/admin')]
#[IsGranted('ROLE_SUPER_ADMIN')]
class AdminController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $hasher,
        UserRepository $userRepository,
    ) {
        $this->entityManager = $entityManager;
        $this->hasher = $hasher;
        $this->userRepository = $userRepository;
    }

    #[Route('/users', methods: ['GET'])]
    public function getUsers(): JsonResponse
    {
        return $this->json($this->userRepository->findAll());
    }

    #[Route('/users', methods: ['POST'])]
    public function createUser(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($hasher->hashPassword($user, $data['password']));
        $user->setRoles(['ROLE_ADMIN']);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($user, Response::HTTP_CREATED);
    }

    /**
     * @throws RandomException
     */
    #[Route('/users/{id}/reset-password', methods: ['POST'])]
    public function resetPassword(User $user, UserPasswordHasherInterface $hasher, EntityManagerInterface $entityManager): JsonResponse
    {
        $newPassword = bin2hex(random_bytes(8));
        $user->setPassword($hasher->hashPassword($user, $newPassword));

        $entityManager->flush();

        return $this->json(['new_password' => $newPassword]);
    }

    #[Route('/users/{id}', methods: ['DELETE'])]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
