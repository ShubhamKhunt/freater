<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AdminBundle\Entity\Usermaster;

class DbController extends Controller
{
	private $product_id;
	private $conn;
	
	public function setConnection($doctrine){
		$this->conn = $doctrine;
	}
	
	public function setProduct($product_id){
		$this->product_id = $product_id;
	}
	
	public function getProductId(){
		return $this->product_id;
	}
	
	public function getProduct(){
		
		$product_id = $this->product_id;
		if(isset($product_id) && $product_id !=''){
			$table = $this->table;
			
			$_product = $conn->find($product_id);
			
			echo '<pre>';
			print_r($_product);
			exit;
			
			if(!empty($_product)){
				return $_product;
			}
		}
		return array();
	}
}
