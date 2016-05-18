<?php

namespace Evalfor\GescompevalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Institution
 *
 * @ORM\Table(name="institutions")
 * @ORM\Entity(repositoryClass="Evalfor\GescompevalBundle\Entity\InstitutionRepository")
 * @UniqueEntity(fields={"name"}, message="Your Institution name has already been registered")
 */
class Institution
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * @Assert\Length(max=255, min=1)
     */
    public $name;

    /**
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
     * getName
     * @return string
     */
    public function getName(){
    	return $this->name;
    }

    /**
     * getDescription
     * @return string
     */
    public function getDescription(){
    	return $this->description;
    }
}

/*class InstitutionProxy extends Institution {
	public function __construct(Institution $institution) {
		unset($this->id, $this->name, $this->description);
		$this->institution = $institution;
	}
	public function __set($name, $value) {
		$this->institution->$name = $value;
	}

	public function __get($name) {
		return $this->institution->$name;
	}
}*/
