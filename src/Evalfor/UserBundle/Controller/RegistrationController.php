<?php

namespace Evalfor\UserBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Controller\RegistrationController as BaseController;


class RegistrationController extends BaseController
{
    public function registerAction()
    {
    	$form = $this->container->get('fos_user.registration.form');
    	$formHandler = $this->container->get('fos_user.registration.form.handler');
    	$confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');

        $process = $formHandler->process($confirmationEnabled);

        if ($process) {
            $user = $form->getData();

            if ($confirmationEnabled) {
                $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                $route = 'fos_user_registration_check_email';
            } else {
                $route = 'fos_user_registration_confirmed';
            }

            $this->setFlash('fos_user_success', 'registration.flash.user_created');
            $url = $this->container->get('router')->generate($route);
            $response = new RedirectResponse($url);

            return $response;
        }

        // Send crsf token because a user could login in the menu form
        $csrfToken = $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate');
        return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:register.html.'.$this->getEngine(), array(
            'form' => $form->createView(), 'csrf_token' => $csrfToken
        ));
    }

    // Para actualizar datos de usuario
    // TODO
    /*public function editAction()
    {
    	// Enviamos una opción para incluir en el formulario un desplegable con todos los usuarios
    	$form = $this->container->get('fos_user.registration.form', array('users' => 'yes'));
    	$formHandler = $this->container->get('fos_user.registration.form.handler');

    	$request = $this->container->get('request');

    	// Obtenemos el usuario a modificar si se han recibido datos por POST
    	if($request->getMethod() == 'POST'){
    		$formdata = $request->request->get('fos_user_registration_form');
    		$userManager = $this->container->get('fos_user.user_manager');
    		$user = $userManager->findUserBy(array('id' => $formdata['id']));

    		// Actualizamos todos los campos
    		//$user->setUsername($formdata['username']);

    		//$userManager->updateUser($user, false);
			//$this->getDoctrine()->getEntityManager()->flush();

    		//exit;
    		$process = $formHandler->process($user);
    		$confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');
    		$process = $formHandler->process($confirmationEnabled);

    		$this->setFlash('fos_user_success', 'registration.flash.user_created');
    		//$url = $this->container->get('router')->generate($route);
    		//$response = new RedirectResponse($url);

    		//return $response;

    	}

    	return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:edit.html.'.$this->getEngine(), array(
    	'form' => $form->createView(),
    	));
    }*/
}
