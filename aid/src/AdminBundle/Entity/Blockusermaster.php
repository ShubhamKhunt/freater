<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="block_user_master")
*/
class Blockusermaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $block_user_master_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $user_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $professional_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_block=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $created_on="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getBlock_user_master_id()
	{
		return $this->block_user_master_id;
	}

	public function getUser_id()
	{
		return $this->user_id;
	}
	public function setUser_id($user_id)
	{
		$this->user_id = $user_id;
	}

	public function getProfessional_id()
	{
		return $this->professional_id;
	}
	public function setProfessional_id($professional_id)
	{
		$this->professional_id = $professional_id;
	}

	public function getIs_block()
	{
		return $this->is_block;
	}
	public function setIs_block($is_block)
	{
		$this->is_block = $is_block;
	}

	public function getCreated_on()
	{
		return $this->created_on;
	}
	public function setCreated_on($created_on)
	{
		$this->created_on = $created_on;
	}

	public function getIs_deleted()
	{
		return $this->is_deleted;
	}
	public function setIs_deleted($is_deleted)
	{
		$this->is_deleted = $is_deleted;
	}
}