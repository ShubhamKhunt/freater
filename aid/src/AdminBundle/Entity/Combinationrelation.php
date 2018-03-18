<?php 
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity
* @ORM\Table(name="combination_relation")
*/
class Combinationrelation
{
	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $combination_relation_id;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $product_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $language_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $attribute_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $attribute_value_id=0;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $combination_id=0;

	/**
	* @ORM\Column(type="string")
	*/
	protected $is_deleted="";

	public function getCombination_relation_id()
	{
		return $this->combination_relation_id;
	}

	public function getProduct_id()
	{
		return $this->product_id;
	}
	public function setProduct_id($product_id)
	{
		$this->product_id = $product_id;
	}

	public function getLanguage_id()
	{
		return $this->language_id;
	}
	public function setLanguage_id($language_id)
	{
		$this->language_id = $language_id;
	}

	public function getAttribute_id()
	{
		return $this->attribute_id;
	}
	public function setAttribute_id($attribute_id)
	{
		$this->attribute_id = $attribute_id;
	}

	public function getAttribute_value_id()
	{
		return $this->attribute_value_id;
	}
	public function setAttribute_value_id($attribute_value_id)
	{
		$this->attribute_value_id = $attribute_value_id;
	}

	public function getCombination_id()
	{
		return $this->combination_id;
	}
	public function setCombination_id($combination_id)
	{
		$this->combination_id = $combination_id;
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