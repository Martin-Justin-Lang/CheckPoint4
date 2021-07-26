<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Slugify;

class BlogController extends AbstractController
{
    private $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }


    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo): Response
    {
        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home() {
        return $this->render('blog/home.html.twig');
    }

    /**
     * @Route("/blog/new", name="blog_new")
     * @Route("/blog/{slug}/edit", name="blog_edit")
     */

    //injection de dépendance
    public function form(Article $article = null, Request $request, EntityManagerInterface $manager) {

        // J'instencie un nouvel objet de la class(ou entité)  USER

        if(!$article) {
            $article = new Article();
        }

        //J'utilise la fonction createForm pour instancier le formulaire en lui passant en parametre le nom de la
        // classe du formulaire ainsi que la variable
        // dans laquelle est mon objet, afin d'avoir acces à ses fonctions.

        $form = $this->createForm(ArticleType::class, $article);

        // handleRequest permet de gérer le traitement de la saisie du formulaire par l'utilisateur.
        //  On lui passe en parametre l'injection de dépendance $request.

        $form->handleRequest($request);

        //C’est l’instruction $form->isSubmitted() qui permet de savoir si le formulaire a été
        // saisi et si de plus les règles de validations sont vérifiées
        // ($form->isValid()) alors l’enregistrement sera ajouté à la base de données avant de rediriger
        // la requête vers un affichage de la liste des locations.


        if($form->isSubmitted() && $form->isValid()) {
            $slug = $this->slugify->generate($article->getTitle());
            $article->setSlug($slug);

            $article->setCreatedAt((new \DateTime()));

            // Le persist va dire que l'entité qu'on lui passe en parametre, dans ce cas $user est lié à la bdd

            $manager->persist($article);

            //Envoi les infos saisies dans la base de donnée. A partir de la, l'objet est converti en requete SQL

            $manager->flush();

            //le redirecttoroute fait une redirection web vers une route ici 'security_login'

            return $this->redirectToRoute('blog_show', ['slug' => $article->getSlug()]);
        }

        //render fait une redirection vers un moteur de template twig
        //'form est le nom qu'on donne au formulaire afin de pouvoir l'appeler dans twig
        //'form' est donc stocké dans une variable $form à qui je dis de créer la vue. Ensuite il faudra aller dans twig créer le formulaire
        //en faisant {{form(form)}}, on met form entre parentheses car il est égal à 'form'

        return $this->render('blog/new.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/blog/{slug}", name="blog_show")
     * @param Article $article
     */
    public function show(Article $article, Request $request, EntityManagerInterface $manager) {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedat(new \DateTime())
                ->setArticle($article);

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['slug' => $article->getSlug()]);
        }
        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
    }

}
