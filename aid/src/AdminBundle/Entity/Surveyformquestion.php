<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="survey_form_question")
*/
class Surveyformquestion
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $survey_form_question_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $question_name="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $display_tag="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $sort_order=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $is_deleted="";

	public function getSurvey_form_question_id()
	{
		return $this->survey_form_question_id;
	}

	public function getDomain_id()
	{
		return $this->domain_id;
	}
	public function setDomain_id($domain_id)
	{
		$this->domain_id = $domain_id;
	}

	public function getQuestion_name()
	{
		return $this->question_name;
	}
	public function setQuestion_name($question_name)
	{
		$this->question_name = $question_name;
	}

	public function getDisplay_tag()
	{
		return $this->display_tag;
	}
	public function setDisplay_tag($display_tag)
	{
		$this->display_tag = $display_tag;
	}

	public function getSort_order()
	{
		return $this->sort_order;
	}
	public function setSort_order($sort_order)
	{
		$this->sort_order = $sort_order;
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