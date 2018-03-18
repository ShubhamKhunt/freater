<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="faq")
*/
class Faq
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $faq_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $question="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $answer="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $type_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $faq_main_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $lang_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $status="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $create_date="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getFaq_id()
	{
		return $this->faq_id;
	}

	public function getQuestion()
	{
		return $this->question;
	}
	public function setQuestion($question)
	{
		$this->question = $question;
	}

	public function getAnswer()
	{
		return $this->answer;
	}
	public function setAnswer($answer)
	{
		$this->answer = $answer;
	}

	public function getType_id()
	{
		return $this->type_id;
	}
	public function setType_id($type_id)
	{
		$this->type_id = $type_id;
	}

	public function getFaq_main_id()
	{
		return $this->faq_main_id;
	}
	public function setFaq_main_id($faq_main_id)
	{
		$this->faq_main_id = $faq_main_id;
	}

	public function getLang_id()
	{
		return $this->lang_id;
	}
	public function setLang_id($lang_id)
	{
		$this->lang_id = $lang_id;
	}

	public function getStatus()
	{
		return $this->status;
	}
	public function setStatus($status)
	{
		$this->status = $status;
	}

	public function getCreate_date()
	{
		return $this->create_date;
	}
	public function setCreate_date($create_date)
	{
		$this->create_date = $create_date;
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