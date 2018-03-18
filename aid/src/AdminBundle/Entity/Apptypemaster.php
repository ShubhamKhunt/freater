<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="app_type_master")
*/
class Apptypemaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $app_type_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $app_type_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $app_type_code="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $app_type_status="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getApp_type_id()
	{
		return $this->app_type_id;
	}

	public function getApp_type_name()
	{
		return $this->app_type_name;
	}
	public function setApp_type_name($app_type_name)
	{
		$this->app_type_name = $app_type_name;
	}

	public function getApp_type_code()
	{
		return $this->app_type_code;
	}
	public function setApp_type_code($app_type_code)
	{
		$this->app_type_code = $app_type_code;
	}

	public function getApp_type_status()
	{
		return $this->app_type_status;
	}
	public function setApp_type_status($app_type_status)
	{
		$this->app_type_status = $app_type_status;
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