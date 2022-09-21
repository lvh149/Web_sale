<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Entity\Parameters;
use App\Form\ProductsType;
use App\Repository\ProductsRepository;
use App\Repository\ParametersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/products")
 */
class ProductsController extends AbstractController
{
    public function __construct(
        ProductsRepository $productsRepository,
        ParametersRepository $parametersRepository,
        FileUploader $fileUploader,
        PaginatorInterface $paginatorInterface
    ){
        $this->paginatorInterface = $paginatorInterface;
        $this->productsRepository = $productsRepository;
        $this->parametersRepository = $parametersRepository;
        $this->fileUploader = $fileUploader;
    }
    /**
     * @Route("/", name="app_products_index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $products = $this->productsRepository->findAll();
        $pagination = $this->paginatorInterface->paginate(
            $products,
            $request->query->getInt('page', 1),
        );
        return $this->render('products/index.html.twig', [
            'products' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="app_products_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ProductsRepository $productsRepository): Response
    {
        $product = new Products();
        $attributes = $this->parametersRepository->findAll();
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('image')->getData();
            if ($brochureFile) {
                $brochureFileName = $this->fileUploader->upload($brochureFile);
                $product->setImage($brochureFileName);
            }
            $productsRepository->add($product, true);

            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('products/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }


    /**
     * @Route("/{id}/edit", name="app_products_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Products $products): Response
    {
        $form = $this->createForm(ProductsType::class, $products);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('image')->getData();
            if ($brochureFile) {
                $brochureFileName = $this->fileUploader->upload($brochureFile);
                $products->setImage($brochureFileName);
            }
            $this->productsRepository->add($products, true);

            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('products/edit.html.twig', [
            'products' => $products,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_products_delete", methods={"POST"})
     */
    public function delete(Request $request, Products $product): Response
    {
        try {
            if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
                $this->productsRepository->remove($product, true);
            }

            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $e) {
            $this->addFlash(
                'error',
                'Sản phẩm đang trong giỏ hàng hoặc hóa đơn'
            );
            return $this->redirectToRoute('app_products_index');
        }
    }
}
