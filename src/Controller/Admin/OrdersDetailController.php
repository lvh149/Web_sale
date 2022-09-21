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
     * OrdersDetailController constructor.
     *
     * @param OrdersDetailRepository $ordersDetailRepository
     * @param PaginatorInterface $paginatorInterface
     */
    public function __construct(
        OrdersDetailRepository $ordersDetailRepository,
        PaginatorInterface $paginatorInterface
    ) {
        $this->ordersDetailRepository = $ordersDetailRepository;
        $this->paginatorInterface = $paginatorInterface;
    }

    /**
     * @Route("/{id}", name="app_orders_detail_show", requirements={"id" = "\d+"}, methods={"GET"})
     */
    public function show(Request $request): Response
    {
        $order_id= $request->get('id');
        $orders_detail = $this->ordersDetailRepository->findBy(['order' => $order_id]);
        $pagination = $this->paginatorInterface->paginate(
            $orders_detail, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            7 /*limit per page*/
        );
        return $this->render('orders_detail/show.html.twig', [
            'orderdetails' => $pagination,
        ]);
    }
}
