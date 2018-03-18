<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="faq_type")
*/
class Faqtype
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $faq_type_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $faq_type_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $status="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $language_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $main_faq_type_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $is_deleted="";

	public function getFaq_type_id()
	{
		return $this->faq_type_id;
	}

	public function getFaq_type_name()
	{
		return $this->faq_type_name;
	}
	public function setFaq_type_name($faq_type_name)
	{
		$this->faq_type_name = $faq_type_name;
	}

	public function getStatus()
	{
		return $this->status;
	}
	public function setStatus($status)
	{
		$this->status = $status;
	}

	public function getLanguage_id()
	{
		return $this->language_id;
	}
	public function setLanguage_id($language_id)
	{
		$this->language_id = $language_id;
	}

	public function getMain_faq_type_id()
	{
		return $this->main_faq_type_id;
	}
	public function setMain_faq_type_id($main_faq_type_id)
	{
		$this->main_faq_type_id = $main_faq_type_id;
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