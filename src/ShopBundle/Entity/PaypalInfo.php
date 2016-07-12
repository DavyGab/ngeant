<?php

namespace ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaypalInfo
 *
 * @ORM\Table(name="bigdoudou_paypal_info")
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\PaypalInfoRepository")
 */
class PaypalInfo
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
     * @var array
     *
     * @ORM\Column(name="info", type="json_array")
     */
    private $info;


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
     * Set info
     *
     * @param array $info
     * @return PaypalInfo
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return array 
     */
    public function getInfo()
    {
        return $this->info;
    }
}
