<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="media_library_master")
*/
class Medialibrarymaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $media_library_master_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $media_type_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $media_title="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $media_location="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $media_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $created_on="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getMedia_library_master_id()
	{
		return $this->media_library_master_id;
	}

	public function getMedia_type_id()
	{
		return $this->media_type_id;
	}
	public function setMedia_type_id($media_type_id)
	{
		$this->media_type_id = $media_type_id;
	}

	public function getMedia_title()
	{
		return $this->media_title;
	}
	public function setMedia_title($media_title)
	{
		$this->media_title = $media_title;
	}

	public function getMedia_location()
	{
		return $this->media_location;
	}
	public function setMedia_location($media_location)
	{
		$this->media_location = $media_location;
	}

	public function getMedia_name()
	{
		return $this->media_name;
	}
	public function setMedia_name($media_name)
	{
		$this->media_name = $media_name;
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