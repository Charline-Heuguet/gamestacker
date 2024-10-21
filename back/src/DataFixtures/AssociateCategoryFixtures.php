<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Announcement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AssociateCategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Associer chaque article à 2-3 catégories de manière aléatoire
        for ($i = 1; $i <= 10; $i++) {
            $article = $this->getReference('article_' . $i);

            // Sélectionner aléatoirement 2 à 3 catégories pour chaque article
            $randomCategories = $faker->randomElements($this->getAllCategories(), rand(2, 3));

            foreach ($randomCategories as $category) {
                $article->addCategory($category); // Associer la catégorie à l'article
            }

            $manager->persist($article); // Re-sauvegarder l'article après ajout des catégories
        }

        // Associer chaque annonce à 2-3 catégories de manière aléatoire
        for ($i = 1; $i <= 10; $i++) {
            $announcement = $this->getReference('announcement_' . $i);

            // Sélectionner aléatoirement 2 à 3 catégories pour chaque annonce
            $randomCategories = $faker->randomElements($this->getAllCategories(), rand(2, 3));

            foreach ($randomCategories as $category) {
                $announcement->addCategory($category); // Associer la catégorie à l'annonce
            }

            $manager->persist($announcement); // Re-sauvegarder l'annonce après ajout des catégories
        }

        $manager->flush();
    }

    // Récupérer toutes les catégories créées dans CategoryFixtures
    private function getAllCategories(): array
    {
        $categories = [];
        $genres = ['Plateformer', 'RPG', 'Réflexion', 'Action', 'Aventure', 'Simulation', 'Course', 'FPS', 'Stratégie', 'Survie', 'MMORPG', 'Puzzle', 'Combat'];

        foreach ($genres as $genre) {
            $categories[] = $this->getReference('category_' . $genre);
        }

        return $categories;
    }

    public function getDependencies(): array
    {
        return [
            ArticleFixtures::class,
            AnnouncementFixtures::class,
            CategoryFixtures::class, // S'assurer que les catégories sont créées avant d'être associées
        ];
    }
}
