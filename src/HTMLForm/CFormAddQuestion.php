<?php
namespace Jovis\HTMLForm;
/**
 * Anax base class for wrapping sessions.
 *
 */
class CFormAddQuestion extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;
        
    private $users;
        
    /**
     * Constructor
     *
     */
    public function __construct()
    {
      
       parent::__construct([], [
           'Titel' => [
                'type'        => 'text',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'Text' => [
                'type'        => 'text',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'Tag' => [
                'type'        => 'text',
            ],
            'Användarid' => [
                'type'        => 'text',
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
      $users = new \Jovis\Question\Question();
      $users->setDI($this->di);
      
      $now = gmdate('Y-m-d H:i:s');
      
         $saved = $users->save([
            'title'   => $this->Value('Titel'),
            'content' => $this->Value('Text'),
            'uid'     => $this->Value('Användarid'),
            'created' => $now, 
            'updated' => $now,
            ]);
         
         return $saved ? true : false;
    }
    
    
    
    public function callbackRegret()
    {
      $this->redirectTo('');
      //$this->url->create('question/list-all');	
    }
    
      
    /**
     * Callback What to do if the form was submitted?
     *
     */
    public function callbackSuccess()
    {
       $this->redirectTo('');
       //$this->url->create('question/list-all');
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
