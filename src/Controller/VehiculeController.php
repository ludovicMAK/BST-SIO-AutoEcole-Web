<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Vehicule;
use App\Form\CategorieType;
use App\Form\ChoixCategorieType;
use App\Form\VehiculeType;
use App\Repository\CategorieRepository;
use App\Repository\VehiculeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/vehicule')]
class VehiculeController extends AbstractController
{
    #[Route('/', name: 'app_vehicule_index', methods: ['GET', 'POST'])]
    public function index(VehiculeRepository $vehiculeRepository, CategorieRepository $categorieRepository, Request $request): Response
    {
        $lesCategories = $categorieRepository->findBy(['statut' => 'actif']);
        $lesVehicules = $vehiculeRepository->findBy([
            'statut' => 'actif',
            'categorie' => $lesCategories,
        ]);

        if($request->get('ajax')){
            $categorie = $request->get('categorie');
            if($categorie != "Toutes"){
                $lesVehiculesByCategorie = $vehiculeRepository->findBy([
                    'categorie' => $categorieRepository->findOneBy(['libelle' => $categorie]),
                    'statut' => 'actif',
                ]);
                $lesVehicules = [];
                foreach($lesVehiculesByCategorie as $vehicule){
                    $lesVehicules[] = $vehicule;
                }
            }
            return new JsonResponse([
                'content'=>$this->renderView('vehicule/_content.html.twig', compact('lesCategories', 'lesVehicules'))
            ]);
        }

        return $this->render('vehicule/index.html.twig', compact('lesCategories', 'lesVehicules'));
    }

    #[Route('/new', name: 'app_vehicule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, VehiculeRepository $vehiculeRepository, CategorieRepository $categorieRepository): Response
    {
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $vehicule, [
            'entity_manager' => $categorieRepository,
        ]);

        $form->handleRequest($request);
        $error = "";

        if ($form->isSubmitted() && $form->isValid()) {
            if(!$vehiculeRepository->findOneBy(['immatriculation' => $vehicule->getImmatriculation(), 'statut' => 'actif'])){
                if(is_numeric($vehicule->getAnnee())){
                    $vehiculeRepository->save($vehicule, true);
                    return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
                }
                else{
                    $error = "L'année doit respecter le format yyyy.";
                }
            }
            else{
                $error = "Cette plaque d'immatriculation existe déjà.";
            }
        }

        return $this->renderForm('vehicule/new.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form,
            'error' => $error,
        ]);
    }

    /*#[Route('/{id}', name: 'app_vehicule_show', methods: ['GET'])]
    public function show(Vehicule $vehicule): Response
    {
        return $this->render('vehicule/show.html.twig', [
            'vehicule' => $vehicule,
        ]);
    }*/

    #[Route('/{id}/edit', name: 'app_vehicule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vehicule $vehicule, VehiculeRepository $vehiculeRepository): Response
    {
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        $confirmation = "";
        $error = "";

        if ($form->isSubmitted() && $form->isValid()) {
            if(is_numeric($vehicule->getAnnee())){
                if(!$vehiculeRepository->findOneBy(['immatriculation' => $vehicule->getImmatriculation()])
                    || $vehiculeRepository->findOneBy(['immatriculation' => $vehicule->getImmatriculation()])->getId() === $vehicule->getId()){
                        $vehiculeRepository->save($vehicule, true);
                        $confirmation = "Le véhicule a bien été modifié";
                }
                else{
                    $error = "Cette plaque d'immatriculation existe déjà.";
                }
            }
            else{
                $error = "La doit doit respecter le format yyyy.";
            }
        }



        return $this->renderForm('vehicule/edit.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form,
            'confirmation' => $confirmation,
            'error' => $error,
        ]);
    }

    #[Route('/{id}', name: 'app_vehicule_delete', methods: ['POST'])]
    public function delete(Request $request, Vehicule $vehicule, VehiculeRepository $vehiculeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vehicule->getId(), $request->request->get('_token'))) {
            $vehicule->setStatut('inactif');
            $vehiculeRepository->save($vehicule, true);
        }

        return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
    }
}
