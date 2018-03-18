<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AdminBundle\Entity\Usermaster;

class AjaxController extends BaseController
{
    /**
     * @Route("/getuserurl")
     */
    public function getuserurlAction(Request $request)
    {
		if($request->request->all()){
			$username = $request->get('username');
			if(isset($username) && $username != ''){
				$repository = $this->getDoctrine()->getRepository(Usermaster::class);
				$user = $repository->findOneBy(['username' => $username]);
				if(!empty($user)){
					$user_image = $this->getMediaUrl($user->getUser_image());
					
					return new Response($user_image);
				}
			}
		}
		return new Response("false");
		
    }
	
	/**
     * @Route("/getMiniCartCount/{param}", defaults={"param":""})
     */
    public function getMiniCartCountAction($param)
    {
		$param = json_decode($param);
		$user_id = $param->user_id;
		
		$item_count = 0;
		if(isset($user_id) && $user_id != ''){
			$cart = $this->getDoctrine()->getRepository('AdminBundle:Cart')->findBy(
				array(
					'created_by' => $user_id,
					'order_status' => 'pending',
					'is_deleted' => 0
				)
			);
			
			$item_count = count($cart);
		}
		
		$data[] = array('item_count' => $item_count);
		echo json_encode($data);exit;
	}
	
	/**
     * @Route("/getBestsellerCollection")
     */
	public function getBestsellerCollectionAction(){
		
		$html = '';
		// get product list for slider
		$product_list = $this->getProductCollection();
		
		$data = array();
		if(!empty($product_list)){
			foreach($product_list as $_product){
				$data[] = $_product;
			}
		}
		
		echo json_encode($data);exit;
	}
}
