<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UsersRepository;
use App\Form\EditUsersType;

class AuthController extends AbstractController
{

    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home_page', [], Response::HTTP_SEE_OTHER);
        } else if ($this->isGranted('ROLE_CUSTOMER')) {
            // route ...
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }
    /**
     * @Route("/dashboard/myinfo", name="app_myinfo", methods={"GET", "POST"})
     */
    public function edit(Request $request, UsersRepository $usersRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user =$this->getUser();
        $currentPassword = $user->getPassword();

        $form = $this->createForm(EditUsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($currentPassword != $user->getPassword()) {
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                );
                $user->setPassword($hashedPassword);
            }
            $usersRepository->add($user, true);
            return $this->redirectToRoute('home_page', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('users/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
