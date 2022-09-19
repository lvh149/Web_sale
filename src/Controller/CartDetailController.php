<?php

namespace App\Controller;

use App\Entity\CartDetail;
use App\Form\CartDetailType;
use App\Repository\CartDetailRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cart/detail")
 */
class CartDetailController extends AbstractController
{
    /**
     * @Route("/", name="app_cart_detail_index", methods={"GET"})
     */
    public function index(CartDetailRepository $cartDetailRepository): Response
    {
        return $this->render('cart_detail/index.html.twig', [
            'cart_details' => $cartDetailRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_cart_detail_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CartDetailRepository $cartDetailRepository): Response
    {
        $cartDetail = new CartDetail();
        $form = $this->createForm(CartDetailType::class, $cartDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cartDetailRepository->add($cartDetail, true);

            return $this->redirectToRoute('app_cart_detail_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cart_detail/new.html.twig', [
            'cart_detail' => $cartDetail,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_cart_detail_show", methods={"GET"})
     */
    public function show(CartDetail $cartDetail): Response
    {
        return $this->render('cart_detail/show.html.twig', [
            'cart_detail' => $cartDetail,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_cart_detail_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, CartDetail $cartDetail, CartDetailRepository $cartDetailRepository): Response
    {
        $form = $this->createForm(CartDetailType::class, $cartDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cartDetailRepository->add($cartDetail, true);

            return $this->redirectToRoute('app_cart_detail_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cart_detail/edit.html.twig', [
            'cart_detail' => $cartDetail,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_cart_detail_delete", methods={"POST"})
     */
    public function delete(Request $request, CartDetail $cartDetail, CartDetailRepository $cartDetailRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cartDetail->getId(), $request->request->get('_token'))) {
            $cartDetailRepository->remove($cartDetail, true);
        }

        return $this->redirectToRoute('app_cart_detail_index', [], Response::HTTP_SEE_OTHER);
    }
}
