<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Cart;
use AdminBundle\Entity\Usermaster;
use AdminBundle\Entity\Medialibrarymaster;
use SiteBundle\Controller\BaseController as Base;

class CheckoutController extends BaseController
{
    /**
     * @Route("/checkout/cart/")
	 * @Template()
     */
    public function cartAction(){
		
		$cart_products = array();
		if(Base::isLoggedIn()){
			$session = $this->get('session');
			
			$em = $this->getDoctrine()->getManager();
			$connection = $em->getConnection();
			
			$query= "SELECT *  FROM `cart` WHERE `created_by` = {$session->get('user_id')} and is_deleted = 0 group by product_id";
			$statement = $connection->prepare($query);
			$statement->execute();
			$cart_items = $statement->fetchAll();
			
			if(!empty($cart_items)){
				
				$final_subtotal = $final_grandtotal = 0;
				foreach($cart_items as $_item){
					
					$cartItems = $this->getCartItems($session->get('user_id'), $_item['product_id']);
					$filter = array(
						'product_master_id' => $_item['product_id'],
						'is_deleted' => 0
					);
					$_product = $this->getProduct($filter);
					
					if(!empty($cartItems) && !empty($_product)){
						
						$quantity = $subtotal = 0;
						foreach($cartItems as $_cart){
							$quantity += $_cart->getQuantity();
							$subtotal += $_cart->getTotal_price();
						}
						
						$_item['product_name'] = $_product->getProduct_title();
						$_item['media_url'] = $this->getProductMediaUrl($_product->getProduct_logo());
						$_item['quantity'] = $quantity;
						$_item['subtotal'] = $subtotal;
					}
					
					$final_subtotal += $_item['subtotal'];
					$final_grandtotal += $_item['subtotal'];
					
					$cart_products[] = $_item;
				}
			}
		}
		
		return array(
			'subtotal' => $final_subtotal,
			'grandtotal' => $final_grandtotal,
			'cart_products' => $cart_products
		);
    }
	
	/**
     * @Route("/checkout/cart/addtocart")
     */
    public function addtocartAction(Request $request){
		
		$product_id = $request->get('product_id');
		if(isset($product_id) && $product_id != ''){
			
			if(Base::isLoggedIn()){
				
				$session = $this->get('session');
				
				$productrelation = $this->getDoctrine()->getRepository('AdminBundle:Productsupplierrelation')->findOneBy(array('main_product_id' => $product_id));
				
				$product = $this->getProduct(array('product_master_id' => $product_id));
				
				$data = array();
				if(!empty($productrelation)){
					$cart = new Cart();
					$cart->setOrder_type(1);
					$cart->setProduct_id($product_id);
					$cart->setSupplier_id($productrelation->getSupplier_id());
					$cart->setProduct_original_price($request->get('special_price'));
					$cart->setUnit_price($request->get('price'));
					$cart->setTotal_price($request->get('price'));
					$cart->setQuantity($request->get('qty'));
					$cart->setOrder_placed('false');
					$cart->setOrder_status('pending');
					$cart->setCreated_by($session->get('user_id'));
					$cart->setCreate_date(date('Y-m-d h:i:s'));
					$cart->setIs_deleted(0);
					$cart->setShop_type('pharmacy');
					
					$em = $this->getDoctrine()->getManager();
					$em->persist($cart);
					$em->flush();
					
					// cart items
					$cart_items = $this->getCartItems($session->get('user_id'));
					
					$item_count = 0;
					if(!empty($cart_items)){
						$item_count = count($cart_items);
					}
					
					$data = array(
						'success' => 'true',
						'message' => 'added to cart',
						'product' => $product->getProduct_title().' added to cart',
						'cart_item' => "<span class='data-count'>{$item_count}</span>"
					);
				}
				echo json_encode($data);exit;
			}
		}
		
		$data = array('success' => 'false');
		echo json_encode($data);exit;
    }
	
	public function getCartItems($user_id, $product_id = 0){
		
		if($product_id != 0){
			$data = array(
					'product_id' => $product_id,
					'created_by' => $user_id,
					'is_deleted' => 0
				);
		} else {
			$data = array(
					'created_by' => $user_id,
					'is_deleted' => 0
				);
		}
		
		$cart_items = $this->getDoctrine()->getRepository('AdminBundle:Cart')->findBy($data);
		
		return $cart_items;
	}
	
	public function getProduct($data){
		
		$product = array();
		if(!empty($data)){
			$repository = $this->getDoctrine()->getRepository('AdminBundle:Productmaster');
			$product = $repository->findOneBy($data);
		}
		
		return $product;
	}
	
	public function getProductMediaUrl($image_id){
		
		$media_url = '';
		if(isset($image_id)){
			$repository = $this->getDoctrine()->getRepository('AdminBundle:Medialibrarymaster');
			$mediamaster = $repository->find($image_id);
			
			if(!empty($mediamaster)){
				$live_path = $this->container->getParameter('live_path');
				$media = $mediamaster->getMedia_location().'/'.$mediamaster->getMedia_name();
				$media_url = $live_path.$media;
			}
		}
		
		return $media_url;
	}
	
	/**
     * @Route("/checkout/onepage/")
	 * @Template()
     */
    public function onepageAction(){
		exit('test');
    }
}
