<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="general_notification")
*/
class Generalnotification
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $general_notification_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $notification_type="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $title="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $message="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $image_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $user_master_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $send_to="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $create_date="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getGeneral_notification_id()
	{
		return $this->general_notification_id;
	}

	public function getNotification_type()
	{
		return $this->notification_type;
	}
	public function setNotification_type($notification_type)
	{
		$this->notification_type = $notification_type;
	}

	public function getTitle()
	{
		return $this->title;
	}
	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function getMessage()
	{
		return $this->message;
	}
	public function setMessage($message)
	{
		$this->message = $message;
	}

	public function getImage_id()
	{
		return $this->image_id;
	}
	public function setImage_id($image_id)
	{
		$this->image_id = $image_id;
	}

	public function getUser_master_id()
	{
		return $this->user_master_id;
	}
	public function setUser_master_id($user_master_id)
	{
		$this->user_master_id = $user_master_id;
	}

	public function getSend_to()
	{
		return $this->send_to;
	}
	public function setSend_to($send_to)
	{
		$this->send_to = $send_to;
	}

	public function getDomain_id()
	{
		return $this->domain_id;
	}
	public function setDomain_id($domain_id)
	{
		$this->domain_id = $domain_id;
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