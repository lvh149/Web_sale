<?php

namespace App\Controller\Admin;

use App\Entity\OrdersDetail;
use App\Form\OrdersDetailType;
use App\Repository\OrdersDetailRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/ordersdetail")
 */
class OrdersDetailController extends AbstractController
{

    /**
     * @Route("/{id}", name="app_orders_detail_show", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(OrdersDetail $ordersDetail, OrdersDetailRepository $ordersDetailRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $order_id= $request->get('id');
        $orders_detail =$ordersDetailRepository->findBy(['order' => $order_id]);
        dd($orders_detail);
        $pagination = $paginator->paginate(
            $orders_detail, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            7 /*limit per page*/
        );
        return $this->render('orders_detail/show.html.twig', [
            'orderdetails' => $pagination,
        ]);
    }
}
