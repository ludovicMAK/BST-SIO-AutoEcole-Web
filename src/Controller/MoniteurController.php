<?php

namespace App\Controller;

use App\Entity\Licence;
use App\Entity\Moniteur;
use App\Entity\User;
use App\Form\EleveType;
use App\Form\LicenceType;
use App\Form\MoniteurType;
use App\Repository\CategorieRepository;
use App\Repository\EleveRepository;
use App\Repository\LeconRepository;
use App\Repository\LicenceRepository;
use App\Repository\MoniteurRepository;
use App\Repository\UserRepository;
use Faker\Factory;
use PhpParser\Node\Scalar\String_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use function Sodium\add;

#[Route('/moniteur')]
class MoniteurController extends AbstractController
{
    /*#[Route('/', name: 'app_moniteur_index', methods: ['GET'])]
    public function index(MoniteurRepository $moniteurRepository): Response
    {
        return $this->render('moniteur/dashboard.html.twig', [
            'moniteurs' => $moniteurRepository->findAll(),
        ]);
    }*/

    #[Route('/new', name: 'app_moniteur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MoniteurRepository $moniteurRepository, UserRepository $userRepository, UserPasswordHasherInterface $hasher, MailerController $mailerController): Response
    {
        $moniteur = new Moniteur();
        $form = $this->createForm(MoniteurType::class, $moniteur);
        $form->handleRequest($request);

        $error = "";
        $confirmation = "";

        if ($form->isSubmitted() && $form->isValid()) {
            //On check la dispo du login et du mail
            if(!$userRepository->findOneBy(['login' => $moniteur->getUser()->getLogin()])){
                if(!$userRepository->findOneBy(['email' => $moniteur->getUser()->getEmail()])){
                    //Il nous faut un mot de passe
                    $faker = Factory::create('fr_FR');
                    $password = $faker->password();
                    $moniteur->getUser()->setPassword($hasher->hashPassword($moniteur->getUser(), $password));
                    //Il faut lui ajouter le role moniteur
                    $moniteur->getUser()->setRoles(['ROLE_MONITEUR']);

                    $moniteurRepository->save($moniteur, true);
                    $this->forward("App\Controller\MailerController::sendEmailMoniteurInscrit", [
                        'moniteur' => $moniteur,
                        'password' => $password,
                    ]);
                    $confirmation = "Un mail a été envoyé au nouveau moniteur";
                }
                else{
                    $error = "Cet email n'est pas disponible.";
                }
            }
            else{
                $error = "Ce login n'est pas disponible.";
            }
        }

        return $this->renderForm('moniteur/new.html.twig', [
            'moniteur' => $moniteur,
            'form' => $form,
            'error' => $error,
            'confirmation' => $confirmation,
            'pathApp' => 'app_user_dashboard',
        ]);
    }

    #[Route('/dashboard', name: 'app_moniteur_dashboard', methods: ['POST','GET'])]
    public function show(): Response
    {
        return $this->render('moniteur/dashboard.html.twig', [
            'login' => $this->getUser()->getLogin(),
        ]);
    }

    #[Route('/planning', name: 'app_moniteur_planning', methods: ['POST'])]
    public function planning(LeconRepository $leconRepository, MoniteurRepository $moniteurRepository, CategorieRepository $categorieRepository): Response
    {
        $lesLecons = $leconRepository->findBy([
            'moniteur' => $moniteurRepository->findOneBy([
                'user' => $this->getUser(),
            ]),
        ]);
        $lecons = [];
        $categories = $categorieRepository->findAll();
        foreach($lesLecons as $event){
            $lecons[] = [
                'id' => $event->getId(),
                'start' => $event->getDate()->format('Y-m-d')." ".$event->getHeure(),
                'end' => $event->getDate()->format('Y-m-d')." ".substr_replace($event->getHeure(), strval(intval(substr($event->getHeure(), 0, 2))+1), 0,2),
                'title' => $event->getVehicule()->getCategorie()->getLibelle(),
                'description' => 'Eleve: '.$event->getEleve()->getPrenom()."</br>"."Véhicule: ".$event->getVehicule()->getMarque()." ".$event->getVehicule()->getModele(),
                'classNames' => ['btn', 'btn-outline-info', 'overflow-visible', 'w-97'],
                'textColor' => 'black',
            ];
        }
        $data = json_encode($lecons);
        return $this->render('moniteur/planning.html.twig', compact('data', 'categories'), );
    }

    #[Route('/profil', name: 'app_moniteur_edit', methods: ['POST'])]
    public function edit(Request $request, MoniteurRepository $moniteurRepository, UserRepository $userRepository): Response
    {
        //On récupère le moniteur connecté
        if ($this->getUser() == null){
            return $this->render('login/index.html.twig', [
                'error' => 'Vous avez été déconnecté',
                'last_username' => "",
            ]);
        }
        $moniteur = $moniteurRepository->findOneBy([
            //Grâce à l'user connecté
            'user' => $this->getUser()->getId(),
        ]);

        //On instancie les erreur et confirmation
        $confirmation = "";
        $error = "";

        $form = $this->createForm(MoniteurType::class, $moniteur);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {
            //On check la dispo du mail
            $userVerif = $userRepository->findOneBy(['email' => $moniteur->getUser()->getEmail()]);
            if($userVerif && $moniteur->getUser()->getLogin() !== $userVerif->getLogin()){
                $error = "Désolé mais cet email n'est pas disponible.";
            }
            else{
                $moniteurRepository->save($moniteur, true);
                $confirmation = "Votre profil a été mis à jour !";
            }
        }

        return $this->renderForm('moniteur/edit.html.twig', [
            'moniteur' => $moniteur,
            'form' => $form,
            'confirmation' => $confirmation,
            'error' => $error,
            'pathApp' => 'app_moniteur_dashboard',
        ]);
    }


