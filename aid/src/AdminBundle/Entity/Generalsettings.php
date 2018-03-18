<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="general_settings")
*/
class Generalsettings
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $settings_master_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $site_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $site_description="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_logo_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $favicon_logo_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $default_language_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $default_time_zone_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $date_format="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $currency_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $domain_settings_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $usermaster_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getSettings_master_id()
	{
		return $this->settings_master_id;
	}

	public function getSite_name()
	{
		return $this->site_name;
	}
	public function setSite_name($site_name)
	{
		$this->site_name = $site_name;
	}

	public function getSite_description()
	{
		return $this->site_description;
	}
	public function setSite_description($site_description)
	{
		$this->site_description = $site_description;
	}

	public function getMain_logo_id()
	{
		return $this->main_logo_id;
	}
	public function setMain_logo_id($main_logo_id)
	{
		$this->main_logo_id = $main_logo_id;
	}

	public function getFavicon_logo_id()
	{
		return $this->favicon_logo_id;
	}
	public function setFavicon_logo_id($favicon_logo_id)
	{
		$this->favicon_logo_id = $favicon_logo_id;
	}

	public function getDefault_language_id()
	{
		return $this->default_language_id;
	}
	public function setDefault_language_id($default_language_id)
	{
		$this->default_language_id = $default_language_id;
	}

	public function getDefault_time_zone_id()
	{
		return $this->default_time_zone_id;
	}
	public function setDefault_time_zone_id($default_time_zone_id)
	{
		$this->default_time_zone_id = $default_time_zone_id;
	}

	public function getDate_format()
	{
		return $this->date_format;
	}
	public function setDate_format($date_format)
	{
		$this->date_format = $date_format;
	}

	public function getCurrency_id()
	{
		return $this->currency_id;
	}
	public function setCurrency_id($currency_id)
	{
		$this->currency_id = $currency_id;
	}

	public function getDomain_settings_id()
	{
		return $this->domain_settings_id;
	}
	public function setDomain_settings_id($domain_settings_id)
	{
		$this->domain_settings_id = $domain_settings_id;
	}

	public function getUsermaster_id()
	{
		return $this->usermaster_id;
	}
	public function setUsermaster_id($usermaster_id)
	{
		$this->usermaster_id = $usermaster_id;
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