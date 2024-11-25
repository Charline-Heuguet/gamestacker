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

        // Liste des noms d'images disponibles
        $imageNames = [
            'article-image-1.jpg',
            'article-image-2.jpg',
            'article-image-3.jpg',
            'article-image-4.jpg',
        ];

        // Création de 10 articles
        for ($i = 1; $i <= 10; $i++) {
            $article = new Article();
            $article->setTitle($faker->sentence());
            $article->setContent($faker->paragraph());
            $article->setDate($faker->dateTimeThisYear());
            // Attribuer une image aléatoire depuis le tableau
            $article->setImage($imageNames[array_rand($imageNames)]);

            $manager->persist($article);

            // Ajouter une référence pour lier les commentaires plus tard
            $this->addReference('article_' . $i, $article);
        }

        $manager->flush();
    }
}
