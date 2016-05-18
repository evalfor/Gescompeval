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
use Evalfor\GescompevalBundle\Entity\Institution;
use Evalfor\GescompevalBundle\Form\InstitutionType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Inflector\Inflector;
use Evalfor\GescompevalBundle\Utils\utilClass;

class InstitutionController extends Controller
{
	/*Code added by Daniel Cabeza
	 * START
	 */
	public function listAction(Request $request, $page = 1)
	{
		$order = $request->query->get('order');
		$search = trim($request->query->get('search'));
		$entityname = 'EvalforGescompevalBundle:Institution';
		
		//preparing pagination
		$items = array();
		$totalItems = 0;
		$pagesCount = 0;
		if($page >= 1){
			// Obtain all elements of the suitable type from DB
			$em = $this->getDoctrine()->getManager();
			$fields = array('name', 'description');
			$institutionsAll = $em->getRepository($entityname)->findAll();
			$inttitutionsRequest = $em->getRepository($entityname)->findAllSimilar($entityname, $fields, $search, $order, $page);
			
			$countRequest = $inttitutionsRequest['count'];
			$countAll = count($institutionsAll);
			$maxPerPage = $em->getRepository($entityname)->getMaxPerPage();
			$pagesCount = ceil($countRequest / $maxPerPage);
			
			if(isset($inttitutionsRequest['resultPage'])){
				$items = $inttitutionsRequest['resultPage'];
				$totalItems = $countRequest;
				if($countAll > $countRequest){
					$totalItems = $countRequest . ' / ' . $countAll;
				}
			}
		}
		
		if(empty($items)){
			$items = false;
			$tr = $this->get('translator');
			$this->get('session')->getFlashBag()->add(
					'notice', $tr->trans('skill.flash.empty', array(), 'EvalforGescompevalBundle'));
		}
		return $this->render(
				$entityname . ':list.html.twig', array('institutions_all' => $items,'search' => $search, 'order' => $order, 'pagesCount' => $pagesCount, 'page' => $page, 'totalItems' => $totalItems, 'totalItemsSearch' => $countRequest));
	}
	
	public function updateidAction($id){
		$entityname = 'EvalforGescompevalBundle:Institution';
		$routeRedirect = 'EGB_list_institution';
	
		// Create object, form and routes
		$em = $this->getDoctrine()->getManager();
		$institution = $em->getRepository($entityname)->find($id);
		if(empty($institution)){
			$tr = $this->get('translator');
			$this->get('session')->getFlashBag()->add('error', $tr->trans('skill.flash.error', array(), 'EvalforGescompevalBundle'));
			return $this->redirect($this->generateURL($routeRedirect));
		}
	
		$form = $this->createForm(new InstitutionType(), $institution);
	
		// Create the view calling a a parent method
		return $this->update($institution, $form, $entityname, $routeRedirect);
	}
	
	public function updateAction()
	{
		$institution = new Institution();
		$form = $this->createForm(new InstitutionType(), $institution);
		$entityname = 'EvalforGescompevalBundle:Institution';
		$routeRedirect = 'EGB_list_institution';
	
		return $this->update($institution, $form, $entityname, $routeRedirect);
	}
	
	public function update($institution, $form, $entityname, $routeRedirect)
	{
		$em = $this->getDoctrine()->getManager();
		$institutions_all = $em->getRepository('EvalforGescompevalBundle:Institution')->findAll();

		// Obtain request that contains the datas
		$request = $this->getRequest();

		$tr = $this->get('translator');

		// If request has been invoked by POST, process the form
		if($request->getMethod() == 'POST'){

			// Obtain the element to update
			$formdata = $request->request->get('InstitutionForm');
			$id = $formdata['id'];
			$name = $formdata['name'];
			$institution = $em->getRepository('EvalforGescompevalBundle:Institution')->find($id);
			
			//Check institution name. Institution name must be unique
			//First, check if Name field has been modified. 
			$institutionExists = false;
			$homogenizeFormName = \Evalfor\GescompevalBundle\Utils\inflector::homogenize($name);
			$homogenizeInstituionName = \Evalfor\GescompevalBundle\Utils\inflector::homogenize($institution->name);
			if($homogenizeFormName !== $homogenizeInstituionName){
				$object = new Institution();
				$object->name = $name;
				
				$util = new utilClass($em);
				if($util->exist($object, 'name', 'EvalforGescompevalBundle:Institution')){
					$institutionExists = true;	
				}
			} 

			// Check if there isn't another institution with the introduced name
			if($id && $institutionExists){
				$this->get('session')->getFlashBag()->add('error',$tr->trans('institution.flash.usedname', array(), 'EvalforGescompevalBundle'));
			}
			else{
				// Create the form and load form's datas into $institution
				$form = $this->createForm(new InstitutionType(), $institution);
				$form->bind($request);

				if($form->isValid() && $institution){
					$em->flush();
					$this->get('session')->getFlashBag()->add('notice', $tr->trans('institution.flash.updated', array(), 'EvalforGescompevalBundle'));

					// Make a redirection because if the user try to refresh, the browser
					// would warn about send the datas again. Redirect to the same page
					return $this->redirect($this->generateURL('EGB_list_institution'));
				}
				// If form is not correct, indicate it
				else{
					$this->get('session')->getFlashBag()->add('error',$tr->trans('institution.flash.error', array(), 'EvalforGescompevalBundle'));
				}
			}
		}

		return $this->render('EvalforGescompevalBundle:Institution:update.html.twig', array('form' => $form->createView(), 'institutions_all' => $institutions_all));
	}
	
