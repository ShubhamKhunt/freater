<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
* @Route("/admin")
*/
class DashboardController extends BaseController
{
	public function __construct(){
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
	
    /**
     * @Route("/dashboard")
     * @Template()
     */
    public function indexAction()
    {
		$session = new Session();
        $domain_id = $this->get('session')->get('domain_id') ;
		$role_id = $this->get('session')->get('role_id') ;
		$user_id = $this->get('session')->get('user_id') ;
		$total_customer = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Usermaster')
					   ->findBy(array("user_role_id"=>7,"is_deleted"=>0,"user_status"=>'active'));
		$total_supplier = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Usermaster')
					   ->findBy(array("user_role_id"=>3,"is_deleted"=>0,"user_status"=>'active'));
		$in_query1 = $in_query = '';
		if($role_id == '3')
		{
				$in_query = " AND order_master.delivery_boy_id='$user_id' ";
				$in_query1 = " AND delivery_boy_id='$user_id' ";
		}
		$query = "SELECT city_master.city_name , order_master.*, domain_master.domain_name, user_master.user_firstname, user_master.username, user_master.user_mobile FROM order_master JOIN user_master ON user_master.user_master_id = order_master.order_createdby JOIN domain_master ON domain_master.domain_code = order_master.domain_id
			JOIN address_master ON order_master.delivery_address_id = address_master.address_master_id
			LEFT JOIN city_master ON address_master.city_id = city_master.city_master_id
			WHERE order_master.order_status_id NOT IN (4,5) AND order_master.order_status_id NOT IN (4,5)  AND order_master.is_deleted = 0 $in_query ORDER BY order_master.order_master_id DESC limit 5";
			// order_master.order_status_id NOT IN (4,5) AND
		 ;
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare($query);
		$statement->execute();
		$order_master = $statement->fetchAll();
		$product_array=null;

		$query = "SELECT * FROM `product_master` WHERE is_deleted=0 and domain_id='".$domain_id."' Group by main_product_master_id order by main_product_master_id desc limit 4";
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare($query);
		$statement->execute();
		$product_list = $statement->fetchAll();

		if(!empty($product_list))
		{
			foreach($product_list as $pkey =>$pval)
			{
				$image_details = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Medialibrarymaster')->findOneBy(array('media_library_master_id'=>$pval['product_logo']));
				$image = '';
				if(!empty($image_details))
				{
					$image = $this->container->getParameter('live_path').$image_details->getMedia_location()."/".$image_details->getMedia_name();
				}
				else
				{
					$image = $this->container->getParameter('live_path').'/bundles/design/images/logo.png';
				}


				$product_array[] = array(
						"product_name"=>$pval['product_title'],
						"image"=>$image,

						);
				}
		}

		$query = "SELECT order_master_id from order_master where order_master.is_deleted = 0 and order_status_id='10' $in_query";
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare($query);
		$statement->execute();
		$pending_order_master = $statement->fetchAll();

		$query = "SELECT order_master_id from order_master where order_master.is_deleted = 0 and order_status_id in (1,3,6) $in_query1 ";
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare($query);
		$statement->execute();
		$inprogress_order_master = $statement->fetchAll();

		$query = "SELECT order_master_id from order_master where order_master.is_deleted = 0 and order_status_id='4' $in_query1 ";
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare($query);
		$statement->execute();
		$delivered_order_master = $statement->fetchAll();


		$query = "SELECT * from product_master where is_deleted=0";
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare($query);
		$statement->execute();
		$out_of_stock_products = $statement->fetchAll();
		$out_of_stock_array = null;
		if(!empty($out_of_stock_products))
		{
			foreach($out_of_stock_products as $pkey =>$pval)
			{
				 $em = $this->getDoctrine()->getManager();
				$connection = $em->getConnection();
				$statement = $connection->prepare("SELECT SUM(quantity) as total  FROM `cart` where is_deleted = 0 and order_id!=0 and order_placed='true' and product_id='".$pval['main_product_master_id']."' group by product_id");
				$statement->execute();
				$view_master_list = $statement->fetch();
			   $total_sale='';
			   if(!empty($view_master_list))
			   {
				   $total_sale=$view_master_list['total'];
			   }

			   $instock=$pval['quantity']-$total_sale;
			   if($instock == 0){
				$image_details = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Medialibrarymaster')->findOneBy(array('media_library_master_id'=>$pval['product_logo']));
				$image = '';
				if(!empty($image_details))
				{
					$image = $this->container->getParameter('live_path').$image_details->getMedia_location()."/".$image_details->getMedia_name();
				}
				else
				{
					$image = $this->container->getParameter('live_path').'/bundles/design/images/logo.png';
				}

				$out_of_stock_array[] = array(
							"product_name"=>$pval['product_title'],
							"image"=>$image,
						);
				}
			}
		}

        return array("total_supplier"=>count($total_supplier),"total_customer"=>count($total_customer),"orders_list"=>$order_master,"products_list"=>$product_array,"pending_order_master"=>count($pending_order_master),"inprogress_order_master"=>count($inprogress_order_master),"delivered_order_master"=>count($delivered_order_master),"out_of_stock_product"=>$out_of_stock_array);

    }
    /**
     * @Route("/keyEncryption/{string}",defaults = {"string"=""},requirements={"string"=".+"})
     */
    public function keyEncryptionAction($string)
	{
		if($string != "" && $string != NULL && !empty($string) && ctype_space($string) == false)
		{
			$key = $this->container->getParameter('key');
			$res = '';
			for( $i = 0; $i < strlen($string); $i++)
            {
                $c = ord(substr($string, $i));

                $c += ord(substr($key, (($i + 1) % strlen($key))));
                $res .= chr($c & 0xFF);

            }
			return new Response(base64_encode($res));
		}
		return new Response("");
	}
	/**
     * @Route("/keyDecryption/{string}",defaults = {"string"=""},requirements={"string"=".+"})
     */
	public function keyDecryptionAction($string)
	{
		if($string != "" && $string != NULL && !empty($string) && ctype_space($string) == false)
		{
			$key = $this->container->getParameter('key');

			$res = '';
			$string = base64_decode($string);
			for( $i = 0; $i < strlen($string); $i++)
            {
                $c = ord(substr($string, $i));

                $c -= ord(substr($key, (($i + 1) % strlen($key))));
                $res .= chr(abs($c) & 0xFF);

            }
			return new Response($res);
		}
		return new Response("");
	}
}
