<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="bwid_ws_security")
*/
class Bwidwssecurity
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $bwid_ws_security_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $device_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $token="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $token_created="";

	public function getBwid_ws_security_id()
	{
		return $this->bwid_ws_security_id;
	}

	public function getDevice_id()
	{
		return $this->device_id;
	}
	public function setDevice_id($device_id)
	{
		$this->device_id = $device_id;
	}
	public function getToken()
	{
		return $this->token;
	}
	public function setToken($token)
	{
		$this->token = $token;
	}

	public function getToken_created()
	{
		return $this->token_created;
	}
	public function setToken_created($token_created)
	{
		$this->token_created = $token_created;
	}
}