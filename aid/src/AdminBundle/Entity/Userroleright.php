<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="user_role_right")
*/
class Userroleright
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $user_role_right_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $role_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $right_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getUser_role_right_id()
	{
		return $this->user_role_right_id;
	}

	public function getRole_id()
	{
		return $this->role_id;
	}
	public function setRole_id($role_id)
	{
		$this->role_id = $role_id;
	}

	public function getRight_id()
	{
		return $this->right_id;
	}
	public function setRight_id($right_id)
	{
		$this->right_id = $right_id;
	}

	public function getDomain_id()
	{
		return $this->domain_id;
	}
	public function setDomain_id($domain_id)
	{
		$this->domain_id = $domain_id;
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