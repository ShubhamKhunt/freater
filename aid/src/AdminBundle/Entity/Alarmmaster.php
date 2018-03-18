<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="alarm_master")
*/
class Alarmmaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $alarm_master_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $user_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $drug_name="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $product_type_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $dosage="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $start_time="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $end_time="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $no_of_time="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $days=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $status="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $created_datetime="";

	public function getAlarm_master_id()
	{
		return $this->alarm_master_id;
	}

	public function getUser_id()
	{
		return $this->user_id;
	}
	public function setUser_id($user_id)
	{
		$this->user_id = $user_id;
	}

	public function getDrug_name()
	{
		return $this->drug_name;
	}
	public function setDrug_name($drug_name)
	{
		$this->drug_name = $drug_name;
	}

	public function getProduct_type_id()
	{
		return $this->product_type_id;
	}
	public function setProduct_type_id($product_type_id)
	{
		$this->product_type_id = $product_type_id;
	}

	public function getDosage()
	{
		return $this->dosage;
	}
	public function setDosage($dosage)
	{
		$this->dosage = $dosage;
	}

	public function getStart_time()
	{
		return $this->start_time;
	}
	public function setStart_time($start_time)
	{
		$this->start_time = $start_time;
	}

	public function getEnd_time()
	{
		return $this->end_time;
	}
	public function setEnd_time($end_time)
	{
		$this->end_time = $end_time;
	}

	public function getNo_of_time()
	{
		return $this->no_of_time;
	}
	public function setNo_of_time($no_of_time)
	{
		$this->no_of_time = $no_of_time;
	}

	public function getDays()
	{
		return $this->days;
	}
	public function setDays($days)
	{
		$this->days = $days;
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

	public function getCreated_datetime()
	{
		return $this->created_datetime;
	}
	public function setCreated_datetime($created_datetime)
	{
		$this->created_datetime = $created_datetime;
	}
}