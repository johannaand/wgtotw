<?php
namespace Anax\HTMLForm;
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
    public function __construct($id, $acronym, $epost, $namn, $deleted=null)
    {
      
      if (!isset($deleted)) {
        parent::__construct([], [
            'id' => [
                'type'        => 'hidden',
                'required'    => true,
                'value'       => $id,
                'validation'  => ['not_empty'],
            ],
            'Acronym' => [
                'type'        => 'text',
                'required'    => true,
                'value'       => $acronym,
                'validation'  => ['not_empty'],
            ],
            'E-post' => [
                'type'        => 'text',
                'required'    => true,
                'value'       => $epost,
                'validation'  => ['not_empty', 'email_adress'],
            ],
            'Namn' => [
                'type'        => 'text',
                'required'    => true,
                'value'       => $namn,
                'validation'  => ['not_empty'],
            ],
            'Spara' => [
                'type'      => 'submit',
                'callback'  => [$this, 'callbackSave'],
            ],
            'Ändra-status' => [
                'type'      => 'submit',
                'callback'  => [$this, 'callbackChangeStatus'],
            ],
            'lägg-i-papperskorg' => [
                'type'      => 'submit',
                'callback'  => [$this, 'callbackSoftDelete'],
            ],
             'ta-bort-permanent' => [
                'type'      => 'submit',
                'callback'  => [$this, 'callbackDelete'],
            ],
        ]);
       }
        else
        {
          parent::__construct([], [
            'id' => [
                'type'        => 'hidden',
                'required'    => true,
                'value'       => $id,
                'validation'  => ['not_empty'],
            ],
            'Acronym' => [
                'type'        => 'text',
                'required'    => true,
                'value'       => $acronym,
                'validation'  => ['not_empty'],
            ],
            'E-post' => [
                'type'        => 'text',
                'required'    => true,
                'value'       => $epost,
                'validation'  => ['not_empty', 'email_adress'],
            ],
            'Namn' => [
                'type'        => 'text',
                'required'    => true,
                'value'       => $namn,
                'validation'  => ['not_empty'],
            ],
            'Spara' => [
                'type'      => 'submit',
                'callback'  => [$this, 'callbackSave'],
            ],
            'Ändra-status' => [
                'type'      => 'submit',
                'callback'  => [$this, 'callbackChangeStatus'],
            ],
            'ta-bort-från-papperskorgen' => [
                'type'      => 'submit',
                'callback'  => [$this, 'callbackUrPapperskorg'],
            ],
            'ta-bort-permanent' => [
                'type'      => 'submit',
                'callback'  => [$this, 'callbackDelete'],
            ],
            
        ]);               
      }
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
        $users = new \Anax\Users\User();
        $users->setDI($this->di);
      
         $saved = $users->save([
            'id'        => $this->Value('id'),
            'acronym'   => $this->Value('Acronym'),
            'name'      => $this->Value('Namn'),
            'email'     => $this->Value('E-post'),
            'password' => password_hash($this->Value('Acronym'), PASSWORD_DEFAULT), 
            'updated'   => gmdate('Y-m-d H:i:s'), 
            ]);
          return $saved ? true : false;
    }
    
    /**
     * Callback for changeStatus-button.
     *
     */
    public function callbackChangeStatus()
    {
       $id =  $this->Value('id');     
       $url = 'users/change-status/'.$id;
       $this->redirectTo($url);
    }
    
     /**
     * Callback for soft-delete-button.
     *
     */
    public function callbackSoftDelete()
    {
       $id =  $this->Value('id'); 
       $url = 'users/soft-delete/'.$id;
       $this->redirectTo($url);
    }
    
    /**
     * Callback for delete-button.
     *
     */
    public function callbackDelete()
    {
       $id =  $this->Value('id'); 
       $url = 'users/delete/'.$id;
       $this->redirectTo($url);
    }
    
    public function callbackUrPapperskorg()
    {
       $id = $this->Value('id');
       $url = 'users/undo-soft-delete/'.$id;
       $this->redirectTo($url);
    }
    
    /**
     * Callback What to do if the form was submitted?
     *
     */
    public function callbackSuccess()
    {
        $this->redirectTo('users/id/'.$this->Value('id'));
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
