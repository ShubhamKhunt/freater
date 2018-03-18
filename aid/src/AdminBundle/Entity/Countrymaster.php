<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="country_master")
*/
class Countrymaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $country_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $iso="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $country_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $country_title="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $country_code="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $iso3="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $country_flag="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_country_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $language_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $status="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getCountry_id()
	{
		return $this->country_id;
	}

	public function getIso()
	{
		return $this->iso;
	}
	public function setIso($iso)
	{
		$this->iso = $iso;
	}

	public function getCountry_name()
	{
		return $this->country_name;
	}
	public function setCountry_name($country_name)
	{
		$this->country_name = $country_name;
	}

	public function getCountry_title()
	{
		return $this->country_title;
	}
	public function setCountry_title($country_title)
	{
		$this->country_title = $country_title;
	}

	public function getCountry_code()
	{
		return $this->country_code;
	}
	public function setCountry_code($country_code)
	{
		$this->country_code = $country_code;
	}

	public function getIso3()
	{
		return $this->iso3;
	}
	public function setIso3($iso3)
	{
		$this->iso3 = $iso3;
	}

	public function getCountry_flag()
	{
		return $this->country_flag;
	}
	public function setCountry_flag($country_flag)
	{
		$this->country_flag = $country_flag;
	}

	public function getMain_country_id()
	{
		return $this->main_country_id;
	}
	public function setMain_country_id($main_country_id)
	{
		$this->main_country_id = $main_country_id;
	}

	public function getLanguage_id()
	{
		return $this->language_id;
	}
	public function setLanguage_id($language_id)
	{
		$this->language_id = $language_id;
	}

	public function getStatus()
	{
		return $this->status;
	}
	public function setStatus($status)
	{
		$this->status = $status;
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