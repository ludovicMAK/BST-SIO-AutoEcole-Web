<?php

namespace App\Controller;

use App\Entity\Licence;
use App\Form\LicenceType;
use App\Repository\CategorieRepository;
use App\Repository\LicenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/licence')]
class LicenceController extends AbstractController
{
    /*#[Route('/', name: 'app_licence_index', methods: ['GET'])]
    public function index(LicenceRepository $licenceRepository): Response
    {
        return $this->render('licence/dashboard.html.twig', [
            'licences' => $licenceRepository->findAll(),
        ]);
    }*/

    #[Route('/new', name: 'app_licence_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LicenceRepository $licenceRepository, CategorieRepository $categorieRepository): Response
    {
        $licence = new Licence();
        $form = $this->createForm(LicenceType::class, $licence, [
            'entity_manager' => $categorieRepository,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $licenceRepository->save($licence, true);

            return $this->redirectToRoute('app_licence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('licence/new.html.twig', [
            'licence' => $licence,
            'form' => $form,
        ]);
    }

    /*#[Route('/{id}', name: 'app_licence_show', methods: ['GET'])]
    public function show(Licence $licence): Response
    {
        return $this->render('licence/show.html.twig', [
            'licence' => $licence,
        ]);
    }*/

    /*#[Route('/{id}/edit', name: 'app_licence_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Licence $licence, LicenceRepository $licenceRepository): Response
    {
        $form = $this->createForm(LicenceType::class, $licence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $licenceRepository->save($licence, true);

            return $this->redirectToRoute('app_licence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('licence/edit.html.twig', [
            'licence' => $licence,
            'form' => $form,
        ]);
    }*/

    /*#[Route('/{id}', name: 'app_licence_delete', methods: ['POST'])]
    public function delete(Request $request, Licence $licence, LicenceRepository $licenceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$licence->getId(), $request->request->get('_token'))) {
            $licenceRepository->remove($licence, true);
        }

        return $this->redirectToRoute('app_licence_index', [], Response::HTTP_SEE_OTHER);
    }*/
}
