<?php

namespace App\Controller;

use App\Entity\Products;
use App\Form\ProductsType;
use App\Repository\ProductsRepository;
use App\Repository\CategoriesRepository;
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
    /**
     * @Route("/", name="app_products_index", methods={"GET"})
     */
    public function index(ProductsRepository $productsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $products = $productsRepository->findAll();
        $pagination = $paginator->paginate(
            $products, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            7 /*limit per page*/
        );
        // dd($pagination);
        return $this->render('products/index.html.twig', [
            'products' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="app_products_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ProductsRepository $productsRepository, ParametersRepository $parametersRepository, FileUploader $fileUploader): Response
    {
        $product = new Products();
        $parameter = $parametersRepository->findAll();
        // dd($parameter);
        $params = [];
        // sap xep mang theo ten param
        
        $i = 0;
        foreach ($parameter as $v) {
            if (!isset($params[$v->getName()])) {
                $i=0;
                $params[$v->getName()][$i]["name"]= $v->getName();
                $params[$v->getName()][$i]["id"]= $v->getId();
                $params[$v->getName()][$i]["value"]= $v->getValue();
            } else {
                $params[$v->getName()][$i]["name"] = $v->getName();
                $params[$v->getName()][$i]["id"]= $v->getId();
                $params[$v->getName()][$i]["value"] = $v->getValue();
            }
            $i +=1;
        }
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('image')->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $product->setImage($brochureFileName);
            }
            $productsRepository->add($product, true);

            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('products/new.html.twig', [
            'product' => $product,
            'params' => $params,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_products_show", methods={"GET"})
     */
    public function show(Products $product): Response
    {
        // dd($product->getCategoryId());
        return $this->render('products/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_products_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Products $product, ProductsRepository $productsRepository, FileUploader $fileUploader): Response
    {

        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $brochureFile = $form->get('image')->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $product->setImage($brochureFileName);
            }
            $productsRepository->add($product, true);

            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('products/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_products_delete", methods={"POST"})
     */
    public function delete(Request $request, Products $product, ProductsRepository $productsRepository): Response
    {
        try {
            if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
                $productsRepository->remove($product, true);
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
