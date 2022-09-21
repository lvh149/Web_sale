<?php

namespace App\Controller\Admin;

use App\Entity\Parameters;
use App\Form\ParametersType;
use App\Repository\ParametersRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/parameters")
 */
class ParametersController extends AbstractController
{
    public function __construct(
        ParametersRepository $parametersRepository,
        PaginatorInterface $paginatorInterface
    ){
        $this->paginatorInterface = $paginatorInterface;
        $this->parametersRepository = $parametersRepository;
    }
    /**
     * @Route("/", name="app_parameters_index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $parameters = $this->categoriesRepository->findAll();
        $pagination = $this->paginatorInterface->paginate(
            $parameters,
            $request->query->getInt('page', 1),
        );
        return $this->render('parameters/index.html.twig', [
            'parameters' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="app_parameters_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $parameter = new Parameters();
        $form = $this->createForm(ParametersType::class, $parameter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->parametersRepository->add($parameter, true);
            $this->addFlash(
                'success',
                'Thêm thành công'
            );
            return $this->redirectToRoute('app_parameters_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('parameters/new.html.twig', [
            'parameter' => $parameter,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_parameters_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Parameters $parameter): Response
    {
        $form = $this->createForm(ParametersType::class, $parameter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->parametersRepository->add($parameter, true);
            $this->addFlash(
                'success',
                'Sửa thành công'
            );
            return $this->redirectToRoute('app_parameters_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('parameters/edit.html.twig', [
            'parameter' => $parameter,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_parameters_delete", methods={"POST"})
     */
    public function delete(Request $request, Parameters $parameter): Response
    {
        if ($this->isCsrfTokenValid('delete'.$parameter->getId(), $request->request->get('_token'))) {
            $this->parametersRepository->remove($parameter, true);
        }

        return $this->redirectToRoute('app_parameters_index', [], Response::HTTP_SEE_OTHER);
    }
}
