<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Création de faux utilisateurs
        for ($i = 0; $i < 30; $i++) {
            $user = new User();
            $user->setEmail('user' . $i . '@example.com');
            $user->setRoles(['ROLE_USER']);
            $user->setPassword('password' . $i); 
            $user->setPseudo('user' . $i);
            $user->setDescription($faker->sentence());
            $user->setAge($faker->numberBetween(18, 60));
            $user->setDiscord('user' . $i);
            $user->setGender('Homme');

            $manager->persist($user);

            // Enregistrer une référence pour réutiliser cet utilisateur dans d'autres fixtures
            $this->addReference('user_' . $i, $user);
        }

        $manager->flush();
    }
}
