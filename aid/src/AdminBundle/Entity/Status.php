<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="status")
*/
class Status
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $status_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $status_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $status_description="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $status_class="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $status_createdby=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $status_dateadded="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getStatus_id()
	{
		return $this->status_id;
	}

	public function getStatus_name()
	{
		return $this->status_name;
	}
	public function setStatus_name($status_name)
	{
		$this->status_name = $status_name;
	}

	public function getStatus_description()
	{
		return $this->status_description;
	}
	public function setStatus_description($status_description)
	{
		$this->status_description = $status_description;
	}

	public function getStatus_class()
	{
		return $this->status_class;
	}
	public function setStatus_class($status_class)
	{
		$this->status_class = $status_class;
	}

	public function getStatus_createdby()
	{
		return $this->status_createdby;
	}
	public function setStatus_createdby($status_createdby)
	{
		$this->status_createdby = $status_createdby;
	}

	public function getStatus_dateadded()
	{
		return $this->status_dateadded;
	}
	public function setStatus_dateadded($status_dateadded)
	{
		$this->status_dateadded = $status_dateadded;
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