<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="product_tax_relation")
*/
class Producttaxrelation
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $product_tax_relation_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $proudct_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $tax_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $tax_value="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getProduct_tax_relation_id()
	{
		return $this->product_tax_relation_id;
	}

	public function getProudct_id()
	{
		return $this->proudct_id;
	}
	public function setProudct_id($proudct_id)
	{
		$this->proudct_id = $proudct_id;
	}

	public function getTax_id()
	{
		return $this->tax_id;
	}
	public function setTax_id($tax_id)
	{
		$this->tax_id = $tax_id;
	}

	public function getTax_value()
	{
		return $this->tax_value;
	}
	public function setTax_value($tax_value)
	{
		$this->tax_value = $tax_value;
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