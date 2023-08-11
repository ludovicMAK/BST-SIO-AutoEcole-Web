<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Entity\Lecon;
use App\Entity\User;
use App\Entity\Verification;
use App\Form\EleveInscriptionType;
use App\Form\EleveType;
use App\Form\LeconType;
use App\Form\SetPasswordType;
use App\Repository\CalendarRepository;
use App\Repository\CategorieRepository;
use App\Repository\EleveRepository;
use App\Repository\LeconRepository;
use App\Repository\MoniteurRepository;
use App\Repository\UserRepository;
use App\Repository\VehiculeRepository;
use App\Repository\VerificationRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Uuid;
use function PHPUnit\Framework\arrayHasKey;

#[Route('/eleve')]
class EleveController extends AbstractController
{
    /*#[Route('/', name: 'app_eleve_index', methods: ['GET'])]
    public function index(EleveRepository $eleveRepository): Response
    {
        return $this->render('eleve/dashboard.html.twig', [
            'eleves' => $eleveRepository->findAll(),
        ]);
    }*/

    #[Route('/inscription', name: 'app_eleve_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EleveRepository $eleveRepository, VerificationRepository $verificationRepository, UserPasswordHasherInterface $hasher, UserRepository $userRepository): Response
    {
        $eleve = new Eleve();
        $form = $this->createForm(EleveType::class, $eleve);
        $form->handleRequest($request);

        $error = "";
        $confirmation = "";

        if ($form->isSubmitted() && $form->isValid()) {
            //On check la dispo du login et du mail
            if(!$userRepository->findOneBy(['login' => $eleve->getUser()->getLogin()])){
                if(!$userRepository->findOneBy(['email' => $eleve->getUser()->getEmail()])){
                    //Il nous faut un mot de passe
                    $faker = Factory::create('fr_FR');
                    $password = $faker->password();
                    $eleve->getUser()->setPassword($hasher->hashPassword($eleve->getUser(), $password));
                    //Il faut lui ajouter le role moniteur
                    $eleve->getUser()->setRoles(['ROLE_ELEVE']);

                    $eleveRepository->save($eleve, true);
                    $this->forward("App\Controller\MailerController::sendEmailEleveInscrit", [
                        'eleve' => $eleve,
                        'password' => $password,
                    ]);
                    $confirmation = "Un mail a été envoyé au nouvel élève";
                }
                else{
                    $error = "Cet email n'est pas disponible.";
                }
            }
            else{
                $error = "Ce login n'est pas disponible.";
            }
        }

        return $this->renderForm('eleve/new.html.twig', [
            'eleve' => $eleve,
            'form' => $form,
            'error' => $error,
            'confirmation' => $confirmation,
        ]);
    }

    /*#[Route('/show', name: 'app_eleve_show', methods: ['GET', 'POST'])]
    public function show(int $user, EleveRepository $repositoryEleve, UserRepository $repositoryUser): Response
    {
        $user = $repositoryUser->findOneBy([
            'id' => $user,
        ]);
        $eleve = $repositoryEleve->findOneBy([
            'user' => $user
        ]);
        return $this->render('eleve/show.html.twig', [
            'eleve' => $eleve,
        ]);
    }*/

    #[Route('/profil', name: 'app_eleve_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EleveRepository $eleveRepository, UserRepository $userRepository): Response
    {
        //On récupère l'élève connecté

        if ($this->getUser() == null){
            return $this->render('login/index.html.twig', [
                'error' => 'Vous avez été déconnecté',
                'last_username' => "",
            ]);
        }
        $eleve = $eleveRepository->findOneBy([
            //Grâce à l'user connecté
            'user' => $this->getUser()->getId(),
        ]);


        $confirmation = "";
        $error = "";

        $form = $this->createForm(EleveType::class, $eleve);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            //On check la dispo du mail
            $userVerif = $userRepository->findOneBy(['email' => $eleve->getUser()->getEmail()]);
            if($userVerif && $eleve->getUser()->getLogin() !== $userVerif->getLogin()){
                $error = "Désolé mais cet email n'est pas disponible.";
            }
            else{
                $eleveRepository->save($eleve, true);
                $confirmation = "Votre profil a été mis à jour !";
            }
        }

