<?php

namespace Jovis\Question;
 
/**
 * En behållare för alla frågor och dess svar och kommentarer
 *
 */
class QuestionContainer implements \Anax\DI\IInjectionAware
{
	use \Anax\DI\TInjectable;

	private $id;
	private $title;	
	private $content;
	private $created;
	private $updated;
	private $uid;
	private $userNic;
	
	private $arrAnswers; //array för att lagra svarsobjekten AnswerContainer
	private $comments; //innehåller kommentarsobjekten Qcomment
	private $tags;
	
	 public function __construct($id, $title, $content, $created, $updated, $uid)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->created = $created;
        $this->updated = $updated;
        $this->uid = $uid;
    }

	public function addComments()
	{
		$this->qcomment = new \Jovis\Question\Qcomment();
		$this->qcomment->setDI($this->di);
			
		$comments = $this->qcomment->find($this->id);
		
		$this->comments = $comments;
	}
	
	public function addAnswers()
	{			
		$manswer = new \Jovis\Question\Answer();
		$manswer->setDI($this->di);
			
		//hitta frågornas svar
		$answers = $manswer->find($this->id);
					
		if (!empty($answers))
		{
			//ta ut värden från svaren
			foreach ($answers as $key1=>$answer) {
				
				$a = $answer->getProperties();
				
				$aid = $a['aid'];
				
				if (isset($aid)) {	
					//skapa en svarsbehållare av svarets värden
					$aContainer = new \Jovis\Question\AnswerContainer($aid, $a['content'], $a['created'], $a['uid'], $a['qid']);
					$aContainer->setDI($this->di);
						
					//hitta svarets kommentarer
					$aContainer->addComments();
					
					//lägg till svaret till arrayen
					$this->arrAnswers[] = $aContainer;
				}
			}
		}
	}
	
	public function addTags()
	{

		$qt = new \Jovis\Question\Qtag();
		$qt->setDI($this->di);
		
		$qtags = $qt->find($this->id);
		
		
		if (!empty($qtags)) {
			foreach ($qtags as $key1=>$tags){
				
				$t = $tags->getProperties();
				$this->addTag($t['tid']);

			}
		}
	}
	
	public function addTag($tid)
	{
		$tag = new \Jovis\Question\Tag();
		$tag->setDI($this->di);
			
		$tagnames = $tag->findName($tid);
					
		foreach ($tagnames as $key1=>$tagname){ //hitta namnet för taggen
			
			$t = $tagname->getProperties();
			
			$this->tags[] = array($tid=>$t['name']); //lägg till namnet i tags - arrayen
		}			
	}
  
	
	public function getTags()
	{
		return $this->tags;
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
	
	public function findUserNic()
	{
		$user = new \Jovis\User\User();
		$user->setDI($this->di);
			
		$username = $user->findNic($this->uid);
			
		$u = $username->getProperties();
		$this->userNic = $u['nic'];
	}
	
	public function getAnswers()
	{
		return $this->arrAnswers;
	}
	
	public function getComments()
	{
		return $this->comments;
	}
}
