<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categorie')]
class CategorieController extends AbstractController
{
    #[Route('/', name: 'app_categorie_index', methods: ['GET', 'POST'])]
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render('categorie/index.html.twig', [
            'categories' => $categorieRepository->findBy(['statut' => 'actif']),
        ]);
    }

    #[Route('/new', name: 'app_categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategorieRepository $categorieRepository): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        $error = "";

        if ($form->isSubmitted() && $form->isValid()) {
            if(is_numeric(floatval($categorie->getPrix()))){
                if(!$categorieRepository->findOneBy([
                    'libelle' => $categorie->getLibelle(),
                ])){
                    $categorieRepository->save($categorie, true);
                    return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
                }
                else{
                    $error = "Cette catégorie existe déjà.";
                }

            }
            else{
                $error = "Ce prix n'est pas valide.";
            }

        }

        return $this->renderForm('categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
            'error' => $error,
        ]);
    }

    /*#[Route('/{id}', name: 'app_categorie_show', methods: ['GET'])]
    public function show(Categorie $categorie): Response
    {
        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }*/

    #[Route('/{id}/edit', name: 'app_categorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categorie $categorie, CategorieRepository $categorieRepository): Response
    {
        $nomCategorie = $categorie->getLibelle();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        $error = "";
        $confirmation = "";

        if ($form->isSubmitted() && $form->isValid()) {
            if(is_numeric(floatval($categorie->getPrix()))){
                if(!$categorieRepository->findOneBy(['libelle' => $categorie->getLibelle()])
                    || $categorieRepository->findOneBy(['libelle' => $categorie->getLibelle()])->getId() === $categorie->getId()){
                        $categorieRepository->save($categorie, true);
                        $confirmation = "La catégorie a été mise à jour";
                }
                else{
                    $error = "Cette catégorie existe déjà.";
                }

            }
            else{
                $error = "Ce prix n'est pas valide.";
            }
        }

        return $this->renderForm('categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
            'error' => $error,
            'confirmation' => $confirmation,
        ]);
    }

    #[Route('/{id}', name: 'app_categorie_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, CategorieRepository $categorieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            $categorie->setStatut('inactif');
            $categorieRepository->save($categorie, true);
        }

        return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
    }
}
