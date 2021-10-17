<?php 


namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController{

    /**
     * @Route("/hello/{prenom}/age/{age}",name="hello")
     * @Route("/salut",name="hello_base")
     * montre la page qui dis Je suis
     */
public function hello($prenom="anonyme", $age=0){
    // return new Response("Je suis ".$prenom);
    return $this->render('hello.html.twig',
    [
        'prenom'=> $prenom,
        'age'=> $age
    ]
);
}

    /**
     * @Route("/", name="homepage")
     */
    public function home(){
        return $this->render('home.html.twig',
        ['title'=>"Bonjour a tous",
        'age'=>7
        ]
    );
//         return new Response("
// <html>
// <head>
// <title>Mon application</title>
// </head>

// <body>
// <h1>Bonjour tout le monde</h1>
// <p>c'est ma premiere page symfony</p>
// </body>
// </html>
//         ");
    }
}
?>