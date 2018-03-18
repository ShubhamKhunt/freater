<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="knet_transaction_master")
*/
class Knettransactionmaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $order_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $user_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $order_amount="";

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
	protected $transaction_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $auth="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $track_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $ref_no="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $ref_code="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $trans_from="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $created_datetime="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $is_deleted="";

	public function getId()
	{
		return $this->id;
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

	public function getOrder_amount()
	{
		return $this->order_amount;
	}
	public function setOrder_amount($order_amount)
	{
		$this->order_amount = $order_amount;
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

	public function getTransaction_id()
	{
		return $this->transaction_id;
	}
	public function setTransaction_id($transaction_id)
	{
		$this->transaction_id = $transaction_id;
	}

	public function getAuth()
	{
		return $this->auth;
	}
	public function setAuth($auth)
	{
		$this->auth = $auth;
	}

	public function getTrack_id()
	{
		return $this->track_id;
	}
	public function setTrack_id($track_id)
	{
		$this->track_id = $track_id;
	}

	public function getRef_no()
	{
		return $this->ref_no;
	}
	public function setRef_no($ref_no)
	{
		$this->ref_no = $ref_no;
	}

	public function getRef_code()
	{
		return $this->ref_code;
	}
	public function setRef_code($ref_code)
	{
		$this->ref_code = $ref_code;
	}

	public function getTrans_from()
	{
		return $this->trans_from;
	}
	public function setTrans_from($trans_from)
	{
		$this->trans_from = $trans_from;
	}

	public function getCreated_datetime()
	{
		return $this->created_datetime;
	}
	public function setCreated_datetime($created_datetime)
	{
		$this->created_datetime = $created_datetime;
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