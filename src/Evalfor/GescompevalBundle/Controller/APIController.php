<?php

namespace Evalfor\GescompevalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Evalfor\GescompevalBundle\Entity\Institution;
use Evalfor\GescompevalBundle\Entity\Skill;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class APIController extends Controller
{
	/**
	 * Show one skill
	 * @Template()
	 */
	public function showSkillAction($id, $type)
	{
		//if($id != null && $id != 0){
		if(!empty($id)){
			$em = $this->getDoctrine()->getManager();

			if($type == Skill::COMPETENCY){
				$skill = $em->getRepository('EvalforGescompevalBundle:Competency')->findOneById($id);
			}
			elseif($type == Skill::OUTCOME){
				$skill = $em->getRepository('EvalforGescompevalBundle:Outcome')->findOneById($id);
			}

			if(isset($skill)){
				return array('skill' => $skill, 'skilltype' => $type);
			}
		}
		return array('skill' => null);
	}


	/**
	 * Show all skills
	 * @Template()
	 */
	public function showSkillsAction()
	{
		$em = $this->getDoctrine()->getManager();
		$skills = array();

		if($this->getRequest()->getMethod() == 'POST'){

			// Obtain request that contains the datas
			if (!isset( $HTTP_RAW_POST_DATA)) {
				$HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
			}

			$xml_string = $HTTP_RAW_POST_DATA;
			$objects = simplexml_load_string($xml_string);

			if($objects){
				// Get all skills
				foreach($objects as $obj){
					if($obj->type == Skill::COMPETENCY){
						$repository = 'EvalforGescompevalBundle:Competency';
					}
					elseif($obj->type == Skill::OUTCOME){
						$repository = 'EvalforGescompevalBundle:Outcome';
					}

					if($skill = $em->getRepository($repository)->findOneById($obj->id)){
						$skills[] = $skill;
					}
				}
			}
		}
		else{
			$competencies = $em->getRepository('EvalforGescompevalBundle:Competency')->findAll();
			$outcomes = $em->getRepository('EvalforGescompevalBundle:Outcome')->findAll();
			$skills = array_merge($competencies, $outcomes);
		}

		return array('skills' => $skills, 'c_type' => Skill::COMPETENCY, 'o_type' => Skill::OUTCOME);
	}

	/**
	 * Show one competency
	 * @Template()
	 */
	public function showCompetencyAction($id)
	{
		if(!empty($id)){
			$em = $this->getDoctrine()->getManager();
			$competency = $em->getRepository('EvalforGescompevalBundle:Competency')->findOneById($id);
			if(isset($competency)){
				return array('competency' => $competency);
			}
		}
		return array('competency' => null);
	}

	/**
	 * Show all competencies
	 * @Template()
	 */
	public function showCompetenciesAction()
	{
		$em = $this->getDoctrine()->getManager();
		$competencies = $em->getRepository('EvalforGescompevalBundle:Competency')->findAll();
		return array('competencies' => $competencies);
	}

	/**
	 * Show one learning outcome
	 * @Template()
	 */
	public function showOutcomeAction($id)
	{
		if(!empty($id)){
			$em = $this->getDoctrine()->getManager();
			$outcome = $em->getRepository('EvalforGescompevalBundle:Outcome')->findOneById($id);
			if(isset($outcome)){
				return array('outcome' => $outcome);
			}
		}
		return array('outcome' => null);
	}

	/**
	 * Show all learning outcomes
	 * @Template()
	 */
	public function showOutcomesAction()
	{
		$em = $this->getDoctrine()->getManager();
		$outcomes = $em->getRepository('EvalforGescompevalBundle:Outcome')->findAll();
		return array('outcomes' => $outcomes);
	}

	/**
	 * Show one institution
	 * @Template()
	 */
	public function showInstitutionAction($id)
	{
		if($id != null && $id != 0){
			$em = $this->getDoctrine()->getManager();
			$institution = $em->getRepository('EvalforGescompevalBundle:Institution')->findOneById($id);

			if(isset($institution)){
				return array('institution' => $institution);
			}
		}
		return array('institution' => false);
	}

	/**
	 * Show all institutions
	 * @Template()
	 */
	public function showInstitutionsAction()
	{
		$em = $this->getDoctrine()->getManager();
		$institutions = $em->getRepository('EvalforGescompevalBundle:Institution')->findAll();
		return array('institutions' => $institutions);
	}

	/**
	 * Show the skills of an institution
	 * @Template()
	 */
	public function showInstitutionSkillsAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$competencies = $em->getRepository('EvalforGescompevalBundle:Competency')->findByInstitution($id);
		$outcomes = $em->getRepository('EvalforGescompevalBundle:Outcome')->findByInstitution($id);
		$skills = array_merge($competencies, $outcomes);
		return array('skills' => $skills, 'c_type' => Skill::COMPETENCY, 'o_type' => Skill::OUTCOME);
	}

	/**
	 * Show one competencetype
	 * @Template()
	 */
	public function showCompetenceTypeAction($id)
	{
		if($id != null && $id != 0){
			$em = $this->getDoctrine()->getManager();
			$competencetype = $em->getRepository('EvalforGescompevalBundle:CompetenceType')->findOneById($id);

			if(isset($competencetype)){
				return array('competencetype' => $competencetype);
			}
		}
		return array('competencetype' => false);
	}

	/**
	 * Show all competencetypes
	 * @Template()
	 */
	public function showCompetenceTypesAction()
	{
		$em = $this->getDoctrine()->getManager();
		$competencetypes = $em->getRepository('EvalforGescompevalBundle:CompetenceType')->findAll();
		return array('competencetypes' => $competencetypes);
	}

	/*
	 * Show one or more abilities
	 * @Template()
	 */
	/*public function showAbilitiesAction()
	{
		// Obtain request that contains the datas
		if (!isset( $HTTP_RAW_POST_DATA)) {
			$HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
		}

		$xml_string = $HTTP_RAW_POST_DATA;
		$ids = simplexml_load_string($xml_string);

		if($ids){
			// Get all elements
			$elements = array();
			foreach($ids as $id){

				$em = $this->getDoctrine()->getManager();
				if($element = $em->getRepository('EvalforGescompevalBundle:Elements')->findOneById($id)){
					$elements[] = $element;
				}
			}

			if($elements != array()){
				return array('elements' => $elements);
			}
		}

		return array('elements' => false);
	}*/

	/*
	 * Show abilities connected with abilities identificators received
	 * @Template()
	 */
	/*public function showConnectedAbilitiesAction()
	{
		// Obtain request that contains the datas
		if (!isset( $HTTP_RAW_POST_DATA)) {
			$HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
		}

		$xml_string = $HTTP_RAW_POST_DATA;
		$ids = simplexml_load_string($xml_string);

		if($ids){

			$ids_already_included = array();
			// Get all connected elements
			$elements = array();
			foreach($ids as $id){

				$em = $this->getDoctrine()->getManager();
				if($element = $em->getRepository('EvalforGescompevalBundle:Elements')->findOneById($id)){

					// Get connected elements
					$connected_elements = $element->myElements;

					// Include the connected elements if it haven't been included yet
					foreach($connected_elements as $connected_element){
						$connected_element_id = $connected_element->id;

						if(!in_array($connected_element_id, $ids_already_included)){
							$elements[] = $connected_element;
							$ids_already_included[] = $connected_element_id;
						}
					}
				}
			}

			if($elements != array()){
				return array('elements' => $elements);
			}
		}

		return array('elements' => false);
	}*/
}
