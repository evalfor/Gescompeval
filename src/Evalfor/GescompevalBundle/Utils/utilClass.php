<?php
/**
 * @package    EvalforGescompevalBundle
 * @copyright  2010 onwards EVALfor Research Group {@link http://evalfor.net/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Daniel Cabeza Sánchez <daniel.cabeza@uca.es>
 */
namespace Evalfor\GescompevalBundle\Utils;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Length;
use Doctrine\ORM\EntityManager;

class utilClass
{
	/**
	 * Entity Manager
	 */
	private $em;
	
	public function __construct(EntityManager $entityManager)
	{
		$this->em = $entityManager;
	}
	/**
	 * Sorts a multidimensional array by a key
	 * @param array $array
	 * @param string $key
	 * @return number
	 */
	public static function sortArray($array = array(), $key = null){		
		usort($array, function($a, $b) use ($key)
		{
			$value = strcmp(strtolower($a[$key]), strtolower($b[$key]));
			return $value;
		});
		return $array;
	}
	
	/**
	 * Gets object properties
	 * @param $object
	 * @return multitype:
	 */
	public static function getObjectProperties($object){
		$result = array();
		if(is_object($object)){
			$objectVars = get_object_vars($object);
			$result = array_keys($objectVars);
		}
		
		return $result;
	}
	
	public function exist($object, $field, $entityname){
		$em = $this->em;
		$list = $em->getRepository($entityname)->findAll();
	
		$objectName = \Evalfor\GescompevalBundle\Utils\inflector::homogenize($object->$field);
		foreach($list as $item){
			$itemName = \Evalfor\GescompevalBundle\Utils\inflector::homogenize($item->$field);
			if($itemName === $objectName){
				return true;
			}
		}
		return false;
	}
	
	public function homogenize($entityname, $field){
		$result = array();
		$em = $this->em;
		$objects = $em->getRepository($entityname)->findAll();
		foreach($objects as $object){
			$field_homogenized = \Evalfor\GescompevalBundle\Utils\inflector::homogenize($object->$field);
			$result[$field_homogenized] = $object;
		}
		return $result;
	}
	
}