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
use Evalfor\GescompevalBundle\Entity\Skill;
use Evalfor\GescompevalBundle\Form\SkillType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Length;
use Evalfor\GescompevalBundle\Form\CompetencyType;
use Evalfor\GescompevalBundle\Form\OutcomeType;
use Evalfor\GescompevalBundle\Form\UploadType;
use Evalfor\GescompevalBundle\Entity\Competency;
use Evalfor\GescompevalBundle\Entity\Outcome;
use Evalfor\GescompevalBundle\Utils\utilClass;
use Evalfor\GescompevalBundle\Utils\csvClass;
use Evalfor\GescompevalBundle\Entity\CompetenceType;
use Evalfor\GescompevalBundle\Utils\inflectorClass;
use Evalfor\GescompevalBundle\Utils\inflector;

class SkillController extends Controller
{
	/*Code added by Daniel Cabeza
	 * START
	 */
	
	/**
	 * Show all skills ordered by $order and containt $search.
	 * @param string $entityname
	 * @param string $order is the name of a field of Competency or Outcome
	 * @param unknown $search is a word to search in the fields of Comptency or Outcome
	 */
	public function showAll($entityname, $order = null, $search = null, $page = 1)
	{
		//preparing pagination
		$items = array();
		$totalItems = 0;
		$pagesCount = 0;
		if($page >= 1){
			// Obtain all elements of the suitable type from DB
			$em = $this->getDoctrine()->getManager();
			$allSkills = $em->getRepository($entityname)->findAll();
			$skillsRequest = $em->getRepository($entityname)->findAllSimilarCompetences($search, $order, $page);
			
			$countSkillsRequest = $skillsRequest['count'];
			$countAllSkills = count($allSkills);
			$maxPerPage = $em->getRepository($entityname)->getMaxPerPage();
			$pagesCount = ceil($countSkillsRequest / $maxPerPage);
			
			if(isset($skillsRequest['resultPage'])){
				$items = $skillsRequest['resultPage'];
				$totalItems = $countSkillsRequest;
				if($countAllSkills > $countSkillsRequest){
					$totalItems = $countSkillsRequest . ' / ' . $countAllSkills;
				}
			}
		}
		
		if(empty($items)){
			$competencies_all = false;
			$tr = $this->get('translator');
			$this->get('session')->getFlashBag()->add(
					'notice', $tr->trans('skill.flash.empty', array(), 'EvalforGescompevalBundle'));
		}
		
		return $this->render(
				$entityname . ':list.html.twig', array('skills_all' => $items, 'search' => $search, 'order' => $order , 
						'pagesCount' => $pagesCount, 'page' => $page, 'totalItems' => $totalItems, 'totalItemsSearch' => $countSkillsRequest));
	}
	
