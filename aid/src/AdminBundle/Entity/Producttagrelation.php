<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="product_tag_relation")
*/
class Producttagrelation
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $product_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $tag_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $is_deleted="";

	public function getId()
	{
		return $this->id;
	}

	public function getProduct_id()
	{
		return $this->product_id;
	}
	public function setProduct_id($product_id)
	{
		$this->product_id = $product_id;
	}

	public function getTag_id()
	{
		return $this->tag_id;
	}
	public function setTag_id($tag_id)
	{
		$this->tag_id = $tag_id;
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