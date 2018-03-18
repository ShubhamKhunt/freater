<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="applied_coupon_relation")
*/
class Appliedcouponrelation
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $applied_coupon_relation_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $coupon_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $assign_on="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $status="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getApplied_coupon_relation_id()
	{
		return $this->applied_coupon_relation_id;
	}

	public function getCoupon_id()
	{
		return $this->coupon_id;
	}
	public function setCoupon_id($coupon_id)
	{
		$this->coupon_id = $coupon_id;
	}

	public function getAssign_on()
	{
		return $this->assign_on;
	}
	public function setAssign_on($assign_on)
	{
		$this->assign_on = $assign_on;
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

	public function getIs_deleted()
	{
		return $this->is_deleted;
	}
	public function setIs_deleted($is_deleted)
	{
		$this->is_deleted = $is_deleted;
	}
}