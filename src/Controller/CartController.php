<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Form\CartType;
use App\Repository\CartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cart")
 */
class CartController extends AbstractController
{
    public function __construct(
        CartRepository $cartRepository
    ){
        $this->cartRepository = $cartRepository;
    }
    /**
     * @Route("/", name="app_cart_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('client/cart.html.twig', [
            'carts' => $this->cartRepository->findAll(),
        ]);
    }

}
