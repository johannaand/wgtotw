<?php
namespace Jovis\HTMLForm;
/**
 * Anax base class for wrapping sessions.
 *
 */
class CFormUpdatePwd extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;
        
    private $users;
        
    /**
     * Constructor
     *
     */
    public function __construct($uid)
    {
		parent::__construct([], [
            'uid' => [
                'type'        => 'hidden',
                'required'    => true,
                'value'       => $uid,
                'validation'  => ['not_empty'],
            ],
            'Lösenord' => [
                'type'        => 'password',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'Upprepa_lösenord' => [
                'type'        => 'password',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'Spara' => [
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
        $users = new \Jovis\User\User();
        $users->setDI($this->di);
        
        $losen1 = $this->Value('Lösenord');
        $losen2 = $this->Value('Upprepa_lösenord'); 
        
        if ($losen1!=$losen2){
			return false;
		}
		else {
      
			$saved = $users->save([
				'uid'        => $this->Value('uid'),
				'pwd'      => password_hash($losen1, PASSWORD_DEFAULT),
			]);
			
			return $saved ? true : false;
		}
    }
    
   public function callbackRegret()
   {
	   $this->redirectTo('question/user/'.$this->Value('uid'));
   }
    
    /**
     * Callback What to do if the form was submitted?
     *
     */
    public function callbackSuccess()
    {
       $this->redirectTo('question/user/'.$this->Value('uid')."/losenuppdaterat");
    }
    /**
     * Callback What to do when form could not be processed?
     *
     */
    public function callbackFail()
    {
		$this->AddOutput("<p><i>Lösenorden matchar inte. Försök igen.</i></p>");
        header("Location: " . $_SERVER['PHP_SELF']);
    }
}
