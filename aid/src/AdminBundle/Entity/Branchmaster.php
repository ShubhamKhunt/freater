<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="branch_master")
*/
class Branchmaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $branch_master_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $branch_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $branch_description="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_branch_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $company_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $language_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getBranch_master_id()
	{
		return $this->branch_master_id;
	}

	public function getBranch_name()
	{
		return $this->branch_name;
	}
	public function setBranch_name($branch_name)
	{
		$this->branch_name = $branch_name;
	}

	public function getBranch_description()
	{
		return $this->branch_description;
	}
	public function setBranch_description($branch_description)
	{
		$this->branch_description = $branch_description;
	}

	public function getMain_branch_id()
	{
		return $this->main_branch_id;
	}
	public function setMain_branch_id($main_branch_id)
	{
		$this->main_branch_id = $main_branch_id;
	}

	public function getCompany_id()
	{
		return $this->company_id;
	}
	public function setCompany_id($company_id)
	{
		$this->company_id = $company_id;
	}

	public function getLanguage_id()
	{
		return $this->language_id;
	}
	public function setLanguage_id($language_id)
	{
		$this->language_id = $language_id;
	}

	public function getDomain_id()
	{
		return $this->domain_id;
	}
	public function setDomain_id($domain_id)
	{
		$this->domain_id = $domain_id;
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