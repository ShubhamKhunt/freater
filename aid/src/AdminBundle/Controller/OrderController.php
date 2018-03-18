<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Ordermaster;
use AdminBundle\Entity\Domainmaster;
use AdminBundle\Entity\Status;
use AdminBundle\Entity\Orderstatushistory;
use AdminBundle\Entity\Cart;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
* @Route("/admin")
*/
class OrderController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
		ini_set('xdebug.var_display_max_depth', -1);
		ini_set('xdebug.var_display_max_children', -1);
		ini_set('xdebug.var_display_max_data', -1);
    }

    /**
     * @Route("/orders/")
     * @Template()
     */
    public function indexAction()
    {
		$in_query_adv = $in_query_txt ='';
		$session = new Session();
		$role_id = $this->get('session')->get('role_id') ;
		$user_id = $this->get('session')->get('user_id') ;
		$in_query1 = $in_query = '';
		if($role_id == '3')
		{
			$in_query_txt = " AND order_master.delivery_boy_id='$user_id' ";
			$in_query_txt1 = " AND delivery_boy_id='$user_id' ";
		}

		$request = array();
		$field = "";
		$join="";
		$where="";
		if(isset($_REQUEST['adv_name_phone']) || isset($_REQUEST['adv_date_from']) || isset($_REQUEST['adv_date_to']) || isset($_REQUEST['adv_status']))
		{
			$in_query = array();
			if(isset($_REQUEST['adv_name_phone']) && !empty($_REQUEST['adv_name_phone']))
			{
				$arr = explode(' - ',$_REQUEST['adv_name_phone']);
				//var_dump(count($arr));exit;
				if(count($arr) >= 2)
				{
					$in_query[] = " user_master.username = '".$this->keyEncryptionAction($arr[0])."' ";
					$in_query[] = " user_master.user_mobile = '".$this->keyEncryptionAction($arr[1])."' ";
					$request[0] = $arr[0];
					$request[1] = $arr[1];
				}
			}

			if(isset($_REQUEST['adv_date_from']) && isset($_REQUEST['adv_date_to']) && !empty($_REQUEST['adv_date_from']) && !empty($_REQUEST['adv_date_to']))
			{
				$in_query[] = " (order_master.order_dateadded >= '".$_REQUEST['adv_date_from']." 00:00:00' and  order_master.order_dateadded <= '".$_REQUEST['adv_date_to']." 23:59:59') ";
				$request[2] = $_REQUEST['adv_date_from'];
				$request[3] = $_REQUEST['adv_date_to'];
			}

			if(isset($_REQUEST['adv_status']) && !empty($_REQUEST['adv_status']))
			{
				$in_query[] = " order_master.order_status_id = '".$_REQUEST['adv_status']."' ";
				$request[4] = $_REQUEST['adv_status'];
			}

			if(count($in_query) > 0)
			{
				$in_query_adv = implode(" AND ",$in_query);
				$in_query_adv = 'AND ( '.$in_query_adv.' )';
			}
			//var_dump($in_query_adv);exit;
		}
		//
		$autoahead_array = array();
		$autoahead = '';
		$order_master = $query = '';
		$status = $this->getDoctrine()
						->getManager()
						->getRepository("AdminBundle:Status")
						->findBy(array("is_deleted"=>0));
		$city_list = $this->getDoctrine()
						->getManager()
						->getRepository("AdminBundle:Citymaster")
						->findBy(array("is_deleted"=>0));

		$all_app_user = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Usermaster')
					   ->findBy(array("user_role_id"=>7,"is_deleted"=>0));
		if(!empty($all_app_user)){
			foreach($all_app_user as $val){
				$autoahead_array[] = "'".($val->getUsername())." - ".($val->getUser_mobile())."'";
				// ".$this->keyDecryptionAction($val->getUser_lastname())."
			}
			$autoahead = implode(",",$autoahead_array);
		}
		$all_delivery_boy = array();
		if($this->get('session')->get('role_id')== '1')
    	{
			$all_delivery_boy = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Usermaster')
					   ->findBy(array("user_role_id"=>3,"user_status"=>'active',"is_deleted"=>0));
		}
		else
		{
			$all_delivery_boy = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Usermaster')
					   ->findBy(array("user_role_id"=>3,"user_status"=>'active',"is_deleted"=>0));
		}

		if($this->get('session')->get('role_id')== '1')
		{
			$query = "SELECT order_master.* ,
					 domain_master.domain_name
				  FROM order_master
				  JOIN domain_master
						ON domain_master.domain_code = order_master.domain_id
					WHERE order_master.is_deleted = 0 $in_query_txt ";
		}
		if($this->get('session')->get('role_id')== '2')
		{
			$field = "";
			$join = "";
			$where = "AND order_master.is_deleted = 0 ORDER BY order_master.order_master_id DESC";
		}
		if($this->get('session')->get('role_id')== '4' || $this->get('session')->get('role_id')== '5' || $this->get('session')->get('role_id')== '6')
		{
			$user_list = $this->getDoctrine()
						->getManager()
						->getRepository('AdminBundle:Usermaster')
						->findOneBy(array("user_master_id"=>$this->get('session')->get('user_id'),"domain_id"=>$this->get('session')->get('domain_id')));

			$address = $this->getaddressAction($user_list->getAddress_master_id(),1);
			$field = ",address_master.city_id";
			$join = "";
			//$join = "JOIN address_master ON order_master.delivery_address_id = address_master.main_address_id";
			$where = " AND address_master.city_id = '".$address['city_id']."' AND order_master.is_deleted = 0 ORDER BY order_master.order_master_id DESC";
		}

		//var_dump("Y-m-d", strtotime("-7 days")));exit;

		if($this->get('session')->get('role_id')!= '1')
		{
			$query = "SELECT order_master.*,
					   city_master.city_name ,
					  domain_master.domain_name,
					  user_master.user_firstname,
					  user_master.username,
					  user_master.user_mobile
					  ".$field."
					  FROM
					  order_master
					  LEFT JOIN user_master
					  ON user_master.user_master_id = order_master.order_createdby
					  JOIN domain_master
					  ON domain_master.domain_code = order_master.domain_id
					  ".$join."
					  JOIN address_master ON order_master.delivery_address_id = address_master.address_master_id
					  LEFT JOIN city_master ON address_master.city_id = city_master.city_master_id
					  WHERE
					  order_master.order_status_id NOT IN (5) $in_query_txt AND
					  order_master.domain_id = '".$this->get('session')->get('domain_id')."' ".$in_query_adv."
					  ".$where."";
					  //order_master.order_status_id NOT IN (4,5) AND
		}
		else{
			$query = "SELECT city_master.city_name , order_master.*, domain_master.domain_name, user_master.user_firstname, user_master.username, user_master.user_mobile FROM order_master JOIN user_master ON user_master.user_master_id = order_master.order_createdby JOIN domain_master ON domain_master.domain_code = order_master.domain_id
			JOIN address_master ON order_master.delivery_address_id = address_master.address_master_id
			LEFT JOIN city_master ON address_master.city_id = city_master.city_master_id
			WHERE order_master.order_status_id NOT IN (4,5) AND order_master.order_status_id NOT IN (4,5) AND order_master.domain_id = 'fortune001' ".$in_query_adv." AND order_master.is_deleted = 0 ORDER BY order_master.order_master_id DESC";
			// order_master.order_status_id NOT IN (4,5) AND
		}
		//$query = '' ;
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare($query);
		$statement->execute();
		$order_master = $statement->fetchAll();

		$query = "SELECT order_master.*,cart.* from order_master,cart where order_master.delivery_time_type=2  and order_master.is_deleted=0 and cart.is_deleted=0 and order_master.order_master_id=cart.order_id and cart.shop_type='pharmacy' and order_master.domain_id='".$this->get('session')->get('domain_id')."'" . " and cart.domain_id='".$this->get('session')->get('domain_id')."'" ;
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare($query);
		$statement->execute();
		$my_orders = $statement->fetchAll();
		$pids=[];
		$generala_setting='';
		$g_settings = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Generalsetting')->findOneBy(array("general_setting_key"=>"delivery_charge"));

		if(!empty($my_orders))
		{
			for($i=0;$i<count($my_orders);$i++)
			{
				$pids[$i]=  $my_orders[$i]['order_master_id'];
			}

		}
		$generala_setting_value = json_decode($g_settings->getGeneral_setting_value());

		return array("orders"=>$order_master,"autoahead"=>$autoahead,"adv_request"=>$request,"status"=>$status,"all_delivery_boy"=>$all_delivery_boy,"city_list"=>$city_list,"pharmacy_order_ids"=>$pids,"general_setting_value"=>$generala_setting_value[0]->amount);
    }

	/**
     * @Route("/cancelled-order/")
     * @Template()
     */
    public function cancelledorderAction()
    {
		$in_query_adv = $in_query_txt ='';
		$session = new Session();
		$role_id = $this->get('session')->get('role_id') ;
		$user_id = $this->get('session')->get('user_id') ;
		$in_query1 = $in_query = '';
		if($role_id == '3')
		{
				$in_query_txt = " AND order_master.delivery_boy_id='$user_id' ";
				$in_query_txt1 = " AND delivery_boy_id='$user_id' ";
		}

		$request = array();
		$field = "";
		$join="";
		$where="";
		if(isset($_REQUEST['adv_name_phone']) || isset($_REQUEST['adv_date_from']) || isset($_REQUEST['adv_date_to']) || isset($_REQUEST['adv_status']))
		{
			$in_query = array();
			if(isset($_REQUEST['adv_name_phone']) && !empty($_REQUEST['adv_name_phone']))
			{
				$arr = explode(' - ',$_REQUEST['adv_name_phone']);
				//var_dump(count($arr));exit;
				if(count($arr) >= 2)
				{
					$in_query[] = " user_master.username = '".$this->keyEncryptionAction($arr[0])."' ";
					$in_query[] = " user_master.user_mobile = '".$this->keyEncryptionAction($arr[1])."' ";
					$request[0] = $arr[0];
					$request[1] = $arr[1];
				}

			}

			if(isset($_REQUEST['adv_date_from']) && isset($_REQUEST['adv_date_to']) && !empty($_REQUEST['adv_date_from']) && !empty($_REQUEST['adv_date_to']))
			{
				$in_query[] = " (order_master.order_dateadded >= '".$_REQUEST['adv_date_from']." 00:00:00' and  order_master.order_dateadded <= '".$_REQUEST['adv_date_to']." 23:59:59') ";
				$request[2] = $_REQUEST['adv_date_from'];
				$request[3] = $_REQUEST['adv_date_to'];
			}

			if(isset($_REQUEST['adv_status']) && !empty($_REQUEST['adv_status']))
			{
				$in_query[] = " order_master.order_status_id = '".$_REQUEST['adv_status']."' ";
				$request[4] = $_REQUEST['adv_status'];
			}

			if(count($in_query) > 0)
			{
				$in_query_adv = implode(" AND ",$in_query);
				$in_query_adv = 'AND ( '.$in_query_adv.' )';
			}
			//var_dump($in_query_adv);exit;
		}
		//
		$autoahead_array = array();
		$autoahead = '';
		$order_master = $query = '';
		$status = array();
		$city_list = array();

		$all_app_user = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Usermaster')
					   ->findBy(array("user_role_id"=>7,"is_deleted"=>0));
		if(!empty($all_app_user)){
			foreach($all_app_user as $val){
				$autoahead_array[] = "'".($val->getUsername())." - ".($val->getUser_mobile())."'";
				// ".$this->keyDecryptionAction($val->getUser_lastname())."
			}
			$autoahead = implode(",",$autoahead_array);
		}
		$all_delivery_boy = array();
		if($this->get('session')->get('role_id')== '1')
    	{
			$all_delivery_boy = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Usermaster')
					   ->findBy(array("user_role_id"=>3,"user_status"=>'active',"is_deleted"=>0));
		}
		else
		{
			$all_delivery_boy = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Usermaster')
					   ->findBy(array("user_role_id"=>3,"user_status"=>'active',"is_deleted"=>0));
		}

		if($this->get('session')->get('role_id')== '1')
		{
			$query = "SELECT order_master.* ,
					 domain_master.domain_name
				  FROM order_master
				  JOIN domain_master
						ON domain_master.domain_code = order_master.domain_id
					WHERE order_master.is_deleted = 0 $in_query_txt ";
		}
		if($this->get('session')->get('role_id')== '2')
		{
			$field = "";
			$join = "";
			$where = "AND order_master.is_deleted = 0 ORDER BY order_master.order_master_id DESC";
		}
		if($this->get('session')->get('role_id')== '4' || $this->get('session')->get('role_id')== '5' || $this->get('session')->get('role_id')== '6')
		{
			$user_list = $this->getDoctrine()
						->getManager()
						->getRepository('AdminBundle:Usermaster')
						->findOneBy(array("user_master_id"=>$this->get('session')->get('user_id'),"domain_id"=>$this->get('session')->get('domain_id')));

			$address = $this->getaddressAction($user_list->getAddress_master_id(),1);
			$field = ",address_master.city_id";
			$join = "";
			//$join = "JOIN address_master ON order_master.delivery_address_id = address_master.main_address_id";
			$where = " AND address_master.city_id = '".$address['city_id']."' AND order_master.is_deleted = 0 ORDER BY order_master.order_master_id DESC";
		}

		//var_dump("Y-m-d", strtotime("-7 days")));exit;

		if($this->get('session')->get('role_id')!= '1')
		{
			$query = "SELECT order_master.*,
					   city_master.city_name ,
					  domain_master.domain_name,
					  user_master.user_firstname,
					  user_master.username,
					  user_master.user_mobile
					  ".$field."
					  FROM
					  order_master
					  LEFT JOIN user_master
					  ON user_master.user_master_id = order_master.order_createdby
					  JOIN domain_master
					  ON domain_master.domain_code = order_master.domain_id
					  ".$join."
					  JOIN address_master ON order_master.delivery_address_id = address_master.address_master_id
					  LEFT JOIN city_master ON address_master.city_id = city_master.city_master_id
					  WHERE
					  order_master.order_status_id IN (5) $in_query_txt AND
					  order_master.domain_id = '".$this->get('session')->get('domain_id')."' ".$in_query_adv."
					  ".$where."";
					  //order_master.order_status_id NOT IN (4,5) AND
		}
		else{
			$query = "SELECT city_master.city_name , order_master.*, domain_master.domain_name, user_master.user_firstname, user_master.username, user_master.user_mobile FROM order_master JOIN user_master ON user_master.user_master_id = order_master.order_createdby JOIN domain_master ON domain_master.domain_code = order_master.domain_id
			JOIN address_master ON order_master.delivery_address_id = address_master.address_master_id
			LEFT JOIN city_master ON address_master.city_id = city_master.city_master_id
			WHERE order_master.order_status_id IN (4,5) AND order_master.order_status_id IN (4,5) AND order_master.domain_id = 'fortune001' ".$in_query_adv." AND order_master.is_deleted = 0 ORDER BY order_master.order_master_id DESC";
			// order_master.order_status_id NOT IN (4,5) AND
		}
		
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare($query);
		$statement->execute();
		$order_master = $statement->fetchAll();

		$query = "SELECT order_master.*,cart.* from order_master,cart where order_master.delivery_time_type=2  and order_master.is_deleted=0 and cart.is_deleted=0 and order_master.order_master_id=cart.order_id and cart.shop_type='pharmacy' and order_master.domain_id='".$this->get('session')->get('domain_id')."'" . " and cart.domain_id='".$this->get('session')->get('domain_id')."'" ;
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare($query);
		$statement->execute();
		$my_orders = $statement->fetchAll();
		$pids=[];
		$generala_setting='';
		$g_settings = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Generalsetting')->findOneBy(array("general_setting_key"=>"delivery_charge"));

		if(!empty($my_orders))
		{
			for($i=0;$i<count($my_orders);$i++)
			{
				$pids[$i]=  $my_orders[$i]['order_master_id'];
			}

		}
		$generala_setting_value = json_decode($g_settings->getGeneral_setting_value());

		return array("orders"=>$order_master,"autoahead"=>$autoahead,"adv_request"=>$request,"status"=>$status,"all_delivery_boy"=>$all_delivery_boy,"city_list"=>$city_list,"pharmacy_order_ids"=>$pids,"general_setting_value"=>$generala_setting_value[0]->amount);
	}



	 /**
     * @Route("/ajaxorderlisting/{domain_id}",defaults={"domain_id":""})
     *
     */
    public function ajaxorderlistingAction($domain_id)
    {
		$in_query_adv = '';
		$request = array();

		$status = $this->getDoctrine()
						->getManager()
						->getRepository("AdminBundle:Status")
						->findBy(array("is_deleted"=>0));

		if(isset($_REQUEST['adv_name_phone']) || isset($_REQUEST['adv_date_from']) || isset($_REQUEST['adv_date_to']) || isset($_REQUEST['adv_status']))
		{
			$in_query = array();
			if(isset($_REQUEST['adv_name_phone']) && !empty($_REQUEST['adv_name_phone']))
			{
				$arr = explode(' - ',$_REQUEST['adv_name_phone']);
				//var_dump(count($arr));exit;
				if(count($arr) >= 2)
				{
					$in_query[] = " user_master.username = '".$this->keyEncryptionAction($arr[0])."' ";
					$in_query[] = " user_master.user_mobile = '".$this->keyEncryptionAction($arr[1])."' ";
					$request[0] = $arr[0];
					$request[1] = $arr[1];
				}

			}

			if(isset($_REQUEST['adv_date_from']) && isset($_REQUEST['adv_date_to']) && !empty($_REQUEST['adv_date_from']) && !empty($_REQUEST['adv_date_to']))
			{
				$in_query[] = " (order_master.order_dateadded >= '".$_REQUEST['adv_date_from']." 00:00:00' and  order_master.order_dateadded <= '".$_REQUEST['adv_date_to']." 23:59:59') ";
				$request[2] = $_REQUEST['adv_date_from'];
				$request[3] = $_REQUEST['adv_date_to'];
			}

			if(isset($_REQUEST['adv_status']) && !empty($_REQUEST['adv_status']))
			{
				$in_query[] = " order_master.order_status_id = '".$_REQUEST['adv_status']."' ";
				$request[4] = $_REQUEST['adv_status'];
			}

			if(count($in_query) > 0)
			{
				$in_query_adv = implode(" AND ",$in_query);
				$in_query_adv = 'AND ( '.$in_query_adv.' )';
			}
			//var_dump($in_query_adv);exit;
		}

		$domain_id = $this->get('session')->get('domain_id');
		$fieldArr = array('0'=>'order_master.unique_no',
						  '1'=>'order_master.order_type',
						  '2'=>'user_master.user_firstname',
						  '3'=>'order_master.total_no_of_items',
						  '4'=>'order_master.order_bill_amount',
						  '5'=>'order_master.delivery_charge',
						  '6'=>'order_master.total_bill_amount',
						  '7'=>'order_master.special_instruction',
						  '8'=>'user_master.user_firstname',
						  '9'=>'order_master.delivery_date',
						  '10'=>'status.status_name'
						  );
		$sWhere ="";
		if(isset($_REQUEST['search']['value']) && !empty($_REQUEST['search']['value']))
		{
			$sWhere = "AND (";
			foreach($fieldArr as  $fieldArr_key => $fieldArr_value)
			{
				if((count($fieldArr)-1) == $fieldArr_key)
				{
					$sWhere.=$fieldArr_value." LIKE '%". $_REQUEST['search']['value']."%'";
				}
				else
				{
					$sWhere.=$fieldArr_value." LIKE '%". $_REQUEST['search']['value']."%' OR ";
				}
			}
			$sWhere .= ')';
		}
		else
		{
			$sWhere .= '';
		}

		$limit = $_REQUEST['length'];
		$start = $_REQUEST['start'];

		$em = $this->getDoctrine()->getEntityManager();
		$connection = $em->getConnection();

		if(isset($_REQUEST['search']) && ($_REQUEST['search'] == "Search") && (isset($_REQUEST['starting_date']) ) && (isset($_REQUEST['ending_date']) ) )
		{
			$start_date = $_REQUEST['starting_date']." 00:00:00" ;
			$end_date = $_REQUEST['ending_date']." 24:60:60" ;


			$query = "SELECT system_user_master.system_user_name ,
				system_user_master.search_term_2 , system_user_master.system_code,counters_of_system.requested_crate,counters_of_system.issued_crate ,
				counters_of_system.received_crate_from_plant ,counters_of_system.return_crate_to_plant ,
				counters_of_system.return_crate_by_grower,counters_of_system.received_crate_from_plant,
				counters_of_system.return_emptycrate_to_hub , counters_of_system.updated_datetime
				FROM
				system_user_master  JOIN counters_of_system ON system_user_master.system_code = counters_of_system.system_code
				WHERE system_user_master.system_user_role_name = 'GROWER'
				AND counters_of_system.updated_datetime >= '".$start_date."' AND counters_of_system.updated_datetime <= '".$end_date."'
				and system_user_master.`search_term_2`
				IN
				(SELECT route_no FROM `system_user_master` WHERE `system_user_role_name` = 'HUB' $plant_condition GROUP BY route_no)
							 AND
							(counters_of_system.requested_crate != 0 OR
							counters_of_system.issued_crate  != 0 OR
										counters_of_system.received_crate_from_plant  != 0 OR
							counters_of_system.return_crate_to_plant  != 0 OR
										counters_of_system.return_crate_by_grower  != 0 OR
							counters_of_system.received_crate_from_plant  != 0 OR
							counters_of_system.return_emptycrate_to_hub != 0

							 )
				$sWhere Limit $start,$limit";
		}
		else
		{
			$query = "	SELECT order_master.* , city_master.city_name , status.status_name , status.status_class , user_master.user_firstname ,user_master.user_lastname,  user_master.user_mobile ,user_master.user_master_id
					FROM
				order_master LEFT JOIN user_master ON order_master.order_createdby = user_master.user_master_id
							 LEFT JOIN address_master ON order_master.delivery_address_id = address_master.address_master_id
							 LEFT  JOIN city_master ON city_master.main_city_id = address_master.city_id
							 LEFT  JOIN status ON order_master.order_status_id = status.status_id  where
				 order_master.order_status_id NOT IN (4,5) AND order_master.domain_id = '". $this->get('session')->get('domain_id')."' ".$in_query_adv." AND order_master.is_deleted = 0 $sWhere GROUP BY order_master.order_master_id  ORDER BY `order_master_id` DESC Limit $start,$limit";

		}
		$statement = $connection->prepare($query);
		$statement->execute();
		$oderMaster = $statement->fetchAll();
		//echo $query;
		//total count of user
		$cnt_query = "select count(DISTINCT  order_master.order_master_id) as totalnouser
				FROM
				order_master LEFT JOIN user_master ON order_master.order_createdby = user_master.user_master_id
							 LEFT JOIN address_master ON order_master.delivery_address_id = address_master.address_master_id
							 LEFT  JOIN city_master ON city_master.main_city_id = address_master.city_id
							 LEFT  JOIN status ON order_master.order_status_id = status.status_id  where
				order_master.order_status_id NOT IN (4,5) AND order_master.domain_id = '". $this->get('session')->get('domain_id')."' ".$in_query_adv." AND order_master.is_deleted = 0 $sWhere   ORDER BY `order_master_id` DESC " ;
		$statement1 = $connection->prepare($cnt_query);
		$statement1->execute();
		$user_master1 = $statement1->fetchAll();

		if(isset($user_master1) && !empty($user_master1))
		{
			$toal_rec  = $user_master1[0]['totalnouser'];
		}
		else
		{
			$toal_rec = 0;
		}
		$minus = 0 ;
		if(isset($oderMaster) && !empty($oderMaster)){
			foreach($oderMaster as $order_master_key => $order_masterV_alue)
			{
				$checkbox_disp = '' ; $sp_inc = ' - ' ;
				if($this->get('session')->get('domain') != 'fortune' && ( $order_masterV_alue['delivery_boy_id'] == 0 ||  $order_masterV_alue['delivery_boy_id'] == NULL) ){
					$checkbox_disp = '<input type="checkbox" value="'.$order_masterV_alue['order_master_id'].'" class="assign_check" name="multi_assign[]">' ;
				}
				//$status_disp = '<span class="label color_field" style="background-color:#'.$order_masterV_alue['status_class'].';">'.$order_masterV_alue['status_name'].'</span>';
				$sele = "";
				if($order_masterV_alue['order_status_id'] == '5')
				{
					$sele = "disabled";
				}

				$status_disp = '<select class="" id="orderstatus" onchange="changeSTS('.$order_masterV_alue['order_master_id'].',this.value);" '.$sele.'>';
				if(!empty($status))
				{
					foreach($status as $val)
					{
						if($val->getStatus_id() != 4 && $val->getStatus_id() != 5)
						{
							$sts = '';
							if($val->getStatus_id() == $order_masterV_alue['order_status_id'])
							{
								$sts = 'selected="selected"';
							}
							$status_disp .='<option value="'.$val->getStatus_id().'" '.$sts.'>'.$val->getStatus_name().'</option>';
						}
					}

				}
				$status_disp .='</select>';

				$user_mobile = ($order_masterV_alue['user_mobile']);
				$user_firstname =($order_masterV_alue['user_firstname']);
				$user_lastname = ($order_masterV_alue['user_lastname']);
				$cust_phno = $user_firstname.  " - ".$user_mobile;
				$amt_bill_delivery = 0 ;
				if($order_masterV_alue['delivery_charge'] == 0 || $order_masterV_alue['delivery_charge'] == NULL || $order_masterV_alue['delivery_charge'] == ''){
					$amt_bill_delivery = $order_masterV_alue['order_bill_amount']  ;
				}
				else{
					$amt_bill_delivery = $order_masterV_alue['order_bill_amount'] . " + " . $order_masterV_alue['delivery_charge'];
				}
				$link_assign = '' ;
				if( $this->get('session')->get('role_id') != 6 && $order_masterV_alue['order_status_id'] == 10 ){
					$link_assign = "<a href=".$this->generateUrl("admin_order_assignorder",array("order_id"=>$order_masterV_alue['order_master_id'],"domain"=>$this->get('session')->get('domain')))." class='btn btn-primary btn-xs'>Assign</a>&nbsp;&nbsp;";
				}
				$link_view = "<a href=".$this->generateUrl("admin_order_vieworder",array("order_id"=>$order_masterV_alue['order_master_id'],"domain"=>$this->get('session')->get('domain')))." class='btn btn-warning btn-xs' >View</a>";
				$link_delete = '' ;
				if($this->get('session')->get('role_id') == 2){
					$link_delete = '<button type="button" onclick="removemodalopen(this);" class="btn btn-danger btn-xs removeitem" value="'.$order_masterV_alue['order_master_id'].'"><i class="fa fa-remove"></i></button>';
				}
				$operation_link = $link_assign . $link_view . " &nbsp;&nbsp;" .$link_delete ;
				$delivery_boy_st = ' - ' ;
				if($order_masterV_alue['delivery_boy_id'] != 0 && $order_masterV_alue['delivery_boy_id'] != NULL){
					$delivery_boy_st = "Assigned";
				}
				if( $order_masterV_alue['special_instruction'] == '(null)' || $order_masterV_alue['special_instruction'] == NULL || $order_masterV_alue['special_instruction'] == '' )
				{
					$sp_inc = ' - ' ;
				}
				else{
					$sp_inc = $order_masterV_alue['special_instruction'] ;
				}


				$order_type=$order_masterV_alue['order_type'];
									if($order_type==1 or $order_type==0)
										$order_type_text="Without Prescription";
									elseif($order_type==2)
										$order_type_text="With Prescription";
									elseif($order_type==3)
										$order_type_text="Medical Examination";

				if($this->get('session')->get('domain') != 'fortune'){
					$new_user_maste_arr[] = array(
					#	$checkbox_disp,
						$order_masterV_alue['unique_no'],
						$order_type_text,
						$cust_phno,

						$order_masterV_alue['total_no_of_items'],
						$amt_bill_delivery,
						$order_masterV_alue['total_bill_amount'],
						$sp_inc,
						$delivery_boy_st,
						$order_masterV_alue['delivery_date'] ." " . $order_masterV_alue['delivery_time'] ,
						$order_masterV_alue['order_dateadded']  ,
						$status_disp ,
						$operation_link
					);
				}
				else{
					$new_user_maste_arr[] = array(
						$order_type_text,
						$order_masterV_alue['unique_no'],
						$cust_phno,

						$order_masterV_alue['total_no_of_items'],
						$amt_bill_delivery,
						$order_masterV_alue['total_bill_amount'],
						$sp_inc,
						$order_masterV_alue['delivery_date'] ." " . $order_masterV_alue['delivery_time'] ,
						$order_masterV_alue['order_dateadded']  ,
						$status_disp ,
						$operation_link
					);
				}
			}
		}
		else
		{
			$new_user_maste_arr = array();
		}
		$json_data = array(
			"draw"            => intval( $_REQUEST['draw'] ),
			"recordsTotal"    => intval( $toal_rec),
			"recordsFiltered" => intval( $toal_rec),
			"data"            => $new_user_maste_arr   // total data array
		);
		return new Response(json_encode($json_data));


	}

	/**
     * @Route("/vieworder/{order_id}",defaults={"order_id":""})
     * @Template()
     */
    public function vieworderAction($order_id)
    {
    	$language_id=1;
    	$em = $this->getDoctrine()->getManager();
		$order_master = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Ordermaster')->findOneBy(array("order_master_id"=>$order_id,'is_deleted'=>'0',"domain_id"=>$this->get('session')->get('domain_id')));
		$prescription_details = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Medialibrarymaster')->findOneBy(array("media_library_master_id"=>$order_master->getPrescription_image_id()));

								$prescription_image = '';
								if(!empty($prescription_details))
								{
									$prescription_image =  $this->container->getParameter('live_path') . $prescription_details->getMedia_location() . "/".$prescription_details->getMedia_name();
								}
		$status_list = $this->getDoctrine()
					->getManager()
					->getRepository('AdminBundle:Status')
					->findBy(array('is_deleted'=>'0'));
		$general_settings = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Generalsetting')->findBy(array("is_deleted"=>0));
					if(!empty($general_settings))
					{
						foreach($general_settings as $key=>$val)
						{
							if($val->getGeneral_setting_key() == 'delivery_charge' && $val->getGeneral_setting_id() == 3)
							{
							foreach(json_decode($val->getGeneral_setting_value()) as $gkey=>$gval)
								{
									$apa_delivery_charge = $gval->amount;
								}
							}

						}
					}

		$product_details_arr = '' ;
		$user_master ='';
		$delivery_address = '';
		if(!empty($order_master))
		{
			// customer details
			$user_master = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Usermaster')->findOneBy(array("user_master_id"=>$order_master->getOrder_createdby()));

			// Delivery Address
			$delivery_address = '';
			if($order_master->getDelivery_address_id() != '0')
			{
				$delivery_address = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Addressmaster')->findOneBy(array("address_master_id"=>$order_master->getDelivery_address_id()));
				$address_title = $delivery_address->getAddress_title();
				$address_name = $delivery_address->getAddress_name();
				$address_name2 = $delivery_address->getAddress_name2();
				$contact_no = $delivery_address->getContact_no();
				$block_no = $delivery_address->getBlock_no();
				$floor_no = $delivery_address->getFloor_no();
				$building_no = $delivery_address->getBuilding_no();
				$street = $delivery_address->getStreet();
				$flate_house_number = $delivery_address->getFlate_house_number();
				$jadda = $delivery_address->getJadda();
				$pincode = $delivery_address->getPincode();
				$add = $building_no.' '.$floor_no.' '.$block_no.' '.$address_title.' '.$address_name.
				' '.$address_name2.' '.$contact_no.
				' '.$street.' '.$flate_house_number.' '.$jadda.' '.$pincode;
			}

			$cart_details = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Cart')->findBy(array("order_id"=>$order_id,'is_deleted'=>'0',"order_placed"=>'true',"domain_id"=>$order_master->getDomain_id(),"created_by"=>$order_master->getOrder_createdby()));

				if(!empty($cart_details))
				{
					$attr_arr = '';
					foreach($cart_details as $ckey=>$cval)
					{
						// product name
						if($cval->getCombination_id() == "0")
						{
							$attr_arr = '';
							$em = $this->getDoctrine()->getManager();
							$connection = $em->getConnection();
							$query = "SELECT * FROM product_category_relation INNER JOIN category_master ON product_category_relation.category_id=category_master.category_master_id WHERE product_category_relation.product_id='".$cval->getProduct_id()."' and product_category_relation.is_deleted=0";
							$statement1 = $connection->prepare($query);
							$statement1->execute();
							$product_category = $statement1->fetchAll();

							$product_details = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Productmaster')->findOneBy(array("main_product_master_id"=>$cval->getProduct_id(),'language_id'=>$language_id));

							$supplier_details = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Suppliermaster')->findOneBy(array("main_supplier_id"=>$cval->getSupplier_id(),'language_id'=>$language_id));

							$product_supplier_details = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Productsupplierrelation')->findOneby(array("main_product_id"=>$cval->getProduct_id(),"main_combination_id"=>0,"supplier_id"=>$cval->getSupplier_id()));

							if(!empty($supplier_details) || !empty($product_details) || !empty($product_supplier_details) || !empty($product_category))
							{
								$logo_details = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Medialibrarymaster')->findOneBy(array("media_library_master_id"=>$product_details->getProduct_logo()));

								$attr_arr = $image = '';
								if(!empty($logo_details))
								{
									$image =  $this->container->getParameter('live_path') . $logo_details->getMedia_location() . "/".$logo_details->getMedia_name();
								}

								$product_details_arr[] = array(
									"main_product_master_id"=>!empty($product_details)?$product_details->getMain_product_master_id():'',
									"product_title"=>!empty($product_details)?$product_details->getProduct_title():'',
									"short_description"=>!empty($product_details)?$product_details->getShort_description():'',
									"product_logo"=>!empty($image)?$image:'',
									"combination_id"=>!empty($cval->getCombination_id())?$cval->getCombination_id():'',
									"selected_attribute"=>!empty($attr_arr)?$attr_arr:'',
									"supplier_main_id"=>!empty($supplier_details)?$supplier_details->getMain_supplier_id():'',
									"supplier_name"=>!empty($supplier_details)?$supplier_details->getsupplier_name():'',
									"original_price"=>!empty($product_details)?$product_details->getOriginal_price():'',
									"impact_price"=>!empty($cval->getUnit_price())?$cval->getUnit_price():'',
									"final_price"=>!empty($product_details)?(float)$product_details->getOriginal_price()+(float)$cval->getUnit_price():'',
									"quantity"=>!empty($cval->getQuantity())?$cval->getQuantity():'',
									"total_price"=>!empty($cval->getTotal_price())?$cval->getTotal_price():'',
									'category_name'=>!empty($product_category)?$product_category[0]['category_name']:'',
									'email'=>!empty($product_category)?$product_category[0]['email_id']:'',
								);
							}
						}
						else
						{
							$attr_arr = '';
							$product_details = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Productmaster')->findOneBy(array("main_product_master_id"=>$cval->getProduct_id(),'language_id'=>$language_id));

							//get Combination
							$combination_details = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Combination')->findOneBy(array("product_id"=>$cval->getProduct_id(),"combination_id"=>$cval->getCombination_id()));

							$combination_relation_details = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Combinationrelation')->findBy(array("product_id"=>$cval->getProduct_id(),"combination_id"=>$cval->getCombination_id()));

							//get Supplier
							$supplier_details = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Suppliermaster')->findOneBy(array("main_supplier_id"=>$cval->getSupplier_id(),'language_id'=>$language_id));

							$product_supplier_details = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Productsupplierrelation')->findOneby(array("main_product_id"=>$cval->getProduct_id(),"main_combination_id"=>$cval->getCombination_id(),"supplier_id"=>$cval->getSupplier_id()));

							if(!empty($supplier_details) || !empty($product_details) || !empty($combination_details))
							{
								$logo_details = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Medialibrarymaster')->findOneBy(array("media_library_master_id"=>$product_details->getProduct_logo()));

								$image = '';
								if(!empty($logo_details))
								{
									$image =  $this->container->getParameter('live_path') . $logo_details->getMedia_location() . "/".$logo_details->getMedia_name();
								}
								$attr_arr = '';
								if(!empty($combination_relation_details))
								{
									$attribute_info = $attribute_value_info = '';
										foreach($combination_relation_details as $crkey=>$crval)
										{
										// get attribute value
										$attribute_info = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Attributemaster')->findOneBy(array("main_attribute_id"=>$crval->getAttribute_id(),"language_id"=>$language_id));
										$attribute_value_info = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Attributevalue')->findOneBy(array("main_attribute_value_id"=>$crval->getAttribute_value_id(),"language_id"=>$language_id));
										$attr_arr[] = array(
											"attribute_id"=>$attribute_info->getMain_attribute_id(),
											"name"=>$attribute_info->getAttribute_name(),
											"attribute_value_id"=>$attribute_value_info->getMain_attribute_value_id(),
											"value_name"=>$attribute_value_info->getValue()
										);
									}
								}
								$product_details_arr[] = array(
									"main_product_master_id"=>!empty($product_details)?$product_details->getMain_product_master_id():'',
									"product_title"=>!empty($product_details)?$product_details->getProduct_title():'',
									"short_description"=>!empty($product_details)?$product_details->getShort_description():'',
									"product_logo"=>!empty($image)?$image:'',
									"combination_id"=>!empty($cval->getCombination_id())?$cval->getCombination_id():'',
									"selected_attribute"=>!empty($attr_arr)?$attr_arr:'',
									"supplier_main_id"=>!empty($supplier_details)?$supplier_details->getMain_supplier_id():'',
									"supplier_name"=>!empty($supplier_details)?$supplier_details->getsupplier_name():'',
									"original_price"=>!empty($product_details)?$product_details->getOriginal_price():'',
									"impact_price"=>!empty($cval->getUnit_price())?$cval->getUnit_price():'',
									"final_price"=>!empty($product_details)?(float)$product_details->getOriginal_price()+(float)$cval->getUnit_price():'',
									"quantity"=>!empty($cval->getQuantity())?$cval->getQuantity():'',
									"total_price"=>!empty($cval->getTotal_price())?$cval->getTotal_price():'',
									'category_name'=>!empty($product_category)?$product_category[0]['category_name']:'',
									'email'=>!empty($product_category)?$product_category[0]['email_id']:'',
								);

							}
						}
					}
				}
			//print_r($product_details_arr);exit;
			$all_delivery_boy = array();
			if(!empty($this->get('session')->get('domain_id')) && $this->get('session')->get('role_id')== '1')
	    	{
				$all_delivery_boy = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Usermaster')
						   ->findBy(array("user_role_id"=>3,"user_status"=>'active',"is_deleted"=>0));
			}
			else
			{
				$all_delivery_boy = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Usermaster')
						   ->findBy(array("user_role_id"=>3,"user_status"=>'active',"is_deleted"=>0));
			}
			return array("user_info"=>$user_master,"delivery_charge"=>$apa_delivery_charge,"prescription_image"=>$prescription_image,"delivery_address"=>$add,"order_details"=>$order_master , "product_details_arr"=>$product_details_arr,"status_list"=>$status_list,"all_delivery_boy"=>$all_delivery_boy);
		}
		else{
			$this->get("session")->getFlashBag()->set("error_msg","Order is not available in this system.");
			return $this->redirect($this->generateUrl("admin_order_index",array("domain"=>$this->get('session')->get('domain'))));
		}
		//var_dump($user_master);exit;

	}
	/**
     * @Route("/assignorder/{order_id}")
     * @Template()
     */
    public function assignorderAction($order_id)
    {
    	$em = $this->getDoctrine()->getManager();
		$order_master = $this->getDoctrine()
						->getManager()
						->getRepository('AdminBundle:Ordermaster')
						->findOneBy(array("order_master_id"=>$order_id,'is_deleted'=>'0',"domain_id"=>$this->get('session')->get('domain_id')));
		$area="";
		/*var_dump($order_master);exit;*/
		$address = $this->getaddressAction($order_master->getDelivery_address_id(),1);
		$cart_pharmcy = $this->getDoctrine()
						->getManager()
						->getRepository('AdminBundle:Cart')
						->findBy(array("order_id"=>$order_id,'is_deleted'=>'0',"domain_id"=>$this->get('session')->get('domain_id'),"shop_type"=>'pharmacy'));
		$cart_foodsuply = $this->getDoctrine()
						->getManager()
						->getRepository('AdminBundle:Cart')
						->findBy(array("order_id"=>$order_id,'is_deleted'=>'0',"domain_id"=>$this->get('session')->get('domain_id'),"shop_type"=>'food_supplement'));
			if($address!='FALSE')
					$area=$address['area_name'];

		/*$delivery_boy_area = $this->getDoctrine()
						->getManager()
						->getRepository('AdminBundle:Deliveryservicearea')
						->findBy(array("area_id"=>$address['area_id'],'is_deleted'=>'0',"domain_id"=>$this->get('session')->get('domain_id')));
						*/
			$user_list =array();
			$user_info3=array();

			$shop_info = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Shopmaster')
					   ->findBy(array("shop_type"=>'food_supplement',"is_deleted"=>0));
			if(!empty($shop_info)){
				foreach($shop_info as $key=>$value){
					$user_info1 = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Usermaster')
					   ->findOneBy(array("user_role_id"=>3,"user_status"=>'active',"is_deleted"=>0,"user_master_id"=>$value->getUsermaster_id()));

								$user_info3[] = $user_info1;

				}

			}
			$user_info2=array();
			$shop_info1 = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Shopmaster')
					   ->findBy(array("shop_type"=>'pharmacy',"is_deleted"=>0));
			if(!empty($shop_info1)){
				foreach($shop_info1 as $key=>$value){
					$user_info4 = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Usermaster')
					   ->findOneBy(array("user_role_id"=>3,"user_status"=>'active',"is_deleted"=>0,"user_master_id"=>$value->getUsermaster_id()));


								$user_info2[] = $user_info4;

				}

			}

			$user_info=null;
			if(!empty($cart_pharmcy)){
				if(!empty($user_info2)){
					$user_info=$user_info2;
				}
			}elseif(!empty($cart_foodsuply)){
				if(!empty($user_info3)){
					$user_info=$user_info3;
				}
			}
			if(!empty($cart_pharmcy) && !empty($cart_foodsuply)){
				if(!empty($user_info3) && !empty($user_info2)){
					$user_info=array_merge($user_info3,$user_info2);
				}
			}



    	return array("user_list"=>$user_info,"area"=>$area,"order_id"=>$order_id,"delivery_boy_id"=>$order_master->getDelivery_boy_id(),"delivery_date"=>$order_master->getDelivery_date(),"delivery_time"=>$order_master->getDelivery_time(),"status_id"=>$order_master->getOrder_status_id());
	}
	/**
     * @Route("/assigndeliveryboy")
     */
    public function assigndeliveryboyAction()
    {
    	if(isset($_REQUEST['flag']) && $_REQUEST['flag'] == "assign_order")
    	{
			$order_id = $_REQUEST['order_id'];
			$user_master_id = $_REQUEST['user_master_id'];
			$date = date("Y-m-d", strtotime($_REQUEST['date']));
			$time = date("H:i", strtotime($_REQUEST['time']));

			$order_master = $this->getDoctrine()
						->getManager()
						->getRepository('AdminBundle:Ordermaster')
						->findOneBy(array("order_master_id"=>$order_id,'is_deleted'=>'0',"domain_id"=>$this->get('session')->get('domain_id')));

			if(!empty($order_master))
			{

				$order_master->setOrder_status_id(1);
				$order_master->setDelivery_boy_id($user_master_id);
				$order_master->setDelivery_date($date);
				$order_master->setDelivery_time($time);
				$em = $this->getDoctrine()->getManager();
				$em->persist($order_master);
				$em->flush();

				$orderstatus_master = new Orderstatushistory();
				$orderstatus_master->setOrder_id($order_id);
				$orderstatus_master->setUser_master_id($user_master_id);
				$orderstatus_master->setStatus_id(1);
				$orderstatus_master->setDomain_id($order_master->getDomain_id());
				$orderstatus_master->setCreate_date(date('Y-m-d H:i:s'));
				$orderstatus_master->setIs_deleted(0);
				$em = $this->getDoctrine()->getManager();
				$em->persist($orderstatus_master);
				$em->flush();

				$domain_code = $order_master->getDomain_id();
				$detail = "Your order is Confirmed";
				$message = json_encode(array("detail"=>$detail,"code" => '2', "response" => "Order Status Change"));

				$gcm_regids = $this->find_gcm_regid($order_master->getOrder_createdby());

				if(!empty($gcm_regids))
				{
					$app_id='CUST';
					if (count($gcm_regids[0])>0)
					{
						//$this->send_notification($gcm_regids,"Order Status Change",$message,2,$app_id,$domain_code,"order_master",$order_id);
					}
				}
				$apns_regids = $this->find_apns_regid($order_master->getOrder_createdby());
				if(!empty($apns_regids))
				{
					$app_id='CUST';
					if (count($apns_regids[0])>0)
					{
						//$this->send_notification($apns_regids,"Order Status Change",$message,1,$app_id,$domain_code,"order_master",$order_id);
					}
				}

				$detail = "You have new order";
				$message = json_encode(array("detail"=>$detail,"code" => '4', "response" => "Order Status Change"));

				$gcm_regids = $this->find_gcm_regid($user_master_id);

				if(!empty($gcm_regids))
				{
					$app_id='DEL';
					if (count($gcm_regids[0])>0)
					{
						//$this->send_notification($gcm_regids,"Order assigned",$message,2,$app_id,$domain_code,"order_master",$order_id);
					}
				}

				$apns_regids = $this->find_apns_regid($user_master_id);
				if(!empty($apns_regids))
				{
					$app_id='CUST';
					if (count($apns_regids[0])>0)
					{
						//$this->send_notification($apns_regids,"Order Status Change",$message,1,$app_id,$domain_code,"order_master",$order_id);
					}
				}

				$content = array("user_id"=>$user_master_id);
			}

			return new Response(json_encode($content));
		}
		return null;
    }
    /**
     * @Route("/changeorderstatus")
     * @Template()
     */
    public function changeorderstatusAction()
    {
    	if(isset($_REQUEST['flag']) && $_REQUEST['flag'] == 'changests' && isset($_REQUEST['status_id']) && $_REQUEST['status_id'] != "" && isset($_REQUEST['order_id']) && $_REQUEST['order_id'] != "")
    	{
				$domain_id = "aid001";
				//var_dump('yes1');exit;
    		$order_master = $this->getDoctrine()
						->getManager()
						->getRepository('AdminBundle:Ordermaster')
						->findOneBy(array("order_master_id"=>$_REQUEST['order_id'],"is_deleted"=>0));

			if(!empty($order_master))
			{
				$order_master->setOrder_status_id($_REQUEST['status_id']); //exit;
				$em = $this->getDoctrine()->getManager();
				$em->persist($order_master);
				$em->flush();

				$check_sts = $this->getDoctrine()
						->getManager()
						->getRepository('AdminBundle:Ordermaster')
						->findOneBy(array("order_master_id"=>$_REQUEST['order_id'],"is_deleted"=>0));

				if($check_sts->getOrder_status_id() == $_REQUEST['status_id'])
				{
					//update qty - START
					$cart_check = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Cart')->findBy(array('domain_id'=>$domain_id,'order_id'=>$_REQUEST['order_id'],'is_deleted'=>0));

					foreach ($cart_check as $key_del => $value_del) {

						$em = $this->getDoctrine()->getManager();
						$connection = $em->getConnection();
						$statement = $connection->prepare("UPDATE product_master SET quantity=quantity+".$value_del->getQuantity()." WHERE main_product_master_id = '".$value_del->getProduct_id()."'");
						$statement->execute();

					}
					//update qty - END
					$status_text = $this->getstatusAction($_REQUEST['status_id']);
					$domain_code = $order_master->getDomain_id();
					$detail = "Your order is ".$status_text;
					$message = json_encode(array("detail"=>$detail,"code" => '2', "response" => "Order Status Change"));

					/*GCM CODE*/
					$gcm_regids = $this->find_gcm_regid($order_master->getOrder_createdby());
					if(!empty($gcm_regids))
					{
						$app_id='CUST';
						if (count($gcm_regids[0])>0)
						{
							$this->send_notification($gcm_regids,"Order Status Change",$message,2,$app_id,$domain_code,"order_master",$_REQUEST['order_id']);
						}
					}
					/*APNS CODE*/
					$apns_regids = $this->find_apns_regid($order_master->getOrder_createdby());
					if(!empty($apns_regids))
					{
						$app_id='CUST';
						if (count($apns_regids[0])>0)
						{
							$this->send_notification($apns_regids,"Order Status Change",$message,1,$app_id,$domain_code,"order_master",$_REQUEST['order_id']);
						}
					}

					return new Response(json_encode(array("msg"=>"Order status changed successfully!","cls"=>"text-success")));
				}
				else
				{
					return new Response(json_encode(array("msg"=>"Oops! Status not changed!","cls"=>"text-danger")));
				}
			}

		}

		return new Response("No");
    }
    /**
     * @Route("/multipleorder")
     */
    public function multipleorderAction()
    {
		//echo "<pre>";
    	if( isset($_REQUEST['flag']) && $_REQUEST['flag'] == 'getmultiple_orders' && isset($_REQUEST['order_id_array']) && !empty($_REQUEST['order_id_array']))
    	{
			$order_id_array = $_REQUEST['order_id_array'];
			//$order_id_array [] = 1502 ;
			//$order_id_array [] = 827 ;
			//$order_id_array [] = 688 ;
			//$_POST['order_id_array'] =  $order_id_array ;
			$em = $this->getDoctrine()->getManager();
			$connection = $em->getConnection();
			$query_fetch  = "SELECT order_master.*,address_master.area_id
				FROM order_master
				JOIN address_master ON order_master.delivery_address_id = address_master.main_address_id
				WHERE
				order_master.order_master_id IN (".$order_id_array.") AND
				order_master.order_status_id = 10 AND
				order_master.domain_id = '".$this->get('session')->get('domain_id')."' AND
				order_master.is_deleted = 0 AND address_master.language_id = 1 AND
				address_master.is_deleted = 0" ;
			$statement = $connection->prepare($query_fetch);
			$statement->execute();
			$order_master = $statement->fetchAll();

			$order_list = NULL;
			if(!empty($order_master)){
				foreach($order_master as $key=>$val)
				{
					/*$delivery_boy_area = $this->getDoctrine()
							->getManager()
							->getRepository('AdminBundle:Deliveryservicearea')
							->findBy(array("area_id"=>$val['area_id'],'is_deleted'=>'0',"domain_id"=>$this->get('session')->get('domain_id')));

					$deliveryboy_list =array();
					if(!empty($delivery_boy_area))
					{
						foreach($delivery_boy_area as $dkey=>$dval)
						{*/
							$user_info = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Usermaster')
					   ->findBy(array("user_role_id"=>3,"user_status"=>'active',"is_deleted"=>0));
							if(!empty($user_info))
							{
								foreach($user_info as $user_info)
								{
								$deliveryboy_list[] = array(
										"user_master_id"=>$user_info->getUser_master_id(),
										"username"=>$user_info->getUsername(),
										"user_firstname"=>$user_info->getUser_firstname(),
										"user_lastname"=>$user_info->getUser_lastname(),
										"user_emailid"=>$user_info->getUser_emailid()
									);
								}
							}

						/*}
					}
					*/
					$order_list[] = array(
					  "order_master_id" => $val['order_master_id'],
					  "unique_no" => $val['unique_no'],
					  "total_no_of_items" => $val['total_no_of_items'],
					  "total_bill_amount" => $val['total_bill_amount'],
					  "order_bill_amount" => $val['order_bill_amount'],
					  "total_discount" => $val['total_discount'],
					  "coupon_code" => $val['coupon_code'],
					  "delivery_charge" => $val['delivery_charge'],
					  "special_instruction" => $val['special_instruction'],
					  "delivery_address_id" => $val['delivery_address_id'],
					  "delivery_boy_id" => $val['delivery_address_id'],
					  "order_status_id" => $val['order_status_id'],
					  "customer_order_receive" => $val['customer_order_receive'],
					  "delivery_date" => $val['delivery_date'],
					  "delivery_time" => $val['delivery_time'],
					  "signature_image_id" => $val['signature_image_id'],
					  "signature_capture_date" => $val['signature_capture_date'],
					  "customer_email" => $val['customer_email'],
					  "order_createdby" => $val['order_createdby'],
					  "order_dateadded" => $val['order_dateadded'],
					  "last_update_on" => $val['last_update_on'],
					  "domain_id" => $val['domain_id'],
					  "is_deleted" => $val['is_deleted'],
					  "area_id" => $val['area_id'],
					  "delivery_boy_list"=>$deliveryboy_list
					);
				}
			}

			?>
			<style>
				.radio{
					margin-bottom: 0px !important;
					margin-top: 0px !important;
				}
				.bootstrap-datepicker{
					z-index:1251 !important;
				}
				.bootstrap-timepicker{
					z-index:1252 !important;
				}
			</style>
			<div id="ord_model">
			<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal">&times;</button>
				  <h4 class="modal-title">Assign orders</h4>
			</div>

			<div class="modal-body">

				<div class="col-md-8">
					<div class="box box-warning box-solid">
					  <div class="box-body">
						  <div class="col-md-6 bootstrap-datepicker">
								<div class="form-group">
									  <label>Select Delivery Date</label>
									  <input required type="text" name="date" id="date" value="<?php echo date('Y-m-d');?>" class="checkvali form-control" >
								</div>
						  </div>
						  <div class="col-md-6 bootstrap-timepicker">
								<div class="form-group">
									  <label>Select Delivery Time</label>
									 <div class="input-append bootstrap-timepicker">
										<input  required id="time" name="time" type="text" value="<?php echo date('H:i A');?>" class="input-small">
										<span class="add-on"><i class="icon-time"></i></span>
									</div>
								</div>

						  </div>
					  </div>
					</div>
				</div>
				<table class="table table-bordered" border=1>
	                <tbody>

	                    <tr>
	                      <th style="width: 10px">#</th>
	                      <th>Order</th>
	                      <th>AID MAN</th>
	                    </tr>
	                    <?php
	                    foreach($order_list as $key=>$val)
						{
	                    ?>
	                    <tr>
	                      <td ><?php echo $key+1; ?></td>
	                      <td >
	                      	<input type="hidden" class="order_id_array" value="<?php echo $val['order_master_id']; ?>"/>
	                      	<dl class="dl-horizontal">
			                    <dt>Order No.</dt>
			                    <dd><?php echo $val['unique_no']; ?></dd>



			                    <dt>Total No of items</dt>
			                    <dd><?php echo $val['total_no_of_items']; ?></dd>

			                    <dt>Bill amount</dt>
			                    <dd><?php echo $val['order_bill_amount']; ?></dd>

			                    <dt>Order date</dt>
			                    <dd><?php echo date('Y-m-d h:i A',strtotime($val['order_dateadded'])); ?></dd>
		                    </dl>
	                      </td>
	                      <td >

								<?php
								$deliveryboy_list = $val['delivery_boy_list'];
								if(isset($deliveryboy_list) && !empty($deliveryboy_list) && count($deliveryboy_list)>0)
								{
									 foreach($deliveryboy_list as $dbkey=>$dbval){ ?>
										<div class="radio">
											  <label>
												<input type="radio" name="del<?php echo $val['order_master_id']; ?>" id="del<?php echo $val['order_master_id']; ?>" value="<?php echo $dbval['user_master_id']; ?>" checked>
												<?php echo $dbval['user_firstname']." ".$dbval['user_lastname']; ?>
											  </label>
										</div>
									<?php }
								}
								else{ ?>
								  <p>No Delivery Boys are available for this city</p>
								<?php }
								?>

		                </td>
	                    </tr>
	                    <?php

	                	}
	                	?>
	              	</tbody>
	            </table>

            </div>
			<div class="modal-footer">
				<div>
				    <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary btn-flat" id="multiassign_btn" onclick="multipleassign_order();" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Loading...">Assign</button>
				</div>
			</div>
			</div>
			<script>

				function multipleassign_order()
				{
					var timevalue = $('#time').val();
					var datevalue = $('#date').val();
					if (datevalue == "" ||  timevalue =="" ) {
                        alert("Please Select Date and time ");
                    }
					else{
						var order_id_array = [];
						var del_array = [];
						$(".order_id_array").each(function(i){
							order_id_array.push($(this).val());
							$("#del"+$(this).val()+":checked").each(function(j){
								del_array.push($(this).val());

							});
						});

						$("#multiassign_btn").button('loading');
						$.ajax({
						  type: 'POST',
						  url : "<?php echo $this->generateUrl('admin_order_assignmultipleorder',array('domain'=>$this->get('session')->get('domain'))) ?>",
						  data: {'date':datevalue,'time':timevalue,'order_id_array':order_id_array,'del_array':del_array,'flag':'assignmultiple_orders'},
						  dataType: 'json',
						  success: function(res)
						  {
								if(res.code == 'yes')
								{
									$("#ord_model").html("<div class='modal-header'><h4 class='modal-title text-green'>"+res.msg+"</h4></div>");
								}
								else
								{
									$("#ord_model").html("<div class='modal-header'><h4 class='modal-title text-red'>"+res.msg+"</h4></div>");
								}
								$("#multiassign_btn").button('reset');
								window.location.reload();
						  }

						  });


					}
				}
			</script>
			<?php
		}
    	return new Response();
    }
    /**
     * @Route("/assignmultipleorder")
     */
    public function assignmultipleorderAction()
    {
    	$content = NULL;
    	if(isset($_POST['flag']) && $_POST['flag'] == 'assignmultiple_orders' && !empty($_POST['order_id_array']) && !empty($_POST['del_array']))
    	{

    		$delivery_date = $_POST['date'];
    		$delivery_time = $_POST['time'];
			foreach($_POST['order_id_array'] as $key=>$val)
			{
				$order_info = $this->getDoctrine()
						->getManager()
						->getRepository('AdminBundle:Ordermaster')
						->findOneBy(array('order_master_id'=>$val,'order_status_id'=>10,'is_deleted'=>'0',"domain_id"=>$this->get('session')->get('domain_id')));

				if(!empty($order_info))
				{
					$order_info->setDelivery_boy_id($_POST['del_array'][$key]);
					$order_info->setDelivery_date($delivery_date);
					$order_info->setDelivery_time($delivery_time);
					$order_info->setOrder_status_id(1);

					$domain_code = $order_info->getDomain_id();
					$detail = "Your order is Confirmed";
					$message = json_encode(array("detail"=>$detail,"code" => '2', "response" => "Order Status Change"));

					/*GCM CODE*/
					$gcm_regids = $this->find_gcm_regid($order_info->getOrder_createdby());
					if(!empty($gcm_regids))
					{
						$app_id='CUST';
						if (count($gcm_regids[0])>0)
						{
							$this->send_notification($gcm_regids,"Order Status Change",$message,2,$app_id,$domain_code,"order_master",$val);
						}
					}
					/*APNS CODE*/
					$apns_regids = $this->find_apns_regid($order_info->getOrder_createdby());
					if(!empty($apns_regids))
					{
						$app_id='CUST';
						if (count($apns_regids[0])>0)
						{
							$this->send_notification($apns_regids,"Order Status Change",$message,1,$app_id,$domain_code,"order_master",$val);
						}
					}



					$detail = "You have new order";
					$message = json_encode(array("detail"=>$detail,"code" => '4', "response" => "Order Status Change"));

					$gcm_regids = $this->find_gcm_regid($_POST['del_array'][$key]);

					if(!empty($gcm_regids))
					{
						$app_id='DEL';
						if (count($gcm_regids[0])>0)
						{
							$this->send_notification($gcm_regids,"Order assigned",$message,2,$app_id,$domain_code,"order_master",$val);
						}
					}

					$apns_regids = $this->find_apns_regid($_POST['del_array'][$key]);
					if(!empty($apns_regids))
					{
						$app_id='DEL';
						if (count($apns_regids[0])>0)
						{
							$this->send_notification($apns_regids,"Order assigned",$message,1,$app_id,$domain_code,"order_master",$val);
						}
					}


					$em = $this->getDoctrine()->getManager();
					$em->persist($order_info);
					$em->flush();
				}
			}
			$this->get("session")->getFlashBag()->set("success_msg","Orders assigned successfully");
			$content = array("msg"=>"Orders assigned successfully.","code"=>'yes');
			return new Response(json_encode($content));
		}
		else
		{
			$this->get("session")->getFlashBag()->set("error_msg","Something was missing, please try again later!");
			$content = array("msg"=>"Something was missing, please try again later!","code"=>'no');
			return new Response(json_encode($content));
		}

    }
    /**
     * @Route("/removeitem")
     */
    public function removeitemAction()
    {
    	$content = array(
				"msg"=>"Operation failed! Try again later",
				"code"=>"error"
			);
    	if(isset($_POST['flag']) && $_POST['flag'] == "remove_item" && $_POST['login_pass'] != "" && $_POST['id'] != "" && $_POST['module'] != "")
    	{

    		$user_check = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Usermaster')
						   ->findOneBy(array('user_master_id'=>$this->get('session')->get('user_id'),'user_role_id'=>$this->get('session')->get('role_id'),'password'=>md5($_POST['login_pass']),'user_status'=>'active','domain_id'=>$this->get('session')->get('domain_id'),'is_deleted'=>0));

			if(!empty($user_check) && count($user_check) > 0)
			{
				if($_POST['module'] == 'order'){$pkey = "order_master_id";$tbl = "Ordermaster";}

				$relation_table = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:'.$tbl)
							   ->findOneBy(array($pkey=>$_POST['id'],'domain_id'=>$this->get('session')->get('domain_id'),'is_deleted'=>0));

				if(!empty($relation_table))
				{
					$relation_table->setIs_deleted(1);
					$em = $this->getDoctrine()->getManager();
					$em->persist($relation_table);
					$em->flush();

					$content = array(
						"msg"=>ucfirst($_POST['module'])." deleted successfully!",
						"code"=>"success"
					);
				}
			}
			else
			{
				$content = array(
						"msg"=>"Incorrect login password!",
						"code"=>"pass_error"
					);
			}
		}
    	return new Response(json_encode($content));
    }
}
