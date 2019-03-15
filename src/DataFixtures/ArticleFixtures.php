<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class ArticleFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');

        for ($a=1; $a<5; $a++) {
            $article = new Article();
            $article->setTitle($faker->sentence());
            $article->setCreatedAt($faker->dateTimeBetween('-6months'));
            $content = '<p>' . join($faker->paragraphs(10), '</p><p>') . '</p>';
            $article->setContent($content);
            $article->setPicture($faker->imageUrl(640,480,'sports'));

            $manager->persist($article);
        }

        $manager->flush();
    }
    public static function getGroups(): array
     {
         return ['group1'];
     }
}
