<?php

namespace App\Controller\Admin;

use App\Entity\Parameters;
use App\Form\ParametersType;
use App\Repository\ParametersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/parameters")
 */
class ParametersController extends AbstractController
{
    /**
     * @Route("/", name="app_parameters_index", methods={"GET"})
     */
    public function index(ParametersRepository $parametersRepository): Response
    {
        return $this->render('parameters/index.html.twig', [
            'parameters' => $parametersRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_parameters_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ParametersRepository $parametersRepository): Response
    {
        $parameter = new Parameters();
        $form = $this->createForm(ParametersType::class, $parameter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parametersRepository->add($parameter, true);

            return $this->redirectToRoute('app_parameters_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('parameters/new.html.twig', [
            'parameter' => $parameter,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_parameters_show", methods={"GET"})
     */
    public function show(Parameters $parameter): Response
    {
        return $this->render('parameters/show.html.twig', [
            'parameter' => $parameter,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_parameters_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Parameters $parameter, ParametersRepository $parametersRepository): Response
    {
        $form = $this->createForm(ParametersType::class, $parameter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parametersRepository->add($parameter, true);

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
    public function delete(Request $request, Parameters $parameter, ParametersRepository $parametersRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$parameter->getId(), $request->request->get('_token'))) {
            $parametersRepository->remove($parameter, true);
        }

        return $this->redirectToRoute('app_parameters_index', [], Response::HTTP_SEE_OTHER);
    }
}
