<?php

/*
 * This file is an extend of the FOSUserBundle package.
 */

namespace Evalfor\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Doctrine\ORM\EntityRepository;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	// Si recibimos opciones cargamos el desplegable
    	/*if($options != null){
    		$builder->add('id', 'entity', array(
    				'label' => 'Seleccione un usuario: ',
    				'required' => true,
    				'attr' => array('class'=>'selectpicker', 'data-style' => 'btn-primary'),
    				'class' => 'Evalfor\UserBundle\Entity\User',
    				'query_builder' => function(EntityRepository $er) use($options) {
    					return $er->createQueryBuilder('u');
    				}
    		));
    	}*/

        $builder
            ->add('username', null, array(
            		'label' => 'form.username', 'translation_domain' => 'FOSUserBundle',
            		'attr' => array('class'=>'form-control-m')))
            ->add('email', 'email', array('label' => 'form.email',
            		'attr' => array('class'=>'form-control-m'),
            		'translation_domain' => 'FOSUserBundle'))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array(
                		'label' => 'form.password', 'attr' => array('class'=>'form-control-m')),
                'second_options' => array(
                		'label' => 'form.password_confirmation',
            			'attr' => array('class'=>'form-control-m')),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
        ;

        // Para seleccionar el rol del usuario
        $builder->add('roles', 'choice', array(
        		'label' => 'Rol: ',
        		'required' => false,
        		'choices' => array(1 => 'Administrador', 2 => 'Usuario'),
        		'attr' => array('class'=>'selectpicker', 'data-style' => 'btn-primary'),
        		'multiple' => true)
        );
    }

    /*public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	$resolver->setDefaults(array(
    			'data_class' => 'Evalfor\UserBundle\Entity\User',
    			'intention'  => 'registration',
    			'users' => null
    	));
    }*/

    public function getName()
    {
        return 'evalfor_user_registration';
    }
}
