<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="tax_master")
*/
class Taxmaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $tax_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $tax_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $tax_percentage="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $description="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getTax_id()
	{
		return $this->tax_id;
	}

	public function getTax_name()
	{
		return $this->tax_name;
	}
	public function setTax_name($tax_name)
	{
		$this->tax_name = $tax_name;
	}

	public function getTax_percentage()
	{
		return $this->tax_percentage;
	}
	public function setTax_percentage($tax_percentage)
	{
		$this->tax_percentage = $tax_percentage;
	}

	public function getDescription()
	{
		return $this->description;
	}
	public function setDescription($description)
	{
		$this->description = $description;
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