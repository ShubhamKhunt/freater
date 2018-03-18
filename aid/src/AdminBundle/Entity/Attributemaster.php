<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="attribute_master")
*/
class Attributemaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $attribute_master_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $attribute_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $public_name="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $attribute_field_type_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $create_date="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $create_by=0;

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
	protected $main_attribute_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getAttribute_master_id()
	{
		return $this->attribute_master_id;
	}

	public function getAttribute_name()
	{
		return $this->attribute_name;
	}
	public function setAttribute_name($attribute_name)
	{
		$this->attribute_name = $attribute_name;
	}

	public function getPublic_name()
	{
		return $this->public_name;
	}
	public function setPublic_name($public_name)
	{
		$this->public_name = $public_name;
	}

	public function getAttribute_field_type_id()
	{
		return $this->attribute_field_type_id;
	}
	public function setAttribute_field_type_id($attribute_field_type_id)
	{
		$this->attribute_field_type_id = $attribute_field_type_id;
	}

	public function getCreate_date()
	{
		return $this->create_date;
	}
	public function setCreate_date($create_date)
	{
		$this->create_date = $create_date;
	}

	public function getCreate_by()
	{
		return $this->create_by;
	}
	public function setCreate_by($create_by)
	{
		$this->create_by = $create_by;
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

	public function getMain_attribute_id()
	{
		return $this->main_attribute_id;
	}
	public function setMain_attribute_id($main_attribute_id)
	{
		$this->main_attribute_id = $main_attribute_id;
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