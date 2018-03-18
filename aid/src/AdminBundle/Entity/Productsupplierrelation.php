<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="product_supplier_relation")
*/
class Productsupplierrelation
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $product_supplier_relation_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_product_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_combination_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $supplier_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $city_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $unit_price="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $quantity=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $created_by=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $create_date="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getProduct_supplier_relation_id()
	{
		return $this->product_supplier_relation_id;
	}

	public function getMain_product_id()
	{
		return $this->main_product_id;
	}
	public function setMain_product_id($main_product_id)
	{
		$this->main_product_id = $main_product_id;
	}

	public function getMain_combination_id()
	{
		return $this->main_combination_id;
	}
	public function setMain_combination_id($main_combination_id)
	{
		$this->main_combination_id = $main_combination_id;
	}

	public function getSupplier_id()
	{
		return $this->supplier_id;
	}
	public function setSupplier_id($supplier_id)
	{
		$this->supplier_id = $supplier_id;
	}

	public function getDomain_id()
	{
		return $this->domain_id;
	}
	public function setDomain_id($domain_id)
	{
		$this->domain_id = $domain_id;
	}

	public function getCity_id()
	{
		return $this->city_id;
	}
	public function setCity_id($city_id)
	{
		$this->city_id = $city_id;
	}

	public function getUnit_price()
	{
		return $this->unit_price;
	}
	public function setUnit_price($unit_price)
	{
		$this->unit_price = $unit_price;
	}

	public function getQuantity()
	{
		return $this->quantity;
	}
	public function setQuantity($quantity)
	{
		$this->quantity = $quantity;
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

	public function getIs_deleted()
	{
		return $this->is_deleted;
	}
	public function setIs_deleted($is_deleted)
	{
		$this->is_deleted = $is_deleted;
	}
}