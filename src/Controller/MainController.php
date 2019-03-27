<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Provider\DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('main/index.html.twig', [
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

        return $this->render('update/trickEdit.html.twig', [
            'title'=>'New',
            'formArticle'=>$form->createView()
        ]);
    }
    /**
     * @Route("/trick/{id}", name="show_trick")
     */
    public function showTrick(Article $article, Request $request, ObjectManager $manager){
        $comment = new Comment();
        $comment->setUser($this->getUser());

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $comment->setArticle($article);

            $manager->persist($comment);
            $manager->flush();
        }

        return $this->render('article/show.html.twig',[
            'title'=>'Trick',
            'article'=>$article,
            'commentForm'=> $form->createView()
        ]);
    }
    /**
    * @Route("/manage", name="manage")
    */
    public function manage(){
        return $this->render('manage/manage.html.twig', [
            'title'=>'Administration'
        ]);
    }
    /**
     * @Route("/manage/user", name="listing_user")
     */
    public function listingUser(UserRepository $repo){
        $users = $repo->findAll();

        return $this->render('manage/user.html.twig', [
            'title'=>'Utilisateurs',
            'users'=>$users
        ]);
    }
    /**
     * @Route("/manage/trick", name="listing_trick")
     */
    public function listingTrick(ArticleRepository $repo){
        $tricks = $repo->findAll();

        return $this->render('manage/tricks.html.twig', [
            'title'=>'Tricks',
            'tricks'=>$tricks
        ]);
    }
    /**
     * @Route("/manage/comment", name="listing_comment")
     */
    public function listingComment(CommentRepository $repo){
        $comments = $repo->findAll();

        return $this->render('manage/comments.html.twig', [
            'title'=>'Commentaires',
            'comments'=>$comments
        ]);
    }
    /**
     * @Route("/manage/comment/{id}/edit", name="comment_update")
     */
    public function formComment(Comment $comment=null, Request $request, ObjectManager $manager){
        if (!$comment){
            $comment = new Comment();
        }
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if(!$comment->getId()){
                $comment->setCreatedAt(new \DateTime()); //déplacer vers constructeur
            }
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('show_trick', ['id'=>$comment->getArticle()->getId()]);
        }

        return $this->render('update/commentEdit.html.twig', [
            'title'=>'New',
            'formComment'=>$form->createView()
        ]);
    }
}
