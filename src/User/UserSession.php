<?php

namespace Jovis\User;

class UserSession implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;
  
/**
* loggar in användaren om användare och lösenord stämmer.
*
*/
  public function Login($nic, $pwd) {
	    
    $user = new \Jovis\User\User();
    $user->setDI($this->di);
    
    $res =  $user->findtoLogin($nic, $pwd);
    
    if(isset($res)AND(!($res==false))) { // om användaren finns
/*		echo $res->pwd;
		echo "-----" . strlen($res->pwd);
		exit();*/
		if (password_verify($pwd, $res->pwd)){ //om lösenordet stämmer
		  //$_SESSION['user'] = $res;
		  $_SESSION['user'] = (object)[
			  'nic' => $res->nic,
			  'fname' => $res->fname,
			  'uid' => $res->uid
			];
		  $success = true;
		}
		else
		$success = false; //löseordet verifierade inte
	}
    else $success = false;
    //header('Location: login.php');
            
    return $success;
  }
  
  
  /**
  * loggar ut användaren.
  *
  */
  public function Logout() {
    // Logout the user
	unset($_SESSION['user']);
	$lastpage = $_SESSION['lastpage'];
	
	$url = $this->di->url->create($lastpage);
	
	$this->response->redirect($url);
  }
  
  /**
  * returnerar true om användaren är inloggad, annars false.
  *
  */
  public static function IsAuthenticated() {
      // Check if user is authenticated.
    if (isset($_SESSION['user']))
      return true;  
    else
      return false;
  }
  
  
  /**
  * returnera användarens akronym.
  *
  */
  public static function GetNic(){
    return $_SESSION['user']->nic;
  }
    
  public static function GetName(){
    return $_SESSION['user']->fname;
  }
  
  public static function GetId(){
    return $_SESSION['user']->uid;
  }
}
