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
 * SkillRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom repository methods below.
 */

/**
 * Added by Daniel Cabeza
 * START
 */
//class SkillRepository extends EntityRepository
class SkillRepository extends GescompevalRepository
/**
 * Added by Daniel Cabeza
 * START
 */
{
	/*
	 * Return all skills whose types are different that the received one
	 */
	/*public function findByDifferentType($type)
	{
		return $this->getEntityManager()->createQuery(
        	'SELECT s FROM EvalforGescompevalBundle:Skill s WHERE s.type <> :type'
        )->setParameter('type', $type)
		->getResult();
	}*/

}