<?php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création de faux utilisateurs
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail('user' . $i . '@example.com');
            $user->setRoles(['ROLE_USER']);
            $user->setPassword('password' . $i);
            $user->setPseudo('user' . $i);
            $user->setDescription('Description de l\'utilisateur n°' . $i);
            $user->setAge(20 + $i);
            $user->setDiscord('user' . $i);
            $user->setGender('Homme');

            $manager->persist($user);
        }

        $manager->flush();
    }
}