	public function deleteidAction($id){
		$entityname = 'EvalforGescompevalBundle:Institution';
		$routeRedirect = 'EGB_list_institution';
	
		$tr = $this->get('translator');
		if($id){
			// Process datas to delete the element from DB
			$em = $this->getDoctrine()->getManager();
			$institutionToDelete = $em->getRepository($entityname)->find($id);

			if(!empty($institutionToDelete)){
				//Deletes foreign keys
				$competencies = $em->getRepository('EvalforGescompevalBundle:Competency')->findAll();
				foreach($competencies as $competency){
					if(isset($competency->institution->id) && $competency->institution->id == $institutionToDelete->id){
						unset($competency->institution);
					}
				}
				
				$outcomes = $em->getRepository('EvalforGescompevalBundle:Outcome')->findAll();
				foreach($outcomes as $outcome){
					if(isset($outcome->institution->id) && $outcome->institution->id == $institutionToDelete->id){
						unset($outcome->institution);
					}
				}	
				
				$em->remove($institutionToDelete);
				$em->flush();
	
				$this->get('session')->getFlashBag()->add(
					'notice', $tr->trans('skill.flash.deleted', array(), 'EvalforGescompevalBundle'));
			}
			else{
				$this->get('session')->getFlashBag()->add(
						'error', $tr->trans('skill.flash.error', array(), 'EvalforGescompevalBundle'));
			}
			
			// Make a redirection because if the user try to refresh, the browser
			// would warn about send the datas again. Redirect to the same page
			return $this->redirect($this->generateURL($routeRedirect));
		}
		else{
			$this->get('session')->getFlashBag()->add(
				'error', $tr->trans('skill.flash.error', array(), 'EvalforGescompevalBundle'));
		}
	}
	
	/*Code added by Daniel Cabeza
	 * END
	 */
	
	public function createAction()
	{
		// Obtain request that contains the datas
		$request = $this->getRequest();

		// Create object and create the suitable form
		$institution = new Institution();
		$form = $this->createForm(new InstitutionType(), $institution);

		// If request has been invoked by POST, process the form
		if($request->getMethod() == 'POST')
		{
			// $form->bind() obtains form's datas and load them into the element object
			// which is content into Type object
			$form->bind($request);

			// Check if the form's datas are valid or not
			if($form->isValid())
			{
				// Process datas that are automatically loaded into
				// $element saving them into the DB
				$em = $this->getDoctrine()->getManager();
				
				/*Code added by Daniel Cabeza
				 * START
				 */
				//$em->persist($institution);
				//$em->flush();

				$tr = $this->get('translator');
				//$this->get('session')->getFlashBag()->add('notice', $tr->trans('institution.flash.created', array(), 'EvalforGescompevalBundle'));
				//return $this->redirect($this->generateURL('EGB_create_institution'));
				$util = new utilClass($em);
				if($util->exist($institution, 'name', 'EvalforGescompevalBundle:Institution') == false){
					$em->persist($institution);
					$em->flush();
					$this->get('session')->getFlashBag()->add('notice', $tr->trans('institution.flash.created', array(), 'EvalforGescompevalBundle'));
				}
				else{
					$this->get('session')->getFlashBag()->add('error', $tr->trans('institution.flash.usedname', array(), 'EvalforGescompevalBundle'));
					return $this->redirect($this->generateURL('EGB_create_institution'));
				}
				
				// Make a redirection because if the user try to refresh, the browser
				// would warn about send the datas again. Redirect to the same page
				return $this->redirect($this->generateURL('EGB_list_institution'));
				
				/*Code added by Daniel Cabeza
				 * END
				 */
			}
		}
		return $this->render('EvalforGescompevalBundle:Institution:create.html.twig',array('form' => $form->createView()));
	}

