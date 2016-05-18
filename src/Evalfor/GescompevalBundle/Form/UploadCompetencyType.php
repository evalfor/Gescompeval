<?php
/**
 * @package    EvalforGescompevalBundle
 * @copyright  2010 onwards EVALfor Research Group {@link http://evalfor.net/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Daniel Cabeza Sánchez <daniel.cabeza@uca.es>
 */
namespace Evalfor\GescompevalBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UploadCompetencyType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('file', 'file', array(
						'label' => 'skill.file',
						'label_attr' => array('class' => 'control-label'),
						'translation_domain' => 'EvalforGescompevalBundle',
						'attr' => array('accept' => '.csv', 'class' => 'form-control')))
				->add('delimiter', 'choice', array(
						'label' => 'skill.delimiter',
						'label_attr' => array('class' => 'control-label'),
						'translation_domain' => 'EvalforGescompevalBundle',
						'choices' => array('cm' => ',', 'sc' => ';', 'cl' => ':'),
						'attr' => array('class' => 'selectpicker', 'style' => 'font-size:2em')
				))	
				->add('competencetype', 'choice', array(
						'label' => 'competency.upload.choiceCompetenceType',
						'label_attr' => array('class' => 'control-label'),
						'translation_domain' => 'EvalforGescompevalBundle',
						'choices' => array('0' => 'competency.upload.choiceNothingCompetenceType', 'ct_auto' => 'competency.upload.choiceCreateCompetenceType'),
						'attr' => array('class' => 'selectpicker')
				))
				->add('submit', 'submit', array(
						'label' => 'skill.upload',
						'translation_domain' => 'EvalforGescompevalBundle'						
						));
	}

	public function getName()
	{
		return 'UploadCompetencyForm';
	}
}