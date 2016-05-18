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
use Evalfor\GescompevalBundle\Entity\Outcome;
use Evalfor\GescompevalBundle\Form\OutcomeType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Length;
use Evalfor\GescompevalBundle\Utils\csvClass;
use Evalfor\GescompevalBundle\Utils\utilClass;
use Evalfor\GescompevalBundle\Form\UploadOutcomeType;
use Evalfor\GescompevalBundle\Utils\inflector;

class OutcomeController extends SkillController
{
	/*Code added by Daniel Cabeza
	 * START
	 */
	public function listAction(Request $request, $page = 1)
	{		
		$order = $request->query->get('order');
		$search = trim($request->query->get('search'));
		$entityname = 'EvalforGescompevalBundle:Outcome';
		
		// Create the view calling a a parent method
        return $this->showAll($entityname, $order, $search, $page);
	}
	
	public function readAction($id)
	{
		$entityname = 'EvalforGescompevalBundle:Outcome';
		// Obtain all elements of the suitable type from DB
		$em = $this->getDoctrine()->getManager();
		$skill = $em->getRepository($entityname)->find($id);
	
		if (get_class($skill) == 'Evalfor\GescompevalBundle\Entity\Outcome'){
			$form = $this->createForm(new OutcomeType(), $skill);
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
		$entityname = 'EvalforGescompevalBundle:Outcome';
		$routeRedirect = 'EGB_list_outcome';
		$form = $this->createForm(new UploadOutcomeType());
		return $this->upload($entityname, $form, $routeRedirect);
	}
	
	public function deleteidAction($id, Request $request){
		$page = $request->query->get('page');
		$entityname = 'EvalforGescompevalBundle:Outcome';
		$routeRedirect = 'EGB_list_outcome';
		$order = $request->query->get('order');
		$search = $request->query->get('search');
		return $this->deleteid($id, $entityname, $routeRedirect, $order, $search, $page);
	}
	
	public function updateidAction($id){
		$entityname = 'EvalforGescompevalBundle:Outcome';
		$routeRedirect = 'EGB_list_outcome';
	
		// Create object, form and routes
		$em = $this->getDoctrine()->getManager();
		$outcome = $em->getRepository($entityname)->find($id);
		if(empty($outcome)){
			$tr = $this->get('translator');
			$this->get('session')->getFlashBag()->add('error', $tr->trans('skill.flash.error', array(), 'EvalforGescompevalBundle'));
			return $this->redirect($this->generateURL($routeRedirect));
		}
	
		$form = $this->createForm(new OutcomeType(), $outcome);
	
		// Create the view calling a a parent method
		return $this->update($outcome, $form, $entityname, $routeRedirect);
	}

	/**
	 * Save $competencies imported in db
	 * @param array $competencies
	 * @return total count of competencies created
	 */
	public function import($outcomes){
		$result = array();
	
		if(empty($outcomes)){
			return 0;
		}
	
		$i = 0;
		foreach($outcomes as $outcome){
			$code = $outcome['code'];
			$shortdescription = $outcome['shortdescription'];
			$longdescription = $outcome['longdescription'];
			$institution = $outcome['institution'];
	
			$object = new Outcome();
			$object->code = $code;
			$object->shortdescription = $shortdescription;
			$object->longdescription = $longdescription;
	
			$em = $this->getDoctrine()->getManager();
			$util = new utilClass($em);
			$objectsOfInstitution = $util->homogenize('EvalforGescompevalBundle:Institution', 'name');
			$institutionName = inflector::homogenize($institution);
			
			//$Iobject = $em->getRepository('EvalforGescompevalBundle:Institution')->findOneByName($institution);
			//$object->institution = $Iobject;
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
		$outcomeObject = new Outcome();
		$outcomeProperties = utilClass::getObjectProperties($outcomeObject);
		$requiredProperties = Outcome::getRequiredProperties();
			
		//Validate whether header values match the properties of entity Competency
		if(!empty($csvColumnHeaders)){
			foreach($csvColumnHeaders as $header){
				if(!in_array($header, $outcomeProperties)){
					$validHeaderFlag = false;
				}
			}
	
			//Validate whether header values contain the required properties of the Competency entity
			if($validHeaderFlag == true){
				foreach($requiredProperties as $property){
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
		$outcome = new Outcome();
		$form = $this->createForm(new OutcomeType(), $outcome);
		$entityname = 'EvalforGescompevalBundle:Outcome';
		
		/*Code added by Daniel Cabeza
		 * START
		 */
		//$routeredirect = 'EGB_create_outcome';
		$routeredirect = 'EGB_list_outcome';
		/*Code added by Daniel Cabeza
		 * END
		 */
		// Use a parent method to create
		return $this->create($outcome, $form, $entityname, $routeredirect);
	}

	public function updateAction()
	{
		// Create object, form, route of redirect and entity name
		$outcome = new Outcome();
		$form = $this->createForm(new OutcomeType(), $outcome);
		$entityname = 'EvalforGescompevalBundle:Outcome';
		$routeredirect = 'EGB_update_outcome';
		
		// Use a parent method to update
		return $this->update($outcome, $form, $entityname, $routeredirect);
	}

	public function deleteAction()
	{
		// Create object, form, route of redirect and entity name
		$outcome = new Outcome();
		$form = $this->createForm(new OutcomeType(), $outcome);
		$entityname = 'EvalforGescompevalBundle:Outcome';
		$routeredirect = 'EGB_delete_outcome';
		
		// Use a parent method to update
		return $this->delete($outcome, $form, $entityname, $routeredirect);
	}
}