<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="product_tag_master")
*/
class Producttagmaster
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $tag_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $tag="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $is_deleted="";

	public function getTag_id()
	{
		return $this->tag_id;
	}

	public function getTag()
	{
		return $this->tag;
	}
	public function setTag($tag)
	{
		$this->tag = $tag;
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