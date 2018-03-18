<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Couponmaster;
use AdminBundle\Entity\Appliedcouponrelation;
use AdminBundle\Entity\Couponappliedlist;
use AdminBundle\Entity\Custassigncopon;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;


/**
* @Route("/{domain}")
*/

class CouponController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
	
    /**
     * @Route("/coupon")
     * @Template()
     */
    public function indexAction()
    {
		
    	if($this->get('session')->get('role_id')== '1')
    	{
	    	$coupon_master = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:Couponmaster')
							   ->findBy(array('is_deleted'=>0));
		}
		else
		{
			$coupon_master = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:Couponmaster')
							   ->findBy(array('is_deleted'=>0,"domain_id"=>$this->get('session')->get('domain_id')));
		}
        return array("coupon_master"=>$coupon_master);
        
    }
    
	/**
     * @Route("/addcoupon/{coupon_master_id}",defaults = {"coupon_master_id" = ""})
     * @Template()
     */
    public function addcouponAction($coupon_master_id)
    {
		if(isset($coupon_master_id) && $coupon_master_id != "")
		{		   
			$coupon_master = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Couponmaster')
						   ->findOneBy(array('coupon_master_id'=>$coupon_master_id,'is_deleted'=>0));
			
			return array("coupon_master"=>$coupon_master,"coupon_master_id"=>$coupon_master_id);
		}			   
        return array();
    }
    
    /**
     * @Route("/applycoupon/{coupon_master_id}",defaults = {"coupon_master_id" = ""})
     * @Template()
     */
    public function applycouponAction($coupon_master_id)
    {
    	$coupon_master = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Couponmaster')
						   ->findBy(array('is_deleted'=>0));
		
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));	
					   
		$applied_product_info = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Appliedcouponrelation')
					   ->findOneBy(array('is_deleted'=>0,'coupon_id'=>$coupon_master_id,'domain_id'=>$this->get('session')->get('domain_id')));
		
		if(!empty($applied_product_info))
		{
			$coupon_applied_list = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Couponappliedlist')
					   ->findBy(array('is_deleted'=>0,'coupon_id'=>$coupon_master_id,'applied_coupon_relation_id'=>$applied_product_info->getApplied_coupon_relation_id()));
			if(empty($coupon_applied_list))
			{
				$coupon_applied_list = array();
			}
		}
		else
		{
			$applied_product_info = array();
			$coupon_applied_list = array();
		}
					   
		foreach($language as $lgkey=>$lgval)
	    {
			$data = $all_category = '';
			$all_category =$all_category_details= '';
			$all_category = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Categorymaster')
						   ->findBy(array('language_id'=>1,'is_deleted'=>0,'parent_category_id'=>0,'domain_id'=>$this->get('session')->get('domain_id')));
			if(count($all_category) > 0 )
			{
				foreach(array_slice($all_category,0) as $lkey=>$lval)
				{
					$parent_category_name = 'No Parent ' ;				
					$data[]  = $this->get_hirerachy(1,'',$lval->getMain_category_id());
					
				}		
			}
		}
		
		$product_array='';
		$domain_id = $this->get('session')->get('domain_id');
		$product_list = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Productmaster')->findBy(array("is_deleted"=>0,"domain_id"=>$domain_id,"language_id"=>1));
		if(!empty($product_list))
		{
			foreach($product_list as $pkey =>$pval)
			{
				$image_details = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Medialibrarymaster')->findOneBy(array('media_library_master_id'=>$pval->getProduct_logo()));
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
						"product_master_id"=>$pval->getProduct_master_id(),
						"main_product_id"=>$pval->getMain_product_master_id(),
						"language_id"=>$pval->getLanguage_id(),
						"product_name"=>$pval->getProduct_title(),
						"language_id"=>$pval->getLanguage_id(),
						"image"=>$image,
						"original_price"=>$pval->getOriginal_price(),
						"status"=>$pval->getStatus()					
						);
			}	
		}
		
        return array("coupon_master"=>$coupon_master,"category_details"=>$data,"product_list"=>$product_array,"coupon_master_id"=>$coupon_master_id,"applied_product_info"=>$applied_product_info,"coupon_applied_list"=>$coupon_applied_list);
    }
	
	/**
     * @Route("/savecoupon/{coupon_master_id}",defaults={"coupon_master_id"=""})
     * @Template()
     */
    public function savecouponAction($coupon_master_id)
    {
    	/*$response = array(
				"coupon_code"=>"dsgdsfg",
				"start_date"=>"dfgfd",
				"end_date"=>"dfgfd",
				"discount_value"=>"10",
				"discount_type"=>"percentage",
				"min_order_val"=>"100"
			);
			
		$message = json_encode(array("detail"=>"New Coupon Code : 'dsgdsfg'","code" => '6', "response" => $response));
		//$apns_regids = $this->find_apns_regid($user_array);
		$apns_regids = array("3904569f4af2336cc3d9d57c57576ea85063fc3a18983c2addb747cda7b99bdc");
		if(!empty($apns_regids))
		{
			if (count($apns_regids[0])>0)
			{
				$this->send_notification($apns_regids,"New Coupon",$message,1,"CUST","fortune001","coupon_master",10);
			}
		}
    	exit;*/
    	
		$domain_id = $this->get("session")->get("domain_id");
		if(isset($_POST['save_coupon']) && $_POST['save_coupon'] == 'save_coupon')
		{
			if($_POST['coupon_name'] != "" && $_POST['coupon_code'] != "" && $_POST['discount_type'] != "" && $_POST['discount_value'] != "" && $_POST['starting_date'] != "" && $_POST['ending_date'] != "" && $_POST['status'] != "" && $_POST['no_of_user_use'] != "" && $_POST['no_of_times_use'] != "" && $_POST['coupon_usage_interval'] != "")
			{
				$coupon_name = $_POST['coupon_name'];
				$coupon_code = $_POST['coupon_code'];
				$discount_type = $_POST['discount_type'];
				$discount_value = $_POST['discount_value'];
				$start_date = date("Y-m-d H:i:s", strtotime($_POST['starting_date']));
				$end_date = date("Y-m-d H:i:s", strtotime($_POST['ending_date']));
				$status = $_POST['status'];
				$no_of_user_use = $_POST['no_of_user_use'];
				$no_of_times_use = $_POST['no_of_times_use'];
				$coupon_usage_interval = $_POST['coupon_usage_interval'];
				$min_amount = $_POST['min_amount'];


				if(isset($_POST['visible_all']) && !empty($_POST['visible_all']))
		    	{
		    		$visible_all = $_POST['visible_all'];
		    	}	
		    	else
		    	{
		    		$visible_all = 'no';
		    	}
				if(isset($coupon_master_id) && $coupon_master_id != "")
				{
					$coupon_master = $this->getDoctrine()
							->getManager()
							->getRepository("AdminBundle:Couponmaster")
							->findOneBy(array("coupon_master_id"=>$coupon_master_id,"is_deleted"=>0));
					if(!empty($coupon_master))
					{
						$em = $this->getDoctrine()->getManager();
						$update = $em->getRepository('AdminBundle:Couponmaster')->find($coupon_master->getCoupon_master_id());					
						$update->setCoupon_name($coupon_name);
						$update->setCoupon_code($coupon_code);
						
						$update->setStart_date($start_date);
						$update->setEnd_date($end_date);
						
						$update->setDiscount_value($discount_value);
						$update->setDiscount_type($discount_type);
						$update->setNo_of_user_use($no_of_user_use);
						$update->setNo_of_times_use($no_of_times_use);
						$update->setCoupon_usage_interval($coupon_usage_interval);
						$update->setMin_order_amount($min_amount);
						$update->setStatus($status);
						$update->setVisible_all($visible_all);
						$em->flush();
						$this->get('session')->getFlashBag()->set('success_msg','Coupon Updated successfully.');
						return $this->redirect($this->generateUrl('admin_coupon_addcoupon',array('domain'=>$this->get('session')->get('domain'),'coupon_master_id'=>$coupon_master_id)));
					// till here done
					}
					else
					{
						
						$couponmaster = new Couponmaster();
						$couponmaster->setCoupon_name($coupon_name);
						$couponmaster->setCoupon_code($coupon_code);
						$couponmaster->setStart_date($start_date);
						$couponmaster->setEnd_date($end_date);
						$couponmaster->setDiscount_value($discount_value);
						$couponmaster->setDiscount_type($discount_type);
						$couponmaster->setNo_of_user_use($no_of_user_use);
						$couponmaster->setNo_of_times_use($no_of_times_use);
						$couponmaster->setCoupon_usage_interval($coupon_usage_interval);
						$couponmaster->setMin_order_amount($min_amount);
						$couponmaster->setStatus($status);
						$couponmaster->setVisible_all($visible_all);
						$couponmaster->setCreated_datetime(date('Y-m-d H:i:s'));
						$couponmaster->setDomain_id($domain_id);
						$couponmaster->setIs_deleted(0);
						$em = $this->getDoctrine()->getManager();
						$em->persist($couponmaster);
						$em->flush();
						
						/*
						//----- Push Notificaiton ---
						$user_array = array();
						if(!empty($this->get('session')->get('domain_id')))
	    				{
	    					if($this->get('session')->get('role_id')== '1')
	    					{
								$user_master = $this->getDoctrine()
									   ->getManager()
									   ->getRepository('AdminBundle:Usermaster')
									   ->findBy(array("user_role_id"=>7,"user_status"=>"active","is_deleted"=>0,"user_type"=>"user"));
									   
								
								foreach($user_master as $key=>$val)
								{
									$user_array[] = $val->getUser_master_id();
								}	
							}
							else
							{
									$user_master = $this->getDoctrine()
														->getManager()
														->getRepository('AdminBundle:Usermaster')
														->findBy(array("user_role_id"=>7,"user_status"=>"active","is_deleted"=>0,"domain_id"=>$this->get('session')->get('domain_id'),"user_type"=>"user"));
									
									foreach($user_master as $key=>$val)
									{
										$user_array[] = $val->getUser_master_id();
									}
							}
						}
						
						$app_id="CUST";
						if(!empty($user_array))
						{
							
							$coupon_master = $this->getDoctrine()
								   ->getManager()
								   ->getRepository('AdminBundle:Couponmaster')
								   ->findOneBy(array('coupon_master_id'=>$couponmaster->getCoupon_master_id(),'is_deleted'=>0));
								   $response = array(
										"coupon_code"=>$couponmaster->getCoupon_code(),
										"start_date"=>$couponmaster->getStart_date(),
										"end_date"=>$couponmaster->getEnd_date(),
										"discount_value"=>$couponmaster->getDiscount_value(),
										"discount_type"=>$couponmaster->getDiscount_type(),
										"min_order_val"=>$couponmaster->getMin_order_amount()
									);
								
									$message = json_encode(array("detail"=>"New Coupon Code : '".$couponmaster->getCoupon_code()."'","code" => '6', "response" => $response));
							
							//save information in cust_assign_coupon
							$data['custcounpon']['domain_id'] = $this->get('session')->get('domain_id');
							$data['custcounpon']['app_id']= $app_id;
							$data['custcounpon']['table_name']= 'coupon_master';
							$data['custcounpon']['table_id']= $couponmaster->getCoupon_master_id();
							$this->_savecustomerassigncoupons($data);
							
							$gcm_regids = $this->find_gcm_regid($user_array);
							if(!empty($gcm_regids) && $visible_all!='no')
							{
								
								if (count($gcm_regids[0])>0)
								{
									$this->send_notification($gcm_regids,"New Coupon",$message,2,$app_id,$this->get('session')->get('domain_id'),"coupon_master",$couponmaster->getCoupon_master_id());
								}
							}
							
							$apns_regids = $this->find_apns_regid($user_array);
							if(!empty($apns_regids) && $visible_all!='no')
							{
								if (count($apns_regids[0])>0)
								{
									$this->send_notification($apns_regids,"New Coupon",$message,1,$app_id,$this->get('session')->get('domain_id'),"coupon_master",$couponmaster->getCoupon_master_id());
								}
							}
						}
						//----- Push Notificaiton ---
						*/
						
						$this->get('session')->getFlashBag()->set('success_msg','Coupon inserted successfully.');
						return $this->redirect($this->generateUrl('admin_coupon_addcoupon',array('domain'=>$this->get('session')->get('domain'),'coupon_master_id'=>$coupon_master_id)));
					}
				}
				else
				{

					$couponmaster = new Couponmaster();
					$couponmaster->setCoupon_name($coupon_name);
					$couponmaster->setCoupon_code($coupon_code);
					$couponmaster->setStart_date($start_date);
					$couponmaster->setEnd_date($end_date);
					$couponmaster->setDiscount_value($discount_value);
					$couponmaster->setDiscount_type($discount_type);
					$couponmaster->setStatus($status);
					$couponmaster->setVisible_all($visible_all);
					$couponmaster->setNo_of_user_use($no_of_user_use);
					$couponmaster->setNo_of_times_use($no_of_times_use);
					$couponmaster->setCoupon_usage_interval($coupon_usage_interval);
					$couponmaster->setMin_order_amount($min_amount);
					$couponmaster->setCreated_datetime(date('Y-m-d H:i:s'));
					$couponmaster->setDomain_id($domain_id);
					$couponmaster->setIs_deleted(0);
					$em = $this->getDoctrine()->getManager();
					$em->persist($couponmaster);
					$em->flush();
					
					$this->get('session')->getFlashBag()->set('success_msg','Coupon inserted successfully.');
					
					/*
					//----- Push Notificaiton ---
					$user_array = array();
					if(!empty($this->get('session')->get('domain_id')))
    				{
    					if($this->get('session')->get('role_id')== '1')
    					{
							$user_master = $this->getDoctrine()
								   ->getManager()
								   ->getRepository('AdminBundle:Usermaster')
								   ->findBy(array("user_role_id"=>7,"user_status"=>"active","is_deleted"=>0,"user_type"=>"user"));
								   
							
							foreach($user_master as $key=>$val)
							{
								$user_array[] = $val->getUser_master_id();
							}	
						}else{
							
								$user_master = $this->getDoctrine()
									   ->getManager()
									   ->getRepository('AdminBundle:Usermaster')
									   ->findBy(array("user_role_id"=>7,"user_status"=>"active","is_deleted"=>0,"domain_id"=>$this->get('session')->get('domain_id'),"user_type"=>"user"));
							
								foreach($user_master as $key=>$val)
								{
									$user_array[] = $val->getUser_master_id();
								}
						}
					}
					$app_id="CUST";
					
					if(!empty($user_array))
					{
						
						$coupon_master = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:Couponmaster')
							   ->findOneBy(array('coupon_master_id'=>$couponmaster->getCoupon_master_id(),'is_deleted'=>0));
							   $response = array(
									"coupon_code"=>$couponmaster->getCoupon_code(),
									"start_date"=>$couponmaster->getStart_date(),
									"end_date"=>$couponmaster->getEnd_date(),
									"discount_value"=>$couponmaster->getDiscount_value(),
									"discount_type"=>$couponmaster->getDiscount_type(),
									"min_order_val"=>$couponmaster->getMin_order_amount()
								);
								
								$message = json_encode(array("detail"=>"New Coupon Code : '".$couponmaster->getCoupon_code()."'","code" => '6', "response" => $response));
						
						//save information in cust_assign_coupon
						$data['custcounpon']['domain_id'] = $this->get('session')->get('domain_id');
						$data['custcounpon']['app_id']= $app_id;
						$data['custcounpon']['table_name']= 'coupon_master';
						$data['custcounpon']['table_id']= $couponmaster->getCoupon_master_id();
						$this->_savecustomerassigncoupons($data);
						
						$gcm_regids = $this->find_gcm_regid($user_array);
						
						if(!empty($gcm_regids) && $visible_all!='no')
						{
							if (count($gcm_regids[0])>0)
							{
							
								
								$this->send_notification($gcm_regids,"New Coupon",$message,2,$app_id,$this->get('session')->get('domain_id'),"coupon_master",$couponmaster->getCoupon_master_id());
							}
						}
						
						$apns_regids = $this->find_apns_regid($user_array);
						if(!empty($apns_regids) && $visible_all!='no')
						{
							if (count($apns_regids[0])>0)
							{
								$this->send_notification($apns_regids,"New Coupon",$message,1,$app_id,$this->get('session')->get('domain_id'),"coupon_master",$couponmaster->getCoupon_master_id());
							}
						}
					}
					//----- Push Notificaiton ---
					*/
					
					return $this->redirect($this->generateUrl('admin_coupon_index',array("domain"=>$this->get('session')->get('domain'))));
				}
			}
			else
			{
				$this->get('session')->getFlashBag()->set('error_msg','Please fill all required fields.');
				return $this->redirect($this->generateUrl('admin_coupon_addcoupon',array("domain"=>$this->get('session')->get('domain'))));
			}
		}
		return array();
	}
	
	/**
	* @Route("/getstateajax")
	*@Template()
	**/
	public function getstateajaxAction(){
		$html = "<option>Select State</option>" ;
		$method = $this->get('request')->getMethod() ;
		if($method = "POST"){
			$country_id = $_POST['country_id'] ;
			$lang_id = $_POST['lang_id'] ;
			
			$state_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Statemaster')
					   ->findBy(array('main_country_id'=>$country_id,"language_id"=>$lang_id,'is_deleted'=>0));
			if(!empty($state_master)){
				foreach($state_master as $key=>$val){
					$html .= "<option value='".$val->getMain_state_id()."'>".$val->getState_name()."</option>";
				}
			}
			
		}
		
		$response = new Response() ;
		$response->setContent($html) ;
		return $response ;
	}
	
	
	/**
     * @Route("/couponstatus")
     * @Template()
    */
    
    public function couponstatusAction()
    {
    	if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && isset($_REQUEST['status']) && !empty($_REQUEST['status']))
		{
    		
    		$request = $this->getRequest() ;
	    	$session = $request->getSession() ;
	    	$em = $this->getDoctrine()->getManager();
    		
			
			$status = $_POST['status'] ;
			$id = $_POST['id'] ;
			
			
			if($_POST['status'] == "true")
			{
				$status = "active";
			}
			else
			{
				$status = "inactive";	
			}
			
			$coupon_list = $em->getRepository('AdminBundle:Couponmaster')
								->findBy(
									array(
										'coupon_master_id'=>$id,
										'is_deleted'=>0
									)
								) ;
								
			if(!empty($coupon_list))
			{
				foreach($coupon_list as $key=>$val){
					$coupon = $em->getRepository('AdminBundle:Couponmaster')
								->findOneBy(
									array(
										'coupon_master_id'=>$val->getCoupon_master_id(),
										'is_deleted'=>0
									)
								) ;
					$coupon->setStatus($status);
					$em->persist($coupon) ;
					$em->flush() ;
				}
			}
		}
		return new Response();
	}
	/**
     * @Route("/couponvisible")
     * @Template()
    */
	public function couponvisibleAction()
    {
    	if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && isset($_REQUEST['visible']) && !empty($_REQUEST['visible']))
		{
    		$request = $this->getRequest() ;
	    	$session = $request->getSession() ;
	    	$em = $this->getDoctrine()->getManager();
			
			$id = $_POST['id'] ;
			
			if($_POST['visible'] == "true")
			{
				$visible = "yes";
			}
			else
			{
				$visible = "no";	
			}
			
			$coupon_list = $em->getRepository('AdminBundle:Couponmaster')
								->findBy(
									array(
										'coupon_master_id'=>$id,
										'is_deleted'=>0
							)
						  ) ;
								
								
			if(!empty($coupon_list))
			{
				foreach($coupon_list as $key=>$val){
					$coupon = $em->getRepository('AdminBundle:Couponmaster')
								->findOneBy(
									array(
										'coupon_master_id'=>$val->getCoupon_master_id(),
										'is_deleted'=>0
									)
								) ;
								
					$coupon->setVisible_all($visible);
					$em->persist($coupon) ;
					$em->flush() ;
				}
			}
		}
		return new Response();
	}
	
	/**
     * @Route("/saveapplycoupon/{coupon_master_id}")
     * @Template()
    */
    
    public function saveapplycouponAction($coupon_master_id)
    {
		
    	if(isset($_POST['save_coupon_apply']) && $_POST['save_coupon_apply'] == "save_coupon_apply")
    	{
			if(isset($_POST['assign_on']) && $_POST['assign_on'] != "" && isset($_POST['relation_id']) && $_POST['relation_id'] != "" && isset($_POST['status']))
			{
				$applycoupon_list = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Appliedcouponrelation')
						   ->findOneBy(array('is_deleted'=>0,'coupon_id'=>$coupon_master_id,'domain_id'=>$this->get('session')->get('domain_id')));
				if(!empty($applycoupon_list))
				{
					$applycoupon_list->setIs_deleted(1);
					$em = $this->getDoctrine()->getManager();
					$em->persist($applycoupon_list);
					$em->flush();
					
					$em = $this->getDoctrine()->getManager();
					$connection = $em->getConnection();
					$statement = $connection->prepare("UPDATE coupon_applied_list SET is_deleted = 1 WHERE applied_coupon_relation_id = '".$applycoupon_list->getApplied_coupon_relation_id()."'");
					$statement->execute();
				}
				$applied_coupon_relation = new Appliedcouponrelation();
				
				$applied_coupon_relation->setCoupon_id($coupon_master_id);
				$applied_coupon_relation->setAssign_on($_POST['assign_on']);
				$applied_coupon_relation->setStatus($_POST['status']);
				$applied_coupon_relation->setDomain_id($this->get('session')->get('domain_id'));
				$applied_coupon_relation->setIs_deleted(0);
				$em = $this->getDoctrine()->getManager();
				$em->persist($applied_coupon_relation);
				$em->flush();
				
				$applied_coupon_relation_id = $applied_coupon_relation->getApplied_coupon_relation_id();
				
				foreach($_POST['relation_id'] as $key=>$val)
				{
					$coupon_applied_list = new Couponappliedlist();
					$coupon_applied_list->setApplied_coupon_relation_id($applied_coupon_relation_id);
					$coupon_applied_list->setCoupon_id($coupon_master_id);
					$coupon_applied_list->setRelation_id($val);
					$coupon_applied_list->setCreate_date(date("Y-m-d H:i:s"));
					$coupon_applied_list->setIs_deleted(0);
					$em = $this->getDoctrine()->getManager();
					$em->persist($coupon_applied_list);
					$em->flush();
				}
				$this->get('session')->getFlashBag()->set('success_msg','Coupon applied successfully');
				
			}
			else
			{
				$this->get('session')->getFlashBag()->set('error_msg','Please fill all required fields');
			}
		}
		else
		{
			$this->get('session')->getFlashBag()->set('error_msg','Oops! Somethign goes wrong! try again later!');
		}
		return $this->redirect($this->generateUrl('admin_coupon_applycoupon',array('domain'=>$this->get('session')->get('domain'),'coupon_master_id'=>$coupon_master_id)));
    }

	/**
     * @Route("/deletecoupon/{coupon_master_id}",defaults={"coupon_master_id":""})
     * @Template()
     */
    public function deletecouponAction($coupon_master_id)
    {
		if($coupon_master_id != '0')
		{
			$coupon_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Couponmaster')
					   ->findOneBy(array('is_deleted'=>0,'coupon_master_id'=>$coupon_master_id));
			if(!empty($coupon_master)){
				$coupon_master->setIs_deleted(1);
				$em = $this->getDoctrine()->getManager();
				$em->persist($coupon_master);
				$em->flush();
			}
			$applycoupon_list = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Appliedcouponrelation')
						   ->findOneBy(array('is_deleted'=>0,'coupon_id'=>$coupon_master_id,'domain_id'=>$this->get('session')->get('domain_id')));
				if(!empty($applycoupon_list))
				{
					$applycoupon_list->setIs_deleted(1);
					$em = $this->getDoctrine()->getManager();
					$em->persist($applycoupon_list);
					$em->flush();
					
					$em = $this->getDoctrine()->getManager();
					$connection = $em->getConnection();
					$statement = $connection->prepare("UPDATE coupon_applied_list SET is_deleted = 1 WHERE applied_coupon_relation_id = '".$applycoupon_list->getApplied_coupon_relation_id()."'");
					$statement->execute();
				}
		}
		$this->get("session")->getFlashBag()->set("success_msg","Coupon Deleted successfully");
	    return $this->redirect($this->generateUrl("admin_coupon_index",array("domain"=>$this->get('session')->get('domain'))));
	}
	
	private function _savecustomerassigncoupons($assing_copon_arr)
	{
		$em = $this->getDoctrine()->getManager();
		//prepare for database
		$connection = $em->getConnection();
		//prepare query
		$customer_user = $connection->prepare("SELECT `user_master_id` as user_master_id FROM user_master WHERE  user_role_id=7 And user_type='user' And user_status='active' AND is_deleted=0 AND domain_id Like '".$assing_copon_arr['custcounpon']['domain_id']."'");
		//query exexute
		$customer_user->execute();
		//fetch record into table
		$customer_user_id_list = $customer_user->fetchAll();
		
		if(isset($customer_user_id_list) && !empty($customer_user_id_list))
		{
			foreach($customer_user_id_list as $customer_user_id_list_key => $customer_user_id_list_value)
		{
			$customer_list[] = $customer_user_id_list_value['user_master_id'];
		}
		$customer_list_slipt = implode(",",$customer_list);
		}
		else
		{	
			$customer_list_slipt = "";
		}
		if(isset($customer_list_slipt) && !empty($customer_list_slipt) && !empty($assing_copon_arr))
		{
			$customer_user_ids = $customer_list_slipt;
			$custassigncopon = new Custassigncopon();
			$custassigncopon->setCustomer_user_id($customer_user_ids);
			$custassigncopon->setDomain_id($assing_copon_arr['custcounpon']['domain_id']);
			$custassigncopon->setApp_id($assing_copon_arr['custcounpon']['app_id']);
			$custassigncopon->setTable_name($assing_copon_arr['custcounpon']['table_name']);
			$custassigncopon->setTable_id($assing_copon_arr['custcounpon']['table_id']);
			$custassigncopon->setIs_deleted(0);
			$em->persist($custassigncopon);
			$em->flush();
			return true;
		}
	}
}