	/**
	 * Upload a CSV file with a set of competencies
	 */
	public function upload($entityname, $form, $routeRedirect){
		//Obtain request that contains the datas
		$request = $this->getRequest();
	
		$em = $this->getDoctrine()->getManager();
		$tr = $this->get('translator');
	
		if($request->getMethod() == 'POST'){
			//$confirmUpload and $rowsConfirmed obtains datas from Preview screen
			$confirmUpload = $request->request->get('confirm');
			$rowsConfirmed = $this->get('session')->get('rowsImportable');
			$rowsNoConfirmed = $this->get('session')->get('rowsNoImportable');
			$competenceTypesToCreate = $this->get('session')->get('newCompetenceTypes');
			//Saves rows confirmed
			if(!empty($confirmUpload)){
				if(!empty($competenceTypesToCreate)){
					$util = new utilClass($em);				
						
					/*$type = inflector::homogenize($trimValue);
					$CTobject = null;
					if(empty($objectsOfCompetenceType[$type])){
						if($ignoreInvalidCompetenceTypes == false){
							$errorDescription .= '-Competence Type "'.$trimValue.'" does not exist in the System-';
							$validRowFlag = false;
						}
						if(!in_array($trimValue, $invalidCompetenceTypes)){
							array_push($invalidCompetenceTypes, $trimValue);
						}
					}
					else{
						$CTobject = $objectsOfCompetenceType[$type];
						$trimValue = $CTobject->type;
					}*/
					
					foreach($competenceTypesToCreate as $ct){
						//$competencetype = $em->getRepository('EvalforGescompevalBundle:CompetenceType')->findByType($ct);
						$objectsOfCompetenceType = $util->homogenize('EvalforGescompevalBundle:CompetenceType', 'type');
						$type = inflector::homogenize($ct);
						if(empty($objectsOfCompetenceType[$type])){
							$competencetype = new CompetenceType();
							$competencetype->type = $ct;
							$em->persist($competencetype);
							$em->flush();
						}
					}
					$this->get('session')->remove('newCompetenceTypes');
				}
				
				$totalRowsCreated = $this->import($rowsConfirmed);
				$totalErrors = (count($rowsConfirmed) - $totalRowsCreated) + count($rowsNoConfirmed);
				
				$messageType = 'notice';
				if($totalErrors > 0){
					$messageType = 'error';
				}
				$this->get('session')->getFlashBag()->add(
						'notice', $tr->trans('competency.upload.created', array(), 'EvalforGescompevalBundle') . ": " . $totalRowsCreated);
				$this->get('session')->getFlashBag()->add(
						$messageType, $tr->trans('skill.errors', array(), 'EvalforGescompevalBundle') . ": " . $totalErrors);

				/*$request->request->remove('confirm');
				$this->get('session')->remove('rowsImportable');
				$this->get('session')->remove('rowsNoImportable');*/
				return $this->redirect($this->generateURL($routeRedirect));
			}else{
				//$this->get('session')->getFlashBag()->add(
				//			'error', $tr->trans('skill.flash.csvvoid', array(), 'EvalforGescompevalBundle'));
				// $form->bind() obtains form's datas and load them into the skill object
				// which is content into Type object
				$form->bind($request);

				// Check if the form's datas are valid or not
				if($form->isValid())
				{
					//Get form datas
					$formdata = $form->getData();
					$fileForm = $formdata['file'];
					$delimiterForm =  $formdata['delimiter'];
					$createCompetenceTypeAutomaticallyForm = null;
					if(isset($formdata['competencetype'])){
						$createCompetenceTypeAutomaticallyForm = $formdata['competencetype'];
					}
					
					$delimiter = ',';
					switch($delimiterForm){
						case 'cl': $delimiter = ':'; break;
						case 'sc': $delimiter = ';'; break;
						case 'cm': $delimiter = ','; break;
					}
					$csv = new csvClass($fileForm, $delimiter);
					
					$createCompetenceTypesAutomatically = false;
					if($createCompetenceTypeAutomaticallyForm === 'ct_auto'){
						$createCompetenceTypesAutomatically = true;
					}
					
					$csvProcessed = $this->processCsv($csv, $entityname, $createCompetenceTypesAutomatically);
						
					$csvRows = $csvProcessed->csvValidRows;
					$csvGeneralErrors = $csvProcessed->csvCriticalErrors;
					$csvValidRowsWithWarnings = $csvProcessed->csvValidRowsWithWarnings;
					$csvNewCompetenceTypesToCreate = $csvProcessed->csvInvalidCompetenceTypes;
						
					if(empty($csvGeneralErrors)){
						if(!empty($csvRows) || !empty($csvValidRowsWithWarnings)){
							$this->get('session')->set('rowsImportable', $csvRows);
							$this->get('session')->set('rowsNoImportable', $csvValidRowsWithWarnings);
							$competenceTypesForTemplate = null;
							
							if($createCompetenceTypesAutomatically == true && !empty($csvNewCompetenceTypesToCreate)){
								$competenceTypesForTemplate = $csvNewCompetenceTypesToCreate;
								$this->get('session')->set('newCompetenceTypes', $csvNewCompetenceTypesToCreate);
							}
							return $this->render(
									$entityname . ':uploadpreview.html.twig', array('skills' => $csvRows, 'errors' => $csvValidRowsWithWarnings, 'report' => '', 'competenceTypes' => $competenceTypesForTemplate));
						}else{
							$this->get('session')->getFlashBag()->add(
									'error', $tr->trans('skill.flash.csvnovalidlines', array(), 'EvalforGescompevalBundle'));
						}
					}
					else{
						foreach($csvGeneralErrors as $csvGeneralError){
							$this->get('session')->getFlashBag()->add(
									'error', $csvGeneralError);
						}
					}
				}
			}
		}
	
		return $this->render(
				$entityname . ':upload.html.twig', array('form' => $form->createView()));
	}
	
