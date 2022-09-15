<?php

namespace App\Controller;

use App\Entity\ProductsParameter;
use App\Form\ProductsParameterType;
use App\Repository\ProductsParameterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/products/parameter")
 */
class ProductsParameterController extends AbstractController
{
    /**
     * @Route("/", name="app_products_parameter_index", methods={"GET"})
     */
    public function index(ProductsParameterRepository $productsParameterRepository): Response
    {
        return $this->render('products_parameter/index.html.twig', [
            'products_parameters' => $productsParameterRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_products_parameter_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ProductsParameterRepository $productsParameterRepository): Response
    {
        $productsParameter = new ProductsParameter();
        $form = $this->createForm(ProductsParameterType::class, $productsParameter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productsParameterRepository->add($productsParameter, true);

            return $this->redirectToRoute('app_products_parameter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('products_parameter/new.html.twig', [
            'products_parameter' => $productsParameter,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_products_parameter_show", methods={"GET"})
     */
    public function show(ProductsParameter $productsParameter): Response
    {
        return $this->render('products_parameter/show.html.twig', [
            'products_parameter' => $productsParameter,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_products_parameter_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, ProductsParameter $productsParameter, ProductsParameterRepository $productsParameterRepository): Response
    {
        $form = $this->createForm(ProductsParameterType::class, $productsParameter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productsParameterRepository->add($productsParameter, true);

            return $this->redirectToRoute('app_products_parameter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('products_parameter/edit.html.twig', [
            'products_parameter' => $productsParameter,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_products_parameter_delete", methods={"POST"})
     */
    public function delete(Request $request, ProductsParameter $productsParameter, ProductsParameterRepository $productsParameterRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$productsParameter->getId(), $request->request->get('_token'))) {
            $productsParameterRepository->remove($productsParameter, true);
        }

        return $this->redirectToRoute('app_products_parameter_index', [], Response::HTTP_SEE_OTHER);
    }
}
