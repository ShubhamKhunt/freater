<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="survey_form_feedback")
*/
class Surveyformfeedback
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $survey_form_feedback_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $user_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $order_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $question_rate_array="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $reason="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $created_datetime="";

	public function getSurvey_form_feedback_id()
	{
		return $this->survey_form_feedback_id;
	}

	public function getUser_id()
	{
		return $this->user_id;
	}
	public function setUser_id($user_id)
	{
		$this->user_id = $user_id;
	}

	public function getOrder_id()
	{
		return $this->order_id;
	}
	public function setOrder_id($order_id)
	{
		$this->order_id = $order_id;
	}

	public function getDomain_id()
	{
		return $this->domain_id;
	}
	public function setDomain_id($domain_id)
	{
		$this->domain_id = $domain_id;
	}

	public function getQuestion_rate_array()
	{
		return $this->question_rate_array;
	}
	public function setQuestion_rate_array($question_rate_array)
	{
		$this->question_rate_array = $question_rate_array;
	}

	public function getReason()
	{
		return $this->reason;
	}
	public function setReason($reason)
	{
		$this->reason = $reason;
	}

	public function getCreated_datetime()
	{
		return $this->created_datetime;
	}
	public function setCreated_datetime($created_datetime)
	{
		$this->created_datetime = $created_datetime;
	}
}