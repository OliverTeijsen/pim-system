<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminWebController extends AbstractController
{
    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('/admin/users', name: 'admin_users')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/admin/users/create', name: 'admin_create_user')]
    public function create(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $user = new User();
            $user->setEmail($request->request->get('email'));
            $user->setPassword($hasher->hashPassword($user, $request->request->get('password')));
            $user->setRoles(['ROLE_ADMIN']);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/create_user.html.twig');
    }

    /**
     * @throws RandomException
     */
    #[Route('/admin/users/{id}/reset', name: 'admin_reset_password')]
    public function resetPassword(User $user, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        $newPassword = bin2hex(random_bytes(8));
        $user->setPassword($hasher->hashPassword($user, $newPassword));

        $em->flush();

        $this->addFlash('success', 'New password: '.$newPassword);

        return $this->redirectToRoute('admin_users');
    }

    #[Route('/admin/users/{id}/delete', name: 'admin_delete_user')]
    public function deleteUser(User $user, EntityManagerInterface $em): Response
    {
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('admin_users');
    }
}
