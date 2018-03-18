<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="supplier_attribute_relation")
*/
class Supplierattributerelation
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $supplier_attribute_relation_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $product_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $supplier_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $combination_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $quantity=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $impact_on_price="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

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

	public function getSupplier_attribute_relation_id()
	{
		return $this->supplier_attribute_relation_id;
	}

	public function getProduct_id()
	{
		return $this->product_id;
	}
	public function setProduct_id($product_id)
	{
		$this->product_id = $product_id;
	}

	public function getSupplier_id()
	{
		return $this->supplier_id;
	}
	public function setSupplier_id($supplier_id)
	{
		$this->supplier_id = $supplier_id;
	}

	public function getCombination_id()
	{
		return $this->combination_id;
	}
	public function setCombination_id($combination_id)
	{
		$this->combination_id = $combination_id;
	}

	public function getQuantity()
	{
		return $this->quantity;
	}
	public function setQuantity($quantity)
	{
		$this->quantity = $quantity;
	}

	public function getImpact_on_price()
	{
		return $this->impact_on_price;
	}
	public function setImpact_on_price($impact_on_price)
	{
		$this->impact_on_price = $impact_on_price;
	}

	public function getDomain_id()
	{
		return $this->domain_id;
	}
	public function setDomain_id($domain_id)
	{
		$this->domain_id = $domain_id;
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