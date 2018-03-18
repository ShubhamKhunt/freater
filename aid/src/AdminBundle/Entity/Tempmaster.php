<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="temp_master")
*/
class Tempmaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $temp_master_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $order_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $transaction_code="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $flag="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $device_flag="";

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
	protected $created_on="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getTemp_master_id()
	{
		return $this->temp_master_id;
	}

	public function getOrder_id()
	{
		return $this->order_id;
	}
	public function setOrder_id($order_id)
	{
		$this->order_id = $order_id;
	}

	public function getTransaction_code()
	{
		return $this->transaction_code;
	}
	public function setTransaction_code($transaction_code)
	{
		$this->transaction_code = $transaction_code;
	}

	public function getFlag()
	{
		return $this->flag;
	}
	public function setFlag($flag)
	{
		$this->flag = $flag;
	}

	public function getDevice_flag()
	{
		return $this->device_flag;
	}
	public function setDevice_flag($device_flag)
	{
		$this->device_flag = $device_flag;
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
}