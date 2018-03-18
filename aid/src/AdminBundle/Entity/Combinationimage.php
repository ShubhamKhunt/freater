<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="combination_image")
*/
class Combinationimage
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $combination_image_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $product_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $combination_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $media_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $is_deleted="";

	public function getCombination_image_id()
	{
		return $this->combination_image_id;
	}

	public function getProduct_id()
	{
		return $this->product_id;
	}
	public function setProduct_id($product_id)
	{
		$this->product_id = $product_id;
	}

	public function getCombination_id()
	{
		return $this->combination_id;
	}
	public function setCombination_id($combination_id)
	{
		$this->combination_id = $combination_id;
	}

	public function getMedia_id()
	{
		return $this->media_id;
	}
	public function setMedia_id($media_id)
	{
		$this->media_id = $media_id;
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