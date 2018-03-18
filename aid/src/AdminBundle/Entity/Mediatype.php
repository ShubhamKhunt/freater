<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="media_type")
*/
class Mediatype
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $media_type_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $media_type_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $media_type_allowed="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getMedia_type_id()
	{
		return $this->media_type_id;
	}

	public function getMedia_type_name()
	{
		return $this->media_type_name;
	}
	public function setMedia_type_name($media_type_name)
	{
		$this->media_type_name = $media_type_name;
	}

	public function getMedia_type_allowed()
	{
		return $this->media_type_allowed;
	}
	public function setMedia_type_allowed($media_type_allowed)
	{
		$this->media_type_allowed = $media_type_allowed;
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