<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Users;
use App\Entity\Categories;
use App\Repository\CartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ProductsRepository;
use App\Repository\CategoriesRepository;
use App\Repository\ParametersRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;


class ClientController extends AbstractController
{
    public function __construct(ProductsRepository $productsRepository, CategoriesRepository $categoriesRepository, ParametersRepository $parametersRepository, CartRepository $cartRepository)
    {
        $this->productsRepository = $productsRepository;
        $this->categoriesRepository = $categoriesRepository;
        $this->parametersRepository = $parametersRepository;
        $this->cartRepository = $cartRepository;
    }

    public function index(PaginatorInterface $paginator, Request $request)
    {
        $product = $this->productsRepository->findAll();
        $categories = $this->categoriesRepository->findAll();
        $products = $paginator->paginate(
            $product, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/
        );

        return $this->render('client/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function category(PaginatorInterface $paginator, Request $request)
    {
        if ($request->get('category')) {
            $category = $request->get('category');
            $product = $this->productsRepository->findByNameCategory($category);
        } else {
            $product = $this->productsRepository->findAll();
        }
        if ($request->get('size')) {
            $id = $request->get('size');
            $product = $this->productsRepository->findBySize($id,$category);
        }
        $categories = $this->categoriesRepository->findAll();
        $params = $this->parametersRepository->findAll();
        if (!$product) {
            // tao 1 trang k co san pham
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }
        $products = $paginator->paginate(
            $product, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/
        );
        return $this->render('client/category.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'params' => $params,
        ]);
    }

    /**
     * @Route("/filter-product", name="filter_product", methods={"GET", "POST"})
     */
    public function filter(Request $request, PaginatorInterface $paginator)
    {
        $id_size = $request->get('size');
        $category = $request->get('category');
        $product = $this->productsRepository->findBySize($id_size, $category);
        $products = $paginator->paginate(
            $product, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/
        );
        return $this->render('client/show_product.html.twig', [
            'products' => $products,
        ]);
        // return $this->json($products);
    }

    public function product(Request $request)
    {
        $product_id = $request->get('product');
        $product = $this->productsRepository->find($product_id);
        return $this->render('client/product.html.twig', [
            'product' => $product,
        ]);
    }
    /**
     * @Route("add-cart", name="add_to_cart", methods={"GET", "POST"})
     */
    public function addtocart(Request $request)
    {
        dd($request);
        $cart = new Cart();
        if ($this->isGranted('ROLE_CUSTOMER')) {
            $customer_id = $this->getUser()->getId();
            $check_cart = $this->cartRepository->findBy(['customer' => $customer_id]);
            if (!$check_cart) {
                $cart->setCustomer($this->getUser());
                $this->cartRepository->add($cart, true);
                $cart_id = $cart->getId();
            } else {
                $cart_id = $check_cart[0]->getId();
            }
        }
        return $this->render('client/product.html.twig', [
            // 'product' => $product,
        ]);
    }
    public function contact(Request $request)
    {
        return $this->render('client/contact.html.twig');
    }
}
