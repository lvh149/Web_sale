<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UsersType;
use App\Form\EditUsersType;
use App\Form\CustomersType;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/users")
 */
class UsersController extends AbstractController
{
    /**
     * @Route("/", name="app_users_index", methods={"GET"})
     */
    public function index(UsersRepository $usersRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $roles = $request->get('role') == 'customer' ? ['ROLE_CUSTOMER'] :  ['ROLE_ADMIN', 'ROLE_SUPERADMIN'];

        $arruser = [];
        foreach ($roles as $role) {
            $users = $usersRepository->findByRole($role);
            $arruser = array_merge($arruser, $users);
        }
        // $test = array_merge($arruser[0],$arruser[1]);
        $pagination = $paginator->paginate(
            $arruser, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            7 /*limit per page*/
        );
        return $this->render('users/index.html.twig', [
            'users' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="app_users_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UsersRepository $usersRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new Users();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);
            $usersRepository->add($user, true);

            return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('users/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_users_show", methods={"GET"})
     */
    public function show(Users $user): Response
    {
        return $this->render('users/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_users_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Users $user, UsersRepository $usersRepository): Response
    {
        $id = $request->get('id');
        $user = $usersRepository->find($id);
        $role = $user->getRoles()[0];
        if ($role == 'ROLE_CUSTOMER') {
            $form = $this->createForm(CustomersType::class, $user);
            $form->handleRequest($request);
        } else {
            $form = $this->createForm(EditUsersType::class, $user);
            $form->handleRequest($request);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $usersRepository->add($user, true);
            return $this->redirectToRoute('app_users_index', ['role'=>$role], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('users/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_users_delete", methods={"POST"})
     */
    public function delete(Request $request, Users $user, UsersRepository $usersRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $usersRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
    }

    public function findUsersOfRoles($roles)
    {
        $condition = 'u.roles LIKE :roles0';
        foreach ($roles as $key => $role) {
            if ($key !== 0) {
                $condition .= " OR u.roles LIKE :roles" . $key;
            }
        }

        $query = $this->entityManager->createQueryBuilder('u')
            ->where($condition);
        foreach ($roles as $key => $role) {
            $query->setParameter('roles' . $key, '%"' . $role . '"%');
        }

        return $query->getQuery()->getResult();
    }
}
