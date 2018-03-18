<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="gallery_master")
*/
class Gallerymaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $gallery_master_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $module_name="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $module_primary_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $media_library_master_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $created_by=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $create_date="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getGallery_master_id()
	{
		return $this->gallery_master_id;
	}

	public function getModule_name()
	{
		return $this->module_name;
	}
	public function setModule_name($module_name)
	{
		$this->module_name = $module_name;
	}

	public function getModule_primary_id()
	{
		return $this->module_primary_id;
	}
	public function setModule_primary_id($module_primary_id)
	{
		$this->module_primary_id = $module_primary_id;
	}

	public function getMedia_library_master_id()
	{
		return $this->media_library_master_id;
	}
	public function setMedia_library_master_id($media_library_master_id)
	{
		$this->media_library_master_id = $media_library_master_id;
	}

	public function getCreated_by()
	{
		return $this->created_by;
	}
	public function setCreated_by($created_by)
	{
		$this->created_by = $created_by;
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