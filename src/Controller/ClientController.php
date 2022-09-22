<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartDetail;
use App\Entity\Users;
use App\Entity\Categories;
use App\Entity\Orders;
use App\Entity\OrdersDetail;
use App\Repository\CartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ProductsRepository;
use App\Repository\CartDetailRepository;
use App\Repository\CategoriesRepository;
use App\Repository\ParametersRepository;
use App\Repository\OrdersDetailRepository;
use App\Repository\OrdersRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\VarDumper\VarDumper;

class ClientController extends AbstractController
{
    public function __construct(
        ProductsRepository $productsRepository,
        PaginatorInterface $paginatorInterface,
        CategoriesRepository $categoriesRepository,
        ParametersRepository $parametersRepository,
        CartRepository $cartRepository,
        OrdersRepository $ordersRepository,
        OrdersDetailRepository $ordersDetailRepository,
        ManagerRegistry $managerRegistry,
        CartDetailRepository $cartDetailRepository
    ) {
        $this->productsRepository = $productsRepository;
        $this->categoriesRepository = $categoriesRepository;
        $this->parametersRepository = $parametersRepository;
        $this->cartRepository = $cartRepository;
        $this->cartDetailRepository = $cartDetailRepository;
        $this->ordersDetailRepository = $ordersDetailRepository;
        $this->ordersRepository = $ordersRepository;
        $this->managerRegistry = $managerRegistry;
        $this->paginatorInterface = $paginatorInterface;
    }

