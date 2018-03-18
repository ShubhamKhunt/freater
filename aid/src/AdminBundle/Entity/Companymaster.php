<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="company_master")
*/
class Companymaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $company_master_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $company_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $company_description="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_company_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $langauge_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getCompany_master_id()
	{
		return $this->company_master_id;
	}

	public function getCompany_name()
	{
		return $this->company_name;
	}
	public function setCompany_name($company_name)
	{
		$this->company_name = $company_name;
	}

	public function getCompany_description()
	{
		return $this->company_description;
	}
	public function setCompany_description($company_description)
	{
		$this->company_description = $company_description;
	}

	public function getMain_company_id()
	{
		return $this->main_company_id;
	}
	public function setMain_company_id($main_company_id)
	{
		$this->main_company_id = $main_company_id;
	}

	public function getDomain_id()
	{
		return $this->domain_id;
	}
	public function setDomain_id($domain_id)
	{
		$this->domain_id = $domain_id;
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