	/**
	 * Checks if $csv contains valid skills
	 * @param CsvController $csv
	 * @param string $entityname  
	 * @param bool $ignoreInvalidCompetenceTypes if set to true, the csvValidRows will contain the valid rows 
	 * 											 even if they are associated with a invalid Competence type 
	 * @return \stdClass with 3 array/properties: 	csvCritialErrors, csvValidRows, csvValidRowsWithWarnings, 
	 * 												csvInvalidInstitutions, csvInvalidCompetenceTypes 
	 */
	public function processCsv(csvClass $csv, $entityname, $ignoreInvalidCompetenceTypes = false){		
		$csvRows = $csv->getRows();
		$csvCriticalErrors = $csv->getGeneralErrors();
		$csvInvalidRows = $csv->getInvalidRows();
		$csvColumnHeaders = $csv->getColumnHeaders();

		//For checking duplicated rows
		$uniqueField = array();
	
		//Values that will be returned
		$validRows = array();
		$validRowsWithWarnings = array();
		$invalidCompetenceTypes = array();
		$invalidInstitutions = array();
	
		if(!empty($csvColumnHeaders)){
			$validHeaderFlag = true;
			
			$object = null;
			if($entityname == 'EvalforGescompevalBundle:Competency'){
				$className = "Evalfor\GescompevalBundle\Entity\Competency";
				$object = new Competency();
			}
			elseif($entityname == 'EvalforGescompevalBundle:Outcome'){
				$className = "Evalfor\GescompevalBundle\Entity\Outcome";
				$object = new Outcome();
			}
			
			$em = $this->getDoctrine()->getManager();
			//To improve performance, all data is collected and kept in arrays
			$objectsOfInstitution = array();
			$objectsOfCompetenceType = array();
			$objectsOfSkill = array();
			
			
			$skills = $em->getRepository($entityname)->findAll();
			foreach($skills as $skill){
				$code = inflector::homogenize($skill->code);
				$objectsOfSkill[$code] = $skill;
			}
			
			/*$institutions = $em->getRepository('EvalforGescompevalBundle:Institution')->findAll();
			foreach($institutions as $institution){
				$institutionName = inflector::homogenize($institution->name);
				$objectsOfInstitution[$institutionName] = $institution;
			}
			
			$competenceTypes = $em->getRepository('EvalforGescompevalBundle:CompetenceType')->findAll();
			foreach($competenceTypes as $competenceType){
				//'transversal' is the same than 'Transversal' and ' tránVersal '. To correct it:
				$type = inflector::homogenize($competenceType->type);
				$objectsOfCompetenceType[$type] = $competenceType;
			}*/
			
			$util = new utilClass($em);
			$objectsOfInstitution = $util->homogenize('EvalforGescompevalBundle:Institution', 'name');
			$objectsOfCompetenceType = $util->homogenize('EvalforGescompevalBundle:CompetenceType', 'type');
			
			/*Validations*/
			$headerErrors = $this->checkProperties($csv);
			$csvCriticalErrors = array_merge($csvCriticalErrors, $headerErrors);
				
			/*Processing*/
			$competencyProperties = utilClass::getObjectProperties($object);
				
			//For getting lengh constraint
			$metadata = $em->getClassMetadata($className);
	
			//For giving format to result
			$headerDiff = array_diff($competencyProperties, $csvColumnHeaders);
				
			//For checking the required fields
			$requiredProperties = Competency::getRequiredProperties();

			$i = 0;
			if(empty($headerErrors)){
				$auxiliarRow = array();
				
				//Processing each row
				foreach($csvRows as $keyrow => $row){
					$validRowFlag = true;
					$errorDescription = '';

					//Processing values of each row
					foreach($row as $key => $value){
						//Check the required fields of each row
						if(in_array($key, $requiredProperties)){
							if(empty($value)){
								$errorDescription .= '-' . $key . ' is a required field, therefore cannot be empty-';
								$validRowFlag = false;
							}
						}

						$homogenizeValue = inflector::homogenize($value);
						$trimValue = trim($value);

						if(isset($metadata->fieldMappings[$key])){
							$nameMetadata = $metadata->fieldMappings[$key];
								
							//Check length of values
							if(!empty($nameMetadata['length'])){
								if(strlen($trimValue) > $nameMetadata['length']){
									$errorDescription .= '-The value of the field "'.$key.'" must be less than '. $nameMetadata['length'] .' characters';
									$validRowFlag = false;
								}
							}
								
							/*//Check the unique constraint of values
								if(!empty($nameMetadata['unique'])){
								
							}*/
						}
	
						//Check the constraints for 'code' field
						if(strtolower($key) == 'code'){
							//$compObject = $em->getRepository($entityname)->findOneByCode($trimValue);
							//if(!empty($compObject)){
							if(!empty($objectsOfSkill[$homogenizeValue])){
								$errorDescription .= '-There is a Competenece in the system with de same Code. Code field must be unique-';
								$validRowFlag = false;
							}

							if(in_array($homogenizeValue, $uniqueField)){
								$errorDescription .= '-There is a Competenece in the CSV File with de same Code. Code field must be unique-';
								$validRowFlag = false;
							}
							else{
								$uniqueField[$keyrow] = $homogenizeValue;
							}
						}
						
						//Check the constraints for 'competencetype' field
						if(strtolower($key) == 'competencetype' && !empty($trimValue)){
							//'transversal' is the same than 'Transversal' and ' tránVersal '. To correct it:
							$type = inflector::homogenize($trimValue);
							$CTobject = null;
							if(empty($objectsOfCompetenceType[$type])){
								if($ignoreInvalidCompetenceTypes == false){
									$errorDescription .= '-Competence Type "'.$trimValue.'" does not exist in the System-';
									$validRowFlag = false;
								}
								if(!in_array($trimValue, $invalidCompetenceTypes)){
									array_push($invalidCompetenceTypes, $trimValue);
								}
							}
							else{
								$CTobject = $objectsOfCompetenceType[$type];
								$trimValue = $CTobject->type;
							}
						}
						//Check the constraints for 'institution' field
						if(strtolower($key) == 'institution' && !empty($trimValue)){
							$institutionName = inflector::homogenize($trimValue);
							$Iobject = null;
							if(empty($objectsOfInstitution[$institutionName])){
								$errorDescription .= '-Institution "'.$trimValue.'" does not exist in the System-';
								$validRowFlag = false;
								if(!in_array($trimValue, $invalidInstitutions)){
									array_push($invalidInstitutions, $trimValue);
								}
							}
							else{
								$Iobject = $objectsOfInstitution[$institutionName];
								$trimValue = $Iobject->name;
							}
						}

						$auxiliarRow[$i][$key] = $trimValue;
					}
						
					foreach($auxiliarRow[$i] as $validKey => $validValue){
						if($validRowFlag == false){
							$validRowsWithWarnings[$i][$validKey] = $validValue;
						}
						else{
							$validRows[$i][$validKey] = $validValue;
						}
					}
						
					//If the header is missing values will be added
					foreach($headerDiff as $missingValue){
						if($validRowFlag == false){
							$validRowsWithWarnings[$i][$missingValue] = '';
						}
						else{
							$validRows[$i][$missingValue] = '';
						}
					}
						
					if($validRowFlag == false){
						$validRowsWithWarnings[$i]['description'] = $errorDescription;
					}
					++$i;
				}
			}
		}
	
		$result = new \stdClass;
		$result->csvCriticalErrors = $csvCriticalErrors;
		$result->csvValidRows = $validRows;
		$result->csvValidRowsWithWarnings = $validRowsWithWarnings;
		$result->csvInvalidInstitutions = $invalidInstitutions;
		$result->csvInvalidCompetenceTypes = $invalidCompetenceTypes;

		return $result;
	}
	
