<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="order_master")
*/
class Ordermaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $order_master_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $order_type=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $unique_no="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $total_no_of_items=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $total_bill_amount="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $order_bill_amount="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $total_discount="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $coupon_code="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $delivery_charge="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $special_instruction="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $order_note="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $delivery_address_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $delivery_boy_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $order_status_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $is_confirmed="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $customer_order_receive=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $delivery_time_type=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $delivery_date="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $delivery_time="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $delivery_end_time="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $prescription_image_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $signature_image_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $signature_capture_date="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $is_insurance="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $customer_email="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $order_createdby=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $order_dateadded="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $last_update_on="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $app_version="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getOrder_master_id()
	{
		return $this->order_master_id;
	}

	public function getOrder_type()
	{
		return $this->order_type;
	}
	public function setOrder_type($order_type)
	{
		$this->order_type = $order_type;
	}

	public function getUnique_no()
	{
		return $this->unique_no;
	}
	public function setUnique_no($unique_no)
	{
		$this->unique_no = $unique_no;
	}

	public function getTotal_no_of_items()
	{
		return $this->total_no_of_items;
	}
	public function setTotal_no_of_items($total_no_of_items)
	{
		$this->total_no_of_items = $total_no_of_items;
	}

	public function getTotal_bill_amount()
	{
		return $this->total_bill_amount;
	}
	public function setTotal_bill_amount($total_bill_amount)
	{
		$this->total_bill_amount = $total_bill_amount;
	}

	public function getOrder_bill_amount()
	{
		return $this->order_bill_amount;
	}
	public function setOrder_bill_amount($order_bill_amount)
	{
		$this->order_bill_amount = $order_bill_amount;
	}

	public function getTotal_discount()
	{
		return $this->total_discount;
	}
	public function setTotal_discount($total_discount)
	{
		$this->total_discount = $total_discount;
	}

	public function getCoupon_code()
	{
		return $this->coupon_code;
	}
	public function setCoupon_code($coupon_code)
	{
		$this->coupon_code = $coupon_code;
	}

	public function getDelivery_charge()
	{
		return $this->delivery_charge;
	}
	public function setDelivery_charge($delivery_charge)
	{
		$this->delivery_charge = $delivery_charge;
	}

	public function getSpecial_instruction()
	{
		return $this->special_instruction;
	}
	public function setSpecial_instruction($special_instruction)
	{
		$this->special_instruction = $special_instruction;
	}

	public function getOrder_note()
	{
		return $this->order_note;
	}
	public function setOrder_note($order_note)
	{
		$this->order_note = $order_note;
	}

	public function getDelivery_address_id()
	{
		return $this->delivery_address_id;
	}
	public function setDelivery_address_id($delivery_address_id)
	{
		$this->delivery_address_id = $delivery_address_id;
	}

	public function getDelivery_boy_id()
	{
		return $this->delivery_boy_id;
	}
	public function setDelivery_boy_id($delivery_boy_id)
	{
		$this->delivery_boy_id = $delivery_boy_id;
	}

	public function getOrder_status_id()
	{
		return $this->order_status_id;
	}
	public function setOrder_status_id($order_status_id)
	{
		$this->order_status_id = $order_status_id;
	}

	public function getIs_confirmed()
	{
		return $this->is_confirmed;
	}
	public function setIs_confirmed($is_confirmed)
	{
		$this->is_confirmed = $is_confirmed;
	}

	public function getCustomer_order_receive()
	{
		return $this->customer_order_receive;
	}
	public function setCustomer_order_receive($customer_order_receive)
	{
		$this->customer_order_receive = $customer_order_receive;
	}

	public function getDelivery_time_type()
	{
		return $this->delivery_time_type;
	}
	public function setDelivery_time_type($delivery_time_type)
	{
		$this->delivery_time_type = $delivery_time_type;
	}

	public function getDelivery_date()
	{
		return $this->delivery_date;
	}
	public function setDelivery_date($delivery_date)
	{
		$this->delivery_date = $delivery_date;
	}

	public function getDelivery_time()
	{
		return $this->delivery_time;
	}
	public function setDelivery_time($delivery_time)
	{
		$this->delivery_time = $delivery_time;
	}

	public function getDelivery_end_time()
	{
		return $this->delivery_end_time;
	}
	public function setDelivery_end_time($delivery_end_time)
	{
		$this->delivery_end_time = $delivery_end_time;
	}

	public function getPrescription_image_id()
	{
		return $this->prescription_image_id;
	}
	public function setPrescription_image_id($prescription_image_id)
	{
		$this->prescription_image_id = $prescription_image_id;
	}

	public function getSignature_image_id()
	{
		return $this->signature_image_id;
	}
	public function setSignature_image_id($signature_image_id)
	{
		$this->signature_image_id = $signature_image_id;
	}

	public function getSignature_capture_date()
	{
		return $this->signature_capture_date;
	}
	public function setSignature_capture_date($signature_capture_date)
	{
		$this->signature_capture_date = $signature_capture_date;
	}

	public function getIs_insurance()
	{
		return $this->is_insurance;
	}
	public function setIs_insurance($is_insurance)
	{
		$this->is_insurance = $is_insurance;
	}

	public function getCustomer_email()
	{
		return $this->customer_email;
	}
	public function setCustomer_email($customer_email)
	{
		$this->customer_email = $customer_email;
	}

	public function getOrder_createdby()
	{
		return $this->order_createdby;
	}
	public function setOrder_createdby($order_createdby)
	{
		$this->order_createdby = $order_createdby;
	}

	public function getOrder_dateadded()
	{
		return $this->order_dateadded;
	}
	public function setOrder_dateadded($order_dateadded)
	{
		$this->order_dateadded = $order_dateadded;
	}

	public function getLast_update_on()
	{
		return $this->last_update_on;
	}
	public function setLast_update_on($last_update_on)
	{
		$this->last_update_on = $last_update_on;
	}

	public function getDomain_id()
	{
		return $this->domain_id;
	}
	public function setDomain_id($domain_id)
	{
		$this->domain_id = $domain_id;
	}

	public function getApp_version()
	{
		return $this->app_version;
	}
	public function setApp_version($app_version)
	{
		$this->app_version = $app_version;
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