<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $gender = ['Homme', 'Femme', 'Autre'];

        // Création de faux utilisateurs
        for ($i = 0; $i < 30; $i++) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setRoles(['ROLE_USER']);
            // Hacher le mot de passe 'password7' pour chaque utilisateur
            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password7');
            $user->setPassword($hashedPassword); 
            $user->setPseudo($faker->userName());
            $user->setDescription($faker->sentence());
            $user->setAge($faker->numberBetween(18, 60));
            $user->setDiscord('#' . $faker->userName());
            $user->setGender($faker->randomElement($gender));
            $user->setImage('default.jpg');

            $manager->persist($user);

            // Enregistrer une référence pour réutiliser cet utilisateur dans d'autres fixtures
            $this->addReference('user_' . $i, $user);
        }

        $manager->flush();
    }
}
