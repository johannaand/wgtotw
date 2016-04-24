<?php
namespace Jovis\HTMLForm;
/**
 * Anax base class for wrapping sessions.
 *
 */
class CFormUpdateUser extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;
        
    private $users;
        
    /**
     * Constructor
     *
     */
    public function __construct($uid, $email, $fname, $lname)
    {
		parent::__construct([], [
            'uid' => [
                'type'        => 'hidden',
                'required'    => true,
                'value'       => $uid,
                'validation'  => ['not_empty'],
            ],
            'E-post' => [
                'type'        => 'text',
                'required'    => true,
                'value'       => $email,
                'validation'  => ['not_empty', 'email_adress'],
            ],
            'Förnamn' => [
                'type'        => 'text',
                'required'    => true,
                'value'       => $fname,
                'validation'  => ['not_empty'],
            ],
            'Efternamn' => [
                'type'        => 'text',
                'required'    => true,
                'value'       => $lname,
                'validation'  => ['not_empty'],
            ],
            'Spara' => [
                'type'      => 'submit',
                'callback'  => [$this, 'callbackSave'],
            ],
            'Återställ' => [
                'type'      => 'submit',
                'callback'  => [$this, 'callbackRestore'],
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
      
        $saved = $users->save([
            'uid'        => $this->Value('uid'),
            'fname'      => $this->Value('Förnamn'),
            'lname'      => $this->Value('Efternamn'),
            'email'     => $this->Value('E-post'),
        ]);
        return $saved ? true : false;
    }
    
   public function callbackRestore()
   {
	   $this->redirectTo('user/update/'.$this->Value('uid'));
   }
    
    /**
     * Callback What to do if the form was submitted?
     *
     */
    public function callbackSuccess()
    {
       $this->redirectTo('question/user/'.$this->Value('uid') . "/infouppdaterat");
    }
    /**
     * Callback What to do when form could not be processed?
     *
     */
    public function callbackFail()
    {
        //$this->AddOutput("<p><i>Form was submitted and the Check() method returned false.</i></p>");
        $this->redirectTo();
    }
}
