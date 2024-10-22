<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\DataFixtures\ArticleFixtures;
use App\DataFixtures\UserFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Créer des commentaires liés aux articles et aux utilisateurs
        for ($k = 0; $k < 15; $k++) {
            $comment = new Comment();
            $comment->setContent($faker->paragraph(1));
            $comment->setDate($faker->dateTimeThisYear());

            
            // Associer chaque commentaire à un article et à un utilisateur via les références
            $comment->setArticle($this->getReference('article_' . rand(1, 10)));
            $comment->setUser($this->getReference('user_' . rand(0, 9)));

            $manager->persist($comment);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ArticleFixtures::class,
            UserFixtures::class,
        ];
    }
}
