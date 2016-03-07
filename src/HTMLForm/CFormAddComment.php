<?php
namespace Anax\HTMLForm;
/**
 * Anax base class for wrapping sessions.
 *
 */
class CFormAddComment extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;
        
    private $users;
        
    /**
     * Constructor
     *
     */
    public function __construct($fromPage)
    {
      
       parent::__construct([], [
           'fromPage' => [
                'type'        => 'hidden',
                'value'       => $fromPage,
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'Namn' => [
                'type'        => 'text',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'E-post' => [
                'type'        => 'text',
                'required'    => true,
                'validation'  => ['not_empty', 'email_adress'],
            ],
            'Innehåll' => [
                'type'        => 'textarea',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'Webbsida' => [
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
      $comments = new \Anax\Comments\Comment();
      $comments->setDI($this->di);
      
      $now = gmdate('Y-m-d H:i:s');
      
         $saved = $comments->save([
            'content'   => $this->Value('Innehåll'),
            'name'      => $this->Value('Namn'),
            'email'     => $this->Value('E-post'),
            'web'       => $this->Value('Webbsida'), 
            'timestamp' => $now, 
            'ip' => $_SERVER['REMOTE_ADDR'],
            'frompage' => $this->Value('fromPage')
            ]);
         
          return $saved ? true : false;
    }
    
    
    
    public function callbackRegret()
    {
      $this->redirectTo($this->Value('fromPage'));
    }
    
      
    /**
     * Callback What to do if the form was submitted?
     *
     */
    public function callbackSuccess()
    {
      $fromPage = $this->Value('fromPage');
       $this->redirectTo($fromPage);
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
