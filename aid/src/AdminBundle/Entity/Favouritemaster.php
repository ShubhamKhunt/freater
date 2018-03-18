<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="favourite_master")
*/
class Favouritemaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $favourite_master_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $favourite_type="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $user_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $favourite_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $favourite_on="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $last_updated="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_favourite=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $is_deleted=0;

	public function getFavourite_master_id()
	{
		return $this->favourite_master_id;
	}

	public function getFavourite_type()
	{
		return $this->favourite_type;
	}
	public function setFavourite_type($favourite_type)
	{
		$this->favourite_type = $favourite_type;
	}

	public function getUser_id()
	{
		return $this->user_id;
	}
	public function setUser_id($user_id)
	{
		$this->user_id = $user_id;
	}

	public function getFavourite_id()
	{
		return $this->favourite_id;
	}
	public function setFavourite_id($favourite_id)
	{
		$this->favourite_id = $favourite_id;
	}

	public function getFavourite_on()
	{
		return $this->favourite_on;
	}
	public function setFavourite_on($favourite_on)
	{
		$this->favourite_on = $favourite_on;
	}

	public function getLast_updated()
	{
		return $this->last_updated;
	}
	public function setLast_updated($last_updated)
	{
		$this->last_updated = $last_updated;
	}

	public function getIs_favourite()
	{
		return $this->is_favourite;
	}
	public function setIs_favourite($is_favourite)
	{
		$this->is_favourite = $is_favourite;
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