<?php

namespace Jovis\Question;
 
/**
 * Model for QTag.
 *
 */
class Qtag extends \Jovis\DatabaseModel\CDatabaseModel
{
	
	//hitta kommentar till fråga med id qid
	public function find($qid)
	{
		$this->db->select()
			->from($this->getSource())
             ->where("qid = ?");
 
		$this->db->execute([$qid]);
		
		$this->db->setFetchModeClass(__CLASS__);
		return $this->db->fetchAll();
	}
	
	public function findQuestions($tid)
	{
		$this->db->select('qid')
			->from($this->getSource())
             ->where("tid = ?");
 
		$this->db->execute([$tid]);
		
		$this->db->setFetchModeClass(__CLASS__);
		return $this->db->fetchAll();
	}
}
