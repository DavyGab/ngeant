<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ShopBundle\Entity\Commande;
use ShopBundle\Form\CommandeEmailType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomeController extends Controller
{
    public function indexAction(Request $request)
    {
        $commande = new Commande();
        $form = $this->get('form.factory')->create(CommandeEmailType::class, $commande);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $info = $this->get('app.info');
            $em = $this->getDoctrine()->getManager();
            $commande->setStatus(0);
            $commande->setProduit(array(
                    'id' => $commande->getProduit(),
                    'nom' => $info->getNomProduit($commande->getProduit()),
                    'prix' => $info->getPrixProduit($commande->getProduit())
                )
            );
            $em->persist($commande);
            $em->flush();

            $info = $this->get('app.info');
            if($info->getFraisDeLivraison($commande->getCodePostal()) === false) {
                $titre = 'Oups..';
                $message = 'Pour l\'instant nous ne livrons qu\'à Paris et petite couronne. Nous étendrons la zone de livraison très prochainement.';
                $this->get('session')->getFlashBag()->add($titre, $message);
            } else {
                $crypt = $this->get('app.crypt');

                /*
                 * Envoi du mail
                 */
                $message = \Swift_Message::newInstance()
                    ->setSubject('Bénéficiez de l\'offre de réduction !')
                    ->setFrom(array('hello@bigdoudou.fr' => 'Team Bigdoudou'))
                    ->setTo($commande->getEmail())
                    ->addBcc('hello@bigdoudou.fr')
                    ->setBody(
                        $this->renderView('ShopBundle:mails:inscription.txt.twig',
                            array(
                                'lien_precommande' => $this->generateUrl('shop_reservation', array(
                                    'email' => $commande->getEmail(),
                                    'id_commande' => urlencode($crypt->crypt($commande->getId()))), UrlGeneratorInterface::ABSOLUTE_URL
                                )
                            )
                        ),
                        'text/plain'
                    );
                $this->get('mailer')->send($message);
                /*
                 * /Mail
                 */

                return $this->redirectToRoute('shop_reservation', array(
                    'email' => $commande->getEmail(),
                    'id_commande' => urlencode($crypt->crypt($commande->getId()))
                ));
            }
        }

        return $this->render('AppBundle:Home:home.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
