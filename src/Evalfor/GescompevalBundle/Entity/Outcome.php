<?php

namespace Evalfor\GescompevalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Outcome
 *
 * @ORM\Table(name="outcomes")
 * @ORM\Entity(repositoryClass="Evalfor\GescompevalBundle\Entity\OutcomeRepository")
 * @UniqueEntity(fields={"code"}, message="Your code has already been registered")
 */
class Outcome extends Skill
{
    /**
     * Construct
     *
     */
    public function __construct() {
    	//$this->institution = new Institution();
    }
}