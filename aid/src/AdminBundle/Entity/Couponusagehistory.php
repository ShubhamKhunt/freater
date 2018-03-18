<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="coupon_usage_history")
*/
class Couponusagehistory
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $coupon_usage_history_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $coupon_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $user_master_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $usage_count=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $create_date="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getCoupon_usage_history_id()
	{
		return $this->coupon_usage_history_id;
	}

	public function getCoupon_id()
	{
		return $this->coupon_id;
	}
	public function setCoupon_id($coupon_id)
	{
		$this->coupon_id = $coupon_id;
	}

	public function getUser_master_id()
	{
		return $this->user_master_id;
	}
	public function setUser_master_id($user_master_id)
	{
		$this->user_master_id = $user_master_id;
	}

	public function getUsage_count()
	{
		return $this->usage_count;
	}
	public function setUsage_count($usage_count)
	{
		$this->usage_count = $usage_count;
	}

	public function getCreate_date()
	{
		return $this->create_date;
	}
	public function setCreate_date($create_date)
	{
		$this->create_date = $create_date;
	}

	public function getDomain_id()
	{
		return $this->domain_id;
	}
	public function setDomain_id($domain_id)
	{
		$this->domain_id = $domain_id;
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