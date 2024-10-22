<?php

namespace App\DataFixtures;

use App\Entity\Forum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ForumFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Créer 10 forums
        for ($i = 1; $i <= 10; $i++) {
            $forum = new Forum();
            $forum->setTitle($faker->sentence(6, true));
            $forum->setContent($faker->paragraph(4, true));
            $forum->setDate($faker->dateTimeBetween('-1 years', 'now'));

            $user = $this->getReference('user_' . rand(0, 9)); // Associer un utilisateur
            $forum->setUser($user);

            $manager->persist($forum);

            // Ajouter une référence pour que les autres fixtures puissent l'utiliser
            $this->addReference('forum_' . $i, $forum);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class, // Charger les utilisateurs avant les forums
        ];
    }
}
