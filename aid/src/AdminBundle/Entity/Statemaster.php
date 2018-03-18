<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="state_master")
*/
class Statemaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $state_master_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $state_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $state_code="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_country_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_state_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $language_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $status="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getState_master_id()
	{
		return $this->state_master_id;
	}

	public function getState_name()
	{
		return $this->state_name;
	}
	public function setState_name($state_name)
	{
		$this->state_name = $state_name;
	}

	public function getState_code()
	{
		return $this->state_code;
	}
	public function setState_code($state_code)
	{
		$this->state_code = $state_code;
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

	public function getLanguage_id()
	{
		return $this->language_id;
	}
	public function setLanguage_id($language_id)
	{
		$this->language_id = $language_id;
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