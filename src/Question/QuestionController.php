<?php

namespace Jovis\Question;
 
/**
 * A controller for handling questions.
 *
 */
class QuestionController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;
    
  
    /**
   * Initialize the controller.
   *
   * @return void
   */
  public function initialize()
  {
      $this->question = new \Jovis\Question\Question();
      $this->question->setDI($this->di);
  }
  
  
  /** Creates content for the first page 
   *  Förstasidan skall ge en översikt av senaste frågor tillsammans med de mest populära taggarna och de mest aktiva användarna.
   * 
   * @return void
   */
  
	public function createFirstpageAction()
	{
		$this->initialize();
		$latestQ = $this->question->findNewest(); 	
		
		$this->theme->setTitle("Hem");
		
		$arrQuestions = $this->listQuestions($latestQ);
		
		$this->views->add('question/list-newest', [
          'title' => "Frågor",
          'questions' => $arrQuestions
		]);	
				
		$this->findMostActive();
	}
	
	
	public function findMostActive($limit = 4)
	{
		
		$this->db->select("COUNT('wgt_question.uid') as Antal, wgt_question.uid, wgt_user.fname, wgt_user.lname, wgt_user.email, wgt_user.nic, wgt_user.created")
			->from ('question')
			->join('user', 'wgt_question.uid=wgt_user.uid')
			->groupby('wgt_question.uid, wgt_user.fname, wgt_user.lname, wgt_user.email, wgt_user.nic, wgt_user.created')
			->orderby('Antal DESC')
			->limit($limit); 
			
		$this->db->execute();
		$this->db->setFetchModeClass(__CLASS__);
		$arrusers = $this->db->fetchAll();
		
		$this->views->add('users/list-active', [
          'title' => "Användare",
          'users' => $arrusers
		]);	
	}

	public function listQuestions($questions)
	{
		foreach ($questions as $question) {	

			$q = $question->getProperties();
			
			//skapa frågecontainer
			$qContainer = new \Jovis\Question\QuestionContainer($q['qid'], $q['title'], $q['content'], $q['created'], $q['updated'], $q['uid']);
			$qContainer->setDI($this->di);
			$qContainer->addAnswers();
			//lägg till taggarna
			$qContainer->addTags();
			$qContainer->findUserNic();
			
			$arrQuestions[] = $qContainer;
		}      
		
		return $arrQuestions;
	}
    
    
   /**
   * Create array of questions and their tags
   *
   * @return void
   */
  public function listAllAction($info=null)
  {
		$this->initialize();
		
		$all = $this->question->findAll();
		
		$arrQuestions = $this->listQuestions($all);
		
		$this->views->add('question/list-all', [
          'title' => "Frågor",
          'questions' => $arrQuestions
		]);	
	}
  
  /**
   * Create questionContainer with question with question id and its answers, comments and tags
   *
   * @param int $id of question to display
   *
   * @return void
   */

	public function idAction($id)
	{	
		$_SESSION['lastpage'] = 'question/id/'. $id; 
		$this->initialize();	
		$q = $this->question->find($id);

		$qContainer = $this->createQContainer($q);

		$this->theme->setTitle("Fråga");
		$this->views->add('question/show-question', [
			'questionContainer' => $qContainer
		]);
		
		//visa formulär för att lägga in ett svar till frågan
		
		if(\Jovis\User\UserSession::IsAuthenticated()){
			$this->newAnswerAction($id);
		}
		else
		{
			$this->views->add('question/generic', [
			'content' => "Logga in för att svara på frågan"
			]);
		}
			
			
	}
	
	public function createQCOntainer($question)
	{
		$q = $question->getProperties();
				
		//skapa frågecontainer
		$qContainer = new \Jovis\Question\QuestionContainer($q['qid'], $q['title'], $q['content'], $q['created'], $q['updated'], $q['uid']);
		$qContainer->setDI($this->di);
		
		//hitta frågornas svar
		$qContainer->addAnswers();
		
		//hitta och lägg till kommentarerna till frågan
		$qContainer->addComments();	
		
		//lägg till kommentarer till frågan
		$qContainer->addTags();
		$qContainer->findUserNic();
		
		return $qContainer;
	}
  
	/**
   * Adds an new answer till a questions. Uses form CFormAddAnswer
   * 
   * @param int $qid , id of the question of which the answer belongs
   *
   * @return void
   */
	public function newAnswerAction($qid)
	{
		$form = new \Jovis\HTMLForm\CFormAddAnswer($qid);
		$form->setDI($this->di);
			
		//Om check har ett värde har formuläret postats och sidan körs vidare via form-objektet, 
		//om check är tom fortsätter den här 
		//funktionen köras och formuläret visas. 
		  
		$form->check();
			
		$this->di->views->add('question/add', [
		 'title' => "Lägg till svar:",
		 'content' => $form->getHTML(),
		]);
			
			
	}
  
  /**
   * Add new Question. Uses form CFormAddQuestion
   *
   * @return void
   */
  public function newQuestionAction()
  {
	  $_SESSION['lastpage'] = 'question/new-question'; 
      $this->initialize();
        
      if(\Jovis\User\UserSession::IsAuthenticated()) { 
               
		  $form = new \Jovis\HTMLForm\CFormAddQuestion();
		  $form->setDI($this->di);
			
		  //Om check har ett värde har formuläret postats och sidan körs vidare via form-objektet, 
		  //om check är tom fortsätter den här 
		  //funktionen köras och formuläret visas. 
		  
		  $form->check();
		  
		  $this->di->theme->setTitle("Lägg till fråga");
		  $this->di->views->add('question/add', [
			  'title' => "Lägg till fråga:",
			  'content' => $form->getHTML(),
			]);

			
	  }
	  else
	  {
		  $this->di->theme->setTitle("Lägg till fråga");
		  $this->di->views->add('question/add', [
			  'title' => "Lägg till fråga:",
			  'content' => "Logga in för att kunna ställa en fråga",
			]);
	  }
  }
  
  
  /**
   * List user with id.
   *
   * @param int $id of question to display
   *
   * @return void
   */

	public function userAction($id, $updated=false)
	{
		$_SESSION['lastpage'] = 'question/user/' . $id . "/" . $updated ; 
		$this->initialize();
		
		$muser = new \Jovis\User\User();
		$muser->setDI($this->di);
		
		$user = $muser->find($id);
		
		$uQ = $this->question->finduid($id);
		
		if($updated == 'losenuppdaterat'){
			$updatedinfo = 'Lösenordet har uppdaterats';
		}
		else if($updated == 'infouppdaterat'){
			$updatedinfo = "Användarinformation har uppdaterats";
		}
		else
			$updatedinfo = false;
	
		if(!empty($uQ)){
			$isset = TRUE;
		
			foreach ($uQ as $q){
				$qContainer = $this->createQCOntainer($q);
				$arrQuestions[] = $qContainer;
			}
			
			$this->di->theme->setTitle("Användare");
			
			$this->di->views->add('users/userinfo', [
			  'user' => $user,
			  'questions' => $arrQuestions,
			  'isset' => TRUE, 
			  'updatedinfo' => $updatedinfo
			]);	
			
		} else {	
			$this->di->theme->setTitle("Användare");
			
			$this->di->views->add('users/userinfo', [
			  'user' => $user,
			  'questions' => null,
			  'isset' => FALSE,
			  'updatedinfo' => $updatedinfo 
			]);	
		}

	}
	
	/**
   * List all questions.
   *
   * @return void
   */
  public function listUserAction($info=null)
  {
		$user = new \Jovis\User\User();
		$user->setDI($this->di);	
		
		$all = $user->findAll();
		$_SESSION['lastpage'] = 'question/list_user'; 
		$this->theme->setTitle("Användare");
		$this->views->add('users/list-all', [
          'title' => "Användare",
          'users' => $all
		]);
	}	
	
	
	public function listTagAction()
	{
		$tag = new \Jovis\Question\Tag();
		$tag->setDI($this->di);
		
		$tags = $tag->findAll();
		
		$_SESSION['lastpage'] = 'question/list-tag'; 
		$this->theme->setTitle("Användare");
		$this->views->add('question/list-tags', [
          'title' => "Tags",
          'tags' => $tags
		]);
	}
	
	public function tagAction($tid, $tname)
	{
		$qtag = new \Jovis\Question\Qtag();
		$qtag->setDI($this->di);
		
		$_SESSION['lastpage'] = 'question/tag/' . $tid . "/" . $tname; 
		
		$questionstag = $qtag->findQuestions($tid);
		

		
		foreach($questionstag as $qt){
			$qtagp = $qt->getProperties();
			$qid = $qtagp['qid'];
			
			$question = new \Jovis\Question\Question();
			$question->setDI($this->di);
			
			$question = $question->find($qid); 
			
			$qContainer = $this->createQContainer($question);
			$arrQuestions[] = $qContainer;
		}
		
		$_SESSION['lastpage'] = 'question/tag/' . $tid; 
		$this->theme->setTitle("Användare");
		$this->views->add('question/tag', [
          'tag' => $tname,
          'questions' => $arrQuestions
		]);		
	}
}
