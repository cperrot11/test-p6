<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Media;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Provider\DateTime;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @Route("/blog/{page<\d+>?1}", name="blog")
     */
    public function blog($page , ArticleRepository $repo){
        $page=(!$page)?1:$page;

        $article = $repo->findAllArticle($page, getenv('LIMIT'));

        $pagination = array(
            'page' => $page,
            'route' => 'blog',
            'pages_count' => ceil($article->count()/getenv('LIMIT')),
            'route_params' => array()
        );

        return $this->render('article/blog.html.twig', [
            'title'=>'Blog',
            'pagination'=>$pagination,
            'articles'=>$article
            ]);
    }
    /**
     * @Route("/user/trick/new", name="blog_create")
     */
    public function ArticleNew(Article $article=null, Request $request, ObjectManager $manager){
        $article = new Article();
        $article->setCreatedAt(new \DateTime());

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $article->setCreatedAt(new \DateTime());

            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form['myFile']->getData();
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $file->move(
                    $this->getParameter('upload_dir'),
                    $fileName
                );
                $article->setMyFile($fileName);
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('show_trick', ['id'=>$article->getId()]);
        }

        return $this->render('article/trickNew.html.twig', [
            'title'=>'New figure',
            'formArticle'=>$form->createView()
        ]);
    }
    /**
     * @Route("/user/trick/{id}/edit", name="blog_update")
     */
    public function ArticleUpdate(Article $article=null, Request $request, ObjectManager $manager){
        if (!$article){
            $article = new Article();
            $action ='Créer';
            $titre = 'New';
            $titre2 = 'Ajouter figure.';
//            $media1 = new Media();
//            $media1->setName('media1');
//            $media2 = new Media();
//            $media2->setName('media2');
//            $article->getMedia()->add($media1);
//            $article->getMedia()->add($media2);
        }
        else {
            $action='Modifier';
            $titre = 'Update';
            $titre2 = 'Modifier figure.';
            $article->setMyFile(
                new File($this->getParameter('upload_dir').'\\'.$article->getMyFile())
            );
        }
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form['myFile']->getData();
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $file->move(
                    $this->getParameter('upload_dir'),
                    $fileName
                );
                $article->setMyFile($fileName);
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('show_trick', ['id'=>$article->getId()]);
        }

        return $this->render('update/trickEdit.html.twig', [
            'title'=>$titre,
            'title2'=>$titre2,
            'action' => $action,
            'formArticle'=>$form->createView()
        ]);
    }
    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
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
