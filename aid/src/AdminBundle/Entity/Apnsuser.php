<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="apns_user")
*/
class Apnsuser
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $apns_user_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $apns_regid="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $user_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $user_type="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $device_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $app_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $name="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $badge=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $created_date="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $status_notification="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getApns_user_id()
	{
		return $this->apns_user_id;
	}

	public function getApns_regid()
	{
		return $this->apns_regid;
	}
	public function setApns_regid($apns_regid)
	{
		$this->apns_regid = $apns_regid;
	}

	public function getUser_id()
	{
		return $this->user_id;
	}
	public function setUser_id($user_id)
	{
		$this->user_id = $user_id;
	}

	public function getUser_type()
	{
		return $this->user_type;
	}
	public function setUser_type($user_type)
	{
		$this->user_type = $user_type;
	}

	public function getDevice_id()
	{
		return $this->device_id;
	}
	public function setDevice_id($device_id)
	{
		$this->device_id = $device_id;
	}

	public function getApp_id()
	{
		return $this->app_id;
	}
	public function setApp_id($app_id)
	{
		$this->app_id = $app_id;
	}

	public function getDomain_id()
	{
		return $this->domain_id;
	}
	public function setDomain_id($domain_id)
	{
		$this->domain_id = $domain_id;
	}

	public function getName()
	{
		return $this->name;
	}
	public function setName($name)
	{
		$this->name = $name;
	}

	public function getBadge()
	{
		return $this->badge;
	}
	public function setBadge($badge)
	{
		$this->badge = $badge;
	}

	public function getCreated_date()
	{
		return $this->created_date;
	}
	public function setCreated_date($created_date)
	{
		$this->created_date = $created_date;
	}

	public function getStatus_notification()
	{
		return $this->status_notification;
	}
	public function setStatus_notification($status_notification)
	{
		$this->status_notification = $status_notification;
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