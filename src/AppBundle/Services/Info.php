<?php

namespace AppBundle\Services;

class Info {

    protected $produitNom = array(
        '1' => 'Grand Nounours',
        '2' => 'Nounours Géant'
    );

    protected $produitPrix = array(
        '1' => 0.01,
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

    protected $codePostalParis = array(
        '75001',
        '75002',
        '75003',
        '75004',
        '75005',
        '75006',
        '75007',
        '75008',
        '75009',
        '75010',
        '75011',
        '75012',
        '75013',
        '75014',
        '75015',
        '75016',
        '75017',
        '75018',
        '75019',
        '75020'
    );

    public function getNomProduit($id) {
        return $this->produitNom[$id];
    }

    public function getPrixProduit($id) {
        return $this->produitPrix[$id];
    }

    public function isLivrable($cp) {
        return in_array($cp, $this->codePostalParis);
    }
} 