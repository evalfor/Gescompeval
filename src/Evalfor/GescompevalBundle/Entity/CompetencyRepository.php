<?php
/**
 * @package    EvalforGescompevalBundle
 * @copyright  2010 onwards EVALfor Research Group {@link http://evalfor.net/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Juan Antonio Caballero Hernández <juanantonio.caballero@uca.es >
 * @author     Daniel Cabeza Sánchez <daniel.cabeza@uca.es>
 */
namespace Evalfor\GescompevalBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CompetenctyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom repository methods below.
 */
class CompetencyRepository extends SkillRepository
{	
/*public function findCompetencies()
	{
		/*$q = Doctrine_Query::create()
		->from('Report sr')
		->innerJoin('sr.Members m');
		return $q->execute();


		return $this->getEntityManager()
        ->createQueryBuilder()
        ->select('c')
        ->from('EvalforGescompevalBundle:Competency', 'c')
        ->innerJoin('c.institution i', 'Institution')
        ->where('i.id = c.institution.id')
        ->getQuery()
        ->getResult();
	}
*/
	
	/**
	 * Added by Daniel Cabeza
	 * START
	 */
	
	/**
	 * Returns all entities that contain similar values to $value ordered by $fieldOrder
	 * @param string $value
	 * @param string $fieldOrder
	 * @param integer $page
	 * @return array
	 */
	public function findAllSimilarCompetences($value = null, $fieldOrder = null, $page = 1){	
		$orderby = '';
		
		if(!empty($fieldOrder)){
			if($fieldOrder == 'type'){
				$orderby = " ORDER BY p." . $fieldOrder . " ASC";
			}
			elseif($fieldOrder == 'name'){
				$orderby = " ORDER BY i." . $fieldOrder . " ASC";
			}
			else{
				$orderby = " ORDER BY o." . $fieldOrder . " ASC";
			}
		}
		
		//array to return
		$result = array();
		$resultQuery = array();
		$resultQueryPage = array();
		
		if($page > 0){
			if(!empty($value)){
				$dql = "SELECT o.id, o.code, o.shortdescription, o.longdescription, p.type, i.name 
	        			 FROM EvalforGescompevalBundle:Competency o
	        			 LEFT JOIN EvalforGescompevalBundle:CompetenceType p
	        		     WITH o.competencetype = p.id
						 LEFT JOIN EvalforGescompevalBundle:Institution i
	        		     WITH o.institution = i.id
						 WHERE  
							(LOWER(o.code) LIKE LOWER(:param) OR LOWER(o.shortdescription) LIKE  LOWER(:param) 
						     OR LOWER(o.longdescription) LIKE LOWER(:param) OR LOWER(p.type) LIKE LOWER(:param) OR LOWER(i.name) LIKE LOWER(:param)) " . $orderby;
				
				$resultQuery = $this->getEntityManager()
					->createQuery($dql)
					->setParameter('param', '%'.$value.'%')
					->getResult();
				
				$resultQueryPage = $this->getEntityManager()
					->createQuery($dql)
					->setParameter('param', '%'.$value.'%')
					->setFirstResult($this->maxPerPage * ($page - 1))
					->setMaxResults($this->maxPerPage)
					->getResult();
			}
			else{
				$dql = "SELECT o.id, o.code, o.shortdescription, o.longdescription, p.type, i.name
	        			 FROM EvalforGescompevalBundle:Competency o 
	        			 LEFT JOIN EvalforGescompevalBundle:CompetenceType p
	        		     WITH o.competencetype = p.id
	        			 LEFT JOIN EvalforGescompevalBundle:Institution i
	        		     WITH o.institution = i.id        		
						 " . $orderby;
				
				$resultQuery =  $this->getEntityManager()
	        		->createQuery($dql)
					->getResult();
				
				$resultQueryPage =  $this->getEntityManager()
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
	/**
	 * Added by Daniel Cabeza
	 * END
	 */
}
