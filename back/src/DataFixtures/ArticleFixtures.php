<?php
namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création de faux articles
        for ($i = 0; $i < 10; $i++) {
            $article = new Article();
            $article->setTitle('Titre de l\'article n°' . $i);
            $article->setContent('Contenu de l\'article n°' . $i);
            $article->setDate(new \DateTime());
            $article->setImage('https://via.placeholder.com/150');
            $manager->persist($article);
        }  

        $manager->flush();
    }
}




