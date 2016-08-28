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
    public function precommandeAction(Request $request, $id_commande)
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

        $info_commande = 'Nounours : ' . $produit['prix'] . '€<br><span style="color: red;font-weight: bolder;">Réduction : 10%</span><br>Livraison : ' . $info->getFraisDeLivraison($commande->getCodePostal()) . '€<br>Total : ' . $total . '€';

        $returnArray = array(
            'form' => array(
                'cancel_return' => $this->generateUrl('shop_precommande_annulation', array('id_commande' => $id_commande), UrlGeneratorInterface::ABSOLUTE_URL),
                'notify_url' => $this->generateUrl('shop_ipn_notification', array(), UrlGeneratorInterface::ABSOLUTE_URL),
                'return' => $this->generateUrl('shop_precommande_valide', array('id_commande' => $id_commande), UrlGeneratorInterface::ABSOLUTE_URL),
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
            'info' => $info_commande,
            'titre' => 'Terminer la pré-commande',
            'bouton' => 'Valider la precommande'
        );
        $currentRoute = $request->attributes->get('_route');
        if ($currentRoute == 'shop_reservation_precommande') {
            $returnArray['message'] = 'Votre commande est bientôt terminée..<br>Nous reviendrons rapidement vers vous pour le choix de la date, de l\'heure et du lieu de livraison afin de profiter de l\'offre de réduction.';
        } else {
            $returnArray['message'] = 'Le site est encore en phase de lancement et devrait être entièrement opérationnel d\'ici un mois. Nous reviendrons rapidement vers vous afin de finaliser votre commande (choix de la date, de l\'heure et du lieu de livraison).<br>
Pour vous remercier de votre confiance, nous vous offrons dès à présent 10% de réduction sur votre commande.<br>
Encore merci et à très bientôt,<br>
L\'équipe BigDoudou';
        }
        
        return $this->render('ShopBundle:Default:IPNPage.html.twig', $returnArray);
    }

    public function precommandeValidationAction($id_commande) {

        $titre = 'Commande enregistrée';
        $message = 'Votre commande a bien été enregistrée. Vous devriez recevoir sous peu un mail de confirmation.';
        $this->get('session')->getFlashBag()->add($titre, $message);
        
        $crypt = $this->container->get('app.crypt');
        $commande = $em->getRepository('ShopBundle:Commande')->findOneById($crypt->decrypt(urldecode($id_commande)));
        
        /*
         * Envoi du mail
         */
        $message = \Swift_Message::newInstance()
            ->setSubject('Merci de votre commande !')
            ->setFrom(array('hello@bigdoudou.fr' => 'Team Bigdoudou'))
            ->setTo($commande->getEmail())
            ->addBcc('hello@bigdoudou.fr')
            ->setBody(
                $this->renderView('ShopBundle:mails:inscription.txt.twig',
                    array(
                        'lien_precommande' => $this->generateUrl('shop_reservation', 
                            array(
                                'email' => $commande->getEmail(),
                                'id_commande' => urlencode($crypt->crypt($commande->getId()))
                            ), 
                            UrlGeneratorInterface::ABSOLUTE_URL
                        )
                    )
                ),
                'text/plain'
            );
        $this->get('mailer')->send($message);
        /*
         * /Mail
         */

        return $this->forward('AppBundle:Home:indexPrecommande');
    }

    public function precommandeAnnulationAction($id_commande) {
        $em = $this->getDoctrine()->getManager();
        $crypt = $this->container->get('app.crypt');
        $commande = $em->getRepository('ShopBundle:Commande')->findOneById($crypt->decrypt(urldecode($id_commande)));
        $commande->setStatus(2);
        $em->persist($commande);
        $em->flush();

        return $this->redirectToRoute('home');
    }

    public function messageAction(Request $request, $id_commande) {
        $commandeMessage = new Commande();
        $form = $this->get('form.factory')->create(CommandeMessageType::class, $commandeMessage);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $info = $this->get('app.info');
            $em = $this->getDoctrine()->getManager();
            $crypt = $this->container->get('app.crypt');
            $commande = $em->getRepository('ShopBundle:Commande')->findOneById($crypt->decrypt(urldecode($id_commande)));
            $commande->setMessage($commandeMessage->getMessage());
            $em->persist($commande);
            $em->flush();

            return $this->redirectToRoute('shop_reservation_commande', array('id_commande' => $id_commande));
        }

        return $this->render('ShopBundle:Default:step2Message.html.twig', array(
            'form' => $form->createView(),
            'id_commande' => $id_commande)
        );
    }
}



