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
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
        ValidatorInterface $validator,
        PaginatorInterface $paginator,
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
        $this->paginator = $paginator;
        $this->validator = $validator;
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
        $name = $request->get('name');
        $min = $request->get('min');
        $max = $request->get('max');
        if ($request->get('category')) {
            $category = $request->get('category');
            $product = $this->productsRepository->findByNameCategory($category);
        } else {
            $product = $this->productsRepository->findAll();
        }
        if ($request->get('min') != null) {
            $product = $this->productsRepository->findProduct($category, $name, $min, $max);
        }
        $categories = $this->categoriesRepository->findAll();
        $params = $this->parametersRepository->findAll();
        // if (!$product) {
        //     // tao 1 trang k co san pham
        //     $referer = $request->headers->get('referer');
        //     return $this->redirect($referer);
        // }
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
     * @Route("/category/point", name="category_point", methods={"GET", "POST"})
     */
    public function categoryPoint(Request $request)
    {
        $name = $request->get('name');
        $min = $request->get('min');
        $max = $request->get('max');
        $product = $this->productsRepository->findBy(['price' => null]);
        if ($request->get('min') != null) {
            $product = $this->productsRepository->findProductPoint($name, $min, $max);
        }
        $categories = $this->categoriesRepository->findAll();
        $params = $this->parametersRepository->findAll();
        // if (!$product) {
        //     // tao 1 trang k co san pham
        //     $referer = $request->headers->get('referer');
        //     return $this->redirect($referer);
        // }
        $products = $this->paginatorInterface->paginate(
            $product,
            $request->query->getInt('page', 1),
            12
        );
        return $this->render('client/category.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'category' => "Point",
            'params' => $params,
        ]);
    }

    /**
     * @Route("/filter-product", name="filter_product", methods={"GET", "POST"})
     */
    public function filter(Request $request)
    {
        $name = $request->get('name');
        $min = $request->get('min');
        $max = $request->get('max');
        $category = $request->get('category');
        if ($request->isXmlHttpRequest()) {
            if ($category == "Point") {
                $product = $this->productsRepository->findProductPoint($name, $min, $max);
                // dd($product);
            } else {
                $product = $this->productsRepository->findProduct($category, $name, $min, $max);
            }
            $products = $this->paginatorInterface->paginate(
                $product,
                $request->query->getInt('page', 1),
                12
            );
            return $this->render('client/show_product.html.twig', [
                'products' => $products,
            ]);
        } else {
            if ($category == "Point") {
                return $this->categoryPoint($request);
            }
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
    public function productByPoint(Request $request)
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
        $product = $this->productsRepository->find($product_id);
        $cart = new Cart();
        if ($this->isGranted('ROLE_CUSTOMER')) {
            try {
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
                    $cartDetail = new CartDetail();
                    $cartDetail->setCart($cart);
                    $cartDetail->setProduct($product);
                    $cartDetail->setQuantity($quantity);
                    $this->cartDetailRepository->add($cartDetail, true);
                }
                return new Response('success', 200);
            } catch (\Exception $e) {
                return new Response('error', 400);
            }
        } else {
            $check_product = $this->productsRepository->find($product_id);
            if($check_product == null){
                return new Response('error', 400);
            }
            if ($product->getPoint()) {
                return new Response('error', 400);
                $this->addFlash(
                    'error',
                    'Ch??? d??nh cho kh??ch h??ng c?? t??i kho???n'
                );
                $referer = $request->headers->get('referer');
                return $this->redirect($referer);
            }
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
            if ($cartDetails == null) {
                return $this->render('client/empty_cart.html.twig');
            }
            return $this->render('client/cart.html.twig', [
                'cartDetails' => $cartDetails
            ]);
        } else {
            $session = $request->getSession();
            $cart =  $session->get('cart');
            if($cart == null){
                return $this->render('client/empty_cart.html.twig');
            }
            return $this->render('client/cartSession.html.twig', [
                // 'cartDetails' => $cartDetails
            ]);
        }
    }
    /**
     * @Route("view-order", name="view_my_order", methods={"GET", "POST"})
     */
    public function viewOrder(Request $request)
    {
        if ($this->isGranted('ROLE_CUSTOMER')) {
            $customer_id = $this->getUser()->getId();
            $orders = $this->ordersRepository->findBy(['customer' => $customer_id]);
            $pagination = $this->paginator->paginate(
                $orders,
                $request->query->getInt('page', 1),
            );
            return $this->render('client/order.html.twig', [
                'pager' => $pagination
            ]);
        }
    }
    /**
     * @Route("view-orderDetail", name="view_order_detail", methods={"GET", "POST"})
     */
    public function viewOrderDetail(Request $request)
    {
        if ($this->isGranted('ROLE_CUSTOMER')) {
            $order_id = $request->get('id');
            $orderDetail = $this->ordersDetailRepository->findBy(['order' => $order_id]);
            $pagination = $this->paginatorInterface->paginate(
                $orderDetail,
                $request->query->getInt('page', 1),
            );
            return $this->render('client/orderDetail.html.twig', [
                'orderdetails' => $pagination,
            ]);
        }
    }
    /**
     * @Route("number-cart", name="number_cart", methods={"GET", "POST"})
     */
    public function getNumberCart(Request $request)
    {
        if ($this->isGranted('ROLE_CUSTOMER')) {
            $customer_id = $this->getUser()->getId();
            $cart = $this->cartRepository->findBy(['customer' => $customer_id]);
            $cartDetails = $this->cartDetailRepository->numberCart($cart);

            return $this->json($cartDetails);
        }
    }
    /**
     * @Route("delete-cart", name="delete_cart", methods={"GET", "POST"})
     */
    public function deleteCart(Request $request)
    {
        $product_id = $request->get('product_id');
        $clear = $request->get('clear');
        if ($this->isGranted('ROLE_CUSTOMER')) {
            $customer_id = $this->getUser()->getId();
            $cart = $this->cartRepository->findBy(['customer' => $customer_id]);
            if ($clear == "clear") {
                $cartDetails = $this->cartDetailRepository->ClearCart($cart);
                return new Response(null, 200);
            }

            $cartDetails = $this->cartDetailRepository->findBy(['cart' => $cart, 'product' => $product_id]);
            $cartDetail = $cartDetails[0];
            $this->cartDetailRepository->remove($cartDetail, true);
            return new Response(null, 200);
        } else {
            $session = $request->getSession();
            $cart =  $session->get('cart');
            if ($clear == "clear") {
                $session->remove('cart');
                return new Response(null, 200);
            }
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
        $name_receiver = $request->get('name_receiver');
        $phone_receiver = $request->get('phone_receiver');
        $address_receiver = $request->get('address_receiver');
        if ($this->isGranted('ROLE_CUSTOMER')) {
            // kiem tra xem point nguoi dung co du ko
            if (!isset($this->getUser()->getCart()->getCartDetail()[0])) {
                return $this->redirectToRoute('client_page', [], Response::HTTP_SEE_OTHER);
            }
            $check_total_point = $this->cartDetailRepository->SumPoint($this->getUser()->getCart());
            $check_point = $check_total_point[0]['total_point'] ?? 0;
            if ($check_point > $this->getUser()->getPoint()) {
                $this->addFlash(
                    'error',
                    'Kh??ng ????? point'
                );
                return $this->redirectToRoute('view_cart', [], Response::HTTP_SEE_OTHER);
            }
            $order = new Orders();
            $order->setCustomerId($this->getUser());
            $order->setStatus(1);
            $order->setTotalPrice(1);
            $order->setNameReceiver($name_receiver);
            $order->setPhoneReceiver($phone_receiver);
            $order->setAddressReceiver($address_receiver);
            $errors = $this->validator->validate($order);
            if (count($errors) > 0) {
                $session = new Session();
                foreach ($errors as $error) {
                    $session->getFlashBag()->add('error', $error->getpropertyPath() . ": " . $error->getMessage());
                }
                $session->getFlashBag()->add('name_receiver', $name_receiver);
                $session->getFlashBag()->add('phone_receiver', $phone_receiver);
                $session->getFlashBag()->add('address_receiver', $address_receiver);
                return $this->redirectToRoute('view_cart', [], Response::HTTP_SEE_OTHER);
            }
            $this->ordersRepository->add($order, true);
            //them tung san pham tu cart sang order
            $addpoint = 0;
            $total_price = 0;
            $total_point = 0;
            $arrCartDetail = $this->cartDetailRepository->findBy(['cart' => $this->getUser()->getCart()]);
            foreach ($arrCartDetail as $cartDetail) {
                $quantity = $cartDetail->getQuantity();
                $price = $cartDetail->getProduct()->getPrice();
                $point = $cartDetail->getProduct()->getPoint();
                $product = $this->productsRepository->find($cartDetail->getProduct());
                $orderDetail = new OrdersDetail();
                $orderDetail->setOrderId($order);
                $orderDetail->setProductId($cartDetail->getProduct());
                $orderDetail->setQuantity($cartDetail->getQuantity());
                $orderDetail->setPrice($price ?? 0);
                $this->ordersDetailRepository->add($orderDetail, true);
                $addpoint += $product->getPointGive() * $quantity;
                $total_price += $price * $quantity ?? 0;
                $total_point += $point * $quantity ?? 0;
            }
            $point = $addpoint - $total_point;
            $order->setTotalPrice($total_price);
            $this->ordersRepository->add($order, true);
            $user = $this->getUser();
            $user->addPoint($point);
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
                ->subject('B???n ???? ?????t h??ng th??nh c??ng')
                ->html("Xin ch??o" . " " . $user->getName() ."<br> ????n h??ng c???a b???n ??ang ???????c x??t duy???t");
                // ->html('????n h??ng c???a b???n ??ang ???????c x??t duy???t');
            // ->htmlTemplate('reset_password/email.html.twig');
            $this->mailer->send($email);
            $this->addFlash(
                'success',
                '?????t h??ng th??nh c??ng'
            );
            // return $this->redirectToRoute('view_cart', [], Response::HTTP_SEE_OTHER);
            return $this->render('client/success_order.html.twig');
        } else {
            $check =  $request->getSession()->get('cart');
            if (!$check) {
                return $this->redirectToRoute('client_page', [], Response::HTTP_SEE_OTHER);
            }
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
            $email = (new TemplatedEmail())
                ->from(new Address('rfaceibookp3@gmail.com', 'hung'))
                ->to($email_customer)
                ->subject('B???n ???? ?????t h??ng th??nh c??ng')
                ->html("Xin ch??o" . " " . $user->getName() ."<br> ????n h??ng c???a b???n ??ang ???????c x??t duy???t");
                // ->html('????n h??ng c???a b???n ??ang ???????c x??t duy???t');
            // ->htmlTemplate('reset_password/email.html.twig');
            $this->mailer->send($email);
            $session->clear();
            $this->addFlash(
                'success',
                '?????t h??ng th??nh c??ng'
            );
            // return $this->redirectToRoute('view_cart', [], Response::HTTP_SEE_OTHER);
            return $this->render('client/success_order.html.twig');
        }
    }
    public function contact(Request $request)
    {
        return $this->render('client/contact.html.twig');
    }
}
