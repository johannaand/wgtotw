<?php

namespace Jovis\User;
 
/**
 * A controller for handling questions.
 *
 */
class UserController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;
    	
	public function loginAction()
	{		
		$form = new \Jovis\HTMLForm\CFormLogin();
		$form->setDI($this->di);
				
		//Om check har ett värde har formuläret postats och sidan körs vidare via form-objektet, 
		//om check är tom fortsätter den här 
		//funktionen köras och formuläret visas. 
		  
		$form->check();
			
		$this->di->views->add('users/login', [
		 'title' => "Logga in:",
		 'content' => $form->getHTML(),
		]);		
	}
	
	public function logoutAction()
	{	
		$userSession = new \Jovis\User\UserSession();
		$userSession->setDI($this->di);
		$userSession->Logout();
	}
	
	public function updateAction($id)
	{
		$user = new \Jovis\User\User();
		$user->setDI($this->di);
		
		$u = $user->find($id);
		
		$this->di->views->add('users/updatetop', [
		 'title' => "Uppdatera",
		 'user' => $u,
		]);
		
		$uprop = $u->getProperties();
		
		$form = new \Jovis\HTMLForm\CFormUpdateUser($uprop['uid'], $uprop['email'],	$uprop['fname'], $uprop['lname']);	
		$form->setDI($this->di);
				
		//Om check har ett värde har formuläret postats och sidan körs vidare via form-objektet, 
		//om check är tom fortsätter den här 
		//funktionen köras och formuläret visas. 
		  
		$form->check();
			
		$this->di->views->add('users/update', [
		 'title' => "Uppdatera",
		 'content' => $form->getHTML(),
		]);			
	}
	
	public function changePasswordAction($uid)
	{
		$user = new \Jovis\User\User();
		$user->setDI($this->di);
		
		$u = $user->find($uid);
				
		$uprop = $u->getProperties();
		
		$form = new \Jovis\HTMLForm\CFormUpdatePwd($uprop['uid']);	
		$form->setDI($this->di);
				
		//Om check har ett värde har formuläret postats och sidan körs vidare via form-objektet, 
		//om check är tom fortsätter den här 
		//funktionen köras och formuläret visas. 
		  
		$form->check();
			
		$this->di->views->add('users/update', [
		 'title' => "Ändra lösenord",
		 'content' => $form->getHTML(),
		]);
	}
}
