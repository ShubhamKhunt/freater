<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="right_master")
*/
class Rightmaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $right_master_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $code="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $description="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $display_name="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getRight_master_id()
	{
		return $this->right_master_id;
	}

	public function getCode()
	{
		return $this->code;
	}
	public function setCode($code)
	{
		$this->code = $code;
	}

	public function getDescription()
	{
		return $this->description;
	}
	public function setDescription($description)
	{
		$this->description = $description;
	}

	public function getDisplay_name()
	{
		return $this->display_name;
	}
	public function setDisplay_name($display_name)
	{
		$this->display_name = $display_name;
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