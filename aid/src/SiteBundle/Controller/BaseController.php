<?php

namespace SiteBundle\Controller;

use SiteBundle\Controller\DbController as DB;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class BaseController extends Controller
{
	public function isLoggedIn(){
		$session = $this->get('session');
		
		if($session->get('user_id') != '' && $session->get('access_token') != '' && $session->get('login_status') == 'logged_in'){
			return true;
		}
		
		return false;
	}
	
	/**
     * @Route("/site_index")
     */
    public function indexAction()
    {
		exit('here');
        return $this->render('SiteBundle:Default:index.html.twig');
    }
	
	// get category list
	public function getCategoryCollection(){
		$repository = $this->getDoctrine()->getRepository('AdminBundle:Categorymaster');
		$category = $repository->findBy(array('is_deleted' => 0));
		
		$category_list = array();
		if(!empty($category)){
			foreach($category as $_category){
				
				$media = $this->getDoctrine()->getRepository('AdminBundle:Medialibrarymaster')->find($_category->getCategory_image_id());
				
				$live_path = $this->container->getParameter('live_path');
				if(!empty($media)){
					$media_url = $live_path.$media->getMedia_location().'/'.$media->getMedia_name();
				} else {
					$media_url = $live_path.'/'.'bundles/design/images/default.png';
				}
				
				$_category->media_url = $media_url;
				$category_list[] = $_category;
			}
		}
		
		return $category_list;
	}
	
	// get product list
	public function getProductCollection($category_id = ''){
		
		if(isset($category_id) && $category_id != ''){
			$query = "select product.* from product_master product left join product_category_relation rel on product.product_master_id = rel.product_id where rel.category_id = {$category_id} and product.is_deleted = 0";
		} else {
			$query = "select product.* from product_master product where product.is_deleted = 0";
		}
		
		$connection = $this->getDoctrine()->getManager()->getConnection();
		$statement = $connection->prepare($query);
		$statement->execute();
		$product = $statement->fetchAll();
		
		$product_list = array();
		if(!empty($product)){
			foreach($product as $_product){
				
				$media = $this->getDoctrine()->getRepository('AdminBundle:Medialibrarymaster')->find($_product['product_logo']);
				
				$live_path = $this->container->getParameter('live_path');
				if(!empty($media)){
					$media_url = $live_path.$media->getMedia_location().'/'.$media->getMedia_name();
				} else {
					$media_url = $live_path.'/'.'bundles/design/images/default.png';
				}
				
				$_product['media_url'] = $media_url;
				$product_list[] = $_product;
			}
		}
		
		shuffle($product_list);
		return $product_list;
	}
	
	// get product
	public function getProduct($product_id){
		if(isset($product_id) && $product_id != ''){
			$_product = $this->getDoctrine()->getRepository('AdminBundle:Productmaster')->find($product_id);
			
			$_product->base_image = $this->getMediaUrl($_product->getProduct_logo());
			$product_gallery = $this->getProductGallery($product_id);
			if(!empty($product_gallery)){
				$_product->gallery = $product_gallery;
			}
			
			return $_product;
		}
		return array();
	}
	
	// get product gallery
	public function getProductGallery($product_id){
		if(isset($product_id) && $product_id != ''){
			$repository = $this->getDoctrine()->getRepository('AdminBundle:Gallerymaster');
			$gallery_repo = $repository->findBy(
					array(
						'module_primary_id' => $product_id,
						'is_deleted' => 0
					)
				);
				
			if(!empty($gallery_repo)){
				
				$gallery_images = array();
				foreach($gallery_repo as $_gallery){
					
					$media_id = $_gallery->getMedia_library_master_id();
					$gallery_images[] = $this->getMediaUrl($media_id);
				}
				return $gallery_images;
			}
		}
		return array();
	}
	
	// get media url
	public function getMediaUrl($media_id){
		$live_path = $this->container->getParameter('live_path');
		$media = $this->getDoctrine()->getRepository('AdminBundle:Medialibrarymaster')->find($media_id);					
		if(!empty($media)){
			return $live_path.$media->getMedia_location().'/'.$media->getMedia_name();
		}
		return '';
	}
	
	// get brand list
	public function getBrandCollection(){
		$repository = $this->getDoctrine()->getRepository('AdminBundle:Brandmaster');
		$brands = $repository->findBy(array('is_deleted' => 0));
		
		$brand_list = array();
		if(!empty($brands)){
			foreach($brands as $_brand){
				
				$media = $this->getDoctrine()->getRepository('AdminBundle:Medialibrarymaster')->find($_brand->getLogo_id());
				
				$live_path = $this->container->getParameter('live_path');
				if(!empty($media)){
					$media_url = $live_path.$media->getMedia_location().'/'.$media->getMedia_name();
				} else {
					$media_url = $live_path.'/'.'bundles/design/images/default.png';
				}
				
				$_brand->media_url = $media_url;
				$brand_list[] = $_brand;
			}
		}
		
		shuffle($brand_list);
		return $brand_list;
	}
}
