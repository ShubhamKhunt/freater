<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="language_master")
*/
class Languagemaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $language_master_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $language_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $display_title="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $language_code="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getLanguage_master_id()
	{
		return $this->language_master_id;
	}

	public function getLanguage_name()
	{
		return $this->language_name;
	}
	public function setLanguage_name($language_name)
	{
		$this->language_name = $language_name;
	}

	public function getDisplay_title()
	{
		return $this->display_title;
	}
	public function setDisplay_title($display_title)
	{
		$this->display_title = $display_title;
	}

	public function getLanguage_code()
	{
		return $this->language_code;
	}
	public function setLanguage_code($language_code)
	{
		$this->language_code = $language_code;
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