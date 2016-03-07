<?php

namespace Jovis\Question;
 
/**
 * Model for Answers Comments.
 *
 */
class AComment extends \Jovis\DatabaseModel\CDatabaseModel
{
	
	//hitta kommentar till fråga med id aid
	public function find($aid)
	{
		$this->db->select()
			->from($this->getSource())
             ->where("aid = ?");
 
		$this->db->execute([$aid]);
		
		$this->db->setFetchModeClass(__CLASS__);
		return $this->db->fetchAll();
	}
}

?>
