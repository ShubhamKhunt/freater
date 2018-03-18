<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Usermaster;
use AdminBundle\Entity\Domainmaster;
use AdminBundle\Entity\Companymaster;
use AdminBundle\Entity\Branchmaster;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
* @Route("/admin")
*/
class UserController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
	
    /**
     * @Route("/users")
     * @Template()
     */
    public function indexAction()
    {
		$user_master = '';
		$user_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Usermaster')
					   ->findBy(array('is_deleted'=>0,'user_role_id'=>7));
		
		return array("user"=>$user_master);
	}
	
	/**
     * @Route("/adduser/{user_master_id}",defaults={"user_master_id":""})
     * @Template()
     */
    public function adduserAction($user_master_id)
    {
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
		
		$domain_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Domainmaster')
					   ->findBy(array('is_deleted'=>0));
		
		$role_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Rolemaster')
					   ->findBy(array('is_deleted'=>0));
		
		$country_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Countrymaster')
					   ->findBy(array('status'=>'active','is_deleted'=>0,"language_id"=>1));
					   
		$city_master = $this->getDoctrine()
				->getManager()
				->getRepository('AdminBundle:Citymaster')
				->findBy(array('is_deleted'=>0,"language_id"=>1));
		
		$state_master = $this->getDoctrine()
				->getManager()
				->getRepository('AdminBundle:Statemaster')
				->findBy(array('is_deleted'=>0,"language_id"=>1));
				
		if(!empty($user_master_id))
		{
			$query = "SELECT user_master.*,
					address_master.city_id ,
					city_master.city_name
					FROM user_master 
					LEFT JOIN address_master on user_master.address_master_id = address_master.address_master_id  
					LEFT JOIN city_master ON city_master.city_master_id = address_master.city_id 
					WHERE user_master.is_deleted= 0   AND user_master_id = '".$user_master_id."' " ;
			$em = $this->getDoctrine()->getManager();
			$connection = $em->getConnection();
			$statement = $connection->prepare($query);
			$statement->execute();
			$user_master = $statement->fetchAll();
			
			if(!empty($user_master)){
				$user_master_array = "" ;
				foreach($user_master as $ukey=>$uval){
					
					$user_mobile = ($uval['user_mobile']);
					$user_firstname = ($uval['user_firstname']);
					$user_lastname =($uval['user_lastname']);
					$username = ($uval['username']);
					$user_master_array = array(
							"user_master_id"=>$uval['user_master_id'],
							"user_role_id"=>$uval['user_role_id'],
							"username"=>$username,
							"password"=>$uval['password'],
							"user_firstname"=>$user_firstname,
							"user_lastname"=>$user_lastname,
							"user_mobile"=>$user_mobile,
							"user_emailid"=>$uval['user_emailid'],
							"user_image"=>$uval['user_image'],
							"address_master_id"=>$uval['address_master_id'],
							"created_by"=>$uval['created_by'],
							"user_status"=>$uval['user_status'],
							"domain_id"=>$uval['domain_id'],
							"last_login"=>$uval['last_login'],
							"login_from"=>$uval['login_from'],
						 );
				}
			}
			
			return array(
				"language" => $language,
				"domain" => $domain_master,
				"role" => $role_master,
				"user" => $user_master_array,
				"country_master" => $country_master,
				"city_list" => $city_master,
				"state_master" => $state_master
			);
		}
		
		return array(
			"language" => $language,
			"domain" => $domain_master,
			"role" => $role_master,
			"country_master" => $country_master,
			"city_list" => $city_master,
			"state_master" => $state_master
		);
	}
	
	/**
     * @Route("/saveuser")
     * @Template()
     */
    public function saveuserAction()
    {
		$user_cover_image_id = $media_id = 0 ;
		$first_name = "";
		if(!empty($_REQUEST['first_name']))
		{
			$first_name = $_REQUEST['first_name'];
		}
		$last_name = "";
		if(!empty( $_REQUEST['last_name']))
		{
			$last_name = $_REQUEST['last_name'];
		}
		
		$role_id = 7;
		$username = "";
		if(!empty($_REQUEST['username']))
		{
			$username = $_REQUEST['username'];
		}
		
		$password = "";
		if(!empty($_REQUEST['password']))
		{
			$password = $_REQUEST['password'];
		}
		
		$email_id = "";
		if(!empty($_REQUEST['email_id']))
		{
			$email_id = $_REQUEST['email_id'];
		}
		
		$user_logo = "";
		if(!empty($_FILES['user_logo']))
		{
			$user_logo = $_FILES['user_logo'];
		}
		
		$lang_id = "";
		if(!empty($_REQUEST['lang_id']))
		{
			$lang_id = $_REQUEST['lang_id'];
		}
		$domain_id = "";
		if(!empty($_REQUEST['domain_id']))
		{
			$domain_id = $_REQUEST['domain_id'];
		}
		$company_id = "";
		if(!empty($_REQUEST['company_id']))
		{
			$company_id = $_REQUEST['company_id'];
		}
		
		$status = "";
		if(!empty($_REQUEST['status']))
		{
			$status = $_REQUEST['status'];
		}
		
		
		if($user_logo['name'] != "" && !empty($user_logo['name']))
		{
			$extension = pathinfo($user_logo['name'],PATHINFO_EXTENSION);		
			$media_type_id = $this->mediatype($extension);				
			if($media_type_id == 1){
				$logo = $user_logo['name'];				
				$tmpname =$user_logo['tmp_name'];					
				$file_path = $this->container->getParameter('file_path');			
				$logo_path = $file_path.'/user';
				$logo_upload_dir = $this->container->getParameter('upload_dir').'/user/';	    	
				$media_id = $this->mediauploadAction($logo,$tmpname,$logo_path,$logo_upload_dir,$media_type_id);	
			}else{
				$this->get("session")->getFlashBag()->set("error_msg","Upload Valid User Logo");
				return $this->redirect($this->generateUrl("admin_subject_addsubject"));
			}
		}
			
		
		$user_master = new Usermaster();
		$user_master->setUser_role_id($role_id);
		$user_master->setUsername($username);
		$user_master->setPassword(md5($password));
		$user_master->setUser_firstname($first_name);
		$user_master->setUser_lastname($last_name);
		$user_master->setUser_emailid($email_id);
		$user_master->setUser_image($media_id);
		$user_master->setCreated_by($this->get('session')->get('user_id'));
		$user_master->setUser_status($status);
		$user_master->setDomain_id($domain_id);
		$user_master->setCreated_datetime(date('Y-m-d H:i:s'));
		$user_master->setLast_modified(date('Y-m-d H:i:s'));
		$user_master->setLogin_from('other');
		$user_master->setIs_deleted(0);
		
		$em = $this->getDoctrine()->getManager();
		$em->persist($user_master);
		$em->flush();
		
		$this->get("session")->getFlashBag()->set("success_msg","User Successfully Add .");
		return $this->redirect($this->generateUrl("admin_user_index"));
	}
	
	/**
     * @Route("/deleteuser/{user_master_id}",defaults={"user_master_id":""})
     * @Template()
     */
    public function deleteuserAction($user_master_id)
    {
		$user_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Usermaster')
					   ->findOneBy(array('is_deleted'=>0,"user_master_id"=>$user_master_id));
			
		$user_master->setIs_deleted(1);
		$em = $this->getDoctrine()->getManager();
		$em->flush();
		
		$this->get("session")->getFlashBag()->set("success_msg","User Successfully Delete .");
		return $this->redirect($this->generateUrl("admin_user_index"));
	}
	
	/**
     * @Route("/updateuser/{user_master_id}",defaults={"user_master_id":""})
     * @Template()
     */
    public function updateuserAction($user_master_id)
    {
		$user_cover_image_id = $media_id = 0 ;
		$first_name = "";
		
		if(!empty($_REQUEST['first_name']))
		{
			$first_name = $_REQUEST['first_name'];
		}
	
		$last_name = "";
		if(!empty( $_REQUEST['last_name']))
		{
			$last_name = $_REQUEST['last_name'];
		}
	
		$role_id = 7;
		$username = "";
		if(!empty($_REQUEST['username']))
		{
			$username = $_REQUEST['username'];
		}
		$password = "";
		if(!empty($_REQUEST['password']))
		{
			$password = $_REQUEST['password'];
		}
		$email_id = "";
		if(!empty($_REQUEST['email_id']))
		{
			$email_id = $_REQUEST['email_id'];
		}
		$user_logo = "";
		if(!empty($_FILES['user_logo']))
		{
			$user_logo = $_FILES['user_logo'];
		}
		$lang_id = "";
		if(!empty($_REQUEST['lang_id']))
		{
			$lang_id = $_REQUEST['lang_id'];
		}
		$domain_id = "";
		if(!empty($_REQUEST['domain_id']))
		{
			$domain_id = $_REQUEST['domain_id'];
		}
		$company_id = "";
		if(!empty($_REQUEST['company_id']))
		{
			$company_id = $_REQUEST['company_id'];
		}
		$status = "";
		if(!empty($_REQUEST['status']))
		{
			$status = $_REQUEST['status'];
		}
		if(!empty($user_master_id))
		{
			$user_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Usermaster')
					   ->findOneBy(array('is_deleted'=>0,"user_master_id"=>$user_master_id));
					   
			if($user_logo['name'] != "" && !empty($user_logo['name']))
			{
				$extension = pathinfo($user_logo['name'],PATHINFO_EXTENSION);
		
				$media_type_id = $this->mediatype($extension);
				
				if($media_type_id == 1){
					$logo = $user_logo['name'];
				
					$tmpname =$user_logo['tmp_name'];
					
					$file_path = $this->container->getParameter('file_path');
			
					$logo_path = $file_path.'/user';

					$logo_upload_dir = $this->container->getParameter('upload_dir').'/user/';
	    	
					$media_id = $this->mediaremoveAction($logo,$tmpname,$logo_path,$logo_upload_dir,$_REQUEST['image_hidden'],$media_type_id);	
				}else{
					$this->get("session")->getFlashBag()->set("error_msg","Upload Valid User Logo");
					return $this->redirect($this->generateUrl("admin_user_index",array("domain"=>$this->get('session')->get('domain'))));
				}
			}
					   
			$user_master->setUser_role_id($role_id);
			$user_master->setUsername($username);
			if(!empty($password) && $password !="")
			{
				$user_master->setPassword(md5($password));
			}
			$user_master->setUser_firstname($first_name);
			$user_master->setUser_lastname($last_name);
			$user_master->setUser_emailid($email_id);
			$user_master->setCreated_by($this->get('session')->get('user_id'));
			$user_master->setUser_status($status);
			$user_master->setDomain_id($domain_id);
			$user_master->setLast_modified(date('Y-m-d H:i:s'));
			
			$em = $this->getDoctrine()->getManager();
			$em->flush();
		
			$this->get("session")->getFlashBag()->set("success_msg","User Successfully Update .");
			return $this->redirect($this->generateUrl("admin_user_index"));
		}
	}
	
	/**
     * @Route("/user/displaycompany")
     * @Template()
     */
    public function displaycompanyAction()
	{
		$html = "";
		if($_POST['flag'] != "" && $_POST['flag'] == "domain_change")
		{
			
			$domain_id = $_POST['domain_id'];
			
			if(empty($_POST['lang_id']))
			{
				$lang_id = 1 ;
			}
			else{
				$lang_id = $_POST['lang_id'];
			}
			
			$company = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Companymaster')
					   ->findBy(array('is_deleted'=>0,"domain_id"=>$domain_id,"langauge_id"=>$lang_id));

			if(!empty($company))
			{
				$html .= "<option value=''>Select Company </option>";
				
				foreach($company as $val)
				{
					$html .= "<option value='" . $val->getMain_company_id() . "'>" . $val->getCompany_name() . "</option>";
				}	
			}
		}
		
		return new Response($html);
	}
	
	/**
     * @Route("/user/displaybranch")
     * @Template()
     */
    public function displaybranchAction()
	{
		$html = "";
		if($_POST['flag'] != "" && $_POST['flag'] == "company_change")
		{	
			$company_id = $_POST['company_id'];	
			if(empty($_POST['lang_id']))
			{
				$lang_id = 1 ;
			}
			else{
				$lang_id = $_POST['lang_id'];
			}
			
			$barnch_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Branchmaster')
					   ->findBy(array('is_deleted'=>0,"company_id"=>$company_id,"language_id"=>$lang_id));

			if(!empty($barnch_master))
			{
				$html .= "<option value=''>Select Branch</option>";
				foreach($barnch_master as $val)
				{
					$html .= "<option value='" . $val->getMain_branch_id() . "'>" . $val->getBranch_name() . "</option>";
				}	
			}
		}
		
		return new Response($html);
	}

	 /**
     * @Route("/ajaxuserlisting/{domain_id}",defaults={"domain_id":""})
     * 
     */
    public function ajaxuserlistingAction($domain_id)
    {
		$plant_condition="";
		$session = new Session();
		$domain_id = $this->get('session')->get('domain_id');
		if($session->get('plant_id') != "" && !empty($session->get('plant_id')))
		{
			$plant_id=$session->get('plant_id');
			$plant_condition="and plant_id=$plant_id";
		}
		$fieldArr = array('0'=>'user_master.user_mobile',
						  '1'=>'user_master.user_firstname',
						  '2'=>'user_master.user_lastname',
						  '3'=>'city_master.city_name',
						  '4'=>'user_master.user_status');
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
			$query = "	SELECT user_master.* , city_master.city_name 
							FROM
						user_master LEFT JOIN address_master ON user_master.address_master_id = address_master.address_master_id
									LEFT  JOIN city_master ON city_master.main_city_id = address_master.city_id  
						WHERE user_master.is_deleted= 0 AND user_master.user_role_id = '7' AND user_master.domain_id = '" . $this->get('session')->get('domain_id') ."'
						$sWhere Limit $start,$limit";
			
		}
		
		
		$statement = $connection->prepare($query);
		$statement->execute();
		$user_master = $statement->fetchAll();
		//echo $query;
		//total count of user
		$statement1 = $connection->prepare("select count(user_master.user_master_id) as totalnouser FROM user_master LEFT JOIN address_master ON user_master.address_master_id = address_master.address_master_id LEFT  JOIN city_master ON city_master.main_city_id = address_master.city_id WHERE user_master.is_deleted= 0 AND   user_master.user_role_id = '7' AND user_master.domain_id = '" . $this->get('session')->get('domain_id') ."'". $sWhere);
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
		if(isset($user_master) && !empty($user_master)){
			foreach($user_master as $user_master_key => $user_masterV_alue)
			{
				$user_mobile = $this->keyDecryptionAction($user_masterV_alue['user_mobile']);
				$user_firstname = $this->keyDecryptionAction($user_masterV_alue['user_firstname']);
				$user_lastname = $this->keyDecryptionAction($user_masterV_alue['user_lastname']);
				$link_edit = "<a href=".$this->generateUrl("admin_user_adduser",array("user_master_id"=>$user_masterV_alue['user_master_id'],"domain"=>$this->get('session')->get('domain')))." class='btn btn-warning btn-xs' data-toggle='tooltip' data-original-title='Edit' onclick=\"return confirm('Want to Edit ?')\"><span class='fa fa-edit'></span></a>";
				$link_delete = "<a href=".$this->generateUrl("admin_user_deleteuser",array("user_master_id"=>$user_masterV_alue['user_master_id'],"domain"=>$this->get('session')->get('domain')))." class='btn btn-danger btn-xs' data-toggle='tooltip' data-original-title='delete' onclick='return confirm('Are you sure , you want to delete ?')'><span class='fa fa-remove'></span></a>";
				$operation_link = $link_edit . " &nbsp;&nbsp;" .$link_delete ;
				
				$new_user_maste_arr[] = array(
					$user_mobile,
					$user_firstname,
					$user_lastname,
					$user_masterV_alue['city_name'],
					$user_masterV_alue['user_status'],
					$operation_link
				);
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
		echo json_encode($json_data);exit;
				
	}

}