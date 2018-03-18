<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="coupon_applied_list")
*/
class Couponappliedlist
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $coupon_applied_list_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $applied_coupon_relation_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $coupon_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $relation_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $create_date="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getCoupon_applied_list_id()
	{
		return $this->coupon_applied_list_id;
	}

	public function getApplied_coupon_relation_id()
	{
		return $this->applied_coupon_relation_id;
	}
	public function setApplied_coupon_relation_id($applied_coupon_relation_id)
	{
		$this->applied_coupon_relation_id = $applied_coupon_relation_id;
	}

	public function getCoupon_id()
	{
		return $this->coupon_id;
	}
	public function setCoupon_id($coupon_id)
	{
		$this->coupon_id = $coupon_id;
	}

	public function getRelation_id()
	{
		return $this->relation_id;
	}
	public function setRelation_id($relation_id)
	{
		$this->relation_id = $relation_id;
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