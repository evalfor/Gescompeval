<?php

namespace Evalfor\GescompevalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	//$request = $this->getRequest();
    	//$locale = $request->getPreferredLanguage(array('en', 'es'));
    	//$this->get('session')->set('_locale', $locale);

    	// If a language is requested, set it for the session
    	$request = $this->getRequest();
    	if($request->get('lang')){
    		$this->get('session')->set('_locale', $request->get('lang'));
    		return $this->redirect($this->generateURL('EGB_homepage'));
    	}

    	// Send crsf token because a user could login in the menu form
    	$csrfToken = $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate');
    	return $this->render('EvalforGescompevalBundle:Default:index.html.twig', array('csrf_token' => $csrfToken));
    }

    public function helpAction()
    {
    	// Send crsf token because a user could login in the menu form
    	$csrfToken = $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate');
    	return $this->render('EvalforGescompevalBundle:Default:help.html.twig', array('csrf_token' => $csrfToken));
    }
}
