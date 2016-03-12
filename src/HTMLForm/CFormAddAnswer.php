<?php
namespace Jovis\HTMLForm;
/**
 * Anax base class for wrapping sessions.
 *
 */
class CFormAddAnswer extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;
        
    private $users;
        
    /**
     * Constructor
     *
     */
    public function __construct($qid)
    {
      
       parent::__construct([], [
            'Text' => [
                'type'        => 'text',
                'required'    => true,
                'validation'  => ['not_empty'],
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
        $this->qid = $qid;
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
      $answer = new \Jovis\Question\Answer();
      $answer->setDI($this->di);
      
      $now = gmdate('Y-m-d H:i:s');
      
         $saved = $answer->save([
            'content'	=> $this->Value('Text'),
            'uid'     	=> $this->Value('Användarid'),
            'qid'		=> $this->qid,
            'created' 	=> $now, 
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
