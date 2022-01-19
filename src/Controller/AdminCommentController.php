<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    // _________________________Affichage des commentaires__________D15-V6 => V11__Pas terminer
    /**
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comments_index")
     */
    public function index(CommentRepository $commentRepo, $page): Response
    {
            // ________________________________systeme de pagination__D15-V2
            $limit=7;
            $start=$page * $limit - $limit;
            $total= count($commentRepo->findAll());
            $pages = ceil($total/$limit);
        
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $commentRepo->findBy([],[],$limit,$start),
            'pages'=>$pages,
            'page'=>$page,
        ]);
    }

    // _____________edition d'un Commentaire par l'admin_________D14-V15 (explication des trois methodes d'edition)

    /**
     * editer un commentaire
     * @Route("/admin/comments/{id}/edit", name="admin_comments_edit")
     * @param Comment $comment
     * @param Request $request
     * @return void
     */
    public function edit (Comment $comment,Request $request)
    {
        // 'AdminCommentType' le nouveau formType du commentaire de l'admin
        $form=$this->createForm(AdminCommentType::class,$comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            // faire persister l'$ad
            $em->persist($comment);
            $em->flush();

            // notification
            $this->addFlash(
                "success",
                "Le commentaire N <strong>{$comment->getId()}</strong> a bien été modifié"
            );

            return $this->redirectToRoute("admin_comments_index");
        }
        return $this->render('admin/comment/edit.html.twig',[
             'formComment'=>$form->createView(),
             'comment'=>$comment
        ]);
    }

    // ____________supprimer un commentaire par l'admin

    /**
     * suprimier une annonce par l'admin
     * @ROUTE("/admin/comments/{id}/delete", name ="admin_comments_delete")
     * @param Comment $comment
     * @return void
     */
    public function delete(Comment $comment)
    {
            $em=$this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();
            // notification
            $this->addFlash(
                "success",
                // avoir acces au "name" par le "cmment" avec la methode des fonctions et non variable qu'on a l'habitude d'utiliser
                // "admin/comment/index.html.twig L-30" Pour la methode des variales
                "Le commentaire de <strong>{$comment->getAuthor()->getFullName()}</strong> a bien été supprimé"
            );
        return $this->redirectToRoute('admin_comments_index');
    }

}
