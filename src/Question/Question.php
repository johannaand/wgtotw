<?php

namespace Jovis\Question;
 
/**
 * Model for Questions.
 *
 */
class Question extends \Jovis\DatabaseModel\CDatabaseModel
{
	
	//hitta kommentar till fråga med id qid
	public function find($qid)
	{
		$this->db->select()
			->from($this->getSource())
             ->where("qid = ?");
 
		$this->db->execute([$qid]);
		return $this->db->fetchInto($this);
	}
	
	public function finduid($uid)
	{
		$this->db->select()
			->from($this->getSource())
             ->where("uid = ?");
 
		$this->db->execute([$uid]);
		$this->db->setFetchModeClass(__CLASS__);
		return $this->db->fetchAll();
		
	}
	
	public function findNewest($top = 5)
	{
		$this->db->select()
			 ->from($this->getSource())
             ->orderby('created DESC')
             ->limit($top);
             
        $this->db->execute();
		$this->db->setFetchModeClass(__CLASS__);
		return $this->db->fetchAll();
	}	
	

}
