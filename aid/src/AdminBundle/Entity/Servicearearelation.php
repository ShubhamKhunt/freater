<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="service_area_relation")
*/
class Servicearearelation
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $service_area_relation_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_area_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_city_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_state_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $domain_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $status="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $create_date="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getService_area_relation_id()
	{
		return $this->service_area_relation_id;
	}

	public function getMain_area_id()
	{
		return $this->main_area_id;
	}
	public function setMain_area_id($main_area_id)
	{
		$this->main_area_id = $main_area_id;
	}

	public function getMain_city_id()
	{
		return $this->main_city_id;
	}
	public function setMain_city_id($main_city_id)
	{
		$this->main_city_id = $main_city_id;
	}

	public function getMain_state_id()
	{
		return $this->main_state_id;
	}
	public function setMain_state_id($main_state_id)
	{
		$this->main_state_id = $main_state_id;
	}

	public function getDomain_id()
	{
		return $this->domain_id;
	}
	public function setDomain_id($domain_id)
	{
		$this->domain_id = $domain_id;
	}

	public function getStatus()
	{
		return $this->status;
	}
	public function setStatus($status)
	{
		$this->status = $status;
	}

	public function getCreate_date()
	{
		return $this->create_date;
	}
	public function setCreate_date($create_date)
	{
		$this->create_date = $create_date;
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