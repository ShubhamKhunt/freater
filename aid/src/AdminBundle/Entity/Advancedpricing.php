<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="advanced_pricing")
*/
class Advancedpricing
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $advanced_pricing_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $product_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $product_supplier_relation_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $ptd="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $distributor_margin="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $final_price_from_distributor="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $awl_margin="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $final_app_price="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $apply_from_date="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $updated_date="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $is_deleted="";

	public function getAdvanced_pricing_id()
	{
		return $this->advanced_pricing_id;
	}

	public function getProduct_id()
	{
		return $this->product_id;
	}
	public function setProduct_id($product_id)
	{
		$this->product_id = $product_id;
	}

	public function getProduct_supplier_relation_id()
	{
		return $this->product_supplier_relation_id;
	}
	public function setProduct_supplier_relation_id($product_supplier_relation_id)
	{
		$this->product_supplier_relation_id = $product_supplier_relation_id;
	}

	public function getPtd()
	{
		return $this->ptd;
	}
	public function setPtd($ptd)
	{
		$this->ptd = $ptd;
	}

	public function getDistributor_margin()
	{
		return $this->distributor_margin;
	}
	public function setDistributor_margin($distributor_margin)
	{
		$this->distributor_margin = $distributor_margin;
	}

	public function getFinal_price_from_distributor()
	{
		return $this->final_price_from_distributor;
	}
	public function setFinal_price_from_distributor($final_price_from_distributor)
	{
		$this->final_price_from_distributor = $final_price_from_distributor;
	}

	public function getAwl_margin()
	{
		return $this->awl_margin;
	}
	public function setAwl_margin($awl_margin)
	{
		$this->awl_margin = $awl_margin;
	}

	public function getFinal_app_price()
	{
		return $this->final_app_price;
	}
	public function setFinal_app_price($final_app_price)
	{
		$this->final_app_price = $final_app_price;
	}

	public function getApply_from_date()
	{
		return $this->apply_from_date;
	}
	public function setApply_from_date($apply_from_date)
	{
		$this->apply_from_date = $apply_from_date;
	}

	public function getDomain_id()
	{
		return $this->domain_id;
	}
	public function setDomain_id($domain_id)
	{
		$this->domain_id = $domain_id;
	}

	public function getUpdated_date()
	{
		return $this->updated_date;
	}
	public function setUpdated_date($updated_date)
	{
		$this->updated_date = $updated_date;
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