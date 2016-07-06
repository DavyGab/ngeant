<?php

namespace ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ShopBundle\Form\CommandeType;

class ReservationController extends Controller
{
    public function precommandeAction($email, $id_commande)
    {
        $em = $this->getDoctrine()->getManager();
        $crypt = $this->container->get('app.crypt');
        $commande = $em->getRepository('ShopBundle:Commande')->findOneBy(
            array(
                'id'  =>  $crypt->decrypt($id_commande),
                'email' => $email
            )
        );
        $form = $this->get('form.factory')->create(CommandeType::class, $commande);

        $produit = $commande->getProduit();

        return $this->render('ShopBundle:Default:shortPage.html.twig', array(
            'cancel_return' => $this->generateUrl('precommande_annulation'),
            'notify_url' => $this->generateUrl('ipn_notification'),
            'return' => $this->generateUrl('precommande_valide'),
            'item_name' => $produit['nom'],
            'amount' => $produit['prix'],
            'lc' => 'FR',
            'cmd' => '_xclick',
            'currency_code' => 'EUR',
            'business' => 'bigdoudou@gmail.com',
            'tax' => 0,
            'no_note' => 1
        ));
    }
}
