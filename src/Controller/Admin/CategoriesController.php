<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/categories")
 */
class CategoriesController extends AbstractController
{
    public function __construct(
        CategoriesRepository $categoriesRepository,
        PaginatorInterface $paginatorInterface
    ){
        $this->paginatorInterface = $paginatorInterface;
        $this->categoriesRepository = $categoriesRepository;
    }

    /**
     * @Route("/", name="app_categories_index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $category = $this->categoriesRepository->findAll();
        $pagination = $this->paginatorInterface->paginate(
            $category,
            $request->query->getInt('page', 1),
        );

        return $this->render('categories/index.html.twig', [
            'categories' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="app_categories_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $category = new Categories();
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoriesRepository->add($category, true);
            $this->addFlash(
                'success',
                'Thêm thành công'
            );
            return $this->redirectToRoute('app_categories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categories/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_categories_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Categories $category): Response
    {
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoriesRepository->add($category, true);
            $this->addFlash(
                'success',
                'Sửa thành công'
            );
            return $this->redirectToRoute('app_categories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categories/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_categories_delete", methods={"POST"})
     */
    public function delete(Request $request, Categories $category): Response
    {
        try{
            if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {

                $this->categoriesRepository->remove($category, true);
            }
            return $this->redirectToRoute('app_categories_index', [], Response::HTTP_SEE_OTHER);
        }
        catch(\Exception $e){
            $this->addFlash(
                'error',
                'Danh mục đang có sản phẩm'
            );
            return $this->redirectToRoute('app_categories_index');
        }

    }
}
