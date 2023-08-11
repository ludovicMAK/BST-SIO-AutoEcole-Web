<?php

namespace App\Controller;

use App\Entity\Verification;
use App\Form\VerificationType;
use App\Repository\VerificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/verification')]
class VerificationController extends AbstractController
{
    /*#[Route('/', name: 'app_verification_index', methods: ['GET'])]
    public function index(VerificationRepository $verificationRepository): Response
    {
        return $this->render('verification/dashboard.html.twig', [
            'verifications' => $verificationRepository->findAll(),
        ]);
    }*/

    /*#[Route('/new', name: 'app_verification_new', methods: ['GET', 'POST'])]
    public function new(Request $request, VerificationRepository $verificationRepository): Response
    {
        $verification = new Verification();
        $form = $this->createForm(VerificationType::class, $verification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $verificationRepository->save($verification, true);

            return $this->redirectToRoute('app_verification_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('verification/new.html.twig', [
            'verification' => $verification,
            'form' => $form,
        ]);
    }*/

    /*#[Route('/{id}', name: 'app_verification_show', methods: ['GET'])]
    public function show(Verification $verification): Response
    {
        return $this->render('verification/show.html.twig', [
            'verification' => $verification,
        ]);
    }*/

    /*#[Route('/{id}/edit', name: 'app_verification_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Verification $verification, VerificationRepository $verificationRepository): Response
    {
        $form = $this->createForm(VerificationType::class, $verification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $verificationRepository->save($verification, true);

            return $this->redirectToRoute('app_verification_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('verification/edit.html.twig', [
            'verification' => $verification,
            'form' => $form,
        ]);
    }*/

    /*#[Route('/{id}', name: 'app_verification_delete', methods: ['POST'])]
    public function delete(Request $request, Verification $verification, VerificationRepository $verificationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$verification->getId(), $request->request->get('_token'))) {
            $verificationRepository->remove($verification, true);
        }

        return $this->redirectToRoute('app_verification_index', [], Response::HTTP_SEE_OTHER);
    }*/
}
