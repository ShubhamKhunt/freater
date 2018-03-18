<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="pricing_rule_master")
*/
class Pricingrulemaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $pricing_rule_master_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $rule_title="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $rule_description="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $status="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $start_date="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $end_date="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $language_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_pricing_rule_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $is_deleted="";

	public function getPricing_rule_master_id()
	{
		return $this->pricing_rule_master_id;
	}

	public function getRule_title()
	{
		return $this->rule_title;
	}
	public function setRule_title($rule_title)
	{
		$this->rule_title = $rule_title;
	}

	public function getRule_description()
	{
		return $this->rule_description;
	}
	public function setRule_description($rule_description)
	{
		$this->rule_description = $rule_description;
	}

	public function getStatus()
	{
		return $this->status;
	}
	public function setStatus($status)
	{
		$this->status = $status;
	}

	public function getStart_date()
	{
		return $this->start_date;
	}
	public function setStart_date($start_date)
	{
		$this->start_date = $start_date;
	}

	public function getEnd_date()
	{
		return $this->end_date;
	}
	public function setEnd_date($end_date)
	{
		$this->end_date = $end_date;
	}

	public function getLanguage_id()
	{
		return $this->language_id;
	}
	public function setLanguage_id($language_id)
	{
		$this->language_id = $language_id;
	}

	public function getMain_pricing_rule_id()
	{
		return $this->main_pricing_rule_id;
	}
	public function setMain_pricing_rule_id($main_pricing_rule_id)
	{
		$this->main_pricing_rule_id = $main_pricing_rule_id;
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