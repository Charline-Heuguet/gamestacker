<?php

namespace App\DataFixtures;

use App\Entity\ReportCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ReportCategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Liste de catégories réalistes pour les rapports
        $categories = [
            'Problème technique',
            'Contenu inapproprié',
            'Harcèlement ou abus',
            'Usurpation d’identité',
            'Spam ou publicité',
            'Violation des règles de la communauté',
            'Problème de paiement',
            'Demande de support',
            'Bug dans l’application',
            'Suggestions ou idées',
        ];

        // Création des catégories
        foreach ($categories as $key => $categoryName) {
            $reportCategory = new ReportCategory();
            $reportCategory->setName($categoryName);

            $manager->persist($reportCategory);

            // Ajouter une référence pour réutiliser dans d'autres fixtures
            $this->addReference('report_category_' . $key, $reportCategory);
        }

        $manager->flush();
    }
}
