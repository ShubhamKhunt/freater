<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="transaction_master")
*/
class Transactionmaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $transaction_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $transaction_code="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $order_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $user_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $payment_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $result_code="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $ref_no="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $payment_type="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $payment_date="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $amount="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $payment_status="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getTransaction_id()
	{
		return $this->transaction_id;
	}

	public function getTransaction_code()
	{
		return $this->transaction_code;
	}
	public function setTransaction_code($transaction_code)
	{
		$this->transaction_code = $transaction_code;
	}

	public function getOrder_id()
	{
		return $this->order_id;
	}
	public function setOrder_id($order_id)
	{
		$this->order_id = $order_id;
	}

	public function getUser_id()
	{
		return $this->user_id;
	}
	public function setUser_id($user_id)
	{
		$this->user_id = $user_id;
	}

	public function getPayment_id()
	{
		return $this->payment_id;
	}
	public function setPayment_id($payment_id)
	{
		$this->payment_id = $payment_id;
	}

	public function getResult_code()
	{
		return $this->result_code;
	}
	public function setResult_code($result_code)
	{
		$this->result_code = $result_code;
	}

	public function getRef_no()
	{
		return $this->ref_no;
	}
	public function setRef_no($ref_no)
	{
		$this->ref_no = $ref_no;
	}

	public function getPayment_type()
	{
		return $this->payment_type;
	}
	public function setPayment_type($payment_type)
	{
		$this->payment_type = $payment_type;
	}

	public function getPayment_date()
	{
		return $this->payment_date;
	}
	public function setPayment_date($payment_date)
	{
		$this->payment_date = $payment_date;
	}

	public function getAmount()
	{
		return $this->amount;
	}
	public function setAmount($amount)
	{
		$this->amount = $amount;
	}

	public function getPayment_status()
	{
		return $this->payment_status;
	}
	public function setPayment_status($payment_status)
	{
		$this->payment_status = $payment_status;
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