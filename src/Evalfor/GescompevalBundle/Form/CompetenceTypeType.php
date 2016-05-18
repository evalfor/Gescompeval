<?php

namespace Evalfor\GescompevalBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CompetenceTypeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder->add('type', null, array(
    			'label' => 'form.type',
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
    	return 'CompetenceTypeForm';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    /*public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Evalfor\GescompevalBundle\Entity\CompetenceType'
        ));
    }*/
}
