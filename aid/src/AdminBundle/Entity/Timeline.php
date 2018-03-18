<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="timeline")
*/
class Timeline
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $timeline_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $operationmaster_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $user_master_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $operation_string="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $labelmaster_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $create_date="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getTimeline_id()
	{
		return $this->timeline_id;
	}

	public function getOperationmaster_id()
	{
		return $this->operationmaster_id;
	}
	public function setOperationmaster_id($operationmaster_id)
	{
		$this->operationmaster_id = $operationmaster_id;
	}

	public function getUser_master_id()
	{
		return $this->user_master_id;
	}
	public function setUser_master_id($user_master_id)
	{
		$this->user_master_id = $user_master_id;
	}

	public function getOperation_string()
	{
		return $this->operation_string;
	}
	public function setOperation_string($operation_string)
	{
		$this->operation_string = $operation_string;
	}

	public function getLabelmaster_id()
	{
		return $this->labelmaster_id;
	}
	public function setLabelmaster_id($labelmaster_id)
	{
		$this->labelmaster_id = $labelmaster_id;
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