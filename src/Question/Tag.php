<?php

namespace Jovis\Question;
 
/**
 * Model for Questions Comments.
 *
 */
class Tag extends \Jovis\DatabaseModel\CDatabaseModel
{
	
	//hitta kommentar till fråga med id qid
	public function findName($tid)
	{
		$this->db->select('name')
			->from($this->getSource())
             ->where("tid = ?");
 
		$this->db->execute([$tid]);
		
		$this->db->setFetchModeClass(__CLASS__);
		return $this->db->fetchAll();
	}
	
	public function findId($name)
	{
		$this->db->select('tid')
			->from($this->getSource())
             ->where("name = ?");
 
		$this->db->execute([$name]);
		
		return $this->db->fetchInto($this);
	}
}

?>
