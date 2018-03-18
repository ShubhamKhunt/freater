<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="user_group_master")
*/
class Usergroupmaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $user_group_master_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $user_group_name="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $user_group_icon=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $user_group_created="";

	public function getUser_group_master_id()
	{
		return $this->user_group_master_id;
	}

	public function getUser_group_name()
	{
		return $this->user_group_name;
	}
	public function setUser_group_name($user_group_name)
	{
		$this->user_group_name = $user_group_name;
	}

	public function getUser_group_icon()
	{
		return $this->user_group_icon;
	}
	public function setUser_group_icon($user_group_icon)
	{
		$this->user_group_icon = $user_group_icon;
	}

	public function getUser_group_created()
	{
		return $this->user_group_created;
	}
	public function setUser_group_created($user_group_created)
	{
		$this->user_group_created = $user_group_created;
	}
}