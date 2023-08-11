<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Eleve;
use App\Entity\Lecon;
use App\Entity\Licence;
use App\Entity\Moniteur;
use App\Entity\User;
use App\Entity\Vehicule;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher){
        $this->hasher = $passwordHasher;
    }


    public function load(ObjectManager $manager): void
    {

        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Faker\Provider\Fakecar($faker));

        //Nouveau fakers en cascade
        //eleve 1
        $user = new User();
        $user->setLogin("eleve");
        //$user->setPassword("eleve");
        $user->setPassword($this->hasher->hashPassword($user, "eleve"));
        $user->setPasswordConfirm('oui');
        $user->setRoles(["ROLE_ELEVE"]);
        $user->setEmail("eleve@mail.com");
        $manager->persist($user);
        $eleve = new Eleve();
        $eleve->setUser($user);
        $eleve->setNom($faker->lastName());
        $eleve->setPrenom($faker->firstName('female'));
        $eleve->setSexe("Femme");
        $eleve->setDateDeNaissance($faker->dateTime());
        $eleve->setCodePostal(random_int(1, 95));
        $eleve->setVille($faker->city());
        $eleve->setTelephone($faker->phoneNumber());
        $eleve->setAdresse($faker->address());
        $manager->persist($eleve);
        $manager->flush();

        //eleve 2
        $user = new User();
        $user->setLogin("eleve2");
        //$user->setPassword("eleve2");
        $user->setPassword($this->hasher->hashPassword($user, "eleve2"));
        $user->setPasswordConfirm('oui');
        $user->setRoles(["ROLE_ELEVE"]);
        $user->setEmail("eleve2@mail.com");
        $manager->persist($user);
        $eleve = new Eleve();
        $eleve->setUser($user);
        $eleve->setNom($faker->lastName());
        $eleve->setPrenom($faker->firstName('male'));
        $eleve->setSexe("Homme");
        $eleve->setDateDeNaissance($faker->dateTime());
        $eleve->setCodePostal(random_int(1, 95));
        $eleve->setVille($faker->city());
        $eleve->setTelephone($faker->phoneNumber());
        $eleve->setAdresse($faker->address());
        $manager->persist($eleve);
        $manager->flush();

        //moniteur 1
        $user = new User();
        $user->setLogin("moniteur");
        //$user->setPassword("moniteur");
        $user->setPassword($this->hasher->hashPassword($user, "moniteur"));
        $user->setPasswordConfirm('oui');
        $user->setRoles(["ROLE_MONITEUR"]);
        $user->setEmail("moniteur@mail.com");
        $manager->persist($user);
        $moniteur = new Moniteur();
        $moniteur->setUser($user);
        $moniteur->setNom($faker->lastName());
        $moniteur->setPrenom($faker->firstName('female'));
        $moniteur->setSexe("Femme");
        $moniteur->setDateDeNaissance($faker->dateTime());
        $moniteur->setCodePostal(random_int(1, 95));
        $moniteur->setVille($faker->city());
        $moniteur->setTelephone($faker->phoneNumber());
        $moniteur->setAdresse($faker->address());
        $manager->persist($moniteur);
        $manager->flush();

        //moniteur 2
        $user = new User();
        $user->setLogin("moniteur2");
        //$user->setPassword("moniteur2");
        $user->setPassword($this->hasher->hashPassword($user, "moniteur2"));
        $user->setPasswordConfirm('oui');
        $user->setRoles(["ROLE_MONITEUR"]);
        $user->setEmail("moniteur2@mail.com");
        $manager->persist($user);
        $moniteur = new Moniteur();
        $moniteur->setUser($user);
        $moniteur->setNom($faker->lastName());
        $moniteur->setPrenom($faker->firstName('male'));
        $moniteur->setSexe("Homme");
        $moniteur->setDateDeNaissance($faker->dateTime());
        $moniteur->setCodePostal(random_int(1, 95));
        $moniteur->setVille($faker->city());
        $moniteur->setTelephone($faker->phoneNumber());
        $moniteur->setAdresse($faker->address());
        $manager->persist($moniteur);
        $manager->flush();

        //admin 1
        $user = new User();
        $user->setLogin("gerante");
        $user->setPassword("gerante");
        $user->setPassword($this->hasher->hashPassword($user, "gerante"));
        $user->setPasswordConfirm('oui');
        $user->setRoles(["ROLE_GERANTE"]);
        $user->setEmail("gerante@mail.com");
        $manager->persist($user);
        $manager->flush();

        //Catégories
        $listCat = [
            0 => ["libelle" => "Voiture", "prix" => 23.2],
            1 => ["libelle" => "Camion", "prix" => 27.7],
            2 => ["libelle" => "Moto", "prix" => 25.4],
            3 => ["libelle" => "Vélo", "prix" => 18.3],
        ];
        foreach($listCat as $item){
            $cat = new Categorie();
            $cat->setLibelle($item["libelle"])->setPrix($item["prix"]);
            $manager->persist($cat);
            $manager->flush();
        }


        //Véhicules
        $allCategories = $manager->getRepository(Categorie::class)->findAll();
        foreach($allCategories as $item){
            for($i = 0; $i<15; $i++){
                $vehicule = new Vehicule();
                $marqueModel = $faker->vehicleArray();
                $vehicule->setCategorie($item)
                    ->setAnnee(random_int(1990, 2022))
                    ->setImmatriculation($faker->vehicleRegistration('[A-Z]{2}-[0-9]{3}-[A-Z]{2}'))
                    ->setMarque($marqueModel["brand"])
                    ->setModele($marqueModel["model"]);
                $manager->persist($vehicule);
                $manager->flush();
            }
        }

        //Licences
        /*$allMoniteurs = $manager->getRepository(Moniteur::class)->findAll();
        foreach($allMoniteurs as $item){
            $nbLicences = random_int(2, count($allCategories));
            $tabLicence = array();
            for($i=0; $i<$nbLicences; $i++){
                $licence = new Licence();
                $licence->setMoniteur($item);
                $licence->setDateObtention($faker->dateTime());
                $value = $allCategories[random_int(0,count($allCategories)-1)];
                while(in_array($value, $tabLicence, true)){
                    $value = $allCategories[random_int(1,count($allCategories)-1)];
                }
                $tabLicence[] = $value;
                $licence->setCategorie($tabLicence[$i]);
                $manager->persist($licence);
                $manager->flush();
            }
        }*/
        $allMoniteurs = $manager->getRepository(Moniteur::class)->findAll();
        foreach($allCategories as $categorie){
            foreach($allMoniteurs as $moniteur){
                if(random_int(0,1) == 1){
                    $licence = new Licence();
                    $licence->setMoniteur($moniteur);
                    $licence->setCategorie($categorie);
                    $licence->setDateObtention($faker->dateTime());
                    $manager->persist($licence);
                    $manager->flush();
                }
            }
        }

        //Leçons
        $horaires = [
            0 => "09:00",
            1 => "10:00",
            2 => "11:00",
            3 => "12:00",
            4 => "13:00",
            5 => "14:00",
            6 => "15:00",
            7 => "16:00",
            8 => "17:00",
            9 => "18:00",
        ];
        $allEleves = $manager->getRepository(Eleve::class)->findAll();
        foreach($allMoniteurs as $moniteur){
            $allLicencesMoniteur = $manager->getRepository(Licence::class)->findBy([
                'moniteur' => $moniteur,
            ]);
            foreach($allLicencesMoniteur as $licence){
                $allVehicules = $manager->getRepository(Vehicule::class)->findBy([
                   'categorie' => $licence->getCategorie(),
                ]);
                for($i=0; $i<40; $i++){
                    $lecon = new Lecon();
                    $lecon->setEleve($allEleves[random_int(0,count($allEleves)-1)]);
                    $lecon->setMoniteur($moniteur);
                    $vehicule = $allVehicules[random_int(0,count($allVehicules)-1)];
                    $lecon->setVehicule($vehicule);
                    $date = $faker->dateTimeThisYear();
                    $lecon->setDate($date);
                    $heure = $horaires[random_int(0, 9)];
                    $lecon->setHeure($heure);
                    $lecon->setPayee(random_int(0, 1));

                    if(!$manager->getRepository(Lecon::class)->findOneBy([
                        'moniteur' => $moniteur,
                        'date' => $date,
                        'heure' => $heure,
                    ])){
                        if(!$manager->getRepository(Lecon::class)->findOneBy([
                            'vehicule' => $vehicule,
                            'date' => $date,
                            'heure' => $heure,
                        ])){
                            $manager->persist($lecon);
                            $manager->flush();
                        }
                    }
                }
            }
        }
    }
}