	/*public function updateAction()
	{
		// Obtain all elements of the suitable type from DB
		$em = $this->getDoctrine()->getManager();
		$institutions_all = $em->getRepository('EvalforGescompevalBundle:Institution')->findAll();

		// Obtain request that contains the datas
		$request = $this->getRequest();

		// Create object and create the suitable form
		$institution = new Institution();
		$form = $this->createForm(new InstitutionType(), $institution);

		$tr = $this->get('translator');

		// If request has been invoked by POST, process the form
		if($request->getMethod() == 'POST'){

			// Obtain the element to update
			$formdata = $request->request->get('InstitutionForm');
			$id = $formdata['id'];
			$name = $formdata['name'];
			$institution = $em->getRepository('EvalforGescompevalBundle:Institution')->find($id);
			$institution_aux = $em->getRepository('EvalforGescompevalBundle:Institution')->findOneByName($name);

			// Check if there isn't another institution with the introduced name
			if($id && $institution_aux && $institution->id != $institution_aux->id){
				$this->get('session')->getFlashBag()->add('error',$tr->trans('institution.flash.usedname', array(), 'EvalforGescompevalBundle'));
			}
			else{
				// Create the form and load form's datas into $institution
				$form = $this->createForm(new InstitutionType(), $institution);
				$form->bind($request);

				if($form->isValid() && $institution){
					$em->flush();
					$this->get('session')->getFlashBag()->add('notice', $tr->trans('institution.flash.updated', array(), 'EvalforGescompevalBundle'));

					// Make a redirection because if the user try to refresh, the browser
					// would warn about send the datas again. Redirect to the same page
					return $this->redirect($this->generateURL('EGB_update_institution'));
				}
				// If form is not correct, indicate it
				else{
					$this->get('session')->getFlashBag()->add('error',$tr->trans('institution.flash.error', array(), 'EvalforGescompevalBundle'));
				}
			}
		}

		return $this->render('EvalforGescompevalBundle:Institution:update.html.twig', array('form' => $form->createView(), 'institutions_all' => $institutions_all));
	}*/

	public function deleteAction()
	{
		// Obtain all elements of the suitable type from DB
		$em = $this->getDoctrine()->getManager();
		$institutions_all = $em->getRepository('EvalforGescompevalBundle:Institution')->findAll();

		// Obtain request that contains the data
		$request = $this->getRequest();

		// Create object and create the suitable form
		$institution = new Institution();
		$form = $this->createForm(new InstitutionType(), $institution);

		$tr = $this->get('translator');

		// If request has been invoked by POST, process the form
		if($request->getMethod() == 'POST')
		{
			// Load form's datas into $element
			$form->bind($request);

			// Only checking ID
			if($institution->id)
			{
				// Process datas to delete the element from DB
				$institution_to_delete = $em->getRepository('EvalforGescompevalBundle:Institution')->find($institution->id);
				
				/*Code added by Daniel Cabeza
				 * START
				 */
				//Deletes foreign keys
				$competencies = $em->getRepository('EvalforGescompevalBundle:Competency')->findAll();
				foreach($competencies as $competency){
					if(isset($competency->institution->id) && $competency->institution->id == $institution_to_delete->id){
						unset($competency->institution);
					}
				}
				
				$outcomes = $em->getRepository('EvalforGescompevalBundle:Outcome')->findAll();
				foreach($outcomes as $outcome){
					if(isset($outcome->institution->id) && $outcome->institution->id == $institution_to_delete->id){
						unset($outcome->institution);
					}
				}
				/*Code added by Daniel Cabeza
				 * END
				 */
				
				$em->remove($institution_to_delete);
				$em->flush();

				$this->get('session')->getFlashBag()->add('notice', $tr->trans('institution.flash.deleted', array(), 'EvalforGescompevalBundle'));

				// Make a redirection because if the user try to refresh, the browser
				// would warn about send the datas again. Redirect to the same page
				
				/*Code added by Daniel Cabeza
				 * START
				 */
				return $this->redirect($this->generateURL('EGB_list_institution'));
				//return $this->redirect($this->generateURL('EGB_delete_institution'));
				/*Code added by Daniel Cabeza
				 * END
				 */
			}
			// If form is not correct, indicate it
			else{
				$this->get('session')->getFlashBag()->add('error', $tr->trans('institution.flash.error', array(), 'EvalforGescompevalBundle'));
				//$this->get('session')->getFlashBag()->add('error', $tr->trans($institution->id.' -', array(), 'EvalforGescompevalBundle'));

				return $this->render('EvalforGescompevalBundle:Institution:delete.html.twig', array('form' => $form->createView(), 'institutions_all' => $institutions_all));
			}
		}
		return $this->render('EvalforGescompevalBundle:Institution:delete.html.twig', array('form' => $form->createView(), 'institutions_all' => $institutions_all));
	}
}