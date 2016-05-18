<?php
/**
 * @package    EvalforGescompevalBundle
 * @copyright  2010 onwards EVALfor Research Group {@link http://evalfor.net/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Juan Antonio Caballero Hernández <juanantonio.caballero@uca.es >
 * @author     Daniel Cabeza Sánchez <daniel.cabeza@uca.es>
 */
namespace Evalfor\GescompevalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Evalfor\GescompevalBundle\Entity\Competency;
use Evalfor\GescompevalBundle\Form\CompetencyType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Length;
use Evalfor\GescompevalBundle\Form\UploadType;
use Evalfor\GescompevalBundle\Entity\CompetenceType;
use Evalfor\GescompevalBundle\Utils\csvClass;
use Evalfor\GescompevalBundle\Utils\utilClass;
use Evalfor\GescompevalBundle\Form\UploadCompetencyType;
use Evalfor\GescompevalBundle\Utils\inflector;


class CompetencyController extends SkillController
{
	/*Code added by Daniel Cabeza
	 * START
	 */
	
	/**
	 * List all Competencies
	 * @param Request $request
	 */
	public function listAction(Request $request, $page = 1)
	{
		$order = $request->query->get('order');
		$search = trim($request->query->get('search'));
		$entityname = 'EvalforGescompevalBundle:Competency';
		
		// Create the view calling a a parent method
        return $this->showAll($entityname, $order, $search, $page);
	}
	
	/**
	 * Show details of a specific competency
	 * @param int $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function readAction($id)
	{
		$entityname = 'EvalforGescompevalBundle:Competency';
		// Obtain all elements of the suitable type from DB
		$em = $this->getDoctrine()->getManager();
		$skill = $em->getRepository($entityname)->find($id);
		
		if (get_class($skill) == 'Evalfor\GescompevalBundle\Entity\Competency'){
			$form = $this->createForm(new CompetencyType(), $skill);
			return $this->render($entityname.':read.html.twig', array('form' => $form->createView(), 'skills_all' => $skill));
		}
		
		$tr = $this->get('translator');
		$this->get('session')->getFlashBag()->add(
				'error', $tr->trans('skill.flash.error', array(), 'EvalforGescompevalBundle'));
		return $this->render(
            'EvalforGescompevalBundle::layout.html.twig');
	}
	
	/**
	 * Upload a CSV file with a set of competencies
	 */
	public function uploadAction(){
		$entityname = 'EvalforGescompevalBundle:Competency';
		$routeRedirect = 'EGB_list_competency';
		$form = $this->createForm(new UploadCompetencyType());
		return $this->upload($entityname, $form, $routeRedirect);
	}
	
	public function deleteidAction($id, Request $request){
		$page = $request->query->get('page');
		$entityname = 'EvalforGescompevalBundle:Competency';
		$routeRedirect = 'EGB_list_competency';
		$order = $request->query->get('order');
		$search = $request->query->get('search');
		return $this->deleteid($id, $entityname, $routeRedirect, $order, $search, $page);
	}
	
	public function updateidAction($id){
		$entityname = 'EvalforGescompevalBundle:Competency';
		$routeRedirect = 'EGB_list_competency';
		
		// Create object, form and routes
		$em = $this->getDoctrine()->getManager();
		$competency = $em->getRepository($entityname)->find($id);
		$competenceType = new CompetencyType();			
		if(empty($competency)){
			$tr = $this->get('translator');
			$this->get('session')->getFlashBag()->add('error', $tr->trans('skill.flash.error', array(), 'EvalforGescompevalBundle')); 
			return $this->redirect($this->generateURL($routeRedirect));
		}
		
		$form = $this->createForm($competenceType, $competency);
		
		// Create the view calling a a parent method
		return $this->update($competency, $form, $entityname, $routeRedirect);
	}
	
