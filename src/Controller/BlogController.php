<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class BlogController extends AbstractController
{ 
    //la page d'accueil
    #[Route('/interface', name: 'blog1')]
    public function index1()
    {
        return $this->render('blog/interface.html.twig' );
      }


    #[Route('/', name: 'blog')]
    public function index(ArticleRepository $articleRepository, PaginatorInterface $paginator,Request $request): Response
    {
        
        
        $articles = $paginator->paginate(
            $articleRepository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        
        return $this->render('blog/index.html.twig', [
          'articles'=> $articles
        ]);
    }
    
   //Add New Article
    #[Route("article/new", name: 'article_new')]

    public function new(Request $request,EntityManagerInterface $entityManager ) : Response  {
    if (!$this->getUser()) {
        throw $this->createAccessDeniedException();
    }
    
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setCreatedAt(New \DateTime());
            $article->setAutheur($this->getUser());
            $entityManager->persist($article);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'votre Article est rajouté!'
            );
            
            return $this->redirectToRoute('blog');
        }
        
        return $this->render('blog/new.html.twig', [
           'form' =>$form->createView()
       ]);
    
    }

    #[Route("article/{id}", name: 'article_show')]
    public function show(Request $request, ArticleRepository $articleRepository, EntityManagerInterface $entityManager):Response
    
    {
        $articleId = $request->attributes->get('id');
        $article = $articleRepository->find($articleId);

        $comment = new Comment();
        $commentform =  $this->createForm(CommentType::class, $comment);
        $commentform->handleRequest($request);
        $this->addComment($commentform, $comment, $article, $entityManager);

       
     
        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'commentform' => $commentform->createView()
        ]); 
    }  

    #[Route("article/{id}/edit", name: 'article_edit')]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $article->getAutheur()) {
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'votre Article est modifié!'
            );
            return $this->redirectToRoute('article_show',['id' =>$article->getId()]);
        }
        

            return $this->render('blog/edit.html.twig', [
                'article' => $article,
                'editform' =>$form->createView()
            ]); 
        }  

        //add comment
        private function addComment($commentform, $comment, $article, $entityManager ){
            if($commentform->isSubmitted() && $commentform->isValid()) {
                $comment->setCreatedAT(New \DateTimeImmutable());
                $comment->setArticle($article);
                $entityManager->persist($comment);
                $entityManager->flush();
                $this->addFlash(
                    'success',
                    'votre commentaire est rajouté!'
                );
                return $this->redirectToRoute('article_show',['id' =>$article->getId()]);
            }
        }
      
   
}
