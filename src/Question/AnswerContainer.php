<?php

namespace Jovis\Question;
 
/**
 * En behållare för alla frågor och dess svar och kommentarer
 *
 */
class AnswerContainer implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

	private $id;
	private $content;
	private $created;
	private $uid;
	private $qid;
	
	private $comments; //array som innehåller kommentarsobjekten Acomment
	
	public function __construct($id, $content, $created, $uid, $qid)
    {
        $this->id = $id;
        $this->content = $content;
        $this->created = $created;
        $this->uid = $uid;
        $this->qid = $qid;
    }

	public function addComments()
	{
		$acomments = new \Jovis\Question\Acomment();
		$acomments->setDI($this->di);
						
		$comments = $acomments->find($this->id);
				
		$this->comments = $comments;
	}
	
	/**
	* Get object properties.
	*
	* @return array with object properties.
	*/
	public function getProperties()
	{
		$properties = get_object_vars($this);
   
		return $properties;
	}
	
	public function getComments()
	{
		return $this->comments;
	}
}

?>
