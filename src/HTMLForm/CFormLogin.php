<?php
namespace Jovis\HTMLForm;
/**
 * Anax base class for wrapping sessions.
 *
 */
class CFormLogin extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;
        
        
    /**
     * Constructor
     *
     */
    public function __construct()
    {
      
       parent::__construct([], [
            'Användarnamn' => [
                'type'        => 'text',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'Lösenord' => [
                'type'        => 'password', 
                'required'    => true,
                
            ],
            'Logga_In' => [
                'type'      => 'submit',
                'callback'  => [$this, 'callbackSave'],
            ],
            
            'Ångra' => [
                'type'      => 'submit',
                'callback'  => [$this, 'callbackRegret'],
            ],
        ]);        
    }

    /**
     * Customise the check() method.
     *
     * @param callable $callIfSuccess handler to call if function returns true.
     * @param callable $callIfFail    handler to call if function returns true.
     */
    public function check($callIfSuccess = null, $callIfFail = null)
    {
        return parent::check([$this, 'callbackSuccess'], [$this, 'callbackFail']);
    }
    /**
     * Callback for save-button.
     *
     */
    public function callbackSave()
    {    
		$userSession = new \Jovis\User\UserSession();
		$userSession->setDI($this->di);
		
		$nic = $this->Value('Användarnamn');
		
		$pwd = $this->Value('Lösenord');
		
        $loggedIn = $userSession->Login($nic, $pwd);
                        
        return $loggedIn ? true : false;
    }
    
    
    
    public function callbackRegret()
    {
		if (isset($_SESSION['lastpage'])) {
			$lastpage = $_SESSION['lastpage'];
			$this->redirectTo($lastpage);
		}	
		else
			$this->redirectTo('');
    }
    
      
    /**
     * Callback What to do if the form was submitted?
     *
     */
    public function callbackSuccess()
    {
       if (isset($_SESSION['lastpage'])) {
			$lastpage = $_SESSION['lastpage'];
			//echo $lastpage;
			$this->redirectTo($lastpage);
		}	
		else
			$this->redirectTo('');
    }
    /**
     * Callback What to do when form could not be processed?
     *
     */
    public function callbackFail()
    {
        $this->AddOutput("<p><i>Fel användarnamn eller lösenord</i></p>");
        header("Location: " . $_SERVER['PHP_SELF']);
    }
}
