<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\CategorieRepository;
use App\Repository\EleveRepository;
use App\Repository\LeconRepository;
use App\Repository\MoniteurRepository;
use App\Repository\UserRepository;
use App\Repository\VehiculeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/user')]
class UserController extends AbstractController
{
    /*#[Route('/index', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {

        return $this->render('user/dashboard.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }*/

    #[Route('/statistiques', name: 'app_user_statistiques', methods: ['GET', 'POST'])]
    public function statistiques(CategorieRepository $categorieRepository, VehiculeRepository $vehiculeRepository, MoniteurRepository $moniteurRepository, LeconRepository $leconRepository, Request $request): Response
    {
        $categories = $categorieRepository->findBy([
            'statut' => 'actif',
        ]);
        $categoriesJSON = [];
        foreach($categories as $categorie){
            $categoriesJSON[] = $categorie->getLibelle();
        }
        $categoriesJSON = json_encode($categoriesJSON);
        $vehiculesLesPlusUtilises = $vehiculeRepository->getVehiculesLesPlusUtilisesByCateg("Toutes");
        $moniteursLesPlusSolicites = $moniteurRepository->getMoniteurMostCalledByCategorie("Toutes");
        $pourcentagesLeconsByCategorie = array();
        foreach($leconRepository->getPourcentageLeconByCategorie() as $pourcentage){
            $pourcentagesLeconsByCategorie[] = doubleval($pourcentage['pourcentage']);
        }
        $pourcentagesLeconsByCategorie = json_encode($pourcentagesLeconsByCategorie);

        if($request->get('ajax')){
            $categorie = $request->get('categorie');
            $vehiculesLesPlusUtilises = $vehiculeRepository->getVehiculesLesPlusUtilisesByCateg($categorie);
            $moniteursLesPlusSolicites = $moniteurRepository->getMoniteurMostCalledByCategorie($categorie);
            return new JsonResponse([
                'content'=>$this->renderView('user/_content.html.twig', compact('categoriesJSON','vehiculesLesPlusUtilises', 'moniteursLesPlusSolicites', 'pourcentagesLeconsByCategorie'))
            ]);
        }
        return $this->render('user/statistiques.html.twig', [
            'categories' => $categories,
            'categoriesJSON' => $categoriesJSON,
            'vehiculesLesPlusUtilises' => $vehiculesLesPlusUtilises,
            'moniteursLesPlusSolicites' => $moniteursLesPlusSolicites,
            'pourcentagesLeconsByCategorie' => $pourcentagesLeconsByCategorie,
        ]);
    }

    #[Route('/planning', name: 'app_user_planning', methods: ['GET', 'POST'])]
    public function planning(Request $request,LeconRepository $leconRepository,EleveRepository $eleveRepository, CategorieRepository $categorieRepository,MoniteurRepository $moniteurRepository): Response
    {
        $lesEleves = array();
        $lesMoniteurs = array();
        $leconsEleve = [];
        $leconsMoniteur = [];
        $categories = $categorieRepository->findAll();
        $dataPlanningEleve = [];

        if ($request->get("ajax") == 1){

            if ($request->get('eleveSelectionne') != 0){

                $lesLeconsEleve = $leconRepository->findBy([
                    'eleve' => $eleveRepository->findOneBy([
                        'id' => $request->get('eleveSelectionne'),
                    ]),
                ]);

                foreach($lesLeconsEleve as $event){
                    $leconsEleve[] = [
                        'id' => $event->getId(),
                        'start' => $event->getDate()->format('Y-m-d')." ".$event->getHeure(),
                        'end' => $event->getDate()->format('Y-m-d')." ".substr_replace($event->getHeure(), strval(intval(substr($event->getHeure(), 0, 2))+1), 0,2),
                        'title' => $event->getVehicule()->getCategorie()->getLibelle(),
                        'description' => 'Moniteur: '.$event->getMoniteur()->getPrenom()."</br>"."Véhicule: ".$event->getVehicule()->getMarque()." ".$event->getVehicule()->getModele(),
                        'classNames' => ['btn', 'btn-outline-info', 'overflow-visible', 'w-97'],
                        'textColor' => 'black',
                    ];
                }
                $dataPlanningEleve = $leconsEleve;
                return new JsonResponse([
                    'donne'=>$dataPlanningEleve,
                    'content'=>$this->renderView('user/_contentPlanning.html.twig', compact('dataPlanningEleve', 'categories','lesEleves'))
                ]);

            }
            if ($request->get('moniteurSelectionne') != 0){

                $lesLeconsMoniteur = $leconRepository->findBy([
                    'moniteur' => $eleveRepository->findOneBy([
                        'id' => $request->get('moniteurSelectionne'),
                    ]),
                ]);

                foreach($lesLeconsMoniteur as $event){
                    $leconsMoniteur[] = [
                        'id' => $event->getId(),
                        'start' => $event->getDate()->format('Y-m-d')." ".$event->getHeure(),
                        'end' => $event->getDate()->format('Y-m-d')." ".substr_replace($event->getHeure(), strval(intval(substr($event->getHeure(), 0, 2))+1), 0,2),
                        'title' => $event->getVehicule()->getCategorie()->getLibelle(),
                        'description' => 'Eleve: '.$event->getMoniteur()->getPrenom()."</br>"."Véhicule: ".$event->getVehicule()->getMarque()." ".$event->getVehicule()->getModele(),
                        'classNames' => ['btn', 'btn-outline-info', 'overflow-visible', 'w-97'],
                        'textColor' => 'black',
                    ];
                }
                $dataPlanningMoniteur= $leconsMoniteur;
                return new JsonResponse([
                    'donne'=>$dataPlanningMoniteur,
                    'content'=>$this->renderView('user/_contentPlanning.html.twig', compact('dataPlanningEleve', 'categories','lesEleves','lesMoniteurs'))
                ]);

            }

            if ($request->get("eleveNom")!=""){
                $lesEleves= $eleveRepository->getEleveLikeNomOrPrenom($request->get("eleveNom"));
                return new JsonResponse([
                    'content'=>$this->renderView('user/_contentSearchEleve.html.twig', compact('dataPlanningEleve', 'categories','lesEleves','lesMoniteurs'))
                ]);
            }
            if ($request->get("moniteurNom")!=""){
                $lesMoniteurs = $moniteurRepository->getMoniteurLikeNomOrPrenom($request->get("moniteurNom"));
                return new JsonResponse([
                    'content'=>$this->renderView('user/_contentSearchMoniteur.html.twig', compact('dataPlanningEleve', 'categories','lesEleves','lesMoniteurs'))
                ]);
            }

        }

        return $this->render('user/planning.html.twig', compact('dataPlanningEleve', 'categories','lesEleves','lesMoniteurs') );
    }
    /*#[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password_clair = $user->getPassword();
            $password_hashed = $passwordHasher->hashPassword(
                $user,
                $password_clair
            );
            $user->setPassword($password_hashed);

            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }*/

    /*#[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }*/

    /*#[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password_clair = $user->getPassword();
            $password_hashed = $passwordHasher->hashPassword(
                $user,
                $password_clair
            );
            $user->setPassword($password_hashed);

            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }*/

    /*#[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }*/

    #[Route('/', name: 'app_user_dashboard', methods: ['GET', 'POST'])]
    public function dashboard(): Response
    {
        return $this->render('user/dashboard.html.twig', [
            'login' => $this->getUser()->getLogin(),
        ]);
    }

}