    #[Route('/statistiques/', name: 'app_moniteur_statistique', methods: ['POST'])]
    public function afficherStatique(Request $request,LeconRepository $leconRepository,MoniteurRepository $moniteurRepository, CategorieRepository $categorieRepository){
        $moniteur = $moniteurRepository->findOneBy([
            //Grace à l'user connecté
            'user' => $this->getUser()->getId(),
        ]);
        $lstCateg =$categorieRepository->getCategoriesByIdMoniteur($moniteur->getId());


        $date = new \DateTime('now'); // Date actuelle
        $dateAuj = $date->format('Y-m-d');
        $dateSemaine = $date->modify('-1 week')->format('Y-m-d');
        $dateMois = $date->modify('-1 months')->format('Y-m-d');
        $dateTrimestre = $date->modify('-3 months')->format('Y-m-d');
        $dataStatNbLecon = array();
        $dataStatChiffreAffaire = array();

        foreach ($lstCateg as $categ){

                $nbLeconAujByCateg = $leconRepository->getNbLeconByIdMoniteur_IdCategorie($moniteur->getId(), $dateAuj, $dateAuj, $categ->getId())[0][1];
                $chiffreAffaireJour = $nbLeconAujByCateg * $categ->getPrix();

                $nbLeconSemaineByCateg = $leconRepository->getNbLeconByIdMoniteur_IdCategorie($moniteur->getId(), $dateSemaine, $dateAuj, $categ->getId())[0][1];
                $chiffreAffaireSemaine = $nbLeconSemaineByCateg * $categ->getPrix();

                $nbLeconMoisByCateg = $leconRepository->getNbLeconByIdMoniteur_IdCategorie($moniteur->getId(), $dateMois, $dateAuj, $categ->getId())[0][1];
                $chiffreAffaireMois = $nbLeconMoisByCateg * $categ->getPrix();

                $nbLeconTrimestreByCateg = $leconRepository->getNbLeconByIdMoniteur_IdCategorie($moniteur->getId(), $dateTrimestre, $dateAuj, $categ->getId())[0][1];
                $chiffreAffaireTrimestre = $nbLeconTrimestreByCateg * $categ->getPrix();

                $dataStatNbLecon[$categ->getLibelle()] = ['Jour' => $nbLeconAujByCateg, 'Semaine' => $nbLeconSemaineByCateg, 'Mois' => $nbLeconMoisByCateg, 'Trimestre' => $nbLeconTrimestreByCateg];
                $dataStatChiffreAffaire[$categ->getLibelle()] = ['Jour' => $chiffreAffaireJour, 'Semaine' => $chiffreAffaireSemaine, 'Mois' => $chiffreAffaireMois, 'Trimestre' => $chiffreAffaireTrimestre];

        }

        return $this->render('moniteur/statistique.html.twig', [
            'lstCateg'=>$lstCateg,
            'dataStatNbLecon'=>json_encode($dataStatNbLecon),
            'dataStatChiffreAffaire'=>json_encode($dataStatChiffreAffaire),
        ]);

    }
    #[Route('/ajouterLicence/', name: 'app_moniteur_addLicence', methods: ['POST'])]
    public function ajouterLicence(Request $request,MoniteurRepository $moniteurRepository,LicenceRepository $licenceRepository, CategorieRepository $categorieRepository){
        $moniteur = $moniteurRepository->findOneBy([
            //Grace à l'user connecté
            'user' => $this->getUser()->getId(),
        ]);
        $licence = new Licence();
        $form = $this->createForm(LicenceType::class, $licence, [
            'entity_manager' => $categorieRepository,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($moniteurRepository->siDejaLicence($moniteur->getId(),$licence->getCategorie()->getId())){
                $erreurMessage = 'Vous avec avez déjà la licence '.$licence->getCategorie()->getLibelle().', veuillez choisir une autre catégorie';
                return $this->renderForm('licence/new.html.twig', [
                    'licence' => $licence,
                    'form' => $form,
                    'messageErreur'=>$erreurMessage,
                ]);
            }
            $licence->setMoniteur($moniteur);
            $licenceRepository->save($licence, true);

            return $this->renderForm('moniteur/dashboard.html.twig', [
                'login'=>$this->getUser()->getUserIdentifier(),
            ]);
        }

        return $this->renderForm('licence/new.html.twig', [
            'licence' => $licence,
            'form' => $form,
        ]);

    }
    /*#[Route('/{id}', name: 'app_moniteur_delete', methods: ['POST'])]
    public function delete(Request $request, Moniteur $moniteur, MoniteurRepository $moniteurRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$moniteur->getId(), $request->request->get('_token'))) {
            $moniteurRepository->remove($moniteur, true);
        }

        return $this->redirectToRoute('app_moniteur_index', [], Response::HTTP_SEE_OTHER);
    }*/

}
