<?php

namespace Jovis\Question
 
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
    
    
   /**
   * List all users.
   *
   * @return void
   */
  public function listAction($info=null)
  {
      $this->initialize();
   
      $all = $this->question->findAll();
      
      $aContent;
            
      //gör om arrayen av objekt till en array av arrayer
      foreach ($all as $key1=>$value) {
        foreach ($value as $key2=>$v){
          if (!in_array($key2, $noListing)) { //$noListing, parametrar som inte ska vara med
              $aContent[$key1][$key2] = $v;
          }
        }
      }
     
      
      $aHeading = [];
      //hittar objektens parametrar/tabellens kolumnnamn och skapar
      //en ny array av rubriker
      foreach ($all as $key1=>$value) {
        foreach ($value as $key2=>$v){
          if (!in_array($key2, $noListing)) { //$noListing, parametrar som inte ska vara med
              $aHeading[] = $key2;
          }
        }
        break;  
      }
      
      $this->chtml = new \Jovis\HTMLTable\CHTMLTable($aHeading, $aContent);
      
      $htmltable = $this->chtml->getTable();
           
   
      $this->theme->setTitle("Frågor");
      $this->views->add('questions/list-all', [
          'title' => "Frågor",
          'html' => $htmltable
      ]);
  }
  
  /**
   * List user with id.
   *
   * @param int $id of user to display
   *
   * @return void
   */
  public function idAction($id = null)
  {
      $this->initialize();
   
      $user = $this->users->find($id);
   
      $this->theme->setTitle("View user with id");
      $this->views->add('users/view', [
          'user' => $user,
          'title' => "Användare"
      ]);
  }
  
  /**
   * Add new user.
   *
   * @param string $acronym of user to add.
   *
   * @return void
   */
  public function addAction()
  {
      $this->initialize();
  
         
     /* $this->users->save([
          'acronym' => $acronym,
          'email' => $acronym . '@mail.se',
          'name' => 'Mr/Mrs ' . $acronym,
          'password' => crypt($acronym),
          'created' => $now,
          'active' => $now,
      ]);
      
       */
      
      session_name('cformuser');
      session_start();
        
      $form = new \Anax\HTMLForm\CFormAddUser();
      $form->setDI($this->di);
        
      //Om check har ett värde har formuläret postats och sidan körs vidare via form-objektet, 
      //om check är tom fortsätter den här 
      //funktionen köras och formuläret visas. 
      
      $form->check();
        
      $this->di->theme->setTitle("Lägg till användare");
      $this->di->views->add('users/add', [
          'title' => "Lägg till användare användare:",
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
