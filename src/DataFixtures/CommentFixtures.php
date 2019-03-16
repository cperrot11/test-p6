<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Article;

use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Repository\RepositoryFactory;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\ORM\EntityManager;

class CommentFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

//        $repoArticle = $repository->getDoctrine()->getRepository(Article::class);
//        $nbrArticle = $repoArticle->count();

          for ($a=1; mt_rand(2, 5); $a++) {
            $comment = new Comment();

//            $idArt = mt_rand(1, $nbrArticle);
            $idArt = mt_rand(145, 148);
            $idUser = mt_rand(21, 24);
            $content = '<p>'. join($faker->paragraphs(1), '</p><p>').'</p>';

            $comment->setCreatedAt($faker->dateTimeBetween('-2months'))
                    ->setArticle($idArt)
                    ->setContent($content)
                    ->setAuthor('A remplacer');

            $manager->persist($comment);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['group2'];
    }

}
