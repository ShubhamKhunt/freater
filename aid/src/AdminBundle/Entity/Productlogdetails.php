<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="product_log_details")
*/
class Productlogdetails
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $product_log_details_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $user_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $operation="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $description="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $success_msg="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $error_msg="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $created_datetime="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $ip_address="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $file_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $is_deleted="";

	public function getProduct_log_details_id()
	{
		return $this->product_log_details_id;
	}

	public function getUser_id()
	{
		return $this->user_id;
	}
	public function setUser_id($user_id)
	{
		$this->user_id = $user_id;
	}

	public function getOperation()
	{
		return $this->operation;
	}
	public function setOperation($operation)
	{
		$this->operation = $operation;
	}

	public function getDescription()
	{
		return $this->description;
	}
	public function setDescription($description)
	{
		$this->description = $description;
	}

	public function getSuccess_msg()
	{
		return $this->success_msg;
	}
	public function setSuccess_msg($success_msg)
	{
		$this->success_msg = $success_msg;
	}

	public function getError_msg()
	{
		return $this->error_msg;
	}
	public function setError_msg($error_msg)
	{
		$this->error_msg = $error_msg;
	}

	public function getCreated_datetime()
	{
		return $this->created_datetime;
	}
	public function setCreated_datetime($created_datetime)
	{
		$this->created_datetime = $created_datetime;
	}

	public function getIp_address()
	{
		return $this->ip_address;
	}
	public function setIp_address($ip_address)
	{
		$this->ip_address = $ip_address;
	}

	public function getFile_name()
	{
		return $this->file_name;
	}
	public function setFile_name($file_name)
	{
		$this->file_name = $file_name;
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