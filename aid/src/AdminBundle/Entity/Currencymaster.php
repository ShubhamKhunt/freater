<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="currency_master")
*/
class Currencymaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $currency_master_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $currency_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $created_on="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getCurrency_master_id()
	{
		return $this->currency_master_id;
	}

	public function getCurrency_name()
	{
		return $this->currency_name;
	}
	public function setCurrency_name($currency_name)
	{
		$this->currency_name = $currency_name;
	}

	public function getCreated_on()
	{
		return $this->created_on;
	}
	public function setCreated_on($created_on)
	{
		$this->created_on = $created_on;
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