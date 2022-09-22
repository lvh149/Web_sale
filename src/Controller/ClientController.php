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
use App\Repository\UsersRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;

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
        UsersRepository $usersRepository,
        MailerInterface $mailer,
        CartDetailRepository $cartDetailRepository
    ) {
        $this->productsRepository = $productsRepository;
        $this->categoriesRepository = $categoriesRepository;
        $this->parametersRepository = $parametersRepository;
        $this->cartRepository = $cartRepository;
        $this->cartDetailRepository = $cartDetailRepository;
        $this->ordersDetailRepository = $ordersDetailRepository;
        $this->ordersRepository = $ordersRepository;
        $this->usersRepository = $usersRepository;
        $this->mailer = $mailer;
        $this->managerRegistry = $managerRegistry;
        $this->paginatorInterface = $paginatorInterface;
    }

    public function index(Request $request)
    {
        if ($this->isGranted('ROLE_CUSTOMER')) {
            $session = $request->getSession();
            $arrCart =  $session->get('cart');
            if ($arrCart != null) {
                $customer_id = $this->getUser()->getId();
                $check_cart = $this->cartRepository->findBy(['customer' => $customer_id]);
                if (!$check_cart) {
                    $cart = new Cart();
                    $cart->setCustomer($this->getUser());
                    $this->cartRepository->add($cart, true);
                    foreach ($arrCart as $product_id => $quantity) {
                        $product = $this->productsRepository->find($product_id);
                        $cartDetail = new CartDetail();
                        $cartDetail->setCart($cart);
                        $cartDetail->setProduct($product);
                        $cartDetail->setQuantity($quantity['quantity']);
                        $this->cartDetailRepository->add($cartDetail, true);
                    }
                    $session->remove('cart');
                }
            }
        }

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
        } else {
            $product = $this->productsRepository->find($product_id);
            $cart = [
                'product' => $product,
                'quantity' => $quantity,
            ];
            $session = $request->getSession();
            // Get Value from session
            $sessionVal =  $session->get('cart');
            if (isset($sessionVal[$product_id])) {
                $sessionVal[$product_id]['quantity'] += $cart['quantity'];
            } else {
                $sessionVal[$product_id] = $cart;
            }
            // Append value to retrieved array.
            // Set value back to session
            $session->set('cart', $sessionVal);
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

            return $this->render('client/cart.html.twig', [
                'cartDetails' => $cartDetails
            ]);
        }
        return $this->render('client/cartSession.html.twig', [
            // 'cartDetails' => $cartDetails
        ]);
    }
    /**
     * @Route("delete-cart", name="delete_cart", methods={"GET", "POST"})
     */
    public function deleteCart(Request $request)
    {
        $product_id = $request->get('product_id');
        if ($this->isGranted('ROLE_CUSTOMER')) {
            $customer_id = $this->getUser()->getId();
            $cart = $this->cartRepository->findBy(['customer' => $customer_id]);
            $cartDetails = $this->cartDetailRepository->findBy(['cart' => $cart, 'product' => $product_id]);
            $cartDetail = $cartDetails[0];
            $this->cartDetailRepository->remove($cartDetail, true);
            return new Response(null, 200);
        } else {
            $session = $request->getSession();
            $cart =  $session->get('cart');
            // xoa san pham khoi cart
            unset($cart[$product_id]);
            $session->set('cart', $cart);

            return new Response(null, 200);
        }
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
            return new Response(null, 200);
        } else {
            $session = $request->getSession();
            $cart =  $session->get('cart');
            foreach ($arr as $product_id => $quantity) {
                $cart[$product_id]['quantity'] = $quantity;
            }
            $session->set('cart', $cart);
            return new Response(null, 200);
        }
    }
    /**
     * @Route("checkout", name="checkout", methods={"GET", "POST"})
     */
    public function checkout(Request $request)
    {
        $arrProduct = $request->get('quantity');
        $name_receiver = $request->get('name_receiver');
        $phone_receiver = $request->get('phone_receiver');
        $address_receiver = $request->get('address_receiver');
        // dd($request->get('quantity'));
        if ($this->isGranted('ROLE_CUSTOMER')) {
            $customer_id = $this->getUser()->getId();
            $order = new Orders();
            $order->setCustomerId($this->getUser());
            $order->setStatus(1);
            $order->setTotalPrice(1);
            $order->setNameReceiver($name_receiver);
            $order->setPhoneReceiver($phone_receiver);
            $order->setAddressReceiver($address_receiver);
            $this->ordersRepository->add($order, true);
            //them tung san pham tu cart sang order
            $point = 0;
            $total_price = 0;
            foreach ($arrProduct as $product_id => $quantity) {
                $product = $this->productsRepository->find($product_id);
                $orderDetail = new OrdersDetail();
                $orderDetail->setOrderId($order);
                $orderDetail->setProductId($product);
                $orderDetail->setQuantity($quantity);
                $orderDetail->setPrice($product->getPrice());
                $this->ordersDetailRepository->add($orderDetail, true);
                $point += $product->getPointGive() * $quantity;
                $total_price += $product->getPrice();
            }
            $order->setTotalPrice($total_price);
            $this->ordersRepository->add($order, true);
            $user = $this->getUser()->addPoint($point);
            $this->usersRepository->add($user, true);
            // xoa cartdetail sau khi gui don
            $cart = $this->getUser()->getCart();
            $cartDetail = $this->cartDetailRepository->findBy(['cart' => $cart]);
            foreach ($cartDetail as $each) {
                $this->cartDetailRepository->remove($each, true);
            }
            $email = (new TemplatedEmail())
                ->from(new Address('rfaceibookp3@gmail.com', 'hung'))
                ->to($this->getUser()->getEmail())
                ->subject('Bạn đã đặt hàng thành công')
                ->html('abc');
                // ->htmlTemplate('reset_password/email.html.twig');
            $this->mailer->send($email);
            $this->addFlash(
                'success',
                'Đặt hàng thành công'
            );
            return $this->redirectToRoute('view_cart', [], Response::HTTP_SEE_OTHER);
        } else {
            //tao khach hang
            $name_customer = $request->get('name_customer');
            $email_customer = $request->get('email_customer');
            $phone_customer = $request->get('phone_customer');
            $address_customer = $request->get('address_customer');
            $user = new Users();
            $user->setName($name_customer);
            $user->setEmail($email_customer);
            $user->setPhone($phone_customer);
            $user->setAddress($address_customer);
            $this->usersRepository->add($user, true);
            //thong tin trong order            
            $order = new Orders();
            $order->setCustomerId($user);
            $order->setStatus(1);
            $order->setTotalPrice(1);
            $order->setNameReceiver($name_receiver);
            $order->setPhoneReceiver($phone_receiver);
            $order->setAddressReceiver($address_receiver);
            $this->ordersRepository->add($order, true);
            //thong tin san pham
            $session = $request->getSession();
            $cart =  $session->get('cart');
            $total_price = 0;
            foreach ($cart as $product_id => $quantity) {
                $product = $this->productsRepository->find($product_id);
                $total_price += $quantity['product']->getPrice();
                $orderDetail = new OrdersDetail();
                $orderDetail->setOrderId($order);
                $orderDetail->setProductId($product);
                $orderDetail->setQuantity($quantity['quantity']);
                $orderDetail->setPrice($quantity['product']->getPrice());
                $this->ordersDetailRepository->add($orderDetail, true);
            }
            $order->setTotalPrice($total_price);
            $this->ordersRepository->add($order, true);
            $session->clear();
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
