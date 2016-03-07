<?php
namespace Anax\HTMLForm;
/**
 * Anax base class for wrapping sessions.
 *
 */
class CFormUpdateComment extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;
        
    private $users;
        
    /**
     * Constructor
     *
     */
    public function __construct($id, $name, $email, $content, $webpage, $fromPage)
    {
        parent::__construct([], [            
            'Id' => [
                'type'        => 'hidden',
                'value'       => $id,
                'required'    => true,
                'validation'  => ['not_empty'],  
            ],
            'fromPage' => [
                'type'        => 'hidden',
                'value'       => $fromPage,
                'required'    => true,
                'validation'  => ['not_empty'],  
            ],
            'Namn' => [
                'type'        => 'text',
                'value'       => $name,
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'E-post' => [
                'type'        => 'text',
                'value'       => $email,
                'required'    => true,
                'validation'  => ['not_empty', 'email_adress'],
            ],
            'Innehåll' => [
                'type'        => 'textarea',
                'value'       => $content,
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'Webbsida' => [
                'type'        => 'text',
                'value'       => $webpage,
            ],
            'Spara' => [
                'type'      => 'submit',
                'callback'  => [$this, 'callbackSave'],
            ],
            'Ta-bort' => [
                'type'      => 'submit',
                'callback'  => [$this, 'callbackDelete'],
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
      
         $saved = $comments->save([
            'id'        => $this->Value('Id'),
            'name'      => $this->Value('Namn'),
            'email'     => $this->Value('E-post'),
            'content'   => $this->Value('Innehåll'),
            'web'       => $this->Value('Webbsida'),
            ]);
          return $saved ? true : false;
    }
       
    /**
     * Callback for delete-button.
     *
     */
    public function callbackDelete()
    {
        $comments = new \Anax\Comments\Comment();
        $comments->setDI($this->di);

        $res = $comments->delete($this->Value('Id'));

        return $res ? true : false; 
    }
    
    public function callbackRegret()
    {
       //$id = $this->Value('id');
       //$url = 'comment/undo-soft-delete/'.$id;
       $fromPage = $this->Value('fromPage');
       $this->redirectTo($fromPage);
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
