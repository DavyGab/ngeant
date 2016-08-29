<?php

namespace ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ShopBundle\Entity\PaypalInfo;

class PaiementController extends Controller
{
    public function IpnNotificationAction(Request $request)
    {
        $e = 0;
        $url = 'https://www.paypal.com/cgi-bin/webscr';
        $message = \Swift_Message::newInstance()
            ->setSubject('ok')
            ->setFrom('hello@bigdoudou.fr')
            ->setTo('paypal@bigdoudou.fr')
            ->setBody('ok');
        $this->get('mailer')->send($message);

        $em = $this->getDoctrine()->getManager();

        $paypal = new PaypalInfo();
        $paypal->setInfo($_POST);
        $em->persist($paypal);

        $curl = curl_init();
        curl_setopt_array($curl, array
        (
            CURLOPT_URL => $url,
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => http_build_query(array('cmd' => '_notify-validate') + $_POST),
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HEADER => FALSE,
        ));

        $response = curl_exec($curl);
        $status   = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);
            $log = $this->get('logger');

        $payment_status = $request->request->get('payment_status');
        $payment_amount = $request->request->get('mc_gross');
        $receiver_email = $request->request->get('receiver_email');
        $payer_email = $request->request->get('payer_email');

        if($status == 200 && $response == 'VERIFIED')
        {
            $e = 1;
            $log->info('Paiement reçu.');
            $email_account = $this->getParameter('receiver_email');

            parse_str($request->request->get('custom'), $custom);

            if ( $payment_status == "Completed") {
                //Vérifie que le paiement est OK
                $log->info('Paiement complété de '. $payer_email);
                $e = 2;
                if ($email_account == $receiver_email) {
                    //Vérifie que nous avons vien recu l'argent
                    $log->info('Paiement bien recu de '. $payer_email);
                    //On récupère la commande correspondant a l'email et à l'id de commande
                    $crypt = $this->get('app.crypt');
                    $commande = $em->getRepository('ShopBundle:Commande')->findOneById($crypt->decrypt(urldecode($custom['id_commande'])));
                    if (!$commande) {
                        $log->info('Le paiement recu  de '. $payer_email .' ne correspond a aucune commande');
                        $subject = 'Erreur lors du paiement de votre commande';
                        $template = 'ShopBundle:mails:erreur_precommande.txt.twig';
                        $message = \Swift_Message::newInstance()
                            ->setSubject($subject)
                            ->setFrom('paypal@bigdoudou.fr')
                            ->setTo('paypal@bigdoudou.fr')
                            ->setBody(
                                $this->renderView(
                                    $template
                                ),
                                'text/plain'
                            )
                        ;
                        $this->get('mailer')->send($message);
                        return new Response();
                    }
                    //On enregistre tout en BDD
                    $commande->setPaypalInfo($_POST);

                    $log->info('Paiement complété de '. $payer_email);
                    $e = 3;

                    $produit = $commande->getProduit();
                    // Si la commande a déja été payé...
                    if ($commande->getStatus() != 0) {
                        $log->info('Le paiement recu  de '. $payer_email .' correspond a une commande déjà payée');
                        $err = 1;
                    } else {
                        if ($payment_amount != $produit['prix']) {
                            $log->info('Le paiement recu  de '. $payer_email .' est diffrent de la commande correspondante');
                            $err = 1;
                        } else {
                            $log->info('Le paiement recu  de '. $payer_email .' correspond à la commande '. $commande->getId() . ' et a été payé.');
                            $commande->setStatus(1);
                            $err = 0;
                        }
                    }
                    $e = 4;
                    $em->persist($commande);
                }
            }
            else {
                $log->info('Paiement echec.');
                $err = 1;
            }
        } else {
            $log->info('Erreur lors de la validation dune commande');
            $err = 1;
        }

        if (!$err) {
            $subject = 'Confirmation de votre pré-commande Bigdoudou';
            $template = 'ShopBundle:mails:precommande.txt.twig';

            $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom('paypal'.$e.'@bigdoudou.fr')
                ->setTo($payer_email)
                ->addBcc('paypal@bigdoudou.fr')
                ->setBody(
                    $this->renderView(
                        $template
                    ),
                    'text/plain'
                );
            $this->get('mailer')->send($message);
        }

        $em->flush();
        return new Response();
    }
}
