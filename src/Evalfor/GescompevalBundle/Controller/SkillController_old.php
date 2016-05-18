<?php

namespace Evalfor\GescompevalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Evalfor\GescompevalBundle\Entity\Skill;
use Evalfor\GescompevalBundle\Form\SkillType;
use Evalfor\GescompevalBundle\Entity\Competency;
use Evalfor\GescompevalBundle\Form\CompetencyType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Length;

class SkillController extends Controller
{
	public function create($skill, $form, $type, $routeredirect, $routerender)
	{
		// Obtain request that contains the datas
		$request = $this->getRequest();

		// If request has been invoked by POST, process the form
		if($request->getMethod() == 'POST')
		{
			// $form->bind() obtains form's datas and load them into the skill object
			// which is content into Type object
			$form->bind($request);

			// Check if the form's datas are valid or not
			if($form->isValid())
			{
				// Process datas that are automatically loaded into
				// $skill saving them into the DB
				$em = $this->getDoctrine()->getManager();
				$em->persist($skill);
				$em->flush();

				$tr = $this->get('translator');
				$this->get('session')->getFlashBag()->add(
						'notice', $tr->trans('skill.flash.created', array(), 'EvalforGescompevalBundle'));

				// Make a redirection because if the user try to refresh, the browser
				// would warn about send the datas again. Redirect to the same page
				return $this->redirect($this->generateURL($routeredirect, array('type' => $type)));
			}
		}
		return $this->render($routerender, array('form' => $form->createView(), 'type' => $type));
	}

	public function createAction($type)
	{
		// Obtain request that contains the datas
		$request = $this->getRequest();

		// Create object, set type and create the suitable form
		if($type == Skill::COMPETENCY){
			$skill = new Competency();
			$form = $this->createForm(new CompetencyType(), $skill);
		}
		else{
			$skill = new Skill();
			$form = $this->createForm(new SkillType(), $skill);
		}

		// If request has been invoked by POST, process the form
		if($request->getMethod() == 'POST')
		{
			// $form->bind() obtains form's datas and load them into the skill object
			// which is content into Type object
			$form->bind($request);

			// Check if the form's datas are valid or not
			if($form->isValid())
			{
				// Process datas that are automatically loaded into
				// $skill saving them into the DB
				$em = $this->getDoctrine()->getManager();
				$em->persist($skill);
				$em->flush();

				$tr = $this->get('translator');
				$this->get('session')->getFlashBag()->add(
						'notice', $tr->trans('skill.flash.created', array(), 'EvalforGescompevalBundle'));

				// Make a redirection because if the user try to refresh, the browser
				// would warn about send the datas again. Redirect to the same page
				return $this->redirect($this->generateURL('EGB_create_skill', array('type' => $type)));
			}
		}
		return $this->render('EvalforGescompevalBundle:Skill:create.html.twig',array('form' => $form->createView(), 'type' => $type));
	}

	public function updateAction($type)
	{
		// Obtain all elements of the suitable type from DB
		$em = $this->getDoctrine()->getManager();
		$skills_all = $em->getRepository('EvalforGescompevalBundle:Skill')->findAll();

		// Obtain request that contains the datas
		$request = $this->getRequest();

		// Create object and create the suitable form
		$skill = new Skill();
		$form = $this->createForm(new SkillType(), $skill);

		$tr = $this->get('translator');

		// If request has been invoked by POST, process the form
		if($request->getMethod() == 'POST'){

			// Obtain the element to update
			$formdata = $request->request->get('SkillForm');
			$id = $formdata['id'];
			$code = $formdata['code'];
			$skill = $em->getRepository('EvalforGescompevalBundle:Skill')->find($id);
			$skill_aux = $em->getRepository('EvalforGescompevalBundle:Skill')->findOneByCode($code);

			// Check if there isn't another skill with the introduced short description
			if($id && $skill_aux && $skill->id != $skill_aux->id){
				$this->get('session')->getFlashBag()->add('error',$tr->trans('skill.flash.usedcode', array(), 'EvalforGescompevalBundle'));
			}
			else{
				// Create the form and load form's datas into $skill
				$form = $this->createForm(new SkillType(), $skill);
				$form->bind($request);

				//$errors = $this->get('validator')->validate($element);
				if($form->isValid() && $skill){
					$em->flush();

					$this->get('session')->getFlashBag()->add(
							'notice', $tr->trans('skill.flash.updated', array(), 'EvalforGescompevalBundle'));

					// Make a redirection because if the user try to refresh, the browser
					// would warn about send the datas again. Redirect to the same page
					return $this->redirect($this->generateURL('EGB_update_skill', array('type' => $type)));
				}
				// If form is not correct, indicate it
				else{
					$this->get('session')->getFlashBag()->add('error', $tr->trans('skill.flash.error', array(), 'EvalforGescompevalBundle'));
				}
			}
		}

		return $this->render('EvalforGescompevalBundle:Skill:update.html.twig', array(
				'form' => $form->createView(), 'skills_all' => $skills_all, 'type' => $type
		));
	}

