<?php

namespace Evalfor\GescompevalBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SkillType extends AbstractType
{
	public function buildSkillForm(FormBuilderInterface $builder){
		$builder->add('code', null, array(
						'label' => 'form.code',
						'translation_domain' => 'EvalforGescompevalBundle',
						'attr' => array('class'=>'form-control-m')))
				->add('shortdescription', null, array(
						'label' => 'form.shortdescription',
						'translation_domain' => 'EvalforGescompevalBundle',
						'attr' => array('class'=>'form-control')))
				->add('longdescription', null, array(
						'label' => 'form.longdescription',
						'translation_domain' => 'EvalforGescompevalBundle',
						'attr' => array('class'=>'form-control')))
				->add('institution', 'entity', array(
						'class' => 'EvalforGescompevalBundle:Institution',
						'property' => 'name',
						'label' => 'form.institution',
						'empty_value' => 'form.noinstitution',
						'required' => false,
						'translation_domain' => 'EvalforGescompevalBundle',
						'attr' => array('class'=>'selectpicker', 'data-style' => 'btn-primary'),
						'multiple' => false))
				->add('id', 'hidden');
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$this->buildSkillForm($builder);
	}

	public function getName()
	{
		return 'SkillForm';
	}
}