<?php
/**
 * @package    EvalforGescompevalBundle
 * @copyright  2010 onwards EVALfor Research Group {@link http://evalfor.net/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Juan Antonio Caballero Hernández <juanantonio.caballero@uca.es >
 * @author     Daniel Cabeza Sánchez <daniel.cabeza@uca.es>
 */
namespace Evalfor\GescompevalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Skill
 *
 * @ORM\MappedSuperclass(repositoryClass="Evalfor\GescompevalBundle\Entity\SkillRepository")
 * @UniqueEntity(fields={"code"}, message="Your code has already been registered")
 */
class Skill
{
	// Constants of values for type attribute
	const COMPETENCY = "competency";
	const OUTCOME = "outcome";

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
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     * @Assert\Length(max=50, min=1)
     */
    public $code;

    /**
     * @var string
     *
     * @ORM\Column(name="shortdescription", type="string", length=255)
     * @Assert\Length(max=255, min=1)
     */
    public $shortdescription;

    /**
     * @var string
     *
     * @ORM\Column(name="longdescription", type="text", nullable=true)
     */
    public $longdescription;

    /**
     * var Institution
     *
     * @ORM\ManyToOne(targetEntity="Institution")
     * @ORM\JoinColumn(name="institutionid", referencedColumnName="id")
     */
    public $institution;


    /**
     * Construct
     *
     */
    public function __construct() {}

    /*Code added by Daniel Cabeza
     * START
     */
    /**
     * 
     * @return multitype:string required properties of the entity
     */
    public static function getRequiredProperties(){
    	return array('code', 'shortdescription');
    }
    /*Code added by Daniel Cabeza
     * END
     */

    ///////////////////////////////////////
    // OLD CONTENT OF A PREVIOUS VERSION //
    ///////////////////////////////////////

   /*
    * @var string
    *
    * @ORM\Column(name="type", type="string", length=20)
    * @Assert\Length(max=20, min=1)
    */
    //public $type;

    /*
     * @ORM\ManyToMany(targetEntity="Elements", mappedBy="myElements")
    */
    //public $elementsWithMe;

    /*
     * @ORM\ManyToMany(targetEntity="Elements", inversedBy="elementsWithMe")
    * @ORM\JoinTable(name="connected_elements",
    		* 		joinColumns={@ORM\JoinColumn(name="element_id", referencedColumnName="id")},
    		*      inverseJoinColumns={@ORM\JoinColumn(name="connected_element_id", referencedColumnName="id")}
    		*      )
    */
    //public $myElements;

    /*
     * addMyElement
     * Connect the received element
     *
     * @param Elements $element
     * @return Elements
     */
    /*public function addMyElement(Elements $element)
    {
    	$this->myElements[] = $element;

    	return $this;
    }*/

    /*
     * setMyElements
     * Set the elements connected
     *
     * @param ArrayCollection $array_elements
     * @return Elements
     */
    /*public function setMyElements(\Doctrine\Common\Collections\ArrayCollection $array_elements){

    	// Get the ids of the old connected elements
    	$old_elements_connected = $this->myElements;

    	// Set the new elements
    	$this->myElements = $array_elements;

    	// Create the bidireccional connection with the added elements
    	$new_ids = null;
    	if(count($array_elements) > 0){
	    	foreach ($array_elements as $new_element){
	    		// Check if the bidireccional connection has been already done
	    		if(!$new_element->isMyElement($this)){
	    			$new_element->addMyElement($this);
	    		}
	    		// Get the ids of the new elements
	    		$new_ids[] = $new_element->id;
	    	}
    	}

    	// Remove the bidireccional connection if it is necessary
    	if(count($old_elements_connected) > 0){
			foreach($old_elements_connected as $old_element){
				// If the element was before but it isn't connect now remove it
				if(!in_array($old_element->id, $new_ids)){
					$old_element->removeMyElement($this);
				}
			}
    	}

    	return $this;
    }*/

    /*
     * isMyElement
     * True if the element received is connected with the element which calls the method
     *
     * @param $element Elements
     * @return boolean
     */
    /*public function isMyElement(Elements $element){

    	$array_connected_elements = $this->myElements;
    	$flag = false;

    	// If the element has one element connected at least
    	if(count($array_connected_elements) > 0){
	    	foreach($array_connected_elements as $connected_element){
	    		if($connected_element->id == $element->id){
	    			$flag = true;
	    			break;
	    		}
	    	}
    	}

    	return $flag;
    }*/

    /*
     * removeMyElement
     * Remove the received element from the elements connected
     *
     * @param $element Elements
     * @return Elements
     */
    /*public function removeMyElement(Elements $element){

    	$myElements = $this->myElements;
    	$myNewElements = null;

    	if(count($myElements) > 0){
    		// We add all the elements except the element to remove
	    	foreach($myElements as $myElement){
	    		if ($myElement->id != $element->id){
	    			$myNewElements[] = $myElement;
	    		}
	    	}
    	}

    	$this->myElements = $myNewElements;

    	return $this;
    }*/
}