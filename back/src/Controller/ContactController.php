<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    #[Route('/api/contact', name: 'api_contact', methods: ['POST'])]
    public function sendEmail(Request $request, MailerInterface $mailer): Response
    {
        // Décoder les données JSON
        $data = json_decode($request->getContent(), true);

        // Vérification des champs requis
        if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
            return $this->json(['error' => 'Veuillez remplir tous les champs.'], Response::HTTP_BAD_REQUEST);
        }

        // Création du message email
        $email = (new Email())
            ->from($data['email']) // L'email de l'expéditeur
            ->to('votre@email.com') // Remplacez par l'adresse de destination
            ->subject('Nouveau message de contact')
            ->html("
                <html>
                    <body style='font-family: Arial, sans-serif; color: #333; line-height: 1.6;'>
                        <h2 style='color: #10B981;'>Nouveau message de contact</h2>
                        <p><strong>Nom :</strong> {$data['name']}</p>
                        <p><strong>Email :</strong> <a href='mailto:{$data['email']}' style='color: #10B981;'>{$data['email']}</a></p>
                        <p><strong>Message :</strong></p>
                        <p style='border-left: 4px solid #10B981; padding-left: 10px; color: #555;'>{$data['message']}</p>
                        <br>
                        <p style='font-size: 0.9em; color: #888;'>Ceci est un email automatique. Veuillez ne pas y répondre.</p>
                    </body>
                </html>
            ");

        // Envoi de l'email
        try {
            $mailer->send($email);
            return $this->json(['status' => 'Votre message a bien été envoyé !'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Erreur lors de l\'envoi de l\'email. Veuillez réessayer plus tard.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
