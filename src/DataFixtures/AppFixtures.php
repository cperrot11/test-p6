<?php

namespace App\DataFixtures;

use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker\Factory;


class AppFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * UserFixtures constructor.
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
//        Création des User
        for ($a = 1; $a < 5; $a++) {
            $user = new User();
            $user->setName($faker->lastName);
            $user->setEmail($faker->unique()->companyEmail);

            $user->setPassword($this->encoder->encodePassword($user, '123456'));

            $manager->persist($user);
            $this->addReference('user_ref'.$a, $user);
        }
        $manager->flush();

        //         Création des articles
        for ($b = 1; $b < 5; $b++) {
            $article = new Article();
            $article->setTitle($faker->sentence());
            $article->setCreatedAt($faker->dateTimeBetween('-6months'));
            $content = '<p>' . join($faker->paragraphs(10), '</p><p>') . '</p>';
            $article->setContent($content);
            $article->setPicture($faker->imageUrl(640, 480, 'sports'));

            $manager->persist($article);
            $this->addReference('article_ref'.$b, $article);
        }
        $manager->flush();

//            Création des commentaires
        for ($c=1; $c<10; $c++) {
            $comment = new Comment();
            $rand_user=rand(1,4);
            $user = $this->getReference('user_ref'.$rand_user);
            $rand_article=rand(1,4);
            $article = $this->getReference('article_ref'.$rand_article);

            $content = '<p>'. join($faker->paragraphs(1), '</p><p>').'</p>';

            $comment->setCreatedAt($faker->dateTimeBetween('-2months'))
                ->setArticle($article)
                ->setContent($content)
                ->setUser($user);

            $manager->persist($comment);
        }
        $manager->flush();
    }
    public static function getGroups(): array
    {
        return ['group3'];
    }
}
