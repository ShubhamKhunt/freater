<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="product_master")
*/
class Productmaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $product_master_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $product_title="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $refrence_code="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $brand_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $product_type_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $short_description="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $description="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $unit_size="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $product_logo=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $status="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $quantity="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $max_allowed_qty_per_order=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $market_price="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $original_price="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $sku="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $on_sale="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $additional_shipping_charge="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $language_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_product_master_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $created_by=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $created_date="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $last_updated="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $favourite_count=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $product_ws_add=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $sort_order=0;

	public function getProduct_master_id()
	{
		return $this->product_master_id;
	}

	public function getProduct_title()
	{
		return $this->product_title;
	}
	public function setProduct_title($product_title)
	{
		$this->product_title = $product_title;
	}

	public function getRefrence_code()
	{
		return $this->refrence_code;
	}
	public function setRefrence_code($refrence_code)
	{
		$this->refrence_code = $refrence_code;
	}

	public function getBrand_id()
	{
		return $this->brand_id;
	}
	public function setBrand_id($brand_id)
	{
		$this->brand_id = $brand_id;
	}

	public function getProduct_type_id()
	{
		return $this->product_type_id;
	}
	public function setProduct_type_id($product_type_id)
	{
		$this->product_type_id = $product_type_id;
	}

	public function getShort_description()
	{
		return $this->short_description;
	}
	public function setShort_description($short_description)
	{
		$this->short_description = $short_description;
	}

	public function getDescription()
	{
		return $this->description;
	}
	public function setDescription($description)
	{
		$this->description = $description;
	}

	public function getUnit_size()
	{
		return $this->unit_size;
	}
	public function setUnit_size($unit_size)
	{
		$this->unit_size = $unit_size;
	}

	public function getProduct_logo()
	{
		return $this->product_logo;
	}
	public function setProduct_logo($product_logo)
	{
		$this->product_logo = $product_logo;
	}

	public function getStatus()
	{
		return $this->status;
	}
	public function setStatus($status)
	{
		$this->status = $status;
	}

	public function getQuantity()
	{
		return $this->quantity;
	}
	public function setQuantity($quantity)
	{
		$this->quantity = $quantity;
	}

	public function getMax_allowed_qty_per_order()
	{
		return $this->max_allowed_qty_per_order;
	}
	public function setMax_allowed_qty_per_order($max_allowed_qty_per_order)
	{
		$this->max_allowed_qty_per_order = $max_allowed_qty_per_order;
	}

	public function getMarket_price()
	{
		return $this->market_price;
	}
	public function setMarket_price($market_price)
	{
		$this->market_price = $market_price;
	}

	public function getOriginal_price()
	{
		return $this->original_price;
	}
	public function setOriginal_price($original_price)
	{
		$this->original_price = $original_price;
	}

	public function getSku()
	{
		return $this->sku;
	}
	public function setSku($sku)
	{
		$this->sku = $sku;
	}

	public function getOn_sale()
	{
		return $this->on_sale;
	}
	public function setOn_sale($on_sale)
	{
		$this->on_sale = $on_sale;
	}

	public function getAdditional_shipping_charge()
	{
		return $this->additional_shipping_charge;
	}
	public function setAdditional_shipping_charge($additional_shipping_charge)
	{
		$this->additional_shipping_charge = $additional_shipping_charge;
	}

	public function getLanguage_id()
	{
		return $this->language_id;
	}
	public function setLanguage_id($language_id)
	{
		$this->language_id = $language_id;
	}

	public function getMain_product_master_id()
	{
		return $this->main_product_master_id;
	}
	public function setMain_product_master_id($main_product_master_id)
	{
		$this->main_product_master_id = $main_product_master_id;
	}

	public function getCreated_by()
	{
		return $this->created_by;
	}
	public function setCreated_by($created_by)
	{
		$this->created_by = $created_by;
	}

	public function getCreated_date()
	{
		return $this->created_date;
	}
	public function setCreated_date($created_date)
	{
		$this->created_date = $created_date;
	}

	public function getLast_updated()
	{
		return $this->last_updated;
	}
	public function setLast_updated($last_updated)
	{
		$this->last_updated = $last_updated;
	}

	public function getDomain_id()
	{
		return $this->domain_id;
	}
	public function setDomain_id($domain_id)
	{
		$this->domain_id = $domain_id;
	}

	public function getFavourite_count()
	{
		return $this->favourite_count;
	}
	public function setFavourite_count($favourite_count)
	{
		$this->favourite_count = $favourite_count;
	}

	public function getProduct_ws_add()
	{
		return $this->product_ws_add;
	}
	public function setProduct_ws_add($product_ws_add)
	{
		$this->product_ws_add = $product_ws_add;
	}

	public function getIs_deleted()
	{
		return $this->is_deleted;
	}
	public function setIs_deleted($is_deleted)
	{
		$this->is_deleted = $is_deleted;
	}

	public function getSort_order()
	{
		return $this->sort_order;
	}
	public function setSort_order($sort_order)
	{
		$this->sort_order = $sort_order;
	}
}