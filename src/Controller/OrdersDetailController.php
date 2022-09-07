<?php

namespace App\Controller;

use App\Entity\OrdersDetail;
use App\Form\OrdersDetailType;
use App\Repository\OrdersDetailRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/orders/detail")
 */
class OrdersDetailController extends AbstractController
{
    /**
     * @Route("/", name="app_orders_detail_index", methods={"GET"})
     */
    public function index(OrdersDetailRepository $ordersDetailRepository): Response
    {
        return $this->render('orders_detail/index.html.twig', [
            'orders_details' => $ordersDetailRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_orders_detail_new", methods={"GET", "POST"})
     */
    public function new(Request $request, OrdersDetailRepository $ordersDetailRepository): Response
    {
        $ordersDetail = new OrdersDetail();
        $form = $this->createForm(OrdersDetailType::class, $ordersDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ordersDetailRepository->add($ordersDetail, true);

            return $this->redirectToRoute('app_orders_detail_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('orders_detail/new.html.twig', [
            'orders_detail' => $ordersDetail,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_orders_detail_show", methods={"GET"})
     */
    public function show(OrdersDetail $ordersDetail): Response
    {
        return $this->render('orders_detail/show.html.twig', [
            'orders_detail' => $ordersDetail,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_orders_detail_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, OrdersDetail $ordersDetail, OrdersDetailRepository $ordersDetailRepository): Response
    {
        $form = $this->createForm(OrdersDetailType::class, $ordersDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ordersDetailRepository->add($ordersDetail, true);

            return $this->redirectToRoute('app_orders_detail_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('orders_detail/edit.html.twig', [
            'orders_detail' => $ordersDetail,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_orders_detail_delete", methods={"POST"})
     */
    public function delete(Request $request, OrdersDetail $ordersDetail, OrdersDetailRepository $ordersDetailRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ordersDetail->getId(), $request->request->get('_token'))) {
            $ordersDetailRepository->remove($ordersDetail, true);
        }

        return $this->redirectToRoute('app_orders_detail_index', [], Response::HTTP_SEE_OTHER);
    }
}
