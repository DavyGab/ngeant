<?php

namespace ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PaiementController extends Controller
{
    public function IpnNotificationAction(Request $request)
    {
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
        if (!$fp) {
        } else {
            fputs ($fp, $header . $req);
            while (!feof($fp)) {
                $res = fgets ($fp, 1024);
                // vérifie que payment_status a la valeur Completed
                if (strcmp ($res, "VERIFIED") == 0) {
                    //Vérifie que le paiement est OK
                    if ( $payment_status == "Completed") {
                        //Vérifie que nous avons vien recu l'argent
                       if ( $email_account == $receiver_email) {
                            //On récupère la commande correspondant a l'email et à l'id de commande
                            $em = $this->getDoctrine()->getManager();
                            $commande = $em->getRepository('ShopBundle:Commande')->findOneBy(
                                array(
                                    'id'  =>  $crypt->decrypt($custom['id_commande']),
                                    'email' => $custom['id_commande']
                                )
                            );
                            $produit = $commande->getProduit();
                            //On enregistre tout en BDD
                            $commande->setPaypalInfo($_POST);

                            // Si la commande a déja été payé...
                            if (!in_array($commande->getStatus(), array(0, 2)) {
                                //Commande déja payé
                            } else {
                                if ($payment_amount != $produit['prix']) {
                                    // Le prix est différent de celui enregistré dans la commande.
                                } else {
                                    // OK !
                                }
                            }
                        }
                    }
                    else {
                            // Statut de paiement: Echec
                    }
                    exit();
               }
                else if (strcmp ($res, "INVALID") == 0) {
                    // Transaction invalide
                }
            }
        }

        return;
    }
}
