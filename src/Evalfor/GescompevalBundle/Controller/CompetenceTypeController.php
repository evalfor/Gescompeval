<?php
/**
 * @package    EvalforGescompevalBundle
 * @copyright  2010 onwards EVALfor Research Group {@link http://evalfor.net/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Juan Antonio Caballero Hernández <juanantonio.caballero@uca.es >
 * @author     Daniel Cabeza Sánchez <daniel.cabeza@uca.es>
 */
namespace Evalfor\GescompevalBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Evalfor\GescompevalBundle\Entity\CompetenceType;
use Evalfor\GescompevalBundle\Form\CompetenceTypeType;
use Evalfor\GescompevalBundle\Utils\utilClass;

/**
 * CompetenceType controller.
 *
 */
class CompetenceTypeController extends Controller
{
	/*Code added by Daniel Cabeza
	 * START
	 */
	public function listAction(Request $request, $page = 1)
	{
		$order = $request->query->get('order');
		$search = trim($request->query->get('search'));
		$entityname = 'EvalforGescompevalBundle:CompetenceType';		
		
		//preparing pagination
		$items = array();
		$totalItems = 0;
		$pagesCount = 0;
		if($page >= 1){
			// Obtain all elements of the suitable type from DB
			$em = $this->getDoctrine()->getManager();
			$fields = array('type', 'description');
			$competenceTypesAll = $em->getRepository($entityname)->findAll();
			$competenceTypesRequest = $em->getRepository($entityname)->findAllSimilar($entityname, $fields, $search, $order, $page);
				
			$countRequest = $competenceTypesRequest['count'];
			$countAll = count($competenceTypesAll);
			$maxPerPage = $em->getRepository($entityname)->getMaxPerPage();
			$pagesCount = ceil($countRequest / $maxPerPage);
				
			if(isset($competenceTypesRequest['resultPage'])){
				$items = $competenceTypesRequest['resultPage'];
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
				$entityname . ':list.html.twig', array('skills_all' => $items,'search' => $search, 'order' => $order , 'pagesCount' => $pagesCount, 'page' => $page, 'totalItems' => $totalItems, 'totalItemsSearch' => $countRequest));
	}
	
	public function updateidAction($id){
		$entityname = 'EvalforGescompevalBundle:CompetenceType';
		$routeRedirect = 'EGB_list_competencetype';
	
		// Create object, form and routes
		$em = $this->getDoctrine()->getManager();
		$competencetype = $em->getRepository($entityname)->find($id);
		if(empty($competencetype)){
			$tr = $this->get('translator');
			$this->get('session')->getFlashBag()->add('error', $tr->trans('skill.flash.error', array(), 'EvalforGescompevalBundle'));
			return $this->redirect($this->generateURL($routeRedirect));
		}
	
		$form = $this->createForm(new CompetenceTypeType(), $competencetype);
	
		// Create the view calling a a parent method
		return $this->update($competencetype, $form, $entityname, $routeRedirect);
	}

	public function updateAction()
	{
		$competencetype = new CompetenceType();
		$form = $this->createForm(new CompetenceTypeType(), $competencetype);
		$entityname = 'EvalforGescompevalBundle:CompetenceType';
		$routeRedirect = 'EGB_list_competencetype';
	
		return $this->update($competencetype, $form, $entityname, $routeRedirect);
	}
	
	public function update($competencetype, $form, $entityname, $routeRedirect)
	{
		// Obtain all elements of the suitable type from DB
		$em = $this->getDoctrine()->getManager();
		$competencetypes_all = $em->getRepository('EvalforGescompevalBundle:CompetenceType')->findAll();
	
		// Obtain request that contains the datas
		$request = $this->getRequest();
	
		$tr = $this->get('translator');
	
		// If request has been invoked by POST, process the form
		if($request->getMethod() == 'POST'){
	
			// Obtain the element to update
			$formdata = $request->request->get('CompetenceTypeForm');
			$id = $formdata['id'];
			$type = $formdata['type'];
			$competencetype = $em->getRepository('EvalforGescompevalBundle:CompetenceType')->find($id);
			//$competencetype_aux = $em->getRepository('EvalforGescompevalBundle:CompetenceType')->findOneByType($type);
			
			//Check type. Type must be unique
			//First, check if Name field has been modified.
			$competenceTypeExists = false;
			$homogenizeFormType = \Evalfor\GescompevalBundle\Utils\inflector::homogenize($type);
			$homogenizeCompetenceTypeType = \Evalfor\GescompevalBundle\Utils\inflector::homogenize($competencetype->type);
			if($homogenizeFormType !== $homogenizeCompetenceTypeType){
				$object = new CompetenceType();
				$object->type = $type;
			
				$util = new utilClass($em);
				if($util->exist($object, 'type', 'EvalforGescompevalBundle:CompetenceType')){
					$competenceTypeExists = true;
				}
			}
	
			// Check if there isn't another competence type with the introduced type
			if($id && $competenceTypeExists){
				$this->get('session')->getFlashBag()->add('error',$tr->trans('competencetype.flash.usedtype', array(), 'EvalforGescompevalBundle'));
			}
			else{
				// Create the form and load form's datas into $competencetype
				$form = $this->createForm(new CompetenceTypeType(), $competencetype);
				$form->bind($request);
	
				if($form->isValid() && $competencetype){
					$em->flush();
					$this->get('session')->getFlashBag()->add('notice', $tr->trans('competencetype.flash.updated', array(), 'EvalforGescompevalBundle'));
	
					// Make a redirection because if the user try to refresh, the browser
					// would warn about send the datas again. Redirect to the same page						
					return $this->redirect($this->generateURL('EGB_list_competencetype'));
				}
				// If form is not correct, indicate it
				else{
					$this->get('session')->getFlashBag()->add('error',$tr->trans('competencetype.flash.error', array(), 'EvalforGescompevalBundle'));
				}
			}
		}
	
		return $this->render('EvalforGescompevalBundle:CompetenceType:update.html.twig', array('form' => $form->createView(), 'competencetypes_all' => $competencetypes_all));
	}
	
	public function deleteidAction($id){
		$entityname = 'EvalforGescompevalBundle:CompetenceType';
		$routeRedirect = 'EGB_list_competencetype';
		
		$tr = $this->get('translator');
		if($id){
			// Process datas to delete the element from DB
			$em = $this->getDoctrine()->getManager();
			$skill_to_delete = $em->getRepository($entityname)->find($id);
			
			if(!empty($skill_to_delete)){
				//Deletes foreign keys
				$competencies = $em->getRepository('EvalforGescompevalBundle:Competency')->findAll();
				foreach($competencies as $competency){
					if(isset($competency->competencetype->id) && $competency->competencetype->id == $skill_to_delete->id){
						unset($competency->competencetype);
					}	
				}
				
				
				$em->remove($skill_to_delete);
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
		// If form is not correct, indicate it
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
		$competencetype = new CompetenceType();
		$form = $this->createForm(new CompetenceTypeType(), $competencetype);

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
				//
				//$em->persist($competencetype);
				//$em->flush();
				
				
				$tr = $this->get('translator');
				//$this->get('session')->getFlashBag()->add('notice', $tr->trans('competencetype.flash.created', array(), 'EvalforGescompevalBundle'));
				//return $this->redirect($this->generateURL('EGB_create_competencetype'));
				
				$util = new utilClass($em);
				if($util->exist($competencetype, 'type','EvalforGescompevalBundle:CompetenceType') == false){
					$em->persist($competencetype);
					$em->flush();
					$this->get('session')->getFlashBag()->add('notice', $tr->trans('competencetype.flash.created', array(), 'EvalforGescompevalBundle'));
				}
				else{
					$this->get('session')->getFlashBag()->add('error', $tr->trans('competencetype.flash.usedtype', array(), 'EvalforGescompevalBundle'));
					return $this->redirect($this->generateURL('EGB_create_competencetype'));
				}
				// Make a redirection because if the user try to refresh, the browser
				// would warn about send the datas again. Redirect to the same page
				
				return $this->redirect($this->generateURL('EGB_list_competencetype'));
				
				/*Code added by Daniel Cabeza
				 * END
				 */
			}
		}
		return $this->render('EvalforGescompevalBundle:CompetenceType:create.html.twig',array('form' => $form->createView()));
	}

	/*Code added by Daniel Cabeza
	 * START
	 */
/*	public function updateAction()
	{
		// Obtain all elements of the suitable type from DB
		$em = $this->getDoctrine()->getManager();
		$competencetypes_all = $em->getRepository('EvalforGescompevalBundle:CompetenceType')->findAll();

		// Obtain request that contains the datas
		$request = $this->getRequest();

		// Create object and create the suitable form
		$competencetype = new CompetenceType();
		$form = $this->createForm(new CompetenceTypeType(), $competencetype);

		$tr = $this->get('translator');

		// If request has been invoked by POST, process the form
		if($request->getMethod() == 'POST'){

			// Obtain the element to update
			$formdata = $request->request->get('CompetenceTypeForm');
			$id = $formdata['id'];
			$type = $formdata['type'];
			$competencetype = $em->getRepository('EvalforGescompevalBundle:CompetenceType')->find($id);
			$competencetype_aux = $em->getRepository('EvalforGescompevalBundle:CompetenceType')->findOneByType($type);

			// Check if there isn't another competence type with the introduced type
			if($id && $competencetype_aux && $competencetype->id != $competencetype_aux->id){
				$this->get('session')->getFlashBag()->add('error',$tr->trans('competencetype.flash.usedtype', array(), 'EvalforGescompevalBundle'));
			}
			else{
				// Create the form and load form's datas into $competencetype
				$form = $this->createForm(new CompetenceTypeType(), $competencetype);
				$form->bind($request);

				if($form->isValid() && $competencetype){
					$em->flush();
					$this->get('session')->getFlashBag()->add('notice', $tr->trans('competencetype.flash.updated', array(), 'EvalforGescompevalBundle'));

					// Make a redirection because if the user try to refresh, the browser
					// would warn about send the datas again. Redirect to the same page
					return $this->redirect($this->generateURL('EGB_update_competencetype'));
				}
				// If form is not correct, indicate it
				else{
					$this->get('session')->getFlashBag()->add('error',$tr->trans('competencetype.flash.error', array(), 'EvalforGescompevalBundle'));
				}
			}
		}

		return $this->render('EvalforGescompevalBundle:CompetenceType:update.html.twig', array('form' => $form->createView(), 'competencetypes_all' => $competencetypes_all));
	}
*/
	/*Code added by Daniel Cabeza
	 * END
	 */
	
	public function deleteAction()
	{
		// Obtain all elements of the suitable type from DB
		$em = $this->getDoctrine()->getManager();
		$competencetypes_all = $em->getRepository('EvalforGescompevalBundle:CompetenceType')->findAll();

		// Obtain request that contains the datas
		$request = $this->getRequest();

		// Create object and create the suitable form
		$competencetype = new CompetenceType();
		$form = $this->createForm(new CompetenceTypeType(), $competencetype);

		// If request has been invoked by POST, process the form
		if($request->getMethod() == 'POST')
		{
			// Load form's datas into $element
			$form->bind($request);

			$tr = $this->get('translator');

			// Only checking ID
			if($competencetype->id)
			{
				// Process datas to delete the element from DB
				$competencetype_to_delete = $em->getRepository('EvalforGescompevalBundle:CompetenceType')->find($competencetype->id);
				
				/*Code added by Daniel Cabeza
				 * START
				 */
				//Deletes foreign keys
				$competencies = $em->getRepository('EvalforGescompevalBundle:Competency')->findAll();
				foreach($competencies as $competency){
					if(isset($competency->competencetype->id) && $competency->competencetype->id == $competencetype_to_delete->id){
						unset($competency->competencetype);
					}
				}
				/*Code added by Daniel Cabeza
				 * END
				 */
				
				$em->remove($competencetype_to_delete);
				$em->flush();

				$this->get('session')->getFlashBag()->add('notice', $tr->trans('competencetype.flash.deleted', array(), 'EvalforGescompevalBundle'));

				// Make a redirection because if the user try to refresh, the browser
				// would warn about send the datas again. Redirect to the same page
				
				/*Code added by Daniel Cabeza
				 * START
				 */
				return $this->redirect($this->generateURL('EGB_list_competencetype'));
				//return $this->redirect($this->generateURL('EGB_delete_competencetype')););
				/*Code added by Daniel Cabeza
				 * END
				 */
			}
			// If form is not correct, indicate it
			else{
				$this->get('session')->getFlashBag()->add('error', $tr->trans('competencetype.flash.error', array(), 'EvalforGescompevalBundle'));
				//$this->get('session')->getFlashBag()->add('error', $tr->trans($competencetype->id.' -', array(), 'EvalforGescompevalBundle'));

				return $this->render('EvalforGescompevalBundle:CompetenceType:delete.html.twig', array('form' => $form->createView(), 'competencetypes_all' => $competencetypes_all));
			}
		}
		return $this->render('EvalforGescompevalBundle:CompetenceType:delete.html.twig', array('form' => $form->createView(), 'competencetypes_all' => $competencetypes_all));
	}
}
