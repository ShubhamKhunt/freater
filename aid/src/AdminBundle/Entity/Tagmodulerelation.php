<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="tag_module_relation")
*/
class Tagmodulerelation
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $tag_module_relation_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $module_name="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_tag_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_category_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $is_deleted="";

	public function getTag_module_relation_id()
	{
		return $this->tag_module_relation_id;
	}

	public function getModule_name()
	{
		return $this->module_name;
	}
	public function setModule_name($module_name)
	{
		$this->module_name = $module_name;
	}

	public function getMain_tag_id()
	{
		return $this->main_tag_id;
	}
	public function setMain_tag_id($main_tag_id)
	{
		$this->main_tag_id = $main_tag_id;
	}

	public function getMain_category_id()
	{
		return $this->main_category_id;
	}
	public function setMain_category_id($main_category_id)
	{
		$this->main_category_id = $main_category_id;
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