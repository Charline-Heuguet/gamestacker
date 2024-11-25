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

            // Contenu réaliste et détaillé pour l'article
            $content = $this->generateRealisticContent($faker);
            $article->setContent($content);

            $article->setDate($faker->dateTimeThisYear());

            // Attribuer une image aléatoire depuis le tableau
            $article->setImage($imageNames[array_rand($imageNames)]);

            $manager->persist($article);

            // Ajouter une référence pour lier les commentaires plus tard
            $this->addReference('article_' . $i, $article);
        }

        $manager->flush();
    }

    private function generateRealisticContent($faker): string
    {
        // Ajout de sections pour simuler un article structuré
        $intro = $faker->paragraph(3); // Introduction avec plusieurs phrases
        $section1 = $faker->paragraphs(4, true); // Section détaillée avec plus de contenu
        $section2 = $faker->paragraphs(3, true); // Une autre section
        $conclusion = $faker->paragraph(2); // Conclusion courte

        return <<<TEXT
### Introduction

$intro

### Section 1: {$faker->sentence(6)}

$section1

### Section 2: {$faker->sentence(6)}

$section2

### Conclusion

$conclusion
TEXT;
    }
}
