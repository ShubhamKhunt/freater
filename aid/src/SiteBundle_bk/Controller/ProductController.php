<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Usermaster;
use AdminBundle\Entity\Medialibrarymaster;

class ProductController extends BaseController
{
    /**
     * @Route("/product/list/{category_id}", defaults={"category_id":""})
	 * @Template()
     */
    public function listAction($category_id){
		
		$product_list = array();
		if(isset($category_id) && $category_id != ''){
			$product_list = $this->getProductCollection($category_id);
		}
		
		return array(
				'product_list' => $product_list
			);
    }
	
	/**
     * @Route("/product/view/{product_id}", defaults={"product_id":""})
	 * @Template()
     */
    public function viewAction($product_id){
		
		$_product = array();
		if(isset($product_id) && $product_id != ''){
			$_product = $this->getProduct($product_id);
		}
		
		return array(
				'product' => $_product
			);
    }
}
