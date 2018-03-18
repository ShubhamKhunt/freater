<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="attribute_product_value_relation")
*/
class Attributeproductvaluerelation
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $attribute_product_value_relation_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $attribute_master_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $attribute_value_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $product_id=0;

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
	protected $is_deleted=0;

	public function getAttribute_product_value_relation_id()
	{
		return $this->attribute_product_value_relation_id;
	}

	public function getAttribute_master_id()
	{
		return $this->attribute_master_id;
	}
	public function setAttribute_master_id($attribute_master_id)
	{
		$this->attribute_master_id = $attribute_master_id;
	}

	public function getAttribute_value_id()
	{
		return $this->attribute_value_id;
	}
	public function setAttribute_value_id($attribute_value_id)
	{
		$this->attribute_value_id = $attribute_value_id;
	}

	public function getProduct_id()
	{
		return $this->product_id;
	}
	public function setProduct_id($product_id)
	{
		$this->product_id = $product_id;
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

	public function getIs_deleted()
	{
		return $this->is_deleted;
	}
	public function setIs_deleted($is_deleted)
	{
		$this->is_deleted = $is_deleted;
	}
}