    public function index(Request $request)
    {
        $product = $this->productsRepository->findAll();
        $categories = $this->categoriesRepository->findAll();
        $products = $this->paginatorInterface->paginate(
            $product,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('client/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function category(Request $request)
    {
        if ($request->get('category')) {
            $category = $request->get('category');
            $product = $this->productsRepository->findByNameCategory($category);
        } else {
            $product = $this->productsRepository->findAll();
        }
        if ($request->get('size')) {
            $id = $request->get('size');
            $product = $this->productsRepository->findBySize($id, $category);
        }
        $categories = $this->categoriesRepository->findAll();
        $params = $this->parametersRepository->findAll();
        if (!$product) {
            // tao 1 trang k co san pham
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }
        $products = $this->paginatorInterface->paginate(
            $product,
            $request->query->getInt('page', 1),
            12
        );
        return $this->render('client/category.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'category' => $category,
            'params' => $params,
        ]);
    }

    /**
     * @Route("/filter-product", name="filter_product", methods={"GET", "POST"})
     */
    public function filter(Request $request)
    {
        $id_size = $request->get('size');
        $category = $request->get('category');
        if ($request->isXmlHttpRequest()) {
            $product = $this->productsRepository->findBySize($id_size, $category);
            $products = $this->paginatorInterface->paginate(
                $product,
                $request->query->getInt('page', 1),
                12
            );
            return $this->render('client/show_product.html.twig', [
                'products' => $products,
            ]);
        } else {
            return $this->category($request);
        }
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
        $product_id = $request->get('product_id');
        $quantity = $request->get('quantity');
        // $parameter_id = $request->get('parameter_id');
        // $check = $this->productsRepository->checkParameterProducts($product_id,$parameter_id);
        // if($check == null){
        //     $this->addFlash(
        //         'error',
        //         'Sản phẩm không tồn tại'
        //     );
        //     $referer = $request->headers->get('referer');
        //     return $this->redirect($referer);
        // }

        $cart = new Cart();
        if ($this->isGranted('ROLE_CUSTOMER')) {
            $customer_id = $this->getUser()->getId();
            $check_cart = $this->cartRepository->findBy(['customer' => $customer_id]);
            if (!$check_cart) {
                $cart->setCustomer($this->getUser());
                $this->cartRepository->add($cart, true);
            } else {
                $cart = $check_cart[0];
            }

            $cart_id = $cart->getId();
            $checkProduct = $this->cartDetailRepository->findBy(['cart' => $cart_id, 'product' => $product_id]);
            if ($checkProduct) {
                $currentQuantity = $checkProduct[0]->getQuantity();
                $checkProduct[0]->setQuantity($currentQuantity + $quantity);
                $this->cartDetailRepository->add($checkProduct[0], true);
            } else {
                $product = $this->productsRepository->find($product_id);
                $cartDetail = new CartDetail();
                $cartDetail->setCart($cart);
                $cartDetail->setProduct($product);
                $cartDetail->setQuantity($quantity);
                $this->cartDetailRepository->add($cartDetail, true);
            }
            $this->addFlash(
                'success',
                'Thêm vào giỏ hàng thành công'
            );
            return $this->redirectToRoute('client_page', [], Response::HTTP_SEE_OTHER);
        }
    }
    /**
     * @Route("view-cart", name="view_cart", methods={"GET", "POST"})
     */
    public function viewCart(Request $request)
    {

        if ($this->isGranted('ROLE_CUSTOMER')) {
            $customer_id = $this->getUser()->getId();
            $cart = $this->cartRepository->findBy(['customer' => $customer_id]);
            $cartDetails = $this->cartDetailRepository->findBy(['cart' => $cart]);
        }
        return $this->render('client/cart.html.twig', [
            'cartDetails' => $cartDetails
        ]);
    }
    /**
     * @Route("delete-cart", name="delete_cart", methods={"GET", "POST"})
     */
    public function deleteCart(Request $request)
    {
        $customer_id = $this->getUser()->getId();
        $cart = $this->cartRepository->findBy(['customer' => $customer_id]);
        $product_id = $request->get('product_id');
        $cartDetails = $this->cartDetailRepository->findBy(['cart' => $cart, 'product' => $product_id]);
        $cartDetail = $cartDetails[0];
        $this->cartDetailRepository->remove($cartDetail, true);
        return new Response(null, 200);
    }
    /**
     * @Route("update-cart", name="update_cart", methods={"GET", "POST"})
     */
    public function updateCart(Request $request)
    {
        $arr = $request->get('quantity');
        if ($this->isGranted('ROLE_CUSTOMER')) {
            $customer_id = $this->getUser()->getId();
            $cart = $this->cartRepository->findBy(['customer' => $customer_id]);
            foreach ($arr as $product_id => $quantity) {
                $cartDetail = $this->cartDetailRepository->findBy(['cart' => $cart, 'product' => $product_id]);
                $cartDetail[0]->setQuantity($quantity);
                $this->cartDetailRepository->add($cartDetail[0], true);
            }
        }
        return new Response(null, 200);
    }
    /**
     * @Route("checkout", name="checkout", methods={"GET", "POST"})
     */
    public function checkout(Request $request)
    {
        $arrProduct = $request->get('quantity');
        // dd($request->get('quantity'));
        if ($this->isGranted('ROLE_CUSTOMER')) {
            $customer_id = $this->getUser()->getId();
            $order = new Orders();
            $order->setCustomerId($this->getUser());
            $order->setStatus(1);
            $order->setTotalPrice(1);
            $this->ordersRepository->add($order, true);
            //them tung san pham tu cart sang order
            foreach ($arrProduct as $product_id => $quantity) {
                $product = $this->productsRepository->find($product_id);
                $orderDetail = new OrdersDetail();
                $orderDetail->setOrderId($order);
                $orderDetail->setProductId($product);
                $orderDetail->setQuantity($quantity);
                $orderDetail->setPrice($product->getPrice());
                $this->ordersDetailRepository->add($orderDetail, true);
            }
            // xoa cartdetail sau khi gui don
            $cart = $this->getUser()->getCart();
            $cartDetail = $this->cartDetailRepository->findBy(['cart' => $cart]);
            foreach ($cartDetail as $each) {
                $this->cartDetailRepository->remove($each, true);
            }

            $this->addFlash(
                'success',
                'Đặt hàng thành công'
            );
            return $this->redirectToRoute('view_cart', [], Response::HTTP_SEE_OTHER);
        }
    }
    public function contact(Request $request)
    {
        return $this->render('client/contact.html.twig');
    }
}
