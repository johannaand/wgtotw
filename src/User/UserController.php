<?php

namespace Jovis\User;
 
/**
 * A controller for handling questions.
 *
 */
class UserController implements \Anax\DI\IInjectionAware
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
      
      $this->answer = new \Jovis\Question\Answer();
      $this->answer->setDI($this->di);
  }
    
    
   /**
   * List all questions.
   *
   * @return void
   */
  public function listAction($info=null)
  {
		$this->initialize();	
		$all = $this->user->findAll();
		$arrUsers;

		foreach ($all as $key1=>$value) {	

			foreach ($value as $key2=>$v){ //hitta värden i frågan
				switch ($key2) {
					case 'nic':
						 $nic = $v;
						 break;
					case 'fname':
						 $fname = $v;
						 break;
					case 'lname':
						 $lname = $v;
						 break;
					case 'email':
						 $email = $v;
						 break;
					case 'created':
						 $created = $v;
						 break;
					}
			}
			
			$arr = new array('nic'=$nic, 'fname'=>$fname, 'lname' => $lname, 'email' => $email, 'created' => $created);
			$arrUsers[] = $arr;		
		}        
   
		$this->theme->setTitle("Användare");
		$this->views->add('users/list-all', [
          'title' => "Användare",
          'arrUsers' => $arrUsers
		]);
	}
  
  
	public function addTags($qid)
	{

		$this->Qtag = new \Jovis\Question\Qtag();
		$this->Qtag->setDI($this->di);
		
		$qtags = $this->Qtag->find($qid);
		
		if (!empty($qtags)) {
			foreach ($qtags as $key1=>$values1){
				foreach ($values1 as $key2=>$values2){ //hitta värden i taggen
					switch ($key2) {
					   case 'qid':
						 $qid = $values2;
						 break;
					   case 'tid':
						 $tid = $values2;
						 $this->addTaggName($tid); //hitta och lägg till taggens namn
						 break;
					}
				}
			}
		}	
	}
	
	public function addTaggName($tid)
	{
		$this->Tag = new \Jovis\Question\Tag();
		$this->Tag->setDI($this->di);
			
		$tagname = $this->Tag->findName($tid);
					
		foreach ($tagname as $key1=>$values1){ //hitta namnet för taggen
			foreach ($values1 as $key2=>$values2){ //hitta namnet för taggen
				switch ($key2) {
					case 'tid':
						$qid = $values2;
						break;
					case 'name':
						$name = $values2;
						$this->qContainer->addTag($name);
						break;
				}
			}
		}			
	}
  
  /**
   * List question with id.
   *
   * @param int $id of question to display
   *
   * @return void
   */

	public function idAction($id)
	{
		$this->initialize();
		
		$all = $this->question->find($id);
		
		foreach ($all as $key=>$v){ //hitta värden i frågan
			switch ($key) {
			   case 'qid':
					 $qid = $v;
					 break;
				case 'title':
				 	$title = $v;
					 break;
			   case 'content':
					 $content = $v;
					 break;
			   case 'created':
					 $created = $v;
					 break;
			   case 'updated':
					 $updated = $v;
					 break;
			   case 'uid':
					 $uid = $v;
					 break;
			}
		}
		
		//skapa frågecontainer
		$this->qContainer = new \Jovis\Question\QuestionContainer($qid, $title, $content, $created, $updated, $uid);
		
		//hitta frågornas svar
		$answers = $this->answer->find($qid);
			
		if (!empty($answers))
		{
			//ta ut värden från svaren
			foreach ($answers as $key1=>$value) {
				if (!empty($value)){
					var_dump($value);
					foreach ($value as $key2=>$v){
						switch ($key2) {
							case 'aid':
								 $aid = $v;
								 break;
							case 'content':
								 $content = $v;
								 break;
							case 'created':
								 $created = $v;
								 break;
							case 'uid':
								 $uid = $v;
								 break;
							case 'qid':
								 $qid = $v;
								 break;
						}
					}
				}
				
				if (isset($aid)) {	
					//skapa en svarsbehållare svarets värden
					$this->aContainer = new \Jovis\Question\AnswerContainer($aid, $content, $created, $uid, $qid);
						
					//hitta svarets kommentarer
					
					$acomments = new \Jovis\Question\Acomment();
					$acomments->setDI($this->di);
						
					$allAComment = $acomments->find($aid);
					
							
					//lägg in kommentarerna till svarsbehållaren	
					$this->aContainer->addComments($allAComment);
				
					
					//lägg till svaren till frågebehållaren
					$this->qContainer->addAnswer($this->aContainer);
				}
			}
		}
		
		
		//hitta kommentarerna till frågan
			
		$this->qcomment = new \Jovis\Question\Qcomment();
		$this->qcomment->setDI($this->di);
			
		$allQComments = $this->qcomment->find($qid);
		
		$this->qContainer->addComments($allQComments);
		
		$this->addTags($qid);
   
		$this->theme->setTitle("Fråga");
		$this->views->add('question/show-question', [
			'title' => $title,
			'questionContainer' => $this->qContainer
		]);
		
		//visa formulär för att lägga in ett svar till frågan
		
		$this->newAnswerAction($qid);
			
	}
  
	/**
   * Adds an new answer till a questions. Uses form
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
			
		$this->di->views->add('question/addquestion', [
		 'title' => "Lägg till svar:",
		 'content' => $form->getHTML(),
		]);
			
			
	}
  
  /**
   * Add new Question.
   *
   * @param string $acronym of user to add.
   *
   * @return void
   */
  public function addAction()
  {
      $this->initialize();
        
      session_name('cformadd');
      session_start();
        
      $form = new \Jovis\HTMLForm\CFormAddQuestion();
      $form->setDI($this->di);
        
      //Om check har ett värde har formuläret postats och sidan körs vidare via form-objektet, 
      //om check är tom fortsätter den här 
      //funktionen köras och formuläret visas. 
      
      $form->check();
        
      $this->di->theme->setTitle("Lägg till fråga");
      $this->di->views->add('question/addquestion', [
          'title' => "Lägg till fråga:",
          'content' => $form->getHTML(),
        ]);
  }
  
  /**
   * Delete user.
   *
   * @param integer $id of user to delete.
   *
   * @return void
   */
  public function deleteAction($id = null)
  {
      $this->initialize();
      
      if (!isset($id)) {
          die("Missing id");
      }
   
      $res = $this->users->delete($id);
   
      $url = $this->url->create('users/list');
      $this->response->redirect($url);
  }
  
  /**
   * Delete (soft) user.
   *
   * @param integer $id of user to delete.
   *
   * @return void
   */
  public function softDeleteAction($id = null)
  {
      $this->initialize();
          
      if (!isset($id)) {
          die("Missing id");
      }
   
      $now = gmdate('Y-m-d H:i:s');
   
      $user = $this->users->find($id);
   
      $user->deleted = $now;
      $user->save();
   
      $url = $this->url->create('users/id/'.$id);
      $this->response->redirect($url);
  }
  
   public function undoSoftDeleteAction($id = null)
  {
      $this->initialize();
          
      if (!isset($id)) {
          die("Missing id");
      }
   
      $user = $this->users->find($id);
   
      $user->deleted = NULL;
      $user->save();
   
      $url = $this->url->create('users/id/'.$id);
      $this->response->redirect($url);
  }
 
  /**
   * List all active and not deleted users.
   *
   * @return void
   */
  public function activeAction()
  {
      $this->initialize();
    
      $all = $this->users->query()
          ->where('active IS NOT NULL')
          ->andWhere('deleted is NULL')
          ->execute();
   
      $this->theme->setTitle("Aktiva användare");
      $this->views->add('users/list-all', [
          'users' => $all,
          'title' => "Aktiva användare",
      ]);
  } 
  
  public function showDeletedAction()
  {
      $this->initialize();
    
      $all = $this->users->query()
          ->where('deleted is NOT NULL')
          ->execute();
   
      $this->theme->setTitle("Borttagna användare");
      $this->views->add('users/list-all', [
          'users' => $all,
          'title' => "Borttagna användare",
      ]);
  } 
  
   public function showInactiveAction()
  {
      $this->initialize();
    
      $all = $this->users->query()
          ->where('active is NULL')
          ->execute();
   
      $this->theme->setTitle("Borttagna användare");
      $this->views->add('users/list-all', [
          'users' => $all,
          'title' => "Borttagna användare",
      ]);
  } 
  
  public function changeStatusAction($id = null)
  {
      $this->initialize();
      
      if (!isset($id)) {
          die("Missing id");
      }
   
      $now = gmdate('Y-m-d H:i:s');    
      $user = $this->users->find($id);

      
      if ($user->active)
        $user->active=NULL;
      elseif (!isset($user->active))
        $user->active = $now;
      
      $user->save();   
      
      $url = $this->url->create('users/id/'.$id);
      $this->response->redirect($url);
  } 
  
  public function updateAction($id = null)
  {
      $this->initialize();
      
      if (!isset($id)) {
          die("Missing id");
      }
   
      $user = $this->users->find($id);

      $acronym = $user->acronym;
      $epost = $user->email;
      $namn = $user->name;
      $deleted = $user->deleted;
      
      session_name('cformuser');
      session_start();
        
      $form = new \Anax\HTMLForm\CFormUpdateUser($id, $acronym, $epost, $namn, $deleted);
      $form->setDI($this->di);
        
      //Om check har ett värde har formuläret postats och sidan körs vidare via form-objektet, 
      //om check är tom fortsätter den här 
      //funktionen köras och formuläret visas. 
      
      $form->check();
        
      $this->di->theme->setTitle("Uppdatera användare");
      $this->di->views->add('users/update', [
          'title' => "Uppdatera användare:",
          'content' => $form->getHTML(),
          'id' => $id
        ]);
  } 
  
  public function populateAction()
  {
    //$this->db->setVerbose();
 
    $this->db->dropTableIfExists('user')->execute();
 
    $this->db->createTable(
        'user',
        [
            'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
            'acronym' => ['varchar(20)', 'unique', 'not null'],
            'email' => ['varchar(80)'],
            'name' => ['varchar(80)'],
            'password' => ['varchar(255)'],
            'created' => ['datetime'],
            'updated' => ['datetime'],
            'deleted' => ['datetime'],
            'active' => ['datetime'],
        ]
    )->execute();
    
        $this->db->insert(
        'user',
        ['acronym', 'email', 'name', 'password', 'created', 'active']
    );
 
    $now = gmdate('Y-m-d H:i:s');
 
    $this->db->execute([
        'admin',
        'admin@dbwebb.se',
        'Administrator',
        password_hash('admin',PASSWORD_DEFAULT) ,
        $now,
        $now
    ]);
 
    $this->db->execute([
        'doe',
        'doe@dbwebb.se',
        'John/Jane Doe',
        password_hash('doe', PASSWORD_DEFAULT),
        $now,
        $now
    ]);  
    
    $url = $this->url->create('users/list/reset');
    $this->response->redirect($url);
    
    
  }
}

?>
