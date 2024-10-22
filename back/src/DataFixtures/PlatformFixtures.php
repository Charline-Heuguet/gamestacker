<?php

namespace App\DataFixtures;

use App\Entity\Platform;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PlatformFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Liste des plateformes qui permettent de jouer en ligne
        $platforms = [
            'PS5', 'PS4', 'Nintendo Switch', 'Xbox Series X', 'Xbox One', 'PC'
        ];

        // Créer les plateformes
        $platformEntities = [];
        foreach ($platforms as $platformName) {
            $platform = new Platform();
            $platform->setName($platformName);
            $manager->persist($platform);
            $platformEntities[] = $platform; // Stocker les plateformes pour les réutiliser plus tard
        }

        $manager->flush();

        // Récupérer les utilisateurs déjà créés (supposons qu'ils ont été créés dans une autre fixture)
        for ($i = 0; $i < 10; $i++) {
            /** @var User $user */
            $user = $this->getReference('user_' . $i);

            // Associer 1 à 3 plateformes à chaque utilisateur aléatoirement
            $randomPlatforms = $faker->randomElements($platformEntities, rand(1, 3));

            foreach ($randomPlatforms as $platform) {
                $user->addPlatform($platform); // Associer la plateforme à l'utilisateur
            }

            $manager->persist($user);
        }

        $manager->flush();
    }
}
