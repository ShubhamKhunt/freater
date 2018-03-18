<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="role_right_relation")
*/
class Rolerightrelation
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $role_right_relation_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $role_master_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $code="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_access=0;

	public function getRole_right_relation_id()
	{
		return $this->role_right_relation_id;
	}

	public function getRole_master_id()
	{
		return $this->role_master_id;
	}
	public function setRole_master_id($role_master_id)
	{
		$this->role_master_id = $role_master_id;
	}

	public function getCode()
	{
		return $this->code;
	}
	public function setCode($code)
	{
		$this->code = $code;
	}

	public function getIs_access()
	{
		return $this->is_access;
	}
	public function setIs_access($is_access)
	{
		$this->is_access = $is_access;
	}
}