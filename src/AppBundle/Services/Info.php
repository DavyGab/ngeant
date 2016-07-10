<?php

namespace AppBundle\Services;

class Info {

    protected $produitNom = array(
        '1' => 'Grand Nounours',
        '2' => 'Nounours Géant'
    );

    protected $produitPrix = array(
        '1' => 60,
        '2' => 120
    );

    protected $fraisDeLivraison = array(
        '2' => 2
    );

    protected $status = array(
        '0' => 'Intéréssé',
        '1' => 'Précommande payée',
        '2' => 'Commande annulé',
        '3' => 'Commande payée',
        '4' => 'Commande envoyée',
        '5' => 'Commande reçue et terminée'
    );

    public function getNomProduit($id) {
        return $this->produitNom[$id];
    }

    public function getPrixProduit($id) {
        return $this->produitPrix[$id];
    }
} 