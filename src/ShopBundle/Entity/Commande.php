<?php

namespace ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Commande
 *
 * @ORM\Table(name="commande")
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\CommandeRepository")
 */
class Commande
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="e_nom", type="string", length=255, nullable=true)
     */
    private $eNom;

    /**
     * @var string
     *
     * @ORM\Column(name="e_prenom", type="string", length=255, nullable=true)
     */
    private $ePrenom;

    /**
     * @var string
     *
     * @ORM\Column(name="d_nom", type="string", length=255, nullable=true)
     */
    private $dNom;

    /**
     * @var string
     *
     * @ORM\Column(name="d_prenom", type="string", length=255, nullable=true)
     */
    private $dPrenom;

    /**
     * @var string
     *
     * @ORM\Column(name="entreprise", type="string", length=255, nullable=true)
     */
    private $entreprise;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=255, nullable=true)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="code_postal", type="string", length=255, nullable=true)
     */
    private $codePostal;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=255, nullable=true)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="complement_adresse", type="string", length=255, nullable=true)
     */
    private $complementAdresse;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_livraison", type="datetime", nullable=true)
     */
    private $dateLivraison;

    /**
     * @var string
     *
     * @ORM\Column(name="horaire_livraison", type="string", length=255, nullable=true)
     */
    private $horaireLivraison;

    /**
     * @var int
     *
     * @ORM\Column(name="frais_livraison", type="integer", nullable=true)
     */
    private $fraisLivraison;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * @var array
     *
     * @ORM\Column(name="produit", type="json_array", nullable=true)
     */
    private $produit;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Commande
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set eNom
     *
     * @param string $eNom
     * @return Commande
     */
    public function setENom($eNom)
    {
        $this->eNom = $eNom;

        return $this;
    }

    /**
     * Get eNom
     *
     * @return string 
     */
    public function getENom()
    {
        return $this->eNom;
    }

    /**
     * Set ePrenom
     *
     * @param string $ePrenom
     * @return Commande
     */
    public function setEPrenom($ePrenom)
    {
        $this->ePrenom = $ePrenom;

        return $this;
    }

    /**
     * Get ePrenom
     *
     * @return string 
     */
    public function getEPrenom()
    {
        return $this->ePrenom;
    }

    /**
     * Set dNom
     *
     * @param string $dNom
     * @return Commande
     */
    public function setDNom($dNom)
    {
        $this->dNom = $dNom;

        return $this;
    }

    /**
     * Get dNom
     *
     * @return string 
     */
    public function getDNom()
    {
        return $this->dNom;
    }

    /**
     * Set dPrenom
     *
     * @param string $dPrenom
     * @return Commande
     */
    public function setDPrenom($dPrenom)
    {
        $this->dPrenom = $dPrenom;

        return $this;
    }

    /**
     * Get dPrenom
     *
     * @return string 
     */
    public function getDPrenom()
    {
        return $this->dPrenom;
    }

    /**
     * Set entreprise
     *
     * @param string $entreprise
     * @return Commande
     */
    public function setEntreprise($entreprise)
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    /**
     * Get entreprise
     *
     * @return string 
     */
    public function getEntreprise()
    {
        return $this->entreprise;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     * @return Commande
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string 
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set codePostal
     *
     * @param string $codePostal
     * @return Commande
     */
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * Get codePostal
     *
     * @return string 
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * Set ville
     *
     * @param string $ville
     * @return Commande
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string 
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set complementAdresse
     *
     * @param string $complementAdresse
     * @return Commande
     */
    public function setComplementAdresse($complementAdresse)
    {
        $this->complementAdresse = $complementAdresse;

        return $this;
    }

    /**
     * Get complementAdresse
     *
     * @return string 
     */
    public function getComplementAdresse()
    {
        return $this->complementAdresse;
    }

    /**
     * Set dateLivraison
     *
     * @param \DateTime $dateLivraison
     * @return Commande
     */
    public function setDateLivraison($dateLivraison)
    {
        $this->dateLivraison = $dateLivraison;

        return $this;
    }

    /**
     * Get dateLivraison
     *
     * @return \DateTime 
     */
    public function getDateLivraison()
    {
        return $this->dateLivraison;
    }

    /**
     * Set horaireLivraison
     *
     * @param string $horaireLivraison
     * @return Commande
     */
    public function setHoraireLivraison($horaireLivraison)
    {
        $this->horaireLivraison = $horaireLivraison;

        return $this;
    }

    /**
     * Get horaireLivraison
     *
     * @return string 
     */
    public function getHoraireLivraison()
    {
        return $this->horaireLivraison;
    }

    /**
     * Set fraisLivraison
     *
     * @param integer $fraisLivraison
     * @return Commande
     */
    public function setFraisLivraison($fraisLivraison)
    {
        $this->fraisLivraison = $fraisLivraison;

        return $this;
    }

    /**
     * Get fraisLivraison
     *
     * @return integer 
     */
    public function getFraisLivraison()
    {
        return $this->fraisLivraison;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Commande
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Commande
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getProduit()
    {
        return $this->produit;
    }

    /**
     * @param mixed $produit
     */
    public function setProduit($produit)
    {
        $this->produit = $produit;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
