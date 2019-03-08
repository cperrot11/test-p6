<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/main", name="main")
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'title'=>'Bienvennue',
            'afficher'=>true
        ]);
    }

    /**
     * @Route("/blog", name="blog")
     */
    public function blog(ArticleRepository $repo){
        $article = $repo->findAll();

        return $this->render('article/blog.html.twig', [
            'title'=>'Blog',
            'articles'=>$article
            ]);
    }
    /**
     * @Route("/trick/new", name="blog_create")
     * @Route("/trick/{id}/edit", name="blog_update")
     */
    public function formArticle(Article $article=null, Request $request, ObjectManager $manager){
        if (!$article){
            $article = new Article();
        }
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }
            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('show_trick', ['id'=>$article->getId()]);
        }

        return $this->render('article/create.html.twig', [
            'title'=>'New',
            'formArticle'=>$form->createView()
        ]);
    }
    /**
     * @Route("/trick/{id}", name="show_trick")
     */
    public function showTrick(Article $article, Request $request, ObjectManager $manager){
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);

            $manager->persist($comment);
            $manager->flush();
        }

        return $this->render('article/show.html.twig',[
            'title'=>'Trick',
            'article'=>$article,
            'commentForm'=> $form->createView()
        ]);
    }
}
