<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="general_setting")
*/
class Generalsetting
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $general_setting_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $general_setting_key="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $general_setting_value="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getGeneral_setting_id()
	{
		return $this->general_setting_id;
	}

	public function getGeneral_setting_key()
	{
		return $this->general_setting_key;
	}
	public function setGeneral_setting_key($general_setting_key)
	{
		$this->general_setting_key = $general_setting_key;
	}

	public function getGeneral_setting_value()
	{
		return $this->general_setting_value;
	}
	public function setGeneral_setting_value($general_setting_value)
	{
		$this->general_setting_value = $general_setting_value;
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