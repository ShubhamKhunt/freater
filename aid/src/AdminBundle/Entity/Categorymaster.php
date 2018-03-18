<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="category_master")
*/
class Categorymaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $category_master_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $category_name="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $parent_category_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $email_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $category_description="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $category_image_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_category_id=0;

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
	protected $popular_count=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $created_datetime="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getCategory_master_id()
	{
		return $this->category_master_id;
	}

	public function getCategory_name()
	{
		return $this->category_name;
	}
	public function setCategory_name($category_name)
	{
		$this->category_name = $category_name;
	}

	public function getParent_category_id()
	{
		return $this->parent_category_id;
	}
	public function setParent_category_id($parent_category_id)
	{
		$this->parent_category_id = $parent_category_id;
	}

	public function getCategory_description()
	{
		return $this->category_description;
	}
	public function setCategory_description($category_description)
	{
		$this->category_description = $category_description;
	}

	public function getCategory_image_id()
	{
		return $this->category_image_id;
	}
	public function setCategory_image_id($category_image_id)
	{
		$this->category_image_id = $category_image_id;
	}

	public function getMain_category_id()
	{
		return $this->main_category_id;
	}
	public function setMain_category_id($main_category_id)
	{
		$this->main_category_id = $main_category_id;
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

	public function getPopular_count()
	{
		return $this->popular_count;
	}
	public function setPopular_count($popular_count)
	{
		$this->popular_count = $popular_count;
	}

	public function getCreated_datetime()
	{
		return $this->created_datetime;
	}
	public function setCreated_datetime($created_datetime)
	{
		$this->created_datetime = $created_datetime;
	}

	public function getIs_deleted()
	{
		return $this->is_deleted;
	}
	public function setIs_deleted($is_deleted)
	{
		$this->is_deleted = $is_deleted;
	}
	public function getEmail_id()
	{
		return $this->email_id;
	}
	public function setEmail_id($email_id)
	{
		$this->email_id = $email_id;
	}
}