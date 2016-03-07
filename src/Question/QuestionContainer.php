<?php

namespace Jovis\Question;
 
/**
 * En behållare för alla frågor och dess svar och kommentarer
 *
 */
class QuestionContainer
{
	private $id;
	private $content;
	private $created;
	private $updated;
	private $uid;
	
	private $arrAnswers; //array för att lagra svarsobjekten AnswerContainer
	private $Comments; //innehåller kommentarsobjekten Qcomment
	private $tags;
	
	 public function __construct($id, $content, $created, $updated, $uid)
    {
        $this->id = $id;
        $this->content = $content;
        $this->created = $created;
        $this->updated = $updated;
        $this->uid = $uid;
    }

	public function addAnswer($answer)
	{
		$this->arrAnswers[] = $answer;
	}
	
	public function addComments($comments)
	{
		$this->Comments = $comments;
	}
	
	public function addTag($tag)
	{
		$this->tags[] = $tag;
	}
}

?>
