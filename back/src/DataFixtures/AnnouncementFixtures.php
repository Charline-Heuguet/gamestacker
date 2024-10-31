<?php

namespace App\DataFixtures;

use App\Entity\Announcement;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AnnouncementFixtures extends Fixture implements DependentFixtureInterface
{
    // Fonction pour générer un ID unique avec lettres et chiffres aléatoires
    private function generateUniqueId($length = 15): string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Récupérer les utilisateurs existants
        $users = $manager->getRepository(User::class)->findAll();
        
        $maxNbPlayers = 8;
        
        // Générer plusieurs annonces
        for ($i = 1; $i <= 15; $i++) {
            $announcement = new Announcement();
            $announcement->setTitle($faker->sentence());
            $announcement->setContent($faker->paragraph());
            $announcement->setGame($faker->randomElement(['FIFA', 'Call of Duty', 'Fortnite']));
            $announcement->setDate($faker->dateTimeBetween('-1 months', '+1 months'));
            $announcement->setRoomId("#" . $this->generateUniqueId(14));

            // Définir max_nb_players
            $announcement->setMaxNbPlayers($maxNbPlayers);

            // Associer à un créateur utilisateur
            $creator = $faker->randomElement($users);
            $announcement->setUser($creator);

            // Ajouter des participants aléatoires, en s'assurant que le créateur n'est pas inclus
            $participants = array_filter($users, function ($user) use ($creator) {
                return $user !== $creator; // Exclure le créateur de la liste des participants
            });

            $randomParticipants = $faker->randomElements($participants, $faker->numberBetween(1, $maxNbPlayers - 1));
            foreach ($randomParticipants as $participant) {
                $announcement->addParticipant($participant);
            }

            $manager->persist($announcement);

            // Ajouter une référence à l'annonce
            $this->addReference('announcement_' . $i, $announcement);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
