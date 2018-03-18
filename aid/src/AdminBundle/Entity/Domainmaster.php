<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="domain_master")
*/
class Domainmaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $domain_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_code="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_name="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $domain_logo_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $domain_cover_image_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $created_by=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $created_datetime="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getDomain_id()
	{
		return $this->domain_id;
	}

	public function getDomain_code()
	{
		return $this->domain_code;
	}
	public function setDomain_code($domain_code)
	{
		$this->domain_code = $domain_code;
	}

	public function getDomain_name()
	{
		return $this->domain_name;
	}
	public function setDomain_name($domain_name)
	{
		$this->domain_name = $domain_name;
	}

	public function getDomain_logo_id()
	{
		return $this->domain_logo_id;
	}
	public function setDomain_logo_id($domain_logo_id)
	{
		$this->domain_logo_id = $domain_logo_id;
	}

	public function getDomain_cover_image_id()
	{
		return $this->domain_cover_image_id;
	}
	public function setDomain_cover_image_id($domain_cover_image_id)
	{
		$this->domain_cover_image_id = $domain_cover_image_id;
	}

	public function getCreated_by()
	{
		return $this->created_by;
	}
	public function setCreated_by($created_by)
	{
		$this->created_by = $created_by;
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
}