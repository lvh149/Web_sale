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
use App\Form\ChangePasswordType;

class AuthController extends AbstractController
{
    public function __construct(
        UsersRepository $usersRepository,
        UserPasswordHasherInterface $passwordHasher,
        AuthenticationUtils $authenticationUtils
    ) {
        $this->authenticationUtils = $authenticationUtils;
        $this->passwordHasher = $passwordHasher;
        $this->usersRepository = $usersRepository;
    }
    public function login(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home_page', [], Response::HTTP_SEE_OTHER);
        } else if ($this->isGranted('ROLE_CUSTOMER')) {
            return $this->redirectToRoute('client_page', [], Response::HTTP_SEE_OTHER);
        }
        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }
    /**
     * @Route("/myinfo", name="app_myinfo", methods={"GET", "POST"})
     */
    public function changeInfo(Request $request): Response
    {
        $user =$this->getUser();
        $form = $this->createForm(EditUsersType::class, $user);
        $form->remove('roles');
        $form->remove('point');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->usersRepository->add($user, true);
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('users/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    /**
     * @Route("/infor", name="customer_infor", methods={"GET", "POST"})
     */
    public function changeInfoCustomer(Request $request): Response
    {
        $user =$this->getUser();
        $form = $this->createForm(EditUsersType::class, $user);
        $form->remove('roles');
        $form->remove('point');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->usersRepository->add($user, true);
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('users/customer_infor.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    /**
     * @Route("/changepassword", name="app_change_password", methods={"GET", "POST"})
     */
    public function changePassword(Request $request): Response
    {
        $user =$this->getUser();

        $form = $this->createForm(ChangePasswordType::class, $user, [
            'validation_groups' => ['default','registration'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->getData()->getNewPassword();
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $newPassword
            );
            $user->setPassword($hashedPassword);
            $this->usersRepository->add($user, true);
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('users/changePassword.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    /**
     * @Route("/cus-changepassword", name="customer_change_password", methods={"GET", "POST"})
     */
    public function changePasswordCustomer(Request $request): Response
    {
        $user =$this->getUser();

        $form = $this->createForm(ChangePasswordType::class, $user, [
            'validation_groups' => ['default','registration'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->getData()->getNewPassword();
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $newPassword
            );
            $user->setPassword($hashedPassword);
            $this->usersRepository->add($user, true);
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('users/cus_changePassword.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
