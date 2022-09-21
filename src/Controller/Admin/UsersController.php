<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use App\Form\UsersType;
use App\Form\EditUsersType;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UsersController extends AbstractController
{
    public function __construct(
        UsersRepository $usersRepository,
        PaginatorInterface $paginatorInterface,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->usersRepository = $usersRepository;
        $this->paginatorInterface = $paginatorInterface;
        $this->passwordHasher = $passwordHasher;
    }
    /**
     * @Route("/customer", name="app_customers_index", methods={"GET"})
     */
    public function indexCustomer(Request $request): Response
    {
        $customer = $this->usersRepository->findBy(['roles' => 2]);
        $pagination = $this->paginatorInterface->paginate(
            $customer,
            $request->query->getInt('page', 1),
        );
        return $this->render('users/customer.html.twig', [
            'users' => $pagination,
        ]);
    }

    /**
     * @Route("customer/{id}/edit", name="app_customers_edit", methods={"GET", "POST"})
     */
    public function editCustomer(Request $request, Users $user): Response
    {
        $id = $request->get('id');
        $user = $this->usersRepository->find($id);
        $form = $this->createForm(EditUsersType::class, $user);
        $form->remove('roles');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->usersRepository->add($user, true);
            $this->addFlash(
                'success',
                'Sửa thông tin thành công'
            );
            return $this->redirectToRoute('app_customers_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('users/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("customer/{id}", name="app_customers_delete", methods={"POST"})
     */
    public function deleteCustomer(Request $request, Users $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $this->usersRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_customers_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/member", name="app_members_index", methods={"GET"})
     */
    public function indexMember(Request $request): Response
    {
        $member = $this->usersRepository->findBy(['roles' => [0, 1]],['id' => 'desc']);
        $pagination = $this->paginatorInterface->paginate(
            $member,
            $request->query->getInt('page', 1),
        );
        return $this->render('users/index.html.twig', [
            'users' => $pagination,
        ]);
    }

    /**
     * @Route("member/new", name="app_members_new", methods={"GET", "POST"})
     */
    public function newMember(Request $request): Response
    {
        $user = new Users();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setIsVerified(true);
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);
            $this->usersRepository->add($user, true);
            $this->addFlash(
                'success',
                'Tạo nhân viên mới thành công'
            );
            return $this->redirectToRoute('app_members_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('users/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }



    /**
     * @Route("member/{id}/edit", name="app_members_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Users $user): Response
    {
        $id = $request->get('id');
        $user = $this->usersRepository->find($id);
        $form = $this->createForm(EditUsersType::class, $user);
        $form->remove('point');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->usersRepository->add($user, true);
            $this->addFlash(
                'success',
                'Sửa thành công'
            );
            return $this->redirectToRoute('app_members_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('users/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("member/{id}", name="app_members_delete", methods={"POST"})
     */
    public function delete(Request $request, Users $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $this->usersRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_members_index', [], Response::HTTP_SEE_OTHER);
    }
}
