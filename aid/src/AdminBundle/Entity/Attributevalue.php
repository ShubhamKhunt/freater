<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="attribute_value")
*/
class Attributevalue
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $attribute_value_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $attribute_master_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $value="";

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
	protected $language_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_attribute_value_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getAttribute_value_id()
	{
		return $this->attribute_value_id;
	}

	public function getAttribute_master_id()
	{
		return $this->attribute_master_id;
	}
	public function setAttribute_master_id($attribute_master_id)
	{
		$this->attribute_master_id = $attribute_master_id;
	}

	public function getValue()
	{
		return $this->value;
	}
	public function setValue($value)
	{
		$this->value = $value;
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

	public function getLanguage_id()
	{
		return $this->language_id;
	}
	public function setLanguage_id($language_id)
	{
		$this->language_id = $language_id;
	}

	public function getMain_attribute_value_id()
	{
		return $this->main_attribute_value_id;
	}
	public function setMain_attribute_value_id($main_attribute_value_id)
	{
		$this->main_attribute_value_id = $main_attribute_value_id;
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