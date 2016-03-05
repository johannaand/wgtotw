<?php

namespace Jovis\Question;
 
/**
 * Model for Questions.
 *
 */
class Question extends \Jovis\DatabaseModel\CDatabaseModel
{
 
 /**
   * Find and return all.
   *
   * @return array
   */
  public function findAll()
  {
      $this->db->select()
               ->from Question;
   
      $this->db->execute();
      $this->db->setFetchModeClass(__CLASS__);
      return $this->db->fetchAll();
  }

    
}

?>
