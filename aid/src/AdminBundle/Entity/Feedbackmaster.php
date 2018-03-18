<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="feedback_master")
*/
class Feedbackmaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $feedback_master_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $order_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $customer_service_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $message="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $ratings="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $user_master_id=0;

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

	public function getFeedback_master_id()
	{
		return $this->feedback_master_id;
	}

	public function getOrder_id()
	{
		return $this->order_id;
	}
	public function setOrder_id($order_id)
	{
		$this->order_id = $order_id;
	}

	public function getCustomer_service_name()
	{
		return $this->customer_service_name;
	}
	public function setCustomer_service_name($customer_service_name)
	{
		$this->customer_service_name = $customer_service_name;
	}

	public function getMessage()
	{
		return $this->message;
	}
	public function setMessage($message)
	{
		$this->message = $message;
	}

	public function getRatings()
	{
		return $this->ratings;
	}
	public function setRatings($ratings)
	{
		$this->ratings = $ratings;
	}

	public function getUser_master_id()
	{
		return $this->user_master_id;
	}
	public function setUser_master_id($user_master_id)
	{
		$this->user_master_id = $user_master_id;
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