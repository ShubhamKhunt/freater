<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="gcm_user")
*/
class Gcmuser
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $gcm_user_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $user_type="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $gcm_regid="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $user_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $device_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $email="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $created_at="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getGcm_user_id()
	{
		return $this->gcm_user_id;
	}

	public function getUser_type()
	{
		return $this->user_type;
	}
	public function setUser_type($user_type)
	{
		$this->user_type = $user_type;
	}

	public function getGcm_regid()
	{
		return $this->gcm_regid;
	}
	public function setGcm_regid($gcm_regid)
	{
		$this->gcm_regid = $gcm_regid;
	}

	public function getUser_id()
	{
		return $this->user_id;
	}
	public function setUser_id($user_id)
	{
		$this->user_id = $user_id;
	}

	public function getDevice_id()
	{
		return $this->device_id;
	}
	public function setDevice_id($device_id)
	{
		$this->device_id = $device_id;
	}

	public function getName()
	{
		return $this->name;
	}
	public function setName($name)
	{
		$this->name = $name;
	}

	public function getEmail()
	{
		return $this->email;
	}
	public function setEmail($email)
	{
		$this->email = $email;
	}

	public function getCreated_at()
	{
		return $this->created_at;
	}
	public function setCreated_at($created_at)
	{
		$this->created_at = $created_at;
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