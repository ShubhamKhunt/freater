<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="organization_master")
*/
class Organizationmaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $organization_master_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $organization_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $organization_description="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_organization_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $langauge_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getOrganization_master_id()
	{
		return $this->organization_master_id;
	}

	public function getOrganization_name()
	{
		return $this->organization_name;
	}
	public function setOrganization_name($organization_name)
	{
		$this->organization_name = $organization_name;
	}

	public function getOrganization_description()
	{
		return $this->organization_description;
	}
	public function setOrganization_description($organization_description)
	{
		$this->organization_description = $organization_description;
	}

	public function getMain_organization_id()
	{
		return $this->main_organization_id;
	}
	public function setMain_organization_id($main_organization_id)
	{
		$this->main_organization_id = $main_organization_id;
	}

	public function getLangauge_id()
	{
		return $this->langauge_id;
	}
	public function setLangauge_id($langauge_id)
	{
		$this->langauge_id = $langauge_id;
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