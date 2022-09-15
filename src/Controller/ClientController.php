<?php

namespace App\Controller;

use App\Entity\Categories;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ProductsRepository;
use App\Repository\CategoriesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;


class ClientController extends AbstractController
{

    public function index(ProductsRepository $productsRepository, CategoriesRepository $categoriesRepository, PaginatorInterface $paginator, Request $request)
    {
        $products = $productsRepository->findAll();
        $categories = $categoriesRepository->findAll();
        // $pagination = $paginator->paginate(
        //     $products, /* query NOT result */
        //     $request->query->getInt('page', 1), /*page number*/
        //     7 /*limit per page*/
        // );

        return $this->render('client/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function category(Request $request, ProductsRepository $productsRepository, CategoriesRepository $categoriesRepository)
    {
        $name = $request->get('category');
        $products = $productsRepository->findByNameCategory($name);
        $categories = $categoriesRepository->findAll();
        return $this->render('client/category.html.twig', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
