<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="delivery_charge")
*/
class Deliverycharge
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $delivery_charge_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_city_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $delivery_charge="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_country_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_state_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $created_by=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $create_date="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getDelivery_charge_id()
	{
		return $this->delivery_charge_id;
	}

	public function getMain_city_id()
	{
		return $this->main_city_id;
	}
	public function setMain_city_id($main_city_id)
	{
		$this->main_city_id = $main_city_id;
	}

	public function getDelivery_charge()
	{
		return $this->delivery_charge;
	}
	public function setDelivery_charge($delivery_charge)
	{
		$this->delivery_charge = $delivery_charge;
	}

	public function getMain_country_id()
	{
		return $this->main_country_id;
	}
	public function setMain_country_id($main_country_id)
	{
		$this->main_country_id = $main_country_id;
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

	public function getIs_deleted()
	{
		return $this->is_deleted;
	}
	public function setIs_deleted($is_deleted)
	{
		$this->is_deleted = $is_deleted;
	}
}