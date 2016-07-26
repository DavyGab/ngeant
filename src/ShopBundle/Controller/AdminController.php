<?php

namespace ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    public function rackelAction(Request $request) {
        $password = $request->request->get('password');
        if ($password == 'alexisdavy123!') {
            return $this->forward('ShopBundle:Admin:showCommande');
        }


        return $this->render('ShopBundle:Admin:rackel.html.twig');
    }

    public function showCommandeAction()
    {
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('ShopBundle:Commande')
        ;

        $commandes = $repository->findAll();
        $info = $this->get('app.info');

        return $this->render('ShopBundle:Admin:show_commande.html.twig', array(
            'commandes' => $commandes,
            'statusToText' => $info->getStatus()
        ));
    }

}
