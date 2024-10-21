<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentForumFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Charger les forums existants
        for ($i = 1; $i <= 10; $i++) {
            $forum = $this->getReference('forum_' . $i); // Récupérer la référence des forums

            // Générer entre 3 et 7 commentaires par forum
            for ($j = 1; $j <= rand(3, 7); $j++) {
                $comment = new Comment();
                $comment->setContent($faker->paragraph(2, true));
                $comment->setDate($faker->dateTimeThisYear());

                // Associer un utilisateur
                $user = $this->getReference('user_' . rand(0, 9));
                $comment->setUser($user);

                // Associer le commentaire au forum
                $comment->setForum($forum);

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ForumFixtures::class, // Charger les forums avant les commentaires
        ];
    }
}
