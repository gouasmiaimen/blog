<?php

namespace App\DataFixtures;
use Faker\Factory;
use App\Entity\Article;
use App\Entity\Categorie;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;



class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
          //on crée 5 catégories avec des noms aléatoires en "Français"
        for ($i=1; $i <5 ; $i++) { 
            $categorie = new Categorie();
            $categorie->setTitre($faker->word);
           
            $manager->persist($categorie);
            //rajout d'un article
           for ($j=1; $j <=7 ; $j++) { 
                $article = new Article();
                        $article->setTitre($faker->sentence(3));
                        $article ->setContenu($faker->text(500));
                        $article ->setImage('');
                        $article ->setCreatedAt($faker->dateTimeBetween('-3months'));
                        $article ->setCategorie($categorie);
        
                    $manager->persist($article);

        }

       

    }
    $manager->flush();
}
}
