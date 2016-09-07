<?php

namespace ShopBundle\Services;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaypalService {

    private $paypal_email;

    private $crypt;
    private $info;

    private $router;

    public function __construct($paypal_email, $info, $crypt, $router) {
        $this->paypal_email = $paypal_email;

        $this->info = $info;
        $this->crypt = $crypt;

        $this->router = $router;
    }

    public function createForm($commande) {

        $produit = $commande->getProduit();
        $crypt_id_commande = urldecode($this->crypt->crypt($commande->getId()));
        $custom = array(
            'id_commande' => $crypt_id_commande
        );
        return array(
            'cancel_return' => $this->router->generate('shop_commande_annulee', array('id_commande' => $crypt_id_commande), UrlGeneratorInterface::ABSOLUTE_URL),
            'notify_url' => $this->router->generate('shop_ipn_notification', array(), UrlGeneratorInterface::ABSOLUTE_URL),
            'return' => $this->router->generate('shop_commande_validee', array(), UrlGeneratorInterface::ABSOLUTE_URL),
            'item_name' => $produit['nom'],
            'amount' => $this->info->getPrixAvecPromo($produit['id']),
            'lc' => 'FR',
            'cmd' => '_xclick',
            'currency_code' => 'EUR',
            'business' => $this->paypal_email,
            'tax' => 0,
            'shipping' => $this->info->getFraisDeLivraison($commande->getCodePostal()),
            'no_note' => 1,
            'custom' => http_build_query($custom)
        );
    }
} 