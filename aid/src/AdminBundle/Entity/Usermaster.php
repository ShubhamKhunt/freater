<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="user_master")
*/
class Usermaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $user_master_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $user_role_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $username="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $password="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $user_firstname="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $user_lastname="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $user_mobile="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $user_emailid="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $image_type="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $user_image=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $user_image_url="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $insurance_image=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $civi_id_image=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $insurance="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $insurance_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $address_master_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $parent_user_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $created_by=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $user_status="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $created_datetime="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $last_modified="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $last_login="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $login_from="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $domain_id="";

	public function getUser_master_id()
	{
		return $this->user_master_id;
	}

	public function getUser_role_id()
	{
		return $this->user_role_id;
	}
	public function setUser_role_id($user_role_id)
	{
		$this->user_role_id = $user_role_id;
	}

	public function getUsername()
	{
		return $this->username;
	}
	public function setUsername($username)
	{
		$this->username = $username;
	}

	public function getPassword()
	{
		return $this->password;
	}
	public function setPassword($password)
	{
		$this->password = $password;
	}

	public function getUser_firstname()
	{
		return $this->user_firstname;
	}
	public function setUser_firstname($user_firstname)
	{
		$this->user_firstname = $user_firstname;
	}

	public function getUser_lastname()
	{
		return $this->user_lastname;
	}
	public function setUser_lastname($user_lastname)
	{
		$this->user_lastname = $user_lastname;
	}

	public function getUser_mobile()
	{
		return $this->user_mobile;
	}
	public function setUser_mobile($user_mobile)
	{
		$this->user_mobile = $user_mobile;
	}

	public function getUser_emailid()
	{
		return $this->user_emailid;
	}
	public function setUser_emailid($user_emailid)
	{
		$this->user_emailid = $user_emailid;
	}

	public function getImage_type()
	{
		return $this->image_type;
	}
	public function setImage_type($image_type)
	{
		$this->image_type = $image_type;
	}

	public function getUser_image()
	{
		return $this->user_image;
	}
	public function setUser_image($user_image)
	{
		$this->user_image = $user_image;
	}

	public function getUser_image_url()
	{
		return $this->user_image_url;
	}
	public function setUser_image_url($user_image_url)
	{
		$this->user_image_url = $user_image_url;
	}

	public function getInsurance_image()
	{
		return $this->insurance_image;
	}
	public function setInsurance_image($insurance_image)
	{
		$this->insurance_image = $insurance_image;
	}

	public function getCivi_id_image()
	{
		return $this->civi_id_image;
	}
	public function setCivi_id_image($civi_id_image)
	{
		$this->civi_id_image = $civi_id_image;
	}

	public function getInsurance()
	{
		return $this->insurance;
	}
	public function setInsurance($insurance)
	{
		$this->insurance = $insurance;
	}

	public function getInsurance_id()
	{
		return $this->insurance_id;
	}
	public function setInsurance_id($insurance_id)
	{
		$this->insurance_id = $insurance_id;
	}

	public function getAddress_master_id()
	{
		return $this->address_master_id;
	}
	public function setAddress_master_id($address_master_id)
	{
		$this->address_master_id = $address_master_id;
	}

	public function getParent_user_id()
	{
		return $this->parent_user_id;
	}
	public function setParent_user_id($parent_user_id)
	{
		$this->parent_user_id = $parent_user_id;
	}

	public function getCreated_by()
	{
		return $this->created_by;
	}
	public function setCreated_by($created_by)
	{
		$this->created_by = $created_by;
	}

	public function getUser_status()
	{
		return $this->user_status;
	}
	public function setUser_status($user_status)
	{
		$this->user_status = $user_status;
	}

	public function getCreated_datetime()
	{
		return $this->created_datetime;
	}
	public function setCreated_datetime($created_datetime)
	{
		$this->created_datetime = $created_datetime;
	}

	public function getLast_modified()
	{
		return $this->last_modified;
	}
	public function setLast_modified($last_modified)
	{
		$this->last_modified = $last_modified;
	}

	public function getLast_login()
	{
		return $this->last_login;
	}
	public function setLast_login($last_login)
	{
		$this->last_login = $last_login;
	}

	public function getLogin_from()
	{
		return $this->login_from;
	}
	public function setLogin_from($login_from)
	{
		$this->login_from = $login_from;
	}

	public function getIs_deleted()
	{
		return $this->is_deleted;
	}
	public function setIs_deleted($is_deleted)
	{
		$this->is_deleted = $is_deleted;
	}

	public function getDomain_id()
	{
		return $this->domain_id;
	}
	public function setDomain_id($domain_id)
	{
		$this->domain_id = $domain_id;
	}
}