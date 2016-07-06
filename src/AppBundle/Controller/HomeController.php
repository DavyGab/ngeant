<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ShopBundle\Entity\Commande;
use ShopBundle\Form\CommandeEmailType;

class HomeController extends Controller
{
    public function indexAction(Request $request)
    {
        $commande = new Commande();
        $form = $this->get('form.factory')->create(CommandeEmailType::class, $commande);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $commande->setStatus(0);
            $commande->setProduit(array(
                    'id' => $commande->getProduit(),
                    'nom' => $produitNom[$commande->getProduit()],
                    'prix' => $commande->getProduit() == 1 ? '60' : '120'
                )
            );
            $em->persist($commande);
            $em->flush();

            $crypt = $this->container->get('app.crypt');
            return $this->redirectToRoute('shop_reservation', array(
                'email' => $commande->getEmail(),
                'id_commande' => $crypt->crypt($commande->getId())
            ));
        }

        return $this->render('AppBundle:Home:home.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
