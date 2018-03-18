<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="app_details")
*/
class Appdetails
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $app_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $app_code="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $app_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $app_type_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $app_gcm_key="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $app_apns_certificate_development="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $app_apns_certificate_development_password="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $app_apns_certificate_production="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $app_apns_certificate_production_password="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $status="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getApp_id()
	{
		return $this->app_id;
	}

	public function getApp_code()
	{
		return $this->app_code;
	}
	public function setApp_code($app_code)
	{
		$this->app_code = $app_code;
	}

	public function getApp_name()
	{
		return $this->app_name;
	}
	public function setApp_name($app_name)
	{
		$this->app_name = $app_name;
	}

	public function getDomain_id()
	{
		return $this->domain_id;
	}
	public function setDomain_id($domain_id)
	{
		$this->domain_id = $domain_id;
	}

	public function getApp_type_id()
	{
		return $this->app_type_id;
	}
	public function setApp_type_id($app_type_id)
	{
		$this->app_type_id = $app_type_id;
	}

	public function getApp_gcm_key()
	{
		return $this->app_gcm_key;
	}
	public function setApp_gcm_key($app_gcm_key)
	{
		$this->app_gcm_key = $app_gcm_key;
	}

	public function getApp_apns_certificate_development()
	{
		return $this->app_apns_certificate_development;
	}
	public function setApp_apns_certificate_development($app_apns_certificate_development)
	{
		$this->app_apns_certificate_development = $app_apns_certificate_development;
	}

	public function getApp_apns_certificate_development_password()
	{
		return $this->app_apns_certificate_development_password;
	}
	public function setApp_apns_certificate_development_password($app_apns_certificate_development_password)
	{
		$this->app_apns_certificate_development_password = $app_apns_certificate_development_password;
	}

	public function getApp_apns_certificate_production()
	{
		return $this->app_apns_certificate_production;
	}
	public function setApp_apns_certificate_production($app_apns_certificate_production)
	{
		$this->app_apns_certificate_production = $app_apns_certificate_production;
	}

	public function getApp_apns_certificate_production_password()
	{
		return $this->app_apns_certificate_production_password;
	}
	public function setApp_apns_certificate_production_password($app_apns_certificate_production_password)
	{
		$this->app_apns_certificate_production_password = $app_apns_certificate_production_password;
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