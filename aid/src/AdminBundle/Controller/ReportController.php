<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Usermaster;
use AdminBundle\Entity\Productcategoryrelation;
use AdminBundle\Entity\Productmaster;
use AdminBundle\Entity\Combination;
use AdminBundle\Entity\Combinationrelation;
use AdminBundle\Entity\Gallerymaster;
use AdminBundle\Entity\Medialibrarymaster;
use AdminBundle\Entity\Productsupplierrelation;
use AdminBundle\Entity\Supplierattributerelation;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
* @Route("/admin")
*/
class ReportController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
    
    /**
     * @Route("/deliveryboywise")
     * @Template()
     */
    public function deliveryboywiseAction()
    {
		$language = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Languagemaster')
				   ->findBy(array('is_deleted'=>0));
					   
		$product_array = '';
		
		$domain_id = $this->get('session')->get('domain_id');
		$domain = $this->get('session')->get('domain');
		$role_id = $this->get('session')->get('role_id');
		$user_id = $this->get('session')->get('user_id');
		
		$delivery_boy_progress = array();
		$total_order = 0;
		$fromdate = $todate = "";
		
		//$start_date = date('Y-m-d H:i:s',strtotime($_POST['starting_date']));
    	//$end_date = date('Y-m-d H:i:s',strtotime($_POST['ending_date']));
		
		//for area manager (delivery manager)
			$areainstring;
			if($role_id == 5)
			{
				
				$areainstring = $this->getAreaInstring($domain_id,$user_id);
				//echo $areainstring;exit;
			}
			
			//for city manager (delivery admin)
			$citystring;
			if($role_id == 4)
			{
				
				$citystring = $this->getCityInstring($domain_id,$user_id);
				//echo $areainstring;exit;
			}
		
		if(empty($_POST['fromdate']))
		{
			$fromdate=date('Y-m-01');
		}else{
			$fromdate=date('Y-m-d H:i:s',strtotime($_POST['fromdate']));
		}
		
		if(empty($_POST['todate']))
		{
			$todate= date('Y-m-t');
		}else{
			$todate=date('Y-m-d H:i:s',strtotime($_POST['todate']));
		}
		
		if(!empty($fromdate) && !empty($todate))
		{
			$query = "SELECT user_master.user_firstname,user_master.user_lastname,order_master.* FROM user_master JOIN order_master ON delivery_boy_id = user_master.user_master_id AND delivery_date BETWEEN '". $fromdate."' AND '". $todate . "' WHERE user_master.is_deleted = 0 AND user_master.domain_id = '" . $domain_id . "' AND user_master.user_role_id = 6 ";

			$em = $this->getDoctrine()->getManager()->getConnection()->prepare($query);
			$em->execute();
			$order_detail = $em->fetchAll();
		//			var_dump($order_detail);exit;
			$total_order = count($order_detail);
			
			//new
			if($role_id==4)
			{//city
				$query = "SELECT um.user_firstname,um.user_lastname,om.delivery_boy_id, count(*) FROM user_master um, order_master om,address_master am where om.delivery_boy_id = um.user_master_id AND om.delivery_date BETWEEN '". $fromdate."' AND '". $todate . "' and um.is_deleted = 0 AND um.domain_id = '" . $domain_id . "' AND um.address_master_id=am.main_address_id AND am.city_id in (".$citystring.") AND um.user_role_id = 6 group by om.delivery_boy_id";
			}
			elseif($role_id==5)
			{//area
				//$query = "SELECT um.user_firstname,um.user_lastname,om.delivery_boy_id, count(*) FROM user_master um, order_master om  where om.delivery_boy_id = um.user_master_id AND om.delivery_date BETWEEN '". $fromdate."' AND '". $todate . "' and um.is_deleted = 0 AND um.domain_id = '" . $domain_id . "' AND um.user_role_id = 6 group by om.delivery_boy_id";
				$query = "SELECT um.user_firstname,um.user_lastname,om.delivery_boy_id, count(*) FROM user_master um, order_master om,address_master am where om.delivery_boy_id = um.user_master_id AND om.delivery_date BETWEEN '". $fromdate."' AND '". $todate . "' and um.is_deleted = 0 AND um.domain_id = '" . $domain_id . "' AND um.address_master_id=am.main_address_id AND am.area_id in (".$areainstring.") AND um.user_role_id = 6 group by om.delivery_boy_id";
			}
			else
			{
				$query = "SELECT um.user_firstname,um.user_lastname,om.delivery_boy_id, count(*) FROM user_master um, order_master om  where om.delivery_boy_id = um.user_master_id AND om.delivery_date BETWEEN '". $fromdate."' AND '". $todate . "' and um.is_deleted = 0 AND um.domain_id = '" . $domain_id . "' AND um.user_role_id = 6 group by om.delivery_boy_id";
			}
			
			//$query = "SELECT um.user_firstname,um.user_lastname,om.delivery_boy_id, count(*) FROM user_master um, order_master om  where om.delivery_boy_id = um.user_master_id AND om.delivery_date BETWEEN '". $fromdate."' AND '". $todate . "' and um.is_deleted = 0 AND um.domain_id = '" . $domain_id . "' AND um.user_role_id = 6 group by om.delivery_boy_id";


			//old
			//$query = "SELECT um.user_firstname,um.user_lastname,om.delivery_boy_id, count(*) FROM user_master um, order_master om  where om.delivery_boy_id = um.user_master_id AND om.delivery_date BETWEEN '". $fromdate."' AND '". $todate . "' and um.is_deleted = 0 AND um.domain_id = '" . $domain_id . "' AND um.user_role_id = 6 group by om.delivery_boy_id";
			//$query = "SELECT user_master.user_firstname,user_master.user_lastname,order_master.delivery_boy_id, count(*) FROM user_master JOIN order_master ON delivery_boy_id = user_master.user_master_id AND delivery_date BETWEEN '". $fromdate."' AND '". $todate . "' WHERE user_master.is_deleted = 0 AND user_master.domain_id = '" . $domain_id . "' AND user_master.user_role_id = 6 group by delivery_boy_id";

			$em = $this->getDoctrine()->getManager()->getConnection()->prepare($query);
			$em->execute();
			$delivery_boy_order = $em->fetchAll();
			//var_dump($delivery_boy_order);exit;
			$delivery_boy_progress = array();
			if(!empty($delivery_boy_order) && !empty($order_detail))
			{
				$progress = 0;
				$delivery_boy_progress = array();
				foreach($delivery_boy_order as $key => $value){
					
					if(!empty($total_order) && !empty($value['count(*)']))
					{
						$progress=intval(($value['count(*)']*100)/$total_order);	
					}
					
					$query = "SELECT * FROM status WHERE is_deleted = 0";

					$em = $this->getDoctrine()->getManager()->getConnection()->prepare($query);
					$em->execute();
					$status_list = $em->fetchAll();
					
					$order_status_arr = array();
					if(!empty($status_list))
					{
						foreach($status_list as $skey=>$sval)
						{
							$query = "SELECT user_master.user_firstname,user_master.user_lastname,order_master.* FROM user_master JOIN order_master ON delivery_boy_id = user_master.user_master_id AND delivery_date BETWEEN '". $fromdate."' AND '". $todate . "' WHERE user_master.is_deleted = 0 AND user_master.domain_id = '" . $domain_id . "' AND user_master.user_role_id = 6 and delivery_boy_id = '". $value['delivery_boy_id']."' and order_status_id = '". $sval['status_id']."'";

							$em = $this->getDoctrine()->getManager()->getConnection()->prepare($query);
							$em->execute();
							$order_status = $em->fetchAll();
							$total_order_status = count($order_status);
							
							$order_status_arr [] = array(
								"status_id"=>$sval['status_id'],
								"status_name"=>$sval['status_name'],
								"order_status_count"=>$total_order_status
								
							);
						}
					}
					
					$delivery_boy_progress [] = array(
							"delivery_boy_id"=>$value['delivery_boy_id'],
							"user_firstname"=>$value['user_firstname'],
							"user_lastname"=>$value['user_lastname'],
							"total_orders"=>$total_order,
							"delivery_boy_order_count"=>$value['count(*)'],
							"progress"=>$progress,
							"orders_status"=>$order_status_arr
					);
				}
				
			}
			
			return array("delivery_boy_progress"=>$delivery_boy_progress,"fromdate"=>$fromdate,"todate"=>$todate);
		}
		//return array("fromdate"=>"","todate"=>"");
    }

  
    /**
     * @Route("/customerwise")
     * @Template()
     */
    public function customerwiseAction()
    {
		$domain_id = $this->get('session')->get('domain_id');
		$user_master = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Usermaster')
				   ->findBy(array('user_type'=>'user','is_deleted'=>0,'user_role_id'=>7,'domain_id'=>$domain_id));
		$Date =  date("Y-m-d");
		$start_date =  date('Y-m-d', strtotime('-15 days', strtotime($Date)));
		$end_date =  date('Y-m-d', strtotime('+15 days', strtotime($Date)));
		foreach($user_master as $ukey=>$uval){
			$array[] = array(
					"user_master_id"=>$uval->getUser_master_id(),
					"username"=>$uval->getUsername(),
					"user_mobile"=>$uval->getUser_mobile(),
					"total_orders"=>0
					);
		}
		return array("customer_list"=>$user_master);
	}
	 /**
     * @Route("/getcustomerwise")
     */
    public function getcustomerwiseAction()
    {
		$domain_id = $this->get('session')->get('domain_id');
		$user_master = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Usermaster')
				   ->findBy(array('user_type'=>'user','is_deleted'=>0,'user_role_id'=>7,'domain_id'=>$domain_id));
				   
		$Date =  date("Y-m-d");
		$start_date =  date('Y-m-d', strtotime('-15 days', strtotime($Date)));
		$end_date =  date('Y-m-d', strtotime('+15 days', strtotime($Date)));
		$str_html = '<table id="example1" class="table table-bordered table-striped">';
		$str_html .= '<thead><th>Customer / Mobile</th><th>Total Orders</th><th>Order Analytics</th></thead><tbody>';
		
		if(isset($_REQUEST['startdate']) && isset($_REQUEST['enddate']))
		{
			$total_cnt = 0;
			foreach($user_master as $ukey=>$uval)
			{
				// get orders from this date to end date
				$query = "SELECT count(*) as total_cnt from order_master where is_deleted=0 and order_createdby = '".$uval->getUser_master_id()."' and order_dateadded > '" .$start_date . "' and order_dateadded < '". $end_date ."'" ;
				$em = $this->getDoctrine()->getManager();
				$connection = $em->getConnection();
				$statement = $connection->prepare($query);
				$statement->execute();
				$order_users = $statement->fetchAll();
				$total_cnt = $total_cnt + $order_users[0]['total_cnt'];
			}
			
			$percentage = 0;
			$spstatus = '';
			foreach($user_master as $ukey=>$uval)
			{
				$query = "SELECT *   from order_master where is_deleted=0 and order_createdby = '".$uval->getUser_master_id()."' and order_dateadded > '" .$start_date . "' and order_dateadded < '". $end_date ."'" ;
				$em = $this->getDoctrine()->getManager();
				$connection = $em->getConnection();
				$statement = $connection->prepare($query);
				$statement->execute();
				$order_users = $statement->fetchAll();
				
				if(!empty(count($order_users)))
				{
					$percentage = (count($order_users) * 100 ) / $total_cnt ;	
				}
				
				if($percentage == 0){$percentage = 0.2;}
				if ( $percentage >= 80 ){
						$spstatus = "success" ;
				}
				elseif($percentage >= 60 && $percentage < 79 ){
					$spstatus = "info" ;
				}
				elseif($percentage >= 40 && $percentage < 59 ){
					$spstatus = "warning" ;
				}
				else{
					$spstatus = "danger" ;
				}
				 
				$array[] = array(
							"user_master_id"=>$uval->getUser_master_id(),
							"username"=>$uval->getUsername(),
							"user_mobile"=>$uval->getUser_mobile(),
							"total_orders"=>count($order_users)
							);
				$str_html .= "<tr><td>".$this->keyDecryptionAction($uval->getUsername())."/".$this->keyDecryptionAction($uval->getUser_mobile())."</td><td>".count($order_users)."</td><td><div class='progress progress-xs' style='height: 11px;'><div style='width:".$percentage."%' class='progress-bar progress-bar-".$spstatus."'></div></div></td></tr>";
			}
		
			$str_html .='</tbody><tfoot><th>Customer / Mobile</th><th>Total Orders</th><th>Order Analytics</th></tfoot>';
			$str_html .= "<script type='text/javascript'>$(function(){ $('#example1').DataTable({ });})";
			echo $str_html;
			return new Response();
		}
		
		return array("customer_list"=>$user_master);
	}
	
  
	 /**
     * @Route("/productsaleswise")
     * @Template()
     */
    public function productsaleswiseAction()
    {
		$domain_id = $this->get('session')->get('domain_id');
		
		$query = "SELECT main_product_master_id,product_title FROM product_master WHERE is_deleted = 0 AND domain_id = '".$domain_id."' GROUP BY main_product_master_id ORDER BY main_product_master_id DESC";
		
		$em = $this->getDoctrine()->getManager()->getConnection()->prepare($query);
		$em->execute();
		$product = $em->fetchAll();
		
		$query = "SELECT * FROM order_master WHERE is_deleted = 0 AND domain_id = '".$domain_id."'";
		$em = $this->getDoctrine()->getManager()->getConnection()->prepare($query);
		$em->execute();
		$orders = $em->fetchAll();
		$total_orders = count($orders);
		
		$product_progress = array();
		if(!empty($product))
		{
			$progress = 0;
			$product_progress = array();
			foreach($product as $pkey=>$pval)
			{
				$query = "SELECT cart_id,product_id FROM cart WHERE is_deleted = 0 AND domain_id = '".$domain_id."' and product_id = '".$pval['main_product_master_id']."' and order_id != 0 ";
				//var_dump($query);
				$em = $this->getDoctrine()->getManager()->getConnection()->prepare($query);
				$em->execute();
				$cart = $em->fetchAll();
				$total_cart_cnt=count($cart);
				if(!empty($total_cart_cnt) && !empty($total_orders))
				{
					$progress=intval(($total_cart_cnt*100)/$total_orders);	
				}
				
				$product_progress [] = array(
							"main_product_master_id"=>$pval['main_product_master_id'],
							"product_title"=>$pval['product_title'],
							"produtc_total"=>$total_cart_cnt,
							"total_orders"=>$total_orders,
							"progress"=>$progress
				);
			}
		}
	//	var_dump($product_progress);exit;
		
		return array("products"=>$product_progress);
	}

	 /**
     * @Route("/areawise")
     * @Template()
     */
    public function areawiseAction()
    {
		$domain_id = $this->get('session')->get('domain_id');
		return array();
	}
	/**
     * @Route("/dailyorder")
     * @Template()
     */
    public function dailyorderAction()
    {
    	return array();
    }
	
}