        return $this->renderForm('eleve/edit.html.twig', [
            'eleve' => $eleve,
            'form' => $form,
            'confirmation' => $confirmation,
            'error' => $error,
        ]);
    }
    #[Route('/statistiques/', name: 'app_eleve_statistique', methods: ['GET','POST'])]
    public function afficherStatique(Request $request,LeconRepository $leconRepository,EleveRepository $eleveRepository,CategorieRepository $categorieRepository){
        $eleve = $eleveRepository->findOneBy([
            //Grace à l'user connecté
            'user' => $this->getUser()->getId(),
        ]);
        $lstCategorie = $categorieRepository->getCategoriesByIdEleve($eleve->getId());

        $dataStat = array();
        $i =0;
        $dataSommePermis=array();
        foreach ($lstCategorie as $categ){
           $nbLeconTotal = $eleveRepository->getNbLeconByIdEleve_IdCategorie($eleve->getId(),$categ->getId())[0][1];

           $nbLeconPasPayee = $eleveRepository->getNbLeconNotPayeeByIdEleve_IdCategorie($eleve->getId(),$categ->getId())[0][1];
           $dataStat[$categ->getLibelle()]=['paye'=>$nbLeconTotal-$nbLeconPasPayee,'nonPayee'=>$nbLeconPasPayee];

           $sommeTotalPermis = $categ->getPrix()*$nbLeconTotal;
           $dataSommePermis[$categ->getLibelle()] =$sommeTotalPermis;


           $i++;
        }
        return $this->render('eleve/statistique.html.twig', [
            'lstCateg'=>$lstCategorie,
            'dataStat'=>json_encode($dataStat),
            'dataSommePermis'=>json_encode($dataSommePermis),
        ]);


    }
    #[Route('/reservation', name: 'app_lecon_new', methods: ['GET', 'POST'])]
    public function ajouterUneLecon(Request $request,EleveRepository $eleveRepository, LeconRepository $leconRepository,CategorieRepository $categorieRepository,MoniteurRepository $moniteurRepository,VehiculeRepository $vehiculeRepository ): Response
    {

        $lesCateg =$categorieRepository->findBy(['statut'=>'actif']);
        $filtersCateg = $request->get("categ");
        $filtersDate = $request->get("date");
        $filtersHoraire = $request->get("horaire");
        $lesMoniteursDispo = array();
        $lesVehiculesDispo = array();

        if ($request->get('idMoniteur')>0 && $request->get('idVehicule')>0 && $request->get('horaire') != "" && $request->get('date') != "" && $request->get('categ') != ""){

            $lecon = new Lecon();
            $leVehicule = $vehiculeRepository->find($request->get('idVehicule'));
            $leMoniteur = $moniteurRepository->find($request->get('idMoniteur'));
            $eleve = $eleveRepository->findOneBy([
                //Grace à l'user connecté
                'user' => $this->getUser()->getId(),
            ]);
            $date = new \DateTime($filtersDate);
            $date->format('d/m/Y');
            $lecon->setVehicule($leVehicule)
                ->setMoniteur($leMoniteur)
                ->setEleve($eleve)
                ->setPayee(false)
                ->setHeure($request->get("horaire"))
                ->setDate($date);
            $leconRepository->save($lecon,true);

            return $this->render('eleve/dashboard.html.twig', [
                'login' => $this->getUser()->getLogin(),
            ]);
        }


        if ($request->get('ajax')){

            $lesMoniteursDispo = $moniteurRepository->getMoniteur($filtersCateg);
            $lesVehiculesDispo = $vehiculeRepository->findBy(['categorie'=>$categorieRepository->findOneBy(['id'=>$filtersCateg]),
                                                                'statut'=>'actif']);
            foreach ($lesVehiculesDispo as $index =>$unVehiculedispo){
                $leconsVehiculeDispo = $leconRepository->findBy(['vehicule'=>$unVehiculedispo]);

                foreach ($leconsVehiculeDispo as $leconVehiculeDispo){

                    if ($leconVehiculeDispo->getDate()->format('Y-m-d') == $filtersDate && $leconVehiculeDispo->getHeure() == $filtersHoraire){
                        unset($lesVehiculesDispo[$index]);
                    }
                }
            }

            foreach ($lesMoniteursDispo as $index => $moniteur){

                if ($moniteurRepository->getSiMoniteurDispo($moniteur['id'],$filtersDate,$filtersHoraire)){

                    unset($lesMoniteursDispo[$index]);
                }
            }


            return new JsonResponse([
                'content'=>$this->renderView('eleve/_content.html.twig', compact('lesMoniteursDispo','lesCateg','lesVehiculesDispo'))
            ]);
        }



        return $this->render('eleve/ajouteLecon.html.twig', compact('lesMoniteursDispo','lesCateg','lesVehiculesDispo'));
    }


    /*#[Route('/{id}', name: 'app_eleve_delete', methods: ['POST'])]
    public function delete(Request $request, Eleve $eleve, EleveRepository $eleveRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eleve->getId(), $request->request->get('_token'))) {
            $eleveRepository->remove($eleve, true);
        }

        return $this->redirectToRoute('app_eleve_index', [], Response::HTTP_SEE_OTHER);
    }*/

    #[Route('/dashboard', name: 'app_eleve_dashboard', methods: ['GET', 'POST'])]
    public function dashboard(Request $request, UserRepository $userRepository): Response
    {
        return $this->render('eleve/dashboard.html.twig', [
            'login' => $this->getUser()->getLogin(),
        ]);
    }

    #[Route('/planning', name: 'app_eleve_planning', methods: ['POST'])]
    public function planning(LeconRepository $leconRepository, EleveRepository $eleveRepository, CategorieRepository $categorieRepository): Response
    {
        $lesLecons = $leconRepository->findBy([
            'eleve' => $eleveRepository->findOneBy([
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
                'description' => 'Moniteur: '.$event->getMoniteur()->getPrenom()."</br>"."Véhicule: ".$event->getVehicule()->getMarque()." ".$event->getVehicule()->getModele(),
                'classNames' => ['btn', 'btn-outline-info', 'overflow-visible', 'w-97'],
                'textColor' => 'black',
            ];
        }
        $data = json_encode($lecons);
        return $this->render('eleve/planning.html.twig', compact('data', 'categories'), );
    }
}
