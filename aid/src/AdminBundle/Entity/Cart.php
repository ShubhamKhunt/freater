<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="cart")
*/
class Cart
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $cart_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $order_type=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $product_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $combination_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $supplier_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $product_original_price="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $unit_price="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $total_price="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $quantity=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $order_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $order_placed="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $order_status="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $organization_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $created_by=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $create_date="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $shop_type="";

	public function getCart_id()
	{
		return $this->cart_id;
	}

	public function getOrder_type()
	{
		return $this->order_type;
	}
	public function setOrder_type($order_type)
	{
		$this->order_type = $order_type;
	}

	public function getProduct_id()
	{
		return $this->product_id;
	}
	public function setProduct_id($product_id)
	{
		$this->product_id = $product_id;
	}

	public function getCombination_id()
	{
		return $this->combination_id;
	}
	public function setCombination_id($combination_id)
	{
		$this->combination_id = $combination_id;
	}

	public function getSupplier_id()
	{
		return $this->supplier_id;
	}
	public function setSupplier_id($supplier_id)
	{
		$this->supplier_id = $supplier_id;
	}

	public function getProduct_original_price()
	{
		return $this->product_original_price;
	}
	public function setProduct_original_price($product_original_price)
	{
		$this->product_original_price = $product_original_price;
	}

	public function getUnit_price()
	{
		return $this->unit_price;
	}
	public function setUnit_price($unit_price)
	{
		$this->unit_price = $unit_price;
	}

	public function getTotal_price()
	{
		return $this->total_price;
	}
	public function setTotal_price($total_price)
	{
		$this->total_price = $total_price;
	}

	public function getQuantity()
	{
		return $this->quantity;
	}
	public function setQuantity($quantity)
	{
		$this->quantity = $quantity;
	}

	public function getOrder_id()
	{
		return $this->order_id;
	}
	public function setOrder_id($order_id)
	{
		$this->order_id = $order_id;
	}

	public function getOrder_placed()
	{
		return $this->order_placed;
	}
	public function setOrder_placed($order_placed)
	{
		$this->order_placed = $order_placed;
	}

	public function getOrder_status()
	{
		return $this->order_status;
	}
	public function setOrder_status($order_status)
	{
		$this->order_status = $order_status;
	}

	public function getOrganization_id()
	{
		return $this->organization_id;
	}
	public function setOrganization_id($organization_id)
	{
		$this->organization_id = $organization_id;
	}

	public function getCreated_by()
	{
		return $this->created_by;
	}
	public function setCreated_by($created_by)
	{
		$this->created_by = $created_by;
	}

	public function getCreate_date()
	{
		return $this->create_date;
	}
	public function setCreate_date($create_date)
	{
		$this->create_date = $create_date;
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

	public function getShop_type()
	{
		return $this->shop_type;
	}
	public function setShop_type($shop_type)
	{
		$this->shop_type = $shop_type;
	}
}