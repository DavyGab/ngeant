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
                'id'  =>  $crypt->decrypt(urldecode($id_commande)),
                'email' => $email
            )
        );
        $produit = $commande->getProduit();

        $custom = array(
            'id_commande' => $id_commande,
            'email' => $email
        );
        $info = $this->get('app.info');

        $total = round(0.90 * ($produit['prix'] + $info->getFraisDeLivraison($commande->getCodePostal())), 2);

        $info_commande = 'Nounours : ' . $produit['prix'] . '<br>Livraison : ' . $info->getFraisDeLivraison($commande->getCodePostal()) . '€<br>Réduction : 10%<br>Total : ' . $total . '€';

        return $this->render('ShopBundle:Default:IPNPage.html.twig', array(
            'form' => array(
                'cancel_return' => $this->generateUrl('shop_precommande_annulation', array('id_commande' => $id_commande), UrlGeneratorInterface::ABSOLUTE_URL),
                'notify_url' => $this->generateUrl('shop_ipn_notification', array('email' => $email), UrlGeneratorInterface::ABSOLUTE_URL),
                'return' => $this->generateUrl('shop_precommande_valide', array(), UrlGeneratorInterface::ABSOLUTE_URL),
                'item_name' => $produit['nom'],
                'amount' => $total,
                'lc' => 'FR',
                'cmd' => '_xclick',
                'currency_code' => 'EUR',
                'business' => $this->getParameter('paypal_email'),
                'tax' => 0,
                'shipping' => $info->getFraisDeLivraison($commande->getCodePostal()),
                'no_note' => 1,
                'custom' => http_build_query($custom)
                ),
            'titre' => 'Terminer la pré-commande',
            'message' => 'Votre commande est bientôt terminée..<br>Nous reviendrons rapidement vers vous pour le choix de la date, de l\'heure et du lieu de livraison afin de profiter de l\'offre de réduction.',
            'info' => $info_commande
            )
        );
    }

    public function precommandeValidationAction() {

        $titre = 'Commande enregistrée';
        $message = 'Votre commande a bien été enregistrée. Vous devriez recevoir sous peu un mail de confirmation.';
        $this->get('session')->getFlashBag()->add($titre, $message);

        return $this->forward('AppBundle:Home:index');
    }

    public function precommandeAnnulationAction($id_commande) {
        $em = $this->getDoctrine()->getManager();
        $crypt = $this->container->get('app.crypt');
        $commande = $em->getRepository('ShopBundle:Commande')->findOneBy(
            array(
                'id'  =>  $crypt->decrypt(urldecode($id_commande)),
            )
        );
        $commande->setStatus(2);
        $em->persist($commande);
        $em->flush();

        return $this->redirectToRoute('home');
    }
}
