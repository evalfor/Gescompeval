<?php
/**
 * @package    EvalforGescompevalBundle
 * @copyright  2010 onwards EVALfor Research Group {@link http://evalfor.net/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Daniel Cabeza Sánchez <daniel.cabeza@uca.es>
 */
namespace Evalfor\GescompevalBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * GescompevalRepository
 *
 * This class was generated by Daniel Cabeza Sánchez (daniel.cabeza@uca.es)
 */
class GescompevalRepository extends EntityRepository
{	
	/**
	 * Max number of rows returned in a query
	 * @var integer
	 */
	public $maxPerPage = 100;
	public function getMaxPerPage(){return $this->maxPerPage;}
	
	/**
	 * Returns all entities that contain similar values to $value in $fields, ordered by $fieldOrder
	 * @param string $entityname
	 * @param array $fields
	 * @param string $value
	 * @param string $fieldOrder
	 * @param integer $page
	 * @return array
	 */
	public function findAllSimilar($entityname, $fields = array(), $value = null, $fieldOrder = null, $page = 1){
		if(empty($entityname)){
			return false;
		}

		//array to return
		$result = array();
		$resultQuery = array();
		$resultQueryPage = array();
		
		if($page > 0){
			$orderby = '';
			if(!empty($fieldOrder)){
				$orderby = " ORDER BY o." . $fieldOrder . " ASC";	
			}
			
			$where = '';
			$params = array();
			$i = 1;
			if( !empty($fields) && !empty($value)){
				$conditions_pieces = array();
				foreach($fields as $field){
					$conditions_pieces[] = " LOWER(o." . $field . ") LIKE LOWER(:param".$i.") ";
					$params['param'.$i] = '%'.$value.'%';
					++$i;
				}
				
				$conditions = implode('OR', $conditions_pieces);
				$where = ' WHERE ' . $conditions;
			}
			
			$dql = "SELECT o FROM ". $entityname . " o " . $where . $orderby;
			if(!empty($params)){
				$resultQuery = $this->getEntityManager()
				->createQuery($dql)
				->setParameters($params)
				->getResult();
				
				$resultQueryPage = $this->getEntityManager()
				->createQuery($dql)
				->setParameters($params)
				->setFirstResult($this->maxPerPage * ($page - 1))
				->setMaxResults($this->maxPerPage)
				->getResult();
			}
			else{
				$resultQuery = $this->getEntityManager()
				->createQuery($dql)
				->getResult();
				
				$resultQueryPage = $this->getEntityManager()
				->createQuery($dql)
				->setFirstResult($this->maxPerPage * ($page - 1))
				->setMaxResults($this->maxPerPage)
				->getResult();
			}
			
			$result['result'] = $resultQuery;
			$result['resultPage'] = $resultQueryPage;
			$result['count'] = count($resultQuery);
		}
		
		return $result;
	}
	
}
