<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="access_token")
*/
class Accesstoken
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $access_token_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $user_id="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $access_token="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $token_status="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $created_datetime="";

	public function getAccess_token_id()
	{
		return $this->access_token_id;
	}

	public function getUser_id()
	{
		return $this->user_id;
	}
	public function setUser_id($user_id)
	{
		$this->user_id = $user_id;
	}

	public function getAccess_token()
	{
		return $this->access_token;
	}
	public function setAccess_token($access_token)
	{
		$this->access_token = $access_token;
	}

	public function getToken_status()
	{
		return $this->token_status;
	}
	public function setToken_status($token_status)
	{
		$this->token_status = $token_status;
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