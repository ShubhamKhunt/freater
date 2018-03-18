<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="chat_master")
*/
class Chatmaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $chat_master_id;

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
	protected $order_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $chat_type="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $message_type="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $message="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $reason_for_payment="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $status="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $created_by="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $created_on="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $is_after_paid="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $is_payment_pending="";

	public function getChat_master_id()
	{
		return $this->chat_master_id;
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

	public function getOrder_id()
	{
		return $this->order_id;
	}
	public function setOrder_id($order_id)
	{
		$this->order_id = $order_id;
	}

	public function getChat_type()
	{
		return $this->chat_type;
	}
	public function setChat_type($chat_type)
	{
		$this->chat_type = $chat_type;
	}

	public function getMessage_type()
	{
		return $this->message_type;
	}
	public function setMessage_type($message_type)
	{
		$this->message_type = $message_type;
	}

	public function getMessage()
	{
		return $this->message;
	}
	public function setMessage($message)
	{
		$this->message = $message;
	}

	public function getReason_for_payment()
	{
		return $this->reason_for_payment;
	}
	public function setReason_for_payment($reason_for_payment)
	{
		$this->reason_for_payment = $reason_for_payment;
	}

	public function getStatus()
	{
		return $this->status;
	}
	public function setStatus($status)
	{
		$this->status = $status;
	}

	public function getCreated_by()
	{
		return $this->created_by;
	}
	public function setCreated_by($created_by)
	{
		$this->created_by = $created_by;
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

	public function getIs_after_paid()
	{
		return $this->is_after_paid;
	}
	public function setIs_after_paid($is_after_paid)
	{
		$this->is_after_paid = $is_after_paid;
	}

	public function getIs_payment_pending()
	{
		return $this->is_payment_pending;
	}
	public function setIs_payment_pending($is_payment_pending)
	{
		$this->is_payment_pending = $is_payment_pending;
	}
}