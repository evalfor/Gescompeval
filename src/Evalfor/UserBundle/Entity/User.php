<?php

namespace Evalfor\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    public function __construct()
    {
        parent::__construct();
        // tu propia lógica
    }

    /**
     * Set id
     *
     * @param int $id
     * @return User
     */
    public function setId($id)
    {
    	$this->id = $id;

    	return $this;
    }

    /**
     * Get id
     *
     * @param int $id
     * @return int
     */
    public function getId()
    {
    	return $this->id;
    }

    /**
     * Agrega un rol al usuario.
     * @throws Exception
     * @param int $rol
     */
    public function addRole($rol)
    {
    	if($rol == 1) {
    		array_push($this->roles, 'ROLE_ADMIN');
    	}
    	else if($rol == 2) {
    		array_push($this->roles, 'ROLE_USER');
    	}
    }

}