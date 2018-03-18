<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="shop_master")
*/
class Shopmaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $shop_master_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $shop_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $shop_detail="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $shop_type="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $shop_logo=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $website_link="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $status="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $usermaster_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $shop_address="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $start_time="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $end_time="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $delivery_time="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $shipping_charge="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $tax="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $created_on="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $created_by=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $last_update_on="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getShop_master_id()
	{
		return $this->shop_master_id;
	}

	public function getShop_name()
	{
		return $this->shop_name;
	}
	public function setShop_name($shop_name)
	{
		$this->shop_name = $shop_name;
	}

	public function getShop_detail()
	{
		return $this->shop_detail;
	}
	public function setShop_detail($shop_detail)
	{
		$this->shop_detail = $shop_detail;
	}

	public function getShop_type()
	{
		return $this->shop_type;
	}
	public function setShop_type($shop_type)
	{
		$this->shop_type = $shop_type;
	}

	public function getShop_logo()
	{
		return $this->shop_logo;
	}
	public function setShop_logo($shop_logo)
	{
		$this->shop_logo = $shop_logo;
	}

	public function getWebsite_link()
	{
		return $this->website_link;
	}
	public function setWebsite_link($website_link)
	{
		$this->website_link = $website_link;
	}

	public function getStatus()
	{
		return $this->status;
	}
	public function setStatus($status)
	{
		$this->status = $status;
	}

	public function getUsermaster_id()
	{
		return $this->usermaster_id;
	}
	public function setUsermaster_id($usermaster_id)
	{
		$this->usermaster_id = $usermaster_id;
	}

	public function getShop_address()
	{
		return $this->shop_address;
	}
	public function setShop_address($shop_address)
	{
		$this->shop_address = $shop_address;
	}

	public function getStart_time()
	{
		return $this->start_time;
	}
	public function setStart_time($start_time)
	{
		$this->start_time = $start_time;
	}

	public function getEnd_time()
	{
		return $this->end_time;
	}
	public function setEnd_time($end_time)
	{
		$this->end_time = $end_time;
	}

	public function getDelivery_time()
	{
		return $this->delivery_time;
	}
	public function setDelivery_time($delivery_time)
	{
		$this->delivery_time = $delivery_time;
	}

	public function getShipping_charge()
	{
		return $this->shipping_charge;
	}
	public function setShipping_charge($shipping_charge)
	{
		$this->shipping_charge = $shipping_charge;
	}

	public function getTax()
	{
		return $this->tax;
	}
	public function setTax($tax)
	{
		$this->tax = $tax;
	}

	public function getCreated_on()
	{
		return $this->created_on;
	}
	public function setCreated_on($created_on)
	{
		$this->created_on = $created_on;
	}

	public function getCreated_by()
	{
		return $this->created_by;
	}
	public function setCreated_by($created_by)
	{
		$this->created_by = $created_by;
	}

	public function getLast_update_on()
	{
		return $this->last_update_on;
	}
	public function setLast_update_on($last_update_on)
	{
		$this->last_update_on = $last_update_on;
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