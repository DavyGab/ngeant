<?php

namespace ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ShopBundle\Form\CommandeType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use ShopBundle\Entity\Commande;
use ShopBundle\Form\CommandeMessageType;
use Symfony\Component\HttpFoundation\Request;

class CommandeStepController extends Controller
{
    public function stepOneAction(Request $request)
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

                return $this->redirectToRoute('shop_commande_step_2', array('id_commande' => urlencode($crypt->crypt($commande->getId()))));
            }
        }

        return $this->render('ShopBundle:Commande:commandeStep1.html.twig', array(
                'form' => $form->createView(),
            )
        );
    }

    public function stepTwoAction(Request $request, $id_commande)
    {
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

            return $this->redirectToRoute('shop_commande_step_3', array('id_commande' => $id_commande));
        }

        return $this->render('ShopBundle:Commande:commandeStep2.html.twig', array(
                'form' => $form->createView(),
            )
        );
    }
}