	public function deleteid($skillId, $entityname, $routeRedirect, $paramOrder = '', $paramSearch = '', $page){
		$tr = $this->get('translator');
		if($skillId){
			// Process datas to delete the element from DB
			$em = $this->getDoctrine()->getManager();
			$skill_to_delete = $em->getRepository($entityname)->find($skillId);
			
			if(!empty($skill_to_delete)){
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
			return $this->redirect($this->generateURL($routeRedirect, array('page' => $page, 'order' => $paramOrder, 'search' => $paramSearch)));
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
	
	public function create($skill, $form, $entityname, $routeredirect)
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
				/*Code added by Daniel Cabeza
				 * START
				 */
				//$em->persist($skill);
				//$em->flush();

				$tr = $this->get('translator');
				//$this->get('session')->getFlashBag()->add(
//						'notice', $tr->trans('skill.flash.created', array(), 'EvalforGescompevalBundle'));
				
				$util = new utilClass($em);
				if($util->exist($skill, 'code',$entityname) == false){
					$em->persist($skill);
					$em->flush();
					$this->get('session')->getFlashBag()->add(
						'notice', $tr->trans('skill.flash.created', array(), 'EvalforGescompevalBundle'));
				}
				else{
					$this->get('session')->getFlashBag()->add(
						'error', $tr->trans('skill.flash.usedcode', array(), 'EvalforGescompevalBundle'));
					$redirect = 'EGB_create_outcome';
					if($entityname == 'EvalforGescompevalBundle:Competency'){
						$redirect = 'EGB_create_competency';
					}
					return $this->redirect($this->generateURL($redirect));
				}
				
				/*Code added by Daniel Cabeza
				 * END
				 */
				// Make a redirection because if the user try to refresh, the browser
				// would warn about send the datas again. Redirect to the same page
				return $this->redirect($this->generateURL($routeredirect));
			}
		}
		return $this->render($entityname.':create.html.twig', array('form' => $form->createView()));
	}

