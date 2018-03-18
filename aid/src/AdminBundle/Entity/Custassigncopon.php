<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="cust_assign_copon")
*/
class Custassigncopon
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $cust_assign_copon_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $customer_user_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $app_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $table_name="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $table_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getCust_assign_copon_id()
	{
		return $this->cust_assign_copon_id;
	}

	public function getCustomer_user_id()
	{
		return $this->customer_user_id;
	}
	public function setCustomer_user_id($customer_user_id)
	{
		$this->customer_user_id = $customer_user_id;
	}

	public function getDomain_id()
	{
		return $this->domain_id;
	}
	public function setDomain_id($domain_id)
	{
		$this->domain_id = $domain_id;
	}

	public function getApp_id()
	{
		return $this->app_id;
	}
	public function setApp_id($app_id)
	{
		$this->app_id = $app_id;
	}

	public function getTable_name()
	{
		return $this->table_name;
	}
	public function setTable_name($table_name)
	{
		$this->table_name = $table_name;
	}

	public function getTable_id()
	{
		return $this->table_id;
	}
	public function setTable_id($table_id)
	{
		$this->table_id = $table_id;
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