<?php

namespace ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PaiementController extends Controller
{
    public function IpnNotificationAction(Request $request)
    {
        $log = $this->get('logger');
        $log->info('Paiement !!!!!!!!');
        //Notre adresse qui doit recevoir l'argent
        $email_account = $this->getParameter('paypal_email');

        $req = 'cmd=_notify-validate';
        foreach ($_POST as $key => $value) {
            $value = urlencode(stripslashes($value));
            $req .= "&$key=$value";
        }
        $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
        $fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);
        $item_name = $request->request->get('item_name');
        $item_number = $request->request->get('item_number');
        $payment_status = $request->request->get('payment_status');
        $payment_amount = $request->request->get('mc_gross');
        $payment_currency = $request->request->get('mc_currency');
        $txn_id = $request->request->get('txn_id');
        $receiver_email = $request->request->get('receiver_email');
        $payer_email = $request->request->get('payer_email');
        parse_str($request->request->get('custom'), $custom);

        $em = $this->getDoctrine()->getManager();

        if (!$fp) {
        } else {
            fputs ($fp, $header . $req);
            while (!feof($fp)) {
                $res = fgets ($fp, 1024);
                // vérifie que payment_status a la valeur Completed
                if (strcmp ($res, "VERIFIED") == 0) {
                    $log->info('Paiement vérifié de '. $payer_email);
                    //Vérifie que le paiement est OK
                    if ( $payment_status == "Completed") {
                        $log->info('Paiement complété de '. $payer_email);
                        //Vérifie que nous avons vien recu l'argent
                       if ( $email_account == $receiver_email) {
                           $log->info('Paiement bien recu de '. $payer_email);
                            //On récupère la commande correspondant a l'email et à l'id de commande
                            $crypt = $this->get('app.crypt');
                            $commande = $em->getRepository('ShopBundle:Commande')->findOneBy(
                                array(
                                    'id'  =>  $crypt->decrypt($custom['id_commande']),
                                    'email' => $custom['id_commande']
                                )
                            );
                            if (!$commande) {
                                $log->info('Le paiement recu  de '. $payer_email .' ne correspond a aucune commande');
                                $message = 'Paiement recu de '. $payer_email .'sans commande correspondante.';
                                mail('paypal@bigdoudou.fr', 'Paiement sans commande', $message);
                                return new Response();
                            }
                            //On enregistre tout en BDD
                            $commande->setPaypalInfo($_POST);

                            $log->info('Paiement complété de '. $payer_email);

                           $produit = $commande->getProduit();
                            // Si la commande a déja été payé...
                            if (!in_array($commande->getStatus(), array(0, 2))) {
                                $message = 'La commande '. $commande->getId() .' a été payé 2 fois.';
                                mail('paypal@bigdoudou.fr', 'Paiement double pour une commande', $message);
                            } else {
                                if ($payment_amount != $produit['prix']) {
                                    $message = 'La commande '. $commande->getId() .' a été payé avec un prix différent.';
                                    mail('paypal@bigdoudou.fr', 'Paiement différent pour une commande', $message);
                                } else {
                                    $message = 'La commande '. $commande->getId() .' a été payé.';
                                    mail('paypal@bigdoudou.fr', 'Paiement reçu - Mazal Tov', $message);
                                    $commande->setStatus(1);
                                }
                            }

                           $em->persist($commande);
                           $em->flush();
                        }
                    }
                    else {
                        $log->info('Paiement echec.');
                    }
                    exit();
               }
                else if (strcmp ($res, "INVALID") == 0) {
                    // Transaction invalide
                    $log->info('Transaction invalide.');
                }
            }
        }

        return new Response();
    }
}