	public function update($skill, $form, $entityname, $routeredirect)
	{
		// Obtain all elements of the suitable type from DB
		$em = $this->getDoctrine()->getManager();
		$skills_all = $em->getRepository($entityname)->findAll();

		// Obtain request that contains the datas
		$request = $this->getRequest();

		$tr = $this->get('translator');

		// If request has been invoked by POST, process the form
		if($request->getMethod() == 'POST'){

			// Obtain the element to update
			$formdata = $request->request->get($form->getName());
			$id = $formdata['id'];
			$code = $formdata['code'];
			$skill = $em->getRepository($entityname)->find($id);
			/*Code added by Daniel Cabeza
			 * START
			 */
			//$skill_aux = $em->getRepository($entityname)->findOneByCode($code);
			
			// Check if there isn't another skill with the introduced code

			//if($id && $skill_aux && $skill->id != $skill_aux->id){
			//Check code. Code name must be unique
			//First, check if Name field has been modified.
			$skillExists = false;
			$homogenizeFormCode = \Evalfor\GescompevalBundle\Utils\inflector::homogenize($code);
			$homogenizeCode = \Evalfor\GescompevalBundle\Utils\inflector::homogenize($skill->code);
			if($homogenizeFormCode !== $homogenizeCode){
				$object = new Competency();
				if (get_class($skill) == 'Evalfor\GescompevalBundle\Entity\Outcome'){
					$object = new Outcome();
				}
				$object->code = $code;
					
				$util = new utilClass($em);
				if($util->exist($object, 'code', $entityname)){
					$skillExists = true;
				}
			}
			
			if($id && $skillExists){
			/*Code added by Daniel Cabeza
			 * END
			 */
				$this->get('session')->getFlashBag()->add('error',$tr->trans('skill.flash.usedcode', array(), 'EvalforGescompevalBundle'));
			}
			else{
				// Load form's datas into $skill
				if (get_class($skill) == 'Evalfor\GescompevalBundle\Entity\Competency'){
					$form = $this->createForm(new CompetencyType(), $skill);
				}
				else{
					$form = $this->createForm(new OutcomeType(), $skill);
				}

				$form->bind($request);

				if($form->isValid() && $skill){
					$em->flush();

					$this->get('session')->getFlashBag()->add(
							'notice', $tr->trans('skill.flash.updated', array(), 'EvalforGescompevalBundle'));

					// Make a redirection because if the user try to refresh, the browser
					// would warn about send the datas again. Redirect to the same page
					return $this->redirect($this->generateURL($routeredirect));
				}
				// If form is not correct, indicate it
				else{
					$this->get('session')->getFlashBag()->add('error', $tr->trans('skill.flash.error', array(), 'EvalforGescompevalBundle'));
				}
			}
		}

		return $this->render($entityname.':update.html.twig', array('form' => $form->createView(), 'skills_all' => $skills_all));
	}

	public function delete($skill, $form, $entityname, $routeredirect)
	{
		// Obtain all elements of the suitable type from DB
		$em = $this->getDoctrine()->getManager();
		$skills_all = $em->getRepository($entityname)->findAll();

		// Obtain request that contains the datas
		$request = $this->getRequest();

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
				$skill_to_delete = $em->getRepository($entityname)->find($skill->id);
				$em->remove($skill_to_delete);
				$em->flush();

				$this->get('session')->getFlashBag()->add(
						'notice', $tr->trans('skill.flash.deleted', array(), 'EvalforGescompevalBundle'));

				// Make a redirection because if the user try to refresh, the browser
				// would warn about send the datas again. Redirect to the same page
				return $this->redirect($this->generateURL($routeredirect));
			}
			// If form is not correct, indicate it
			else{
				$this->get('session')->getFlashBag()->add(
						'error', $tr->trans('skill.flash.error', array(), 'EvalforGescompevalBundle'));
			}
		}
		return $this->render($entityname.':delete.html.twig', array('form' => $form->createView(),
				 'skills_all' => $skills_all, 'form_name' => $form->getName()));
	}
	
}