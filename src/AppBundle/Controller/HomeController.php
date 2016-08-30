<?php
//HomeController
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
        $currentRoute = $request->attributes->get('_route');

        $commande = new Commande();
        $form = $this->get('form.factory')->create(CommandeEmailType::class, $commande);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $info = $this->get('app.info');
            $em = $this->getDoctrine()->getManager();
            $commande->setStatus(0);
            $commande->setProduit(array(
                    'id' => $commande->getProduit(),
                    'nom' => $info->getNomProduit($commande->getProduit()),
                    'prix' => 0,9 * $info->getPrixProduit($commande->getProduit())
                )
            );
            dump(0,9 * $info->getPrixProduit($commande->getProduit()));
            $em->persist($commande);
            $em->flush();

            $info = $this->get('app.info');
            if($info->getFraisDeLivraison($commande->getCodePostal()) === false) {
                $titre = 'Oups..';
                $message = 'Pour l\'instant nous ne livrons qu\'à Paris et petite couronne. Nous étendrons la zone de livraison très prochainement.';
                $this->get('session')->getFlashBag()->add($titre, $message);
            } else {
                $crypt = $this->get('app.crypt');

                if ($currentRoute == 'home') {
                    return $this->redirectToRoute('shop_step_2_message', array(
                        'id_commande' => urlencode($crypt->crypt($commande->getId()))
                    ));
                } else {
                    return $this->redirectToRoute('shop_reservation_precommande', array('id_commande' => urlencode($crypt->crypt($commande->getId()))));
                }

            }
        }

        if ($currentRoute == 'home') {
            $render_array = array(
                'form' => $form->createView(),
                'precommande' => 0
            );
        } else {
            $render_array = array(
                'form' => $form->createView(),
                'precommande' => 1
            );
        }

        return $this->render('AppBundle:Home:home.html.twig', $render_array);
    }
}
