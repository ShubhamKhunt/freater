<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="tag_master")
*/
class Tagmaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $tag_master_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $tag_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $tag_display_name="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_tag_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $language_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $is_deleted="";

	public function getTag_master_id()
	{
		return $this->tag_master_id;
	}

	public function getTag_name()
	{
		return $this->tag_name;
	}
	public function setTag_name($tag_name)
	{
		$this->tag_name = $tag_name;
	}

	public function getTag_display_name()
	{
		return $this->tag_display_name;
	}
	public function setTag_display_name($tag_display_name)
	{
		$this->tag_display_name = $tag_display_name;
	}

	public function getMain_tag_id()
	{
		return $this->main_tag_id;
	}
	public function setMain_tag_id($main_tag_id)
	{
		$this->main_tag_id = $main_tag_id;
	}

	public function getLanguage_id()
	{
		return $this->language_id;
	}
	public function setLanguage_id($language_id)
	{
		$this->language_id = $language_id;
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