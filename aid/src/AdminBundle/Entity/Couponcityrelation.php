<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="coupon_city_relation")
*/
class Couponcityrelation
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $coupon_city_relation_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $coupon_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $city_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $country_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $state_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $status="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $create_date="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getCoupon_city_relation_id()
	{
		return $this->coupon_city_relation_id;
	}

	public function getCoupon_id()
	{
		return $this->coupon_id;
	}
	public function setCoupon_id($coupon_id)
	{
		$this->coupon_id = $coupon_id;
	}

	public function getCity_id()
	{
		return $this->city_id;
	}
	public function setCity_id($city_id)
	{
		$this->city_id = $city_id;
	}

	public function getCountry_id()
	{
		return $this->country_id;
	}
	public function setCountry_id($country_id)
	{
		$this->country_id = $country_id;
	}

	public function getState_id()
	{
		return $this->state_id;
	}
	public function setState_id($state_id)
	{
		$this->state_id = $state_id;
	}

	public function getStatus()
	{
		return $this->status;
	}
	public function setStatus($status)
	{
		$this->status = $status;
	}

	public function getDomain_id()
	{
		return $this->domain_id;
	}
	public function setDomain_id($domain_id)
	{
		$this->domain_id = $domain_id;
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