<?php

namespace App\Controller\Admin;

use App\Entity\Orders;
use App\Form\OrdersType;
use App\Repository\OrdersRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/orders")
 */
class OrdersController extends AbstractController
{
    /**
     * @Route("/", name="app_orders_index", methods={"GET"})
     */
    public function index(OrdersRepository $ordersRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $status = $request->get('status');
        $orders = $ordersRepository->findBy(['status' => $status],);
        $pagination = $paginator->paginate(
            $orders, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            7 /*limit per page*/
        );
        // dd($pagination);
        return $this->render('orders/index.html.twig', [
            'pager' => $pagination,
        ]);
    }

    /**
     * @Route("/{id}", name="app_orders_update", methods={"POST"})
     */
    public function update(Request $request, Orders $order, OrdersRepository $ordersRepository): Response
    {
        $status = $request->request->get('status');
        $order->setStatus($status);
        $ordersRepository->add($order, true);
        return $this->redirectToRoute('app_orders_index', ['status' => 1], Response::HTTP_SEE_OTHER);
    }
}
