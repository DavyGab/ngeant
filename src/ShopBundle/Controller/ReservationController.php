<?php

namespace ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ShopBundle\Form\CommandeType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use ShopBundle\Entity\Commande;
use ShopBundle\Form\CommandeMessageType;
use Symfony\Component\HttpFoundation\Request;

class ReservationController extends Controller
{
    public function precommandeAction($id_commande)
    {
        $em = $this->getDoctrine()->getManager();
        $crypt = $this->container->get('app.crypt');
        $commande = $em->getRepository('ShopBundle:Commande')->findOneById($crypt->decrypt(urldecode($id_commande)));

        if (!in_array($commande->getStatus(), array(0, 2))) {
            return $this->redirectToRoute('home_precommande');
        }

        $produit = $commande->getProduit();

        $custom = array(
            'id_commande' => $id_commande
        );
        $info = $this->get('app.info');

        $total = round(0.90 * $produit['prix'], 2) + $info->getFraisDeLivraison($commande->getCodePostal());

        $info_commande = 'Nounours : ' . $produit['prix'] . '€<br>Réduction : 10%<br>Livraison : ' . $info->getFraisDeLivraison($commande->getCodePostal()) . '€<br>Total : ' . $total . '€';

        return $this->render('ShopBundle:Default:IPNPage.html.twig', array(
            'form' => array(
                'cancel_return' => $this->generateUrl('shop_precommande_annulation', array('id_commande' => $id_commande), UrlGeneratorInterface::ABSOLUTE_URL),
                'notify_url' => $this->generateUrl('shop_ipn_notification', array(), UrlGeneratorInterface::ABSOLUTE_URL),
                'return' => $this->generateUrl('shop_precommande_valide', array(), UrlGeneratorInterface::ABSOLUTE_URL),
                'item_name' => $produit['nom'],
                'amount' => round(0.90 * $produit['prix'], 2),
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

        return $this->forward('AppBundle:Home:indexPrecommande');
    }

    public function precommandeAnnulationAction($id_commande) {
        $em = $this->getDoctrine()->getManager();
        $crypt = $this->container->get('app.crypt');
        $commande = $em->getRepository('ShopBundle:Commande')->findOneById($crypt->decrypt(urldecode($id_commande)));
        $commande->setStatus(2);
        $em->persist($commande);
        $em->flush();

        return $this->redirectToRoute('home_precommande');
    }

    public function messageAction(Request $request, $id_commande) {
        $commandeMessage = new Commande();
        $form = $this->get('form.factory')->create(CommandeMessageType::class, $commandeMessage);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $info = $this->get('app.info');
            $em = $this->getDoctrine()->getManager();
            $commande = $em->getRepository('ShopBundle:Commande')->findOneById($crypt->decrypt(urldecode($id_commande)));
            $commande->setMessage($commandeMessage->getMessage());
            $em->persist($commande);
            $em->flush();

            return $this->redirectToRoute('shop_reservation_precommande', array('id_campaign' => $id_commande));
        }

        return $this->render('ShopBundle:Default:step2Message.html.twig', array(
            'form' => $form->createView(),
            'id_campaign' => $id_commande)
        );
    }
}



