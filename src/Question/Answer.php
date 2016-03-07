<?php

namespace Jovis\Question;
 
/**
 * Model for Answers.
 *
 */
class Answer extends \Jovis\DatabaseModel\CDatabaseModel
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


		//return $this->db->fetchInto($this);
	}
}

?>
