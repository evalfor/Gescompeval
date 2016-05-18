<?php

namespace Evalfor\GescompevalBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CompetencyType extends SkillType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$this->buildSkillForm($builder);

		$builder->add('competencetype', 'entity', array(
						'class' => 'EvalforGescompevalBundle:CompetenceType',
						'property' => 'type',
						'label' => 'form.associatedtype',
						'empty_value' => 'form.noassociatedtype',
						'required' => false,
						'translation_domain' => 'EvalforGescompevalBundle',
						'attr' => array('class'=>'selectpicker', 'data-style' => 'btn-primary'),
						'multiple' => false));
	}

	public function getName()
	{
		return 'CompetencyForm';
	}
}