	public function deleteAction($type)
	{
		// Obtain all elements of the suitable type from DB
		$em = $this->getDoctrine()->getManager();
		$skills_all = $em->getRepository('EvalforGescompevalBundle:Skill')->findAll();

		// Obtain request that contains the datas
		$request = $this->getRequest();

		// Create object and create the suitable form
		$skill = new Skill();
		$form = $this->createForm(new SkillType(), $skill);

		$tr = $this->get('translator');

		// If request has been invoked by POST, process the form
		if($request->getMethod() == 'POST')
		{
			// Load form's datas into $element
			$form->bind($request);

			// Only checking ID
			if($skill->id)
			{
				// Process datas to delete the element from DB
				$skill_to_delete = $em->getRepository('EvalforGescompevalBundle:Skill')->find($skill->id);
				$em->remove($skill_to_delete);
				$em->flush();

				$this->get('session')->getFlashBag()->add(
						'notice', $tr->trans('skill.flash.deleted', array(), 'EvalforGescompevalBundle'));

				// Make a redirection because if the user try to refresh, the browser
				// would warn about send the datas again. Redirect to the same page
				return $this->redirect($this->generateURL('EGB_delete_skill', array('type' => $type)));
			}
			// If form is not correct, indicate it
			else{
				$this->get('session')->getFlashBag()->add(
						'error', $tr->trans('skill.flash.error', array(), 'EvalforGescompevalBundle'));

				return $this->render('EvalforGescompevalBundle:Skill:delete.html.twig', array(
						'form' => $form->createView(), 'skills_all' => $skills_all, 'type' => $type));
			}
		}
		return $this->render('EvalforGescompevalBundle:Skill:delete.html.twig', array(
				'form' => $form->createView(), 'skills_all' => $skills_all, 'type' => $type));
	}

	/*public function connectAction($type)
	{
		// Obtain all elements of the suitable type from DB
		$em = $this->getDoctrine()->getManager();
		$elements_all = $em->getRepository('EvalforGescompevalBundle:Elements')->findByType($type);
		$diff_elements_all = $em->getRepository('EvalforGescompevalBundle:Elements')->findByDifferentType($type);

		// Obtain request that contains the datas
		$request = $this->getRequest();

		// If request has been invoked by POST, process the form
		if($request->getMethod() == 'POST')
		{
			// Obtain all datas received by POST
			$id = $request->request->get('select_elements');
			$new_ids_elements_connected = $request->request->get('duallistbox_elements');

			// Make sure it isn't empty to apply array_unique
			if (count($new_ids_elements_connected) > 0){
				$new_ids_elements_connected = array_unique($request->request->get('duallistbox_elements'));
			}

			$tr = $this->get('translator');

			// Check correct ID
			if($id != null && $id != 0)
			{
				// Obtain all elements to connect through ids received by por POST
				$new_elements_connected = new ArrayCollection();

				// Make sure that it isn't empty to iterate it
				if (count($new_ids_elements_connected) > 0){
					foreach ($new_ids_elements_connected as $new_id){
						$new_elements_connected[] =	$em->getRepository('EvalforGescompevalBundle:Elements')->findOneById($new_id);
					}
				}

				// Save new connections in DB
				$element = $em->getRepository('EvalforGescompevalBundle:Elements')->findOneById($id);
				$element->setMyElements($new_elements_connected);
				$em->persist($element);
				$em->flush();

				$this->get('session')->getFlashBag()->add(
						'notice', $tr->trans('element.flash.connected', array(), 'EvalforGescompevalBundle'));

				// Make a redirection because if the user try to refresh, the browser
				// would warn about send the datas again. Redirect to the same page
				return $this->redirect($this->generateURL('EGB_connect_elements', array('type' => $type)));
			}
			// If form is not correct, indicate it
			else{
				$this->get('session')->getFlashBag()->add(
						'error', $tr->trans('element.flash.error', array(), 'EvalforGescompevalBundle'));

				return $this->render('EvalforGescompevalBundle:Elements:connect.html.twig', array(
						'elements_all' => $elements_all, 'diff_elements_all' => $diff_elements_all, 'type' => $type));
			}
		}
		return $this->render('EvalforGescompevalBundle:Elements:connect.html.twig', array(
				'elements_all' => $elements_all, 'diff_elements_all' => $diff_elements_all, 'type' => $type));
	}*/
}