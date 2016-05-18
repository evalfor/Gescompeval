<?php

namespace Evalfor\GescompevalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CompetenceType
 *
 * @ORM\Table(name="competencetypes")
 * @ORM\Entity(repositoryClass="Evalfor\GescompevalBundle\Entity\CompetenceTypeRepository")
 * @UniqueEntity(fields={"type"}, message="Your competence type has already been registered")
 */
class CompetenceType
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, unique=true)
     * @Assert\Length(max=255, min=1)
     */
    public $type;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    public $description;


    /**
     * Construct
     *
     */
    public function __construct() {}

    /**
     * getId
     * @return integer
     */
    public function getId(){
    	return $this->id;
    }

    /**
     * getType
     * @return string
     */
	public function getType(){
    	return $this->type;
    }

    /**
     * getDescription
     * @return string
     */
    public function getDescription(){
    	return $this->description;
    }
}
