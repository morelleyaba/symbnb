<?php
namespace App\Service;

use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// Notion de service pour la pagination / L'applicquer uniquement sur la liste des reservations et celle des commentaires a titre d'exemple et laisser celle des annonces afin de mieux comprendre les modifications

class PaginationService extends AbstractController
{
    // ______________________________________D15-V5____(voir AdminAdController)

    #l'entity qui nous interresse (si je veux faire la pagination sur les Comments ou les Bookings ou les Ad)
    private $entityClass;
    private $limit=10;
    // la page surlaquelle je me trouve actuellement / par defaut = 1
    private $currentPage=1;
    private $manager;


    public function setEntityClass($entityClass){

        $this->entityClass = $entityClass;

        return $this;
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }


    public function setLimit($limit){

        $this->limit = $limit;

        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }


    public function setPage($page){

        $this->currentPage = $page;

        return $this;
    }

    public function getPage()
    {
        return $this->currentPage;
    }

    
    public function __construct()
    {
        $manager = $this->getDoctrine()->getManager();
        return $manager;
    }
    
    public function getData()
    {
        // 1) Calculer l'offset
        $offset=$this->currentPage * $this->limit - $this->limit;
        // 1 * 10 = 10 - 10 = 0
        // 2 * 10 = 20 - 10 = 10

        // 2) Demander au repository de trouver les elements / Recuperer le repo de l'entity conserné a l'aide du manager
        $entiyRepo = $this->manager->getRepository($this->entityClass);
        $data =$entiyRepo->findBy([],[],$this->limit,$offset);

        // 3) Renvoyer les elements en question
        return $data;
    }
    
    public function getPages()
    {
        // recuperer le repository qui sera baser sur l'entity en question
        $entiyRepo = $this->manager->getRepository($this->entityClass);
        // Connaitre le total des enregistrements de la table /  Rendre dynamique la pagination, determiner le nbre de page qu'on peut afficher en fonction du nbre d'annonces divisé par la limit
        $total= count($entiyRepo->findAll());

        // faire la division, l'arrondir et le renvoyer
        $pages = ceil($total/$this->limit); # 3.4 => 4 [arrondie a 4 (l'entier superieur) par la fonction "ceil()"]

        return $pages;
    }
}
