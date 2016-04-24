<?php

namespace Jovis\User;
 
/**
 * Model for Users.
 *
 */
class User extends \Jovis\DatabaseModel\CDatabaseModel
{
 
 /**
 * Find and return specific.
 *
 * @return this
 */
 
	public function find($uid)
	{
		$this->db->select()
			->from($this->getSource())
             ->where("uid = ?");
 
		$this->db->execute([$uid]);
		return $this->db->fetchInto($this);
	}
	
	public function findtoLogin($nic, $pwd)
	{
		$this->db->select()
			->from($this->getSource())
             ->where("nic = ?");
 
		$this->db->execute(array($nic));
		
		return $this->db->fetchInto($this);
	}
	
	public function findNic($uid)
	{
		$this->db->select('nic')
			->from($this->getSource())
             ->where("uid = ?");
 
		$this->db->execute([$uid]);
		//$this->db->execute(array($nic, password_hash($pwd, PASSWORD_DEFAULT)));
		return $this->db->fetchInto($this);
	}
    
    /**
   * Save current object/row.
   *
   * @param array $values key/values to save or empty to use object properties.
   *
   * @return boolean true or false if saving went okey.
   */
    public function save($values = [])
    {
		$this->setProperties($values);
		$values = $this->getProperties();
				
		if (isset($values['uid'])) {
			return $this->update($values);
		} else {
			return $this->create($values);
		} 
	}
	
	/**
   * Update row.
   *
   * @param array $values key/values to save.
   *
   * @return boolean true or false if saving went okey.
   */
  public function update($values)
  {
	  
	  $uid = $values['uid'];
	  unset($values['uid']);
	  
      $keys = array_keys($values);
      
      $values = array_values($values);
       
      $values[] = $uid;
   
      $this->db->update(
          $this->getSource(),
          $keys,
          "uid = ?"
      );
   
      return $this->db->execute($values);
  }
}

?>
