<?php
namespace App\Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class PaginationService {

    private $entityClass;
    private $limit=10;
    private $currentPage=1;
    private $manager;
    private $twig;
    private $route;
    private $templatePath;


    public function __construct(EntityManagerInterface $manager,Environment $twig,RequestStack $request,$templatePath)
    {

        $this->route=$request->getCurrentRequest()->attributes->get('_route');
        $this->manager=$manager;
        $this->twig=$twig;
        $this->templatePath=$templatePath;

    }
    public function setTemplatePath($templatePath){
        $this->templatePath=$templatePath;
        return $this;
    }
    public function getTemplatePath(){
        return $this->templatePath;
    }

    public function display(){
        $this->twig->display($this->templatePath,[
            'page'=>$this->currentPage,
            'pages'=>$this->getPages(),
            'current_path'=>$this->route
        ]);
    }

    public function getPages(){
        if(empty($this->entityClass)){
            throw new \Exception('Vous n\'avez pas specifier l\'entité');
        }
        $repo=$this->manager->getRepository($this->entityClass);
        $total=count($repo->findAll());
        return ceil($total/$this->limit);
    }

    public function getData(){
        if(empty($this->entityClass)){
            throw new \Exception('Vous n\'avez pas specifier l\'entité');
        }
      $offset = $this->currentPage * $this->limit-$this->limit;
      $repo = $this->manager->getRepository($this->entityClass);
      return $repo->findBy([],[],$this->limit,$offset);
    }

    public function setPage($page){
        $this->currentPage = $page;
        return $this;
    }

    public function getPage(){
        return $this->currentPage;
    }

    public function setLimit($limit){
        $this->limit=$limit;
        return $this;
    }
    public function getLimit(){
        return $this->limit;
    }

    public function setEntityClass($entityClass){
        $this->entityClass=$entityClass;
        return $this;
    }
    public function getEntityClass(){
        return $this->entityClass;
    }
    public function getEntityName(){
        return  str_replace("App\Entity\"","","$this->manager->getClassMetadata($this->entityClass)->name") ;
    }

}