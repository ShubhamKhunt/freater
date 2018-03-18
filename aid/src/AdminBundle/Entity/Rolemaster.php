<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="role_master")
*/
class Rolemaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $role_master_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $role_name="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $parent_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $description="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $status="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $create_by=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $create_date="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getRole_master_id()
	{
		return $this->role_master_id;
	}

	public function getRole_name()
	{
		return $this->role_name;
	}
	public function setRole_name($role_name)
	{
		$this->role_name = $role_name;
	}

	public function getParent_id()
	{
		return $this->parent_id;
	}
	public function setParent_id($parent_id)
	{
		$this->parent_id = $parent_id;
	}

	public function getDescription()
	{
		return $this->description;
	}
	public function setDescription($description)
	{
		$this->description = $description;
	}

	public function getStatus()
	{
		return $this->status;
	}
	public function setStatus($status)
	{
		$this->status = $status;
	}

	public function getDomain_id()
	{
		return $this->domain_id;
	}
	public function setDomain_id($domain_id)
	{
		$this->domain_id = $domain_id;
	}

	public function getCreate_by()
	{
		return $this->create_by;
	}
	public function setCreate_by($create_by)
	{
		$this->create_by = $create_by;
	}

	public function getCreate_date()
	{
		return $this->create_date;
	}
	public function setCreate_date($create_date)
	{
		$this->create_date = $create_date;
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