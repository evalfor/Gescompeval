<?php

namespace Evalfor\GescompevalBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InstitutionType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('name', null, array(
						'label' => 'form.name',
						'translation_domain' => 'EvalforGescompevalBundle',
						'attr' => array('class'=>'form-control-m')))
				->add('description', null, array(
						'label' => 'form.description',
						'translation_domain' => 'EvalforGescompevalBundle',
						'attr' => array('class'=>'form-control')))
				->add('id', 'hidden');
	}

	public function getName()
	{
		return 'InstitutionForm';
	}
}