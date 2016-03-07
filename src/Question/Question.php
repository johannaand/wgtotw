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
	
}

?>
