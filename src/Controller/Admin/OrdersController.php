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
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

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
        $orders = $ordersRepository->findByStatus($status);
        $pagination = $paginator->paginate(
            $orders,
            $request->query->getInt('page', 1),
        );
        return $this->render('orders/index.html.twig', [
            'pager' => $pagination,
        ]);
    }

    /**
     * @Route("/{id}", name="app_orders_update", methods={"POST"})
     */
    public function update(Request $request, Orders $order, OrdersRepository $ordersRepository,MailerInterface $mailer): Response
    {
        $status = $request->request->get('status');
        $order->setStatus($status);
        $order->setAdminId($this->getUser());
        $ordersRepository->add($order, true);
        if ($status = 2) {
            $email = (new TemplatedEmail())
                ->from(new Address('rfaceibookp3@gmail.com', 'hung'))
                ->to($order->getCustomerId()->getEmail())
                ->subject('Bạn đã đặt hàng thành công')
                ->html("Xin chào" . " " . $order->getCustomerId()->getName() . "<br> Đơn hàng của bạn đã được xét duyệt");
            // ->html('Đơn hàng của bạn đang được xét duyệt');
            // ->htmlTemplate('reset_password/email.html.twig');
            $mailer->send($email);
        } else {
            $email = (new TemplatedEmail())
                ->from(new Address('rfaceibookp3@gmail.com', 'hung'))
                ->to($order->getCustomerId()->getEmail())
                ->subject('Bạn đã đặt hàng thành công')
                ->html("Xin chào" . " " . $order->getCustomerId()->getName() . "<br> Đơn hàng của bạn bị huỷ");
            // ->html('Đơn hàng của bạn đang được xét duyệt');
            // ->htmlTemplate('reset_password/email.html.twig');
            $mailer->send($email);
        }
        return $this->redirectToRoute('app_orders_index', ['status' => 1], Response::HTTP_SEE_OTHER);
    }
}
