<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="supplier_master")
*/
class Suppliermaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $supplier_master_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $supplier_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $supplier_description="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $phone_no="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $mobile_no="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $address_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $supplier_logo=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $status="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $created_by=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $created_date="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_supplier_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $language_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getSupplier_master_id()
	{
		return $this->supplier_master_id;
	}

	public function getSupplier_name()
	{
		return $this->supplier_name;
	}
	public function setSupplier_name($supplier_name)
	{
		$this->supplier_name = $supplier_name;
	}

	public function getSupplier_description()
	{
		return $this->supplier_description;
	}
	public function setSupplier_description($supplier_description)
	{
		$this->supplier_description = $supplier_description;
	}

	public function getPhone_no()
	{
		return $this->phone_no;
	}
	public function setPhone_no($phone_no)
	{
		$this->phone_no = $phone_no;
	}

	public function getMobile_no()
	{
		return $this->mobile_no;
	}
	public function setMobile_no($mobile_no)
	{
		$this->mobile_no = $mobile_no;
	}

	public function getAddress_id()
	{
		return $this->address_id;
	}
	public function setAddress_id($address_id)
	{
		$this->address_id = $address_id;
	}

	public function getSupplier_logo()
	{
		return $this->supplier_logo;
	}
	public function setSupplier_logo($supplier_logo)
	{
		$this->supplier_logo = $supplier_logo;
	}

	public function getStatus()
	{
		return $this->status;
	}
	public function setStatus($status)
	{
		$this->status = $status;
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

	public function getMain_supplier_id()
	{
		return $this->main_supplier_id;
	}
	public function setMain_supplier_id($main_supplier_id)
	{
		$this->main_supplier_id = $main_supplier_id;
	}

	public function getLanguage_id()
	{
		return $this->language_id;
	}
	public function setLanguage_id($language_id)
	{
		$this->language_id = $language_id;
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