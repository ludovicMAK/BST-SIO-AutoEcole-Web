<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

#[Route('/email')]
class MailerController extends AbstractController
{
    #[Route('/inscriptionEleve', name: 'app_email_inscription_eleve', methods: ['POST'])]
    public function sendEmailEleveInscrit(MailerInterface $mailer, Request $request): void
    {
        $eleve = $request->get('eleve');
        $clearPSWD = $request->get('password');
        //On créé le mail
        $email = (new TemplatedEmail())
            ->from('contact@turbopermis.com')
            ->to($eleve->getUser()->getEmail())
            ->subject('Inscription TurboPermis')
            ->htmlTemplate('email/pswdEleveMail.twig')
            ->context([
                'eleve' => $eleve,
                'password' => $clearPSWD,
            ]);

        //On envoie le mail
        $mailer->send($email);
    }

    #[Route('/inscriptionMoniteur', name: 'app_email_inscription_moniteur', methods: ['POST'])]
    public function sendEmailMoniteurInscrit(MailerInterface $mailer, Request $request): void
    {
        $moniteur = $request->get('moniteur');
        $clearPSWD = $request->get('password');
        //On créé le mail
        $email = (new TemplatedEmail())
            ->from('contact@turbopermis.com')
            ->to($moniteur->getUser()->getEmail())
            ->subject('Inscription TurboPermis')
            ->htmlTemplate('email/pswdMoniteurMail.html.twig')
            ->context([
                'moniteur' => $moniteur,
                'password' => $clearPSWD,
            ]);

        //On envoie le mail
        $mailer->send($email);
    }
}