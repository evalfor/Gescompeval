<?php

namespace Evalfor\GescompevalBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class OutcomeType extends SkillType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$this->buildSkillForm($builder);
	}

	public function getName()
	{
		return 'OutcomeForm';
	}
}