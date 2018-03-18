<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name=" issue_title_master")
*/
class Issuetitlemaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $issue_title_master_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $issue_title_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $issue_type="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $language_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_issue_title_master_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $status="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $created_by=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $create_date="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getIssue_title_master_id()
	{
		return $this->issue_title_master_id;
	}

	public function getIssue_title_name()
	{
		return $this->issue_title_name;
	}
	public function setIssue_title_name($issue_title_name)
	{
		$this->issue_title_name = $issue_title_name;
	}

	public function getIssue_type()
	{
		return $this->issue_type;
	}
	public function setIssue_type($issue_type)
	{
		$this->issue_type = $issue_type;
	}

	public function getLanguage_id()
	{
		return $this->language_id;
	}
	public function setLanguage_id($language_id)
	{
		$this->language_id = $language_id;
	}

	public function getMain_issue_title_master_id()
	{
		return $this->main_issue_title_master_id;
	}
	public function setMain_issue_title_master_id($main_issue_title_master_id)
	{
		$this->main_issue_title_master_id = $main_issue_title_master_id;
	}

	public function getStatus()
	{
		return $this->status;
	}
	public function setStatus($status)
	{
		$this->status = $status;
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