	/**
	 * Save $competencies imported in db
	 * @param array $competencies
	 * @return integer total count of competencies created
	 */
	public function import($competencies){
		$result = array();
		
		if(empty($competencies)){
			return 0;
		}
	
		$i = 0;
		foreach($competencies as $competency){
			$code = $competency['code'];
			$shortdescription = $competency['shortdescription'];
			$longdescription = $competency['longdescription'];
			$competencetype = $competency['competencetype'];
			$institution = $competency['institution'];
				
			$object = new Competency();
			$object->code = $code;
			$object->shortdescription = $shortdescription;
			$object->longdescription = $longdescription;
				
			$em = $this->getDoctrine()->getManager();
			
			$util = new utilClass($em);
			$objectsOfInstitution = $util->homogenize('EvalforGescompevalBundle:Institution', 'name');
			$objectsOfCompetenceType = $util->homogenize('EvalforGescompevalBundle:CompetenceType', 'type');
			$type = inflector::homogenize($competencetype);
			$institutionName = inflector::homogenize($institution);
			
			//$CTobject = $em->getRepository('EvalforGescompevalBundle:CompetenceType')->findOneByType($competencetype);
			if(!empty($objectsOfCompetenceType[$type])){
				$object->competencetype = $objectsOfCompetenceType[$type];
			}
			//$Iobject = $em->getRepository('EvalforGescompevalBundle:Institution')->findOneByName($institution);
			if(!empty($objectsOfInstitution[$institutionName])){
				$object->institution = $objectsOfInstitution[$institutionName];
			}
			
			$em->persist($object);
			$em->flush();
			++$i;
		}
		
		return $i;
	}
	
	
	/**
	 * Checks properties of $competencies
	 * @param array $competencies
	 * @return boolean
	 */
	public function checkProperties(csvClass $csv){
		$csvRows = $csv->getRows();
		$csvColumnHeaders = $csv->getColumnHeaders();
		
		$validHeaderFlag = true;
		$errors = array();
		
		/*Validations*/
		$competencyObject = new Competency();
		$competencyProperties = utilClass::getObjectProperties($competencyObject);
		$requiredCompetencyProperties = Competency::getRequiredProperties();
		 
		//Validate whether header values match the properties of entity Competency
		if(!empty($csvColumnHeaders)){
			foreach($csvColumnHeaders as $header){
				if(!in_array($header, $competencyProperties)){
					$validHeaderFlag = false;
				}
			}
		
			//Validate whether header values contain the required properties of the Competency entity
			if($validHeaderFlag == true){
				foreach($requiredCompetencyProperties as $property){
					if(!in_array($property, $csvColumnHeaders)){
						array_push($errors, 'Invalid file header or the selected delimiter character does not correspond to the file');
						$validHeaderFlag = false;
					}
				}
			}
			else{
				array_push($errors, 'Invalid file header or the selected delimiter character does not correspond to the file');
			}
		}
		
		return $errors;
	}
	
	
	/*Code added by Daniel Cabeza
	 * END
	 */
	
	public function createAction()
	{
		// Create object, form and routes
		$competency = new Competency();
		$form = $this->createForm(new CompetencyType(), $competency);
		$entityname = 'EvalforGescompevalBundle:Competency';
		
		/*Code added by Daniel Cabeza
		 * START
		 */
		//$routeredirect = 'EGB_create_competency';
		$routeredirect = 'EGB_list_competency';
		/*Code added by Daniel Cabeza
		 * END
		 */
		
		// Create the view calling a a parent method
		return $this->create($competency, $form, $entityname, $routeredirect);
	}

	public function updateAction()
	{
		// Create object, form and routes
		$competency = new Competency();
		$form = $this->createForm(new CompetencyType(), $competency);
		$entityname = 'EvalforGescompevalBundle:Competency';
		$routeredirect = 'EGB_update_competency';
		
		// Create the view calling a a parent method
		return $this->update($competency, $form, $entityname, $routeredirect);
	}

	public function deleteAction()
	{
		// Create object, form and routes
		$competency = new Competency();
		$form = $this->createForm(new CompetencyType(), $competency);
		$entityname = 'EvalforGescompevalBundle:Competency';
		$routeredirect = 'EGB_delete_competency';

		// Use a parent method to update
		return $this->delete($competency, $form, $entityname, $routeredirect);
	}
	
}