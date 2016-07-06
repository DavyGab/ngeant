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

    public function getProduitNom($id) {
        return $this->produitNom[$id];
    }
} 