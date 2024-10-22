<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Création de 10 articles
        for ($i = 1; $i <= 10; $i++) {
            $article = new Article();
            $article->setTitle($faker->sentence());
            $article->setContent($faker->paragraph());
            $article->setDate($faker->dateTimeThisYear());
            $article->setImage($faker->imageUrl(640, 480, 'technics', true));

            $manager->persist($article);

            // Ajouter une référence pour lier les commentaires plus tard
            $this->addReference('article_' . $i, $article);
        }

        $manager->flush();
    }
}
