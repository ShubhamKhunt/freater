<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="coupon_master")
*/
class Couponmaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $coupon_master_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $coupon_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $coupon_code="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $start_date="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $end_date="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $discount_value="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $discount_type="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $no_of_user_use=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $no_of_times_use=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $coupon_usage_interval=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $min_order_amount=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $status="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $visible_all="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $created_datetime="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getCoupon_master_id()
	{
		return $this->coupon_master_id;
	}

	public function getCoupon_name()
	{
		return $this->coupon_name;
	}
	public function setCoupon_name($coupon_name)
	{
		$this->coupon_name = $coupon_name;
	}

	public function getCoupon_code()
	{
		return $this->coupon_code;
	}
	public function setCoupon_code($coupon_code)
	{
		$this->coupon_code = $coupon_code;
	}

	public function getStart_date()
	{
		return $this->start_date;
	}
	public function setStart_date($start_date)
	{
		$this->start_date = $start_date;
	}

	public function getEnd_date()
	{
		return $this->end_date;
	}
	public function setEnd_date($end_date)
	{
		$this->end_date = $end_date;
	}

	public function getDiscount_value()
	{
		return $this->discount_value;
	}
	public function setDiscount_value($discount_value)
	{
		$this->discount_value = $discount_value;
	}

	public function getDiscount_type()
	{
		return $this->discount_type;
	}
	public function setDiscount_type($discount_type)
	{
		$this->discount_type = $discount_type;
	}

	public function getNo_of_user_use()
	{
		return $this->no_of_user_use;
	}
	public function setNo_of_user_use($no_of_user_use)
	{
		$this->no_of_user_use = $no_of_user_use;
	}

	public function getNo_of_times_use()
	{
		return $this->no_of_times_use;
	}
	public function setNo_of_times_use($no_of_times_use)
	{
		$this->no_of_times_use = $no_of_times_use;
	}

	public function getCoupon_usage_interval()
	{
		return $this->coupon_usage_interval;
	}
	public function setCoupon_usage_interval($coupon_usage_interval)
	{
		$this->coupon_usage_interval = $coupon_usage_interval;
	}

	public function getMin_order_amount()
	{
		return $this->min_order_amount;
	}
	public function setMin_order_amount($min_order_amount)
	{
		$this->min_order_amount = $min_order_amount;
	}

	public function getStatus()
	{
		return $this->status;
	}
	public function setStatus($status)
	{
		$this->status = $status;
	}

	public function getVisible_all()
	{
		return $this->visible_all;
	}
	public function setVisible_all($visible_all)
	{
		$this->visible_all = $visible_all;
	}

	public function getCreated_datetime()
	{
		return $this->created_datetime;
	}
	public function setCreated_datetime($created_datetime)
	{
		$this->created_datetime = $created_datetime;
	}

	public function getDomain_id()
	{
		return $this->domain_id;
	}
	public function setDomain_id($domain_id)
	{
		$this->domain_id = $domain_id;
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