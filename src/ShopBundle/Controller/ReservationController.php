<?php

namespace ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ShopBundle\Form\CommandeType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
        $produit = $commande->getProduit();

        $custom = array(
            'id_commande' => $id_commande,
            'email' => $email
        );

        return $this->render('ShopBundle:Default:IPNPage.html.twig', array(
            'form' => array(
                'cancel_return' => $this->generateUrl('shop_precommande_annulation', array('id_commande' => $id_commande), UrlGeneratorInterface::ABSOLUTE_URL),
                'notify_url' => 'http://bigdoudou.shareyourstartup.com/app_dev.php/paypal/ipn',//$this->generateUrl('shop_ipn_notification'),
                'return' => $this->generateUrl('shop_precommande_valide', array(), UrlGeneratorInterface::ABSOLUTE_URL),
                'item_name' => $produit['nom'],
                'amount' => $produit['prix'],
                'lc' => 'FR',
                'cmd' => '_xclick',
                'currency_code' => 'EUR',
                'business' => $this->getParameter('paypal_email'),
                'tax' => 0,
                'no_note' => 1,
                'custom' => http_build_query($custom)
                ),
            'titre' => 'Terminer la commande',
            'message' => 'Blablabla'
            )
        );
    }
}
