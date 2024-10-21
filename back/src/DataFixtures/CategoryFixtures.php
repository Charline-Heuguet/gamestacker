<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Liste des genres de jeux vidéo à utiliser comme catégories
        $genres = [
            'Plateformer', 'RPG', 'Réflexion', 'Action', 'Aventure', 'Simulation',
            'Course', 'FPS', 'Stratégie', 'Survie', 'MMORPG', 'Puzzle', 'Combat'
        ];

        // Créer les catégories
        foreach ($genres as $genre) {
            $category = new Category();
            $category->setName($genre);
            $manager->persist($category);

            // Ajouter une référence pour les utiliser dans d'autres fixtures
            $this->addReference('category_' . $genre, $category);
        }

        $manager->flush();
    }
}
