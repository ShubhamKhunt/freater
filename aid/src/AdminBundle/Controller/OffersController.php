<?php
namespace AdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;
use AdminBundle\Entity\Productmaster;
use AdminBundle\Entity\Offersmaster;
/**
* @Route("/{domain}")
*/
class OffersController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
    /**
     * @Route("/offers/{domain_id}",defaults={"domain_id":""})
     * @Template()
     */
    public function indexAction($domain_id)
    {
    	if($this->get('session')->get('role_id')== '1')
    	{
			$offers_list = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Offersmaster')
						   ->findBy(array('is_deleted'=>0));	
		}else{
			$offers_list = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Offersmaster')
						   ->findBy(array('is_deleted'=>0,"domain_id"=>$this->get('session')->get('domain_id')));	
		}
		if(!empty($offers_list)){
			foreach($offers_list as $ofkey=>$ofval){
				$media_type_id = $media_url = '' ;
				$media_library_info = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Medialibrarymaster')->findOneBy(array("is_deleted"=>0,"media_library_master_id"=>$ofval->getImage_id()));
				if(!empty($media_library_info)){
					$media_url = $media_library_info->getMedia_location()."/".$media_library_info->getMedia_name() ;
					$media_type_id = $media_library_info->getMedia_type_id();
				}
				$offer_array[] = array(
					"offers_master_id"=>$ofval->getOffers_master_id(),				   
					"title"=>$ofval->getTitle(),				   
					"description"=>$ofval->getDescription(),				   
					"image_id"=>$ofval->getImage_id(),
					"media_type_id"=>$media_type_id,
					"media_url"=>$media_url,
					"url"=>$ofval->getUrl(),				   
					"contact"=>$ofval->getContact(),				   
					"language_id"=>$ofval->getLanguage_id(),				   
					"main_offers_master_id"=>$ofval->getMain_offers_master_id(),				   
					"status"=>$ofval->getStatus(),				   
					"domain_id"=>$ofval->getDomain_id(),				   
					"created_by"=>$ofval->getCreated_by(),				   
					"create_date"=>$ofval->getCreate_date(),				   
					"is_deleted"=>$ofval->getIs_deleted()			   
									   
									   );
				
			}
		}
		return array("offers_list"=>$offer_array);
	}
	/**
     * @Route("/addoffers/{main_offers_master_id}",defaults={"main_offers_master_id"=""})
     * @Template()
     */
    public function addoffersAction($main_offers_master_id)
    {
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
		if(isset($main_offers_master_id) && $main_offers_master_id != "")
		{
			$offers_list = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Offersmaster')
					   ->findBy(array('main_offers_master_id'=>$main_offers_master_id,'is_deleted'=>0));
			if(!empty($offers_list)){
				foreach($offers_list as $ofkey=>$ofval){
					$media_type_id = $media_url = '' ;
					$media_library_info = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Medialibrarymaster')->findOneBy(array("is_deleted"=>0,"media_library_master_id"=>$ofval->getImage_id()));
					if(!empty($media_library_info)){
						$media_url = $media_library_info->getMedia_location()."/".$media_library_info->getMedia_name() ;
						$media_type_id = $media_library_info->getMedia_type_id();
					}
					$offer_array[] = array(
						"offers_master_id"=>$ofval->getOffers_master_id(),				   
						"title"=>$ofval->getTitle(),				   
						"description"=>$ofval->getDescription(),				   
						"image_id"=>$ofval->getImage_id(),
						"media_type_id"=>$media_type_id,
						"media_url"=>$media_url,
						"url"=>$ofval->getUrl(),				   
						"contact"=>$ofval->getContact(),				   
						"language_id"=>$ofval->getLanguage_id(),				   
						"main_offers_master_id"=>$ofval->getMain_offers_master_id(),				   
						"status"=>$ofval->getStatus(),				   
						"domain_id"=>$ofval->getDomain_id(),				   
						"created_by"=>$ofval->getCreated_by(),				   
						"create_date"=>$ofval->getCreate_date(),				   
						"is_deleted"=>$ofval->getIs_deleted()			   
										   
										   );
					
				}
			}
			return array("language"=>$language,"offer_info"=>$offer_array);	
		}
		else
		{
			return array("language"=>$language);
		}
	}
	/**
     * @Route("/saveoffer/{language_id}",defaults={"language_id":""})
     * @Template()
     */
    public function saveofferAction($language_id)
    {
		if(isset($_POST['save_offer']) && $_POST['save_offer'] == "save_offer" && $language_id != "")
		{
			if($_POST['title'] != "" && !empty($_FILES['image']))
			{
				$media_type_id = $_POST['media_type'];
				$Config_live_site = $this->container->getParameter('live_path') ;
				$file_path = $this->container->getParameter('file_path');
				$file = $_FILES['image']['name'];
				// only profile image is allowed
				$extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
				//file extension check
				if (  ( ($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg' ) && $media_type_id == 1   ) ||
					  ( ($extension == 'mp4' || $extension == 'mkv' || $extension == 'avi' ) && $media_type_id ==  2  )  
					)
				{
					$tmpname = $_FILES['image']['tmp_name'];
					$path = $file_path.'/uploads/offers';
					$upload_dir = $this->container->getParameter('upload_dir').'/uploads/offers/';
					$media_id = $this->mediauploadAction($file,$tmpname,$path,$upload_dir,$media_type_id);
					//media upload check
					if($media_id != "FALSE")
					{
						$offers_master = new Offersmaster();
						$offers_master->setTitle($_POST['title']);
						$offers_master->setDescription($_POST['description']);
						$offers_master->setImage_id($media_id);
						$offers_master->setUrl($_POST['url']);
						$offers_master->setContact($_POST['contact']);
						$offers_master->setLanguage_id($language_id);
						$offers_master->setMain_offers_master_id(0);
						$offers_master->setStatus($_POST['status']);
						$offers_master->setDomain_id($this->get('session')->get('domain_id'));
						$offers_master->setCreated_by($this->get('session')->get('user_id'));
						$offers_master->setCreate_date(date("Y-m-d H:i:s"));
						$offers_master->setIs_deleted(0);
						$em = $this->getDoctrine()->getManager();
						$em->persist($offers_master);
						$em->flush();
						$offers_master_id = $offers_master->getOffers_master_id();
						$offers_master->setMain_offers_master_id($offers_master_id);
						$em->flush();
		 /*********************** Push Notification : START ********************************************************/
		 				if($this->get('session')->get('role_id')== '1')
				    	{
							$user_master = $this->getDoctrine()
											   ->getManager()
											   ->getRepository('AdminBundle:Usermaster')
											   ->findBy(array("user_role_id"=>7,"user_status"=>"active","is_deleted"=>0));
						}else{
							$user_master = $this->getDoctrine()
											   ->getManager()
											   ->getRepository('AdminBundle:Usermaster')
											   ->findBy(array("user_role_id"=>7,"user_status"=>"active","is_deleted"=>0,"domain_id"=>$this->get('session')->get('domain_id')));
						}
						$user_array = array();
						foreach($user_master as $key=>$val)
						{
							$user_array[] = $val->getUser_master_id();
						}	   
						$message = json_encode(array("detail"=>"New Offer ".$_POST['title']." available in store.","code" => '3', "response" => "New offer available"));
						$gcm_regids = $this->find_gcm_regid($user_array);
						if(!empty($gcm_regids))
						{
							if (count($gcm_regids[0])>0)
							{
								$this->send_notification($gcm_regids,"New offer available",$message,2,'CUST',$this->get('session')->get('domain_id'),"offers_master",$offers_master_id);
							}
						}
						/*$apns_regids = $this->find_apns_regid($user_array);
						if(!empty($apns_regids))
						{
							if (count($apns_regids[0])>0)
							{
								$this->send_notification($apns_regids,"New offer available",$message,1,'CUST',$this->get('session')->get('domain_id'),"offers_master",$offers_master_id);
							}
						}*/
		/*********************** Push Notification : END ********************************************************/					
						$this->get('session')->getFlashBag()->set('success_msg', "Offer ".$_POST['title']." saved successfully");	
						return $this->redirect($this->generateUrl('admin_offers_index',array("domain"=>$this->get('session')->get('domain'))));
					}
					//media upload check else part
					else
					{
						$this->get('session')->getFlashBag()->set('error_msg', 'Offer image uploading failed');
					}
				}
				//file extension check else part
				else
				{
					$this->get('session')->getFlashBag()->set('error_msg', 'Offer Image extension invalid, please use jpg,jpeg & png image');
				}
			}
			else
			{
				$this->get('session')->getFlashBag()->set('error_msg', 'Please fill all required fields');
			}	
		}
		else
		{
			$this->get('session')->getFlashBag()->set('error_msg', 'Oops! Something goes wrong! Try again later');
		}
		return $this->redirect($this->generateUrl('admin_offers_addoffer',array("domain"=>$this->get('session')->get('domain'))));
	}
	/**
     * @Route("/updateoffer/{language_id}/{main_offers_master_id}")
     * @Template()
     */
    public function updateofferAction($language_id,$main_offers_master_id)
    {
		//update button click check
		if(isset($_POST['update_offer']) && $_POST['update_offer'] == 'update_offer' && $language_id != "" && $main_offers_master_id != "")
		{
			//validation check
			if($_POST['title'] != "")
			{
				if(!empty($_FILES['image']) && $_FILES['image']['name'] != "")
				{
					$media_type_id = $_POST['media_type'];
					$Config_live_site = $this->container->getParameter('live_path') ;
					$file_path = $this->container->getParameter('file_path');
					$file = $_FILES['image']['name'];
					// only profile image is allowed
					$extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
					//file extension check
					//if($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg')
					if (  ( ($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg' ) && $media_type_id == 1   ) ||
					  ( ($extension == 'mp4' || $extension == 'mkv' || $extension == 'avi' ) && $media_type_id ==  2  )  
					)				
					{
						$tmpname = $_FILES['image']['tmp_name'];
						$path = $file_path.'/uploads/offers';
						$upload_dir = $this->container->getParameter('upload_dir').'/uploads/offers/';
						$media_id = $this->mediauploadAction($file,$tmpname,$path,$upload_dir,$media_type_id);
						//media upload check
						if($media_id != "FALSE")
						{
						}
						else
						{
							$this->get('session')->getFlashBag()->set('error_msg', 'Offer image uploading failed');
						}
					}
					else
					{
						$this->get('session')->getFlashBag()->set('error_msg', 'Product Logo extension invalid, please use jpg,jpeg & png image');
					}
				}
				else
				{
					$media_id = $_POST['image_hidden'];
				}
				$offer_info = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Offersmaster')
					   ->findOneBy(array('main_offers_master_id'=>$main_offers_master_id,"language_id"=>$language_id,'is_deleted'=>0));
				if(!empty($offer_info))
				{
					$em = $this->getDoctrine()->getManager();
					$connection = $em->getConnection();
					$statement = $connection->prepare("UPDATE offers_master SET title='".$_POST['title']."',description='".$_POST['description']."',contact='".$_POST['contact']."' WHERE main_offers_master_id = '".$main_offers_master_id."' AND language_id = '".$language_id."' AND is_deleted = 0");
					$statement->execute();
					$connection = $em->getConnection();
					$statement = $connection->prepare("UPDATE offers_master SET image_id = '".$media_id."',url = '".$_POST['url']."',status = '".$_POST['status']."' WHERE main_offers_master_id = '".$main_offers_master_id."' AND is_deleted = 0");
					$statement->execute();
					$this->get('session')->getFlashBag()->set('success_msg',"Offer updated successfully!");	
				}
				else
				{
					$offers_master = new Offersmaster();
					$offers_master->setTitle($_POST['title']);
					$offers_master->setDescription($_POST['description']);
					$offers_master->setImage_id($media_id);
					$offers_master->setUrl($_POST['url']);
					$offers_master->setContact($_POST['contact']);
					$offers_master->setLanguage_id($language_id);
					$offers_master->setMain_offers_master_id($main_offers_master_id);
					$offers_master->setStatus($_POST['status']);
					$offers_master->setDomain_id($this->get('session')->get('domain_id'));
					$offers_master->setCreated_by($this->get('session')->get('user_id'));
					$offers_master->setCreate_date(date("Y-m-d H:i:s"));
					$offers_master->setIs_deleted(0);
					$em = $this->getDoctrine()->getManager();
					$em->persist($offers_master);
					$em->flush();
					$this->get('session')->getFlashBag()->set('success_msg',"Offer saved successfully!");	
				}
			}
			//validation check else part
			else
			{
				$this->get('session')->getFlashBag()->set('error_msg',"Please! Fill all required fields");
			}
		}
		//update button click check else part
		else
		{
			$this->get('session')->getFlashBag()->set('error_msg', 'Oops! Something goes wrong! Try again later');
		}
		return $this->redirect($this->generateUrl('admin_offers_addoffers',array("domain"=>$this->get('session')->get('domain'),"main_offers_master_id"=>$main_offers_master_id)));
	}
	/**
     * @Route("/deleteoffer/{main_offers_master_id}",defaults={"main_offers_master_id"=""})
     * @Template()
     */
    public function deleteofferAction($main_offers_master_id)
    {
		if($main_offers_master_id != "" && $main_offers_master_id != 0)
		{
			$em = $this->getDoctrine()->getManager();
			$connection = $em->getConnection();
			$statement = $connection->prepare("UPDATE offers_master SET is_deleted = 1 WHERE main_offers_master_id = '".$main_offers_master_id."'");
			$statement->execute();
			$this->get('session')->getFlashBag()->set('success_msg',"Offer deleted successfully!");	
		}
		else
		{
			$this->get('session')->getFlashBag()->set('error_msg',"Offer not deleted");	
		}
		return $this->redirect($this->generateUrl('admin_offers_index',array("domain"=>$this->get('session')->get('domain'))));
	}
	/**
     * @Route("/offerstatus")
     * @Template()
    */
    public function offerstatusAction()
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
			$Offers_list = $em->getRepository('AdminBundle:Offersmaster')
								->findBy(
									array(
										'main_offers_master_id'=>$id,
										'is_deleted'=>0
									)
								) ;
			if(!empty($Offers_list))
			{
				foreach($Offers_list as $key=>$val){
					$Offers = $em->getRepository('AdminBundle:Offersmaster')
								->findOneBy(
									array(
										'offers_master_id'=>$val->getOffers_master_id(),
										'is_deleted'=>0
									)
								) ;
					$Offers->setStatus($status);
					$em->persist($Offers) ;
					$em->flush() ;
				}
			}
		}
		return new Response();
	}
}