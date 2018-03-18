<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ProductController extends BaseController
{
    /**
     * @Route("/ws/product-collection", name="homepage")
	 * @Template()
     */
    public function indexAction(Request $request)
    {
		$repository = $this->getDoctrine()->getRepository('AdminBundle:Productmaster');
		$collection = $repository->findBy(
			array(
				'is_deleted' => 0,
			)
		);
		
		$product_collection = array();
		foreach($collection as $_collection){
			
			$media_url = '';
			if($_collection->getProduct_logo()){
				$media_url = $this->getImage($_collection->getProduct_logo());
			}
			
			$data = array(
				'id' => $_collection->getProduct_master_id(),
				'name' => $_collection->getProduct_title(),
				'image' => $media_url,
			);
			$product_collection[] = $data;
		}
		
		$this->jsonEncode($product_collection);
    }
}
