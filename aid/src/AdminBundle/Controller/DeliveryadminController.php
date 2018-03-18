<?phpnamespace AdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;

use AdminBundle\Entity\Usermaster;
use AdminBundle\Entity\Addressmaster;
use AdminBundle\Entity\Countrymaster;
use AdminBundle\Entity\Statemaster;
use AdminBundle\Entity\Citymaster;
use AdminBundle\Entity\Areamaster;
use AdminBundle\Entity\Usersetting;
/**
* @Route("/{domain}")
*/

class DeliveryadminController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
    /**
     * @Route("/deliveryadminlist")
     * @Template()
     */
    public function indexAction()
    {
    	if(!empty($this->get('session')->get('domain_id')) && $this->get('session')->get('role_id')== '1')
    	{
			$deliverymanager_list = $this->getDoctrine()
				->getManager()
				->getRepository('AdminBundle:Usermaster')
				->findBy(array('user_role_id'=>4,'is_deleted'=>0));
		}else{
			$deliverymanager_list = $this->getDoctrine()
				->getManager()
				->getRepository('AdminBundle:Usermaster')
				->findBy(array('user_role_id'=>4,'is_deleted'=>0,"domain_id"=>$this->get('session')->get('domain_id')));
		}
			
		return array("deliverymanager_list"=>$deliverymanager_list);
	}
	/**
     * @Route("/createdeliveryadmin/{user_master_id}",defaults = {"user_master_id" = ""})
     * @Template()
     */
    public function createdeliveryadminAction($user_master_id)
    {
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
					   
		$country_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Countrymaster')
					   ->findBy(array('status'=>'active','is_deleted'=>0));
					   
		if(!empty($this->get('session')->get('domain_id')) && $this->get('session')->get('role_id')!= '1')
		{
			$domain_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Domainmaster')
					   ->findBy(array('is_deleted'=>0,"domain_code"=>$this->get('session')->get('domain_id')));	
		}
		else{
			$domain_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Domainmaster')
					   ->findBy(array('is_deleted'=>0));	
		}
		
		if(!empty($user_master_id)){
			$user_master = $this->getDoctrine()
			   ->getManager()
			   ->getRepository('AdminBundle:Usermaster')
			   ->findOneBy(array('user_master_id'=>$user_master_id,'is_deleted'=>0));
			
			if(!empty($this->get('session')->get('domain_id')) && $this->get('session')->get('role_id')!= '1')
			{
				$domain_master = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Domainmaster')
						   ->findBy(array('is_deleted'=>0,"domain_code"=>$this->get('session')->get('domain_id')));	
			}
			else{
				$domain_master = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Domainmaster')
						   ->findBy(array('is_deleted'=>0));	
			}
					
			$domain_id = $user_master->getDomain_id();
			
			$company_master = $this->getDoctrine()
			   ->getManager()
			   ->getRepository('AdminBundle:Companymaster')
			   ->findBy(array('domain_id'=>$domain_id,'is_deleted'=>0,"langauge_id"=>'1'));
			$main_company_id = $user_master->getCompany_id();
			
			$branch_master = $this->getDoctrine()
			   ->getManager()
			   ->getRepository('AdminBundle:Branchmaster')
			   ->findBy(array('company_id'=>$main_company_id,'is_deleted'=>0,"language_id"=>'1')); 
			$main_branch_id = $user_master->getBranch_id();   
			
			$address_master = $this->getDoctrine()
				->getManager()
				->getRepository('AdminBundle:Addressmaster')
				->findBy(array('owner_id'=>$user_master_id,'is_deleted'=>0));
			$main_area_id = $address_master[0]->getArea_id();
			$main_city_id = $address_master[0]->getCity_id();
			$city_master = $this->getDoctrine()
				->getManager()
				->getRepository('AdminBundle:Citymaster')
				->findBy(array('main_city_id'=>$address_master[0]->getCity_id(),'is_deleted'=>0));
			$main_state_id = $city_master[0]->getMain_state_id();
			
			$state_master = $this->getDoctrine()
				->getManager()
				->getRepository('AdminBundle:Statemaster')
				->findBy(array('main_state_id'=>$main_state_id,'is_deleted'=>0));
			$main_country_id = $state_master[0]->getMain_country_id();
			
			$city_list = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Citymaster')
					   ->findBy(array('status'=>'active','is_deleted'=>0));
			$area_list = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Areamaster')
					   ->findBy(array('status'=>'active','is_deleted'=>0));
			
				return array("language"=>$language,"city_list"=>$city_list,"area_list"=>$area_list,"country_master"=>$country_master,"state_master"=>$state_master,"user_master_id"=>$user_master_id,"user_master"=>$user_master,"address_master"=>$address_master,"main_area_id"=>$main_area_id,"main_city_id"=>$main_city_id,"main_state_id"=>$main_state_id,"main_country_id"=>$main_country_id,"domain_id"=>$domain_id,"domain_list"=>$domain_master,"main_company_id"=>$main_company_id,"company_master"=>$company_master,"main_branch_id"=>$main_branch_id,"branch_master"=>$branch_master);
			
		}
		
		return array("language"=>$language,"country_master"=>$country_master,"user_master_id"=>$user_master_id,"domain_list"=>$domain_master);
	}
	/**
     * @Route("/savedadmin/{user_master_id}",defaults = {"user_master_id" = ""})
     */
    public function savedadminAction($user_master_id)
    {
		//button click
			if(isset($_POST['save_dadmin']) && $_POST['save_dadmin'] == "save_dadmin")
			{
				if(!empty($user_master_id)){
					if($_POST['first_name'] != "" && $_POST['last_name'] != "" && $_POST['email_address'] != "" && $_POST['username'] != "")
					{
						//image upload
						$media_id = 0;
						if(!empty($_FILES['image']))
						{
							$Config_live_site = $this->container->getParameter('live_path') ;
							$file_path = $this->container->getParameter('file_path');
				
							$file = $_FILES['image']['name'];
							// only profile image is allowed
							$extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
							//file extension check
							if($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg')
							{
								$tmpname = $_FILES['image']['tmp_name'];
								$path = $file_path.'/uploads/users';
								$upload_dir = $this->container->getParameter('upload_dir').'/uploads/users/';
								$media_id = $this->mediauploadAction($file,$tmpname,$path,$upload_dir,1);
							}
						}
						
							
						$usermaster = $this->getDoctrine()
							->getManager()
							->getRepository('AdminBundle:Usermaster')
							->findOneBy(array('user_master_id'=>$user_master_id,'is_deleted'=>0));
							
						if(!empty($usermaster)){
							
							$usermaster->setUser_firstname($_POST['first_name']);
							$usermaster->setUser_lastname($_POST['last_name']);
							$usermaster->setUsername($_POST['username']);
							if(!empty($_POST['pass'])){
								if($_POST['pass'] == $_POST['confirm_pass']){	
									$usermaster->setPassword(md5($_POST['confirm_pass']));
									$usermaster->setShow_password($_POST['confirm_pass']);
								}
							}
							$usermaster->setUser_emailid($_POST['email_address']);
							if($media_id != 0){
								$usermaster->setLogo_id($media_id);
								$usermaster->setUser_cover_image($media_id);
							}
							$usermaster->setDomain_id($_POST['main_domain_id']);
							$usermaster->setBranch_id($_POST['main_branch_id']);
							$usermaster->setCompany_id($_POST['main_company_id']);
							$usermaster->setCountry_id($_POST['main_country_id']);
							$usermaster->setState_id($_POST['main_state_id']);
							$usermaster->setUser_status($_POST['status']);
							$em = $this->getDoctrine()->getManager();
							$em->persist($usermaster);
							$em->flush();			
							
						$address_master = $this->getDoctrine()
							->getManager()
							->getRepository('AdminBundle:Addressmaster')
							->findBy(array('owner_id'=>$user_master_id,'is_deleted'=>0));
						if(!empty($address_master)){
							foreach($address_master as $key=>$val){
								$aaa = $this->getDoctrine()
									->getManager()
									->getRepository('AdminBundle:Addressmaster')
									->findOneBy(array('address_master_id'=>$val->getAddress_master_id(),'is_deleted'=>0));
								if(!empty($aaa)){
									$aaa->setIs_deleted(1);
									$em = $this->getDoctrine()->getManager();
									$em->persist($aaa);
									$em->flush();	
								}
							}
						}
							
						$language = $this->getDoctrine()
							->getManager()
							->getRepository('AdminBundle:Languagemaster')
							->findBy(array('is_deleted'=>0));
						
						$a = 0;
						foreach($language as $key=>$val){
							$addressmaster = new Addressmaster();
							$addressmaster->setAddress_name($_POST['address_'.$val->getLanguage_master_id().'']);
							$addressmaster->setOwner_id($user_master_id);
							$addressmaster->setBase_address_type("primary");
							$addressmaster->setAddress_type("Home");
							$addressmaster->setCity_id($_POST['city_'.$val->getLanguage_master_id().'']);
							$addressmaster->setArea_id($_POST['area_'.$val->getLanguage_master_id().'']);
							$addressmaster->setStreet($_POST['street_'.$val->getLanguage_master_id().'']);
							$addressmaster->setFlate_house_number($_POST['house_no_'.$val->getLanguage_master_id().'']);
							$addressmaster->setSociety_building_name($_POST['society_building_name_'.$val->getLanguage_master_id().'']);
							$addressmaster->setLandmark($_POST['landmark_'.$val->getLanguage_master_id().'']);
							$addressmaster->setPincode($_POST['pincode_'.$val->getLanguage_master_id().'']);
							$addressmaster->setLanguage_id($val->getLanguage_master_id());
							$addressmaster->setMain_address_id($a);
							$addressmaster->setIs_defaulte_ship_address($_POST['pincode_'.$val->getLanguage_master_id().'']);
							$addressmaster->setGmap_link("");
							$addressmaster->setLat($_POST['lat_'.$val->getLanguage_master_id().'']);
							$addressmaster->setLng($_POST['lng_'.$val->getLanguage_master_id().'']);
							$addressmaster->setIs_deleted(0);
							$em = $this->getDoctrine()->getManager();
							$em->persist($addressmaster);
							$em->flush();
							if($a == 0){
								$a = $addressmaster->getAddress_master_id();
								$addressmaster->setMain_address_id($a);
								$em->flush();
							}else{
								$addressmaster->setMain_address_id($a);
								$em->flush();
							}
							$usermaster = $this->getDoctrine()
								->getManager()
								->getRepository('AdminBundle:Usermaster')
								->findOneBy(array('user_master_id'=>$user_master_id,'is_deleted'=>0));
							if(!empty($usermaster)){
								$usermaster->setAddress_master_id($a);
								$em->flush();
							}
						}
						
						
						$this->get('session')->getFlashBag()->set('success_msg', $_POST['first_name']." ".$_POST['last_name']." saved successfully");	
						}
					}
				}
				else
				{
				//Validation check
				if($_POST['first_name'] != "" && $_POST['last_name'] != "" && !empty($_FILES['image']) && $_POST['email_address'] != "" && $_POST['username'] != "" && $_POST['confirm_pass'] != "" && $_POST['status'] != "")
				{
					//password check
					if($_POST['pass'] == $_POST['confirm_pass'])
					{
						$Config_live_site = $this->container->getParameter('live_path') ;
						$file_path = $this->container->getParameter('file_path');
			
						$file = $_FILES['image']['name'];
						// only profile image is allowed
						$extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
						//file extension check
						if($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg')
						{
							$tmpname = $_FILES['image']['tmp_name'];
							$path = $file_path.'/uploads/users';
							$upload_dir = $this->container->getParameter('upload_dir').'/uploads/users/';
							
							$media_id = $this->mediauploadAction($file,$tmpname,$path,$upload_dir,1);
							//media upload check
							if($media_id != "FALSE")
							{
								$usermaster = new Usermaster();
								$usermaster->setUser_role_id(4);
								$usermaster->setUser_firstname($_POST['first_name']);
								$usermaster->setUser_lastname($_POST['last_name']);
								$usermaster->setUsername($_POST['username']);
								$usermaster->setPassword(md5($_POST['confirm_pass']));
								$usermaster->setShow_password($_POST['confirm_pass']);
								$usermaster->setUser_emailid($_POST['email_address']);
								$usermaster->setUser_image($media_id);
								$usermaster->setUser_cover_image($media_id);
								$usermaster->setUser_tag_line("");
								$usermaster->setUser_status($_POST['status']);
								$usermaster->setUser_type('user');
								$usermaster->setDomain_id($_POST['main_domain_id']);
								$usermaster->setBranch_id($_POST['main_branch_id']);
								$usermaster->setCompany_id($_POST['main_company_id']);
								$usermaster->setCountry_id($_POST['main_country_id']);
								$usermaster->setState_id($_POST['main_state_id']);
								$usermaster->setCreated_by($this->get('session')->get('user_id'));
								$usermaster->setCreated_datetime(date("Y-m-d H:i:s"));
								$usermaster->setLast_modified(date("Y-m-d H:i:s"));
								$usermaster->setLast_login(date("Y-m-d H:i:s"));
								$usermaster->setCurrent_lang_id(1);
								$usermaster->setIs_deleted(0);
								$em = $this->getDoctrine()->getManager();
								$em->persist($usermaster);
								$em->flush();
								$user_master_id = $usermaster->getUser_master_id();
								
								$language = $this->getDoctrine()
									->getManager()
									->getRepository('AdminBundle:Languagemaster')
									->findBy(array('is_deleted'=>0));
								
								$a = 0;
								foreach($language as $key=>$val){
									$addressmaster = new Addressmaster();
									$addressmaster->setAddress_name($_POST['address_'.$val->getLanguage_master_id().'']);
									$addressmaster->setOwner_id($user_master_id);
									$addressmaster->setBase_address_type("primary");
									$addressmaster->setAddress_type("Home");
									$addressmaster->setCity_id($_POST['city_'.$val->getLanguage_master_id().'']);
									$addressmaster->setArea_id($_POST['area_'.$val->getLanguage_master_id().'']);
									$addressmaster->setStreet($_POST['street_'.$val->getLanguage_master_id().'']);
									$addressmaster->setFlate_house_number($_POST['house_no_'.$val->getLanguage_master_id().'']);
									$addressmaster->setSociety_building_name($_POST['society_building_name_'.$val->getLanguage_master_id().'']);
									$addressmaster->setLandmark($_POST['landmark_'.$val->getLanguage_master_id().'']);
									$addressmaster->setPincode($_POST['pincode_'.$val->getLanguage_master_id().'']);
									$addressmaster->setLanguage_id($val->getLanguage_master_id());
									$addressmaster->setMain_address_id($a);
									$addressmaster->setIs_defaulte_ship_address($_POST['pincode_'.$val->getLanguage_master_id().'']);
									$addressmaster->setGmap_link("");
									$addressmaster->setLat($_POST['lat_'.$val->getLanguage_master_id().'']);
									$addressmaster->setLng($_POST['lng_'.$val->getLanguage_master_id().'']);
									$addressmaster->setIs_deleted(0);
									$em = $this->getDoctrine()->getManager();
									$em->persist($addressmaster);
									$em->flush();
									if($a == 0){
										$a = $addressmaster->getAddress_master_id();
										$addressmaster->setMain_address_id($a);
										$em->flush();
									}else{
										$addressmaster->setMain_address_id($a);
										$em->flush();
									}
									$usermaster->setAddress_master_id($a);
									$em->flush();
								}
								
								$setting=array(
										"language"=>'1',
										"notification"=>'on',
									);
									
								$setting_value=json_encode($setting);
								
								// create new address and edit
								$usersetting = new Usersetting() ;
								$usersetting->setUser_id($user_master_id) ;					
								$usersetting->SetSetting_value($setting_value) ;
								$usersetting->SetIs_deleted('0') ;
								$em->persist($usersetting) ;
								$em->flush() ;
								
								$this->get('session')->getFlashBag()->set('success_msg', $_POST['first_name']." ".$_POST['last_name']." saved successfully");	
							}
							//media upload check else part
							else
							{
							}
						}
						//file extension check else part
						else
						{
						}
					}
					//password check else part
					else
					{
					}
				}
				//Validation check else part
				else
				{
				}
			}
			return $this->redirect($this->generateUrl('admin_deliveryadmin_index',array("domain"=>$this->get('session')->get('domain'))));
		}
	}
	
	
	/**
     * @Route("/deladminuser/{user_master_id}",defaults = {"user_master_id" = ""})
     */
    public function deluserAction($user_master_id)
    {
		if(!empty($user_master_id)){
			$usermaster = $this->getDoctrine()
					->getManager()
					->getRepository('AdminBundle:Usermaster')
					->findOneBy(array('user_master_id'=>$user_master_id,'is_deleted'=>0));
					
				if(!empty($usermaster)){
					$usermaster->setIs_deleted(1);
					$em = $this->getDoctrine()->getManager();
					$em->persist($usermaster);
					$em->flush();
				}
			$this->get('session')->getFlashBag()->set('success_msg', "User Deleted successfully");	
			return $this->redirect($this->generateUrl('admin_deliveryadmin_index',array("domain"=>$this->get('session')->get('domain'))));
		}
	}
	
	/**
	* @Route("/getcompanyajax")
	* /
	**/
	public function getcompanyajaxAction(){
		$html = "<option value=''>Select Company</option>" ;
		$method = $this->get('request')->getMethod() ;
		if($method = "POST"){
			$domain_id = $_POST['domain_id'] ;
			
			$company_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Companymaster')
					   ->findBy(array('domain_id'=>$domain_id,"langauge_id"=>'1','is_deleted'=>0));
		
			if(!empty($company_master)){
				foreach($company_master as $key=>$val){
					$html .= "<option value='".$val->getMain_company_id()."'>".$val->getCompany_name()."</option>";
				}
			}
			
		}
		
		$response = new Response() ;
		$response->setContent($html) ;
		return $response ;
	}


	/**
	* @Route("/getbranchajax")
	* /
	**/
	public function getbranchajaxAction(){
		$html = "<option value=''>Select Branch</option>" ;
		$method = $this->get('request')->getMethod() ;
		if($method = "POST"){
			$company_id = $_POST['company_id'] ;
			
			$branch_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Branchmaster')
					   ->findBy(array('company_id'=>$company_id,"language_id"=>'1','is_deleted'=>0));
		
			if(!empty($branch_master)){
				foreach($branch_master as $key=>$val){
					$html .= "<option value='".$val->getMain_branch_id()."'>".$val->getBranch_name()."</option>";
				}
			}
			
		}
		
		$response = new Response() ;
		$response->setContent($html) ;
		return $response ;
	}
	
}