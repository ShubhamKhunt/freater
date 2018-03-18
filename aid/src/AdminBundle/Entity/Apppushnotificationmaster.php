<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="app_push_notification_master")
*/
class Apppushnotificationmaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $notification_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $device_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $app_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $user_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $language_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $device_token="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $device_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $data="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $code=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $table_name="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $table_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $response="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $datetime="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getNotification_id()
	{
		return $this->notification_id;
	}

	public function getDevice_name()
	{
		return $this->device_name;
	}
	public function setDevice_name($device_name)
	{
		$this->device_name = $device_name;
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

	public function getUser_id()
	{
		return $this->user_id;
	}
	public function setUser_id($user_id)
	{
		$this->user_id = $user_id;
	}

	public function getLanguage_id()
	{
		return $this->language_id;
	}
	public function setLanguage_id($language_id)
	{
		$this->language_id = $language_id;
	}

	public function getDevice_token()
	{
		return $this->device_token;
	}
	public function setDevice_token($device_token)
	{
		$this->device_token = $device_token;
	}

	public function getDevice_id()
	{
		return $this->device_id;
	}
	public function setDevice_id($device_id)
	{
		$this->device_id = $device_id;
	}

	public function getData()
	{
		return $this->data;
	}
	public function setData($data)
	{
		$this->data = $data;
	}

	public function getCode()
	{
		return $this->code;
	}
	public function setCode($code)
	{
		$this->code = $code;
	}

	public function getTable_name()
	{
		return $this->table_name;
	}
	public function setTable_name($table_name)
	{
		$this->table_name = $table_name;
	}

	public function getTable_id()
	{
		return $this->table_id;
	}
	public function setTable_id($table_id)
	{
		$this->table_id = $table_id;
	}

	public function getResponse()
	{
		return $this->response;
	}
	public function setResponse($response)
	{
		$this->response = $response;
	}

	public function getDatetime()
	{
		return $this->datetime;
	}
	public function setDatetime($datetime)
	{
		$this->datetime = $datetime;
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