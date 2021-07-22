<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;


class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $faker = \Faker\Factory::create('fr_Fr');

        //Creer 3 catégories

        for($i = 1; $i <= 4; $i++) {
            $category = new Category();
            $category->setTitle($faker->word())
                ->setDescription($faker->paragraph());

            $manager->persist($category);

            //Créer entr 8 et 10 articles
            for($j = 1; $j <= mt_rand(8, 10); $j++){
                $article = new Article();

                $content = join($faker->paragraphs(5), '</p><p>');

                $article->setTitle($faker->sentence())
                    ->setContent($content)
                    ->setImage($faker->imageUrl())
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setCategory($category);

                $manager->persist($article);

                for($k = 1; $k <= mt_rand(8, 10); $k++) {
                    $comment = new Comment();

                    $content =  join($faker->paragraphs(2), '</p><p>') ;

                    $days = (new \DateTime())->diff($article->getCreatedAt())->days;

                    $comment->setAuthor($faker->name)
                        ->setContent($content)
                        ->setCreatedAt($faker->dateTimeBetween( '-' . $days . ' days'))
                        ->setArticle($article);

                    $manager->persist($comment);

                }
            }
        }

        $manager->flush();
    }
}
