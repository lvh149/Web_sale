<?php

namespace App\Controller;

use App\Entity\Admins;
use App\Form\AdminsType;
use App\Form\EditAdminsType;
use App\Repository\AdminsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/admins")
 */
class AdminsController extends AbstractController
{
    /**
     * @Route("/", name="app_admins_index", methods={"GET"})
     */
    public function index(AdminsRepository $adminsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $admins = $adminsRepository->findAll();
        $pagination = $paginator->paginate(
            $admins, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/ 
            7 /*limit per page*/
        );
        return $this->render('admins/index.html.twig', [
            'admins' => $pagination
        ]);
    }

    /**
     * @Route("/new", name="app_admins_new", methods={"GET", "POST"})
     */
    public function new(Request $request, AdminsRepository $adminsRepository): Response
    {
        $admin = new Admins();
        $form = $this->createForm(AdminsType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adminsRepository->add($admin, true);

            return $this->redirectToRoute('app_admins_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admins/new.html.twig', [
            'admin' => $admin,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_admins_show", methods={"GET"})
     */
    public function show(Admins $admin): Response
    {
        return $this->render('admins/show.html.twig', [
            'admin' => $admin,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_admins_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Admins $admin, AdminsRepository $adminsRepository): Response
    {
        $form = $this->createForm(EditAdminsType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adminsRepository->add($admin, true);

            return $this->redirectToRoute('app_admins_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admins/edit.html.twig', [
            'admin' => $admin,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_admins_delete", methods={"POST"})
     */
    public function delete(Request $request, Admins $admin, AdminsRepository $adminsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$admin->getId(), $request->request->get('_token'))) {
            $adminsRepository->remove($admin, true);
        }

        return $this->redirectToRoute('app_admins_index', [], Response::HTTP_SEE_OTHER);
    }
}
