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
    // /**
    //  * @Route("/", name="app_cart_detail_index", methods={"GET"})
    //  */
    // public function index(CartDetailRepository $cartDetailRepository): Response
    // {
    //     return $this->render('cart_detail/index.html.twig', [
    //         'cart_details' => $cartDetailRepository->findAll(),
    //     ]);
    // }

}
