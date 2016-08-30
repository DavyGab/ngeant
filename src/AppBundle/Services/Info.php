<?php

namespace AppBundle\Services;

class Info {

    protected $produitNom = array(
        '1' => 'Grand Nounours',
        '2' => 'Nounours Géant'
    );

    protected $produitPrix = array(
        '1' => 0.3,
        '2' => 120
    );

    protected $status = array(
        '0' => 'Intéréssé',
        '1' => 'Précommande payée',
        '2' => 'Commande annulé',
        '3' => 'Commande payée',
        '4' => 'Commande envoyée',
        '5' => 'Commande reçue et terminée'
    );

    protected $codePostal = array(
        "75001" => 1,
        "75002" => 1,
        "75003" => 1,
        "75004" => 1,
        "75005" => 1,
        "75006" => 1,
        "75007" => 1,
        "75008" => 1,
        "75009" => 1,
        "75010" => 1,
        "75011" => 1,
        "75012" => 1,
        "75013" => 1,
        "75014" => 1,
        "75015" => 1,
        "75016" => 1,
        "75017" => 1,
        "75018" => 1,
        "75019" => 1,
        "75020" => 1,
        "75116" => 1,
        "92000" => 2,
        "92100" => 2,
        "92110" => 2,
        "92120" => 2,
        "92130" => 2,
        "92150" => 2,
        "92170" => 2,
        "92200" => 2,
        "92210" => 2,
        "92240" => 2,
        "92300" => 2,
        "92800" => 2,
        "93100" => 2,
        "93170" => 2,
        "93200" => 2,
        "93210" => 2,
        "93260" => 2,
        "93300" => 2,
        "93310" => 2,
        "93400" => 2,
        "93500" => 2,
        "94100" => 2,
        "94120" => 2,
        "94130" => 2,
        "94200" => 2,
        "94210" => 2,
        "94220" => 2,
        "94250" => 2,
        "94270" => 2,
        "94300" => 2,
        "94340" => 2
    );

    protected $fraisDeLivraison = array(
        '1' => 0,
        '2' => 10
    );

    public function getNomProduit($id) {
        return $this->produitNom[$id];
    }

    public function getPrixProduit($id) {
        return $this->produitPrix[$id];
    }

    public function getFraisDeLivraison($cp) {
        return isset($this->codePostal[$cp]) ? $this->fraisDeLivraison[$this->codePostal[$cp]] : false;
    }

    public function getStatus() {
        return $this->status;
    }
} 