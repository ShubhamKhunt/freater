<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="product_attruibutes")
*/
class Productattruibutes
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $product_attruibute_id;

	/**
	* @ORM\Column(type="string")
	*/
	protected $sku="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $brand="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $color="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $size="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $length="";

	/**
	* @ORM\Column(type="string")
	*/
	protected $weight="";

	/**
	* @ORM\Column(type="integer")
	*/
	protected $height=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $product_id="";

	public function getProduct_attruibute_id()
	{
		return $this->product_attruibute_id;
	}

	public function getSku()
	{
		return $this->sku;
	}
	public function setSku($sku)
	{
		$this->sku = $sku;
	}

	public function getBrand()
	{
		return $this->brand;
	}
	public function setBrand($brand)
	{
		$this->brand = $brand;
	}

	public function getColor()
	{
		return $this->color;
	}
	public function setColor($color)
	{
		$this->color = $color;
	}

	public function getSize()
	{
		return $this->size;
	}
	public function setSize($size)
	{
		$this->size = $size;
	}

	public function getLength()
	{
		return $this->length;
	}
	public function setLength($length)
	{
		$this->length = $length;
	}

	public function getWeight()
	{
		return $this->weight;
	}
	public function setWeight($weight)
	{
		$this->weight = $weight;
	}

	public function getHeight()
	{
		return $this->height;
	}
	public function setHeight($height)
	{
		$this->height = $height;
	}

	public function getProduct_id()
	{
		return $this->product_id;
	}
	public function setProduct_id($product_id)
	{
		$this->product_id = $product_id;
	}
}