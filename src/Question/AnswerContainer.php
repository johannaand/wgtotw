<?php

namespace Jovis\Question;
 
/**
 * En behållare för alla frågor och dess svar och kommentarer
 *
 */
class AnswerContainer
{
	private $id;
	private $content;
	private $created;
	private $uid;
	private $qid;
	
	private $Comments; //innehåller kommentarsobjekten Acomment
	
	 public function __construct($id, $content, $created, $uid, $qid)
    {
        $this->id = $id;
        $this->content = $content;
        $this->created = $created;
        $this->uid = $uid;
        $this->qid = $qid;
    }

	public function addComments($comments)
	{
		$this->Comments = $comments;
	}
}

?>
