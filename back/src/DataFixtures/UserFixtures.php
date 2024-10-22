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

        $gender = ['Homme', 'Femme', 'Autre'];

        // Création de faux utilisateurs
        for ($i = 0; $i < 30; $i++) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setRoles(['ROLE_USER']);
            $user->setPassword('password' . $i); 
            $user->setPseudo($faker->userName());
            $user->setDescription($faker->sentence());
            $user->setAge($faker->numberBetween(18, 60));
            $user->setDiscord('#' . $faker->userName());
            $user->setGender($faker->randomElement($gender));

            $manager->persist($user);

            // Enregistrer une référence pour réutiliser cet utilisateur dans d'autres fixtures
            $this->addReference('user_' . $i, $user);
        }

        $manager->flush();
    }
}
