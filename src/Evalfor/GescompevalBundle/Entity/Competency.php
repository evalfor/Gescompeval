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
 * Competency
 *
 * @ORM\Table(name="competencies")
 * @ORM\Entity(repositoryClass="Evalfor\GescompevalBundle\Entity\CompetencyRepository")
 * @UniqueEntity(fields={"code"}, message="Your code has already been registered")
 */
class Competency extends Skill
{
    /**
     * var CompetenceType
     *
     * @ORM\ManyToOne(targetEntity="CompetenceType")
     * @ORM\JoinColumn(name="competencetypeid", referencedColumnName="id")
     */
    public $competencetype;

    /**
     * Added by Daniel Cabeza
     * START
     */
    
    /**
     * var Institution
     *
     * @ORM\ManyToOne(targetEntity="Institution")
     * @ORM\JoinColumn(name="institutionid", referencedColumnName="id")
     */
    public $institution;
    
    /**
     * Added by Daniel Cabeza
     * END
     */
    
    /**
     * Construct
     *
     */
    public function __construct() {
    	//$this->institution = new Institution();
    }
    
 }