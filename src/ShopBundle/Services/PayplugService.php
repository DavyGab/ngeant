<?php

namespace ShopBundle\Services;

use ShopBundle\Entity\Commande;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PayplugService {

    private $sandbox;
    private $key_sandbox;
    private $key_live;

    private $crypt;
    private $info;

    private $router;

    public function __construct($sandbox, $key_sandbox, $key_live, $info, $crypt, $router) {
        $this->sandbox = $sandbox;
        $this->key_sandbox = $key_sandbox;
        $this->key_live = $key_live;

        $this->info = $info;
        $this->crypt = $crypt;

        $this->router = $router;

        if ($this->sandbox) {
            \Payplug\Payplug::setSecretKey($this->key_sandbox);
        } else {
            \Payplug\Payplug::setSecretKey($this->key_live);
        }
    }

    public function createPayment($commande) {
        $payment = \Payplug\Payment::create($this->convertCommandeToArray($commande));

        $commande->setPaiementId($payment->id);
        $em->persist($commande);

        return array(
            'url' => $payment->hosted_payment->payment_url,
            'info' => $payment
            );
    }

    private function convertCommandeToArray($commande) {

        $produit = $commande->getProduit();
        $fraisDePort = $this->info->getFraisDeLivraison($commande->getCodePostal());

        return array(
            'amount'            => ($produit['prix'] + $fraisDePort) * 100, // Prix en centimes
            'currency'          => 'EUR',
            'customer'          => array(
                'email'             => $commande->getEmail()
            ),
            'hosted_payment'    => array(
                'return_url'        => $this->router->generate('shop_commande_validee', array('id_commande' => urlencode($this->crypt->crypt($commande->getId()))), UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url'        => $this->router->generate('shop_commande_annulee', array('id_commande' => urlencode($this->crypt->crypt($commande->getId()))), UrlGeneratorInterface::ABSOLUTE_URL)
            ),
            'notification_url'  => $this->router->generate('shop_commande_paylug', array('id_commande' => urlencode($this->crypt->crypt($commande->getId()))), UrlGeneratorInterface::ABSOLUTE_URL),
            'metadata'          => array(
                'commande_id'       => $commande->getId()
            )
        );
    }
    
    public function retrievePaiement(Commande $commande) {
        $payment_id = $commande->getPaiementId();
        
        return \Payplug\Payment::retrieve($payment_id);
    }
} 