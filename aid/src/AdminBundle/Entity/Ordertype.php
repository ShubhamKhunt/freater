<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="order_type")
*/
class Ordertype
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $order_type_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $order_type_name="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getOrder_type_id()
	{
		return $this->order_type_id;
	}

	public function getOrder_type_name()
	{
		return $this->order_type_name;
	}
	public function setOrder_type_name($order_type_name)
	{
		$this->order_type_name = $order_type_name;
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