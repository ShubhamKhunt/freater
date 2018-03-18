<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Suppliermaster;
use AdminBundle\Entity\Domainmaster;
use AdminBundle\Entity\Addressmaster;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;



/**
* @Route("/{domain}")
*/

 
class SuppliersController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
	
    /**
     * @Route("/suppliers/{domain_id}",defaults={"domain_id":""})
     * @Template()
     */
    public function indexAction($domain_id)
    {
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
		$all_suppliers = '';
		/*if($domain_id == "" || $domain_id == 0 ){*/
		/*if(!empty($this->get('session')->get('domain_id')) && $this->get('session')->get('role_id')== '1')
    	{
			$all_suppliers = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Suppliermaster')
					   ->findBy(array('is_deleted'=>0));
		}else{			
			$all_suppliers = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Suppliermaster')
					   ->findBy(array('is_deleted'=>0,'domain_id'=>$this->get('session')->get('domain_id')));
		}*/
		if($this->get('session')->get('role_id')== '1')
    	{
    		$query = "SELECT * FROM `supplier_master` WHERE is_deleted=0 Group by main_supplier_id";
    	}else{
			$query = "SELECT * FROM `supplier_master` WHERE is_deleted=0 and domain_id='".$this->get('session')->get('domain_id')."' Group by main_supplier_id";
		}
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare($query);
		$statement->execute();
		$all_suppliers = $statement->fetchAll();
		if(count($all_suppliers) > 0 ){
			foreach(array_slice($all_suppliers,0) as $lkey=>$lval){
				
				$lang_arr_supplier_wise = array();
				foreach($language as $key=>$val){
					$lang_supplier_name = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Suppliermaster')->findOneBy(array('language_id'=>$val->getLanguage_master_id(),'main_supplier_id'=>$lval['main_supplier_id']));
					$supplier_name='';
					if(!empty($lang_supplier_name)){
						$supplier_name = $lang_supplier_name->getSupplier_name();
					}	
					$lang_arr_supplier_wise[] = array(
						"language_id"=>$val->getLanguage_master_id(),
						"supplier_name"=>$supplier_name
						);
				}
				
				$all_suppliers_details[] = array(
					"supplier_master_id"=>$lval['supplier_master_id'],
					"supplier_name"=>$lval['supplier_name'],
					"supplier_description"=>$lval['supplier_description'],
					"phone_no"=>$lval['phone_no'],
					"mobile_no"=>$lval['mobile_no'],
					"supplier_logo"=>$lval['supplier_logo'],
					"language_id"=>$lval['language_id'],
					"status"=>$lval['status'],
					"main_supplier_id"=>$lval['main_supplier_id'],
					"domain_id"=>$lval['domain_id'],
					"is_deleted"=>$lval['is_deleted'],
					"lang_arr_supplier_wise"=>$lang_arr_supplier_wise
				);
				
			}
			
			return array("language"=>$language,"all_suppliers"=>$all_suppliers_details);
		}
		 return array("language"=>$language);
    }
	
	/**
     * @Route("/addsupplier/{supplier_id}",defaults={"supplier_id":""})
     * @Template()
     */
    public function addsupplierAction($supplier_id)
    {
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
		
		/*$domain_list = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Domainmaster')
					   ->findBy(array('is_deleted'=>0));*/
		if(!empty($this->get('session')->get('domain_id')) && $this->get('session')->get('role_id')!= '1')
		{
			$domain_list = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Domainmaster')
					   ->findBy(array('is_deleted'=>0,"domain_code"=>$this->get('session')->get('domain_id')));	
		}
		else{
			$domain_list = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Domainmaster')
					   ->findBy(array('is_deleted'=>0));	
		}
		if(!empty($supplier_id)){
			
			$query = "SELECT supplier_master.*,address_master.address_master_id,address_master.address_name,address_master.owner_id,address_master.main_address_id,address_master.lat,address_master.lng,
					  media_library_master.media_location,media_library_master.media_name
					  FROM supplier_master
					  JOIN media_library_master
						ON supplier_master.supplier_logo = media_library_master.media_library_master_id
						JOIN address_master
						ON supplier_master.address_id = address_master.main_address_id  and supplier_master.language_id = address_master.language_id  
					WHERE address_master.is_deleted=0 and supplier_master.is_deleted = 0 AND supplier_master.main_supplier_id = '" . $supplier_id . "'";
				
				$em = $this->getDoctrine()->getManager();
				$connection = $em->getConnection();
				$statement = $connection->prepare($query);
				$statement->execute();
				$selected_supplier = $statement->fetchAll();
			
			return array("language"=>$language,"selected_supplier"=>$selected_supplier,"domain_list"=>$domain_list);
		}else{
			return array("language"=>$language,"domain_list"=>$domain_list);	
		}		
		
    }
    
    /**
     * @Route("/savesupplier")
     * @Template()
     */
    public function savesupplierAction()
    {
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
					   
		if($_REQUEST['save'] == 'save_supplier' && !empty($_REQUEST['language_id'] )  && !empty($_REQUEST['supplier_name']) && !empty($_REQUEST['supplier_address']) && !empty($_REQUEST['lat']) && !empty($_REQUEST['long']) && !empty($_REQUEST['phone_number']) && !empty($_REQUEST['mobile_number']) && !empty($_REQUEST['domain_id']) && !empty($_REQUEST['status'])){
			
			//add image
			$supplier_logo = $_FILES['supplier_logo'];
			if(isset($_REQUEST['image_hidden']) && !empty($_REQUEST['image_hidden']))
			{
				$media_id = $_REQUEST['image_hidden'];
				
				if($supplier_logo['name'] != "" && !empty($supplier_logo['name']))
				{
					$extension = pathinfo($supplier_logo['name'],PATHINFO_EXTENSION);
			
					$media_type_id = $this->mediatype($extension);
					
					if(!empty($media_type_id)){
						$logo = $supplier_logo['name'];					
						$tmpname =$supplier_logo['tmp_name'];						
						$file_path = $this->container->getParameter('file_path');				
						$logo_path = $file_path.'/suppliers';	
						$logo_upload_dir = $this->container->getParameter('upload_dir').'/suppliers/';				
						
						$media_id = $this->mediaremoveAction($logo,$tmpname,$logo_path,$logo_upload_dir,$media_id,$media_type_id);	
					
					}else{
						$this->get("session")->getFlashBag()->set("error_msg","Suppliers Logo is Required");
						return $this->redirect($this->generateUrl("admin_category_addcategory",array("domain"=>$this->get('session')->get('domain'),"category_id"=>$category_id)));
					}
				}
			}
			else{
			
				if($supplier_logo['name'] != "" && !empty($supplier_logo['name']))
				{
					$extension = pathinfo($supplier_logo['name'],PATHINFO_EXTENSION);
			
					$media_type_id = $this->mediatype($extension);
			
					if(!empty($media_type_id)){
						$logo = $supplier_logo['name'];
					
						$tmpname =$supplier_logo['tmp_name'];
						
						$file_path = $this->container->getParameter('file_path');
				
						$logo_path = $file_path.'/suppliers';
	
						$logo_upload_dir = $this->container->getParameter('upload_dir').'/suppliers/';
				
						$media_id = $this->mediauploadAction($logo,$tmpname,$logo_path,$logo_upload_dir,$media_type_id);	
					}else{
						$this->get("session")->getFlashBag()->set("error_msg","Supplier Logo is Required");
						return $this->redirect($this->generateUrl("admin_category_addcategory",array("domain"=>$this->get('session')->get('domain'))));
					}
				}
			}
			
			
			$date_time = date("Y-m-d H:i:s") ; 
			$supplier = new Suppliermaster();
			$supplier->setSupplier_name($_REQUEST['supplier_name']);
			$supplier->setSupplier_description($_REQUEST['supplier_description']);
			$supplier->setPhone_no($_REQUEST['phone_number']);
			$supplier->setMobile_no($_REQUEST['mobile_number']);
			$supplier->setAddress_id(0);
			$supplier->setSupplier_logo($media_id);
			$supplier->setStatus($_REQUEST['status']);
			$supplier->setCreated_by($this->get('session')->get('user_id'));
			$supplier->setCreated_date(date('Y-m-d H:i:s'));
			$supplier->setLanguage_id($_REQUEST['language_id']);
			$supplier->setDomain_id($_REQUEST['domain_id']);
			$supplier->setIs_deleted(0);
			
			if(!empty($_REQUEST['main_supplier_id']) && $_REQUEST['main_supplier_id'] != '0'){
				$address_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Addressmaster')
					   ->findOneBy(array('is_deleted'=>0,'owner_id'=>$_REQUEST['main_supplier_id'],"language_id"=>$_REQUEST['language_id']));
				
				if(empty($address_master))
				{
					$address = new Addressmaster();
					$address->setOwner_id($_REQUEST['main_supplier_id']);
					$address->setAddress_name($_REQUEST['supplier_address']);
					$address->setLat($_REQUEST['lat']);
					$address->setLng($_REQUEST['long']);
					$address->setLanguage_id($_REQUEST['language_id']);
					$address->setMain_address_id($_REQUEST['main_supplier_address_id']);
					$em = $this->getDoctrine()->getManager();
					$em->persist($address);
					$em->flush();
				}
				$supplier->setAddress_id($_REQUEST['main_supplier_address_id']);
				$supplier->setMain_supplier_id($_REQUEST['main_supplier_id']);
				$em = $this->getDoctrine()->getManager();
				$em->persist($supplier);
				$em->flush();
				
				
				
			 }
			else{
			
				$supplier->setMain_supplier_id(0);
				$em = $this->getDoctrine()->getManager();
				$em->persist($supplier);
				$em->flush();
				$main_supplier = $supplier->getSupplier_master_id();
				
				$address = new Addressmaster();
				$address->setOwner_id($main_supplier);
				$address->setAddress_name($_REQUEST['supplier_address']);
				$address->setLat($_REQUEST['lat']);
				$address->setLng($_REQUEST['long']);
				$address->setLanguage_id($_REQUEST['language_id']);
				
				$em = $this->getDoctrine()->getManager();
				$em->persist($address);
				$em->flush();
				
				$main_address = $address->getAddress_master_id();
				$address->setMain_address_id($main_address);
				$em = $this->getDoctrine()->getManager();
				$em->persist($address);
				$em->flush();
				
				$supplier->setAddress_id($main_address);
				$supplier->setMain_supplier_id($main_supplier);
				$em = $this->getDoctrine()->getManager();
				$em->persist($supplier);
				$em->flush();
				
			}
			
			// Basic Fileds Start
			
			$basic_supplier = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Suppliermaster')
					   ->findBy(array('is_deleted'=>0,'main_supplier_id'=>$supplier->getMain_supplier_id()));
					   
			if(!empty($basic_supplier))
			{
				foreach($basic_supplier as $bkey=>$bval)
				{
					$one_supplier = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:Suppliermaster')
							   ->findOneBy(array('is_deleted'=>0,'supplier_master_id'=>$bval->getSupplier_master_id()));
					$one_supplier->setPhone_no($_REQUEST['phone_number']);
					$one_supplier->setMobile_no($_REQUEST['mobile_number']);
					$one_supplier->setDomain_id($_REQUEST['domain_id']);
					$one_supplier->setStatus($_REQUEST['status']);
					$em = $this->getDoctrine()->getManager();
					$em->persist($one_supplier);
					$em->flush();
				}
			}
			
			$basic_address = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Addressmaster')
					   ->findBy(array('is_deleted'=>0,'main_address_id'=>$supplier->getAddress_id()));
					   
			if(!empty($basic_address))
			{
				foreach($basic_address as $bakey=>$baval)
				{
					$one_address = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:Addressmaster')
							   ->findOneBy(array('is_deleted'=>0,'address_master_id'=>$baval->getAddress_master_id()));
					$one_address->setLat($_REQUEST['lat']);
					$one_address->setLng($_REQUEST['long']);
					$em = $this->getDoctrine()->getManager();
					$em->persist($one_address);
					$em->flush();
				}
			}
			
			// Basic Fileds end
		
		}
       $this->get("session")->getFlashBag()->set("success_msg","Supplier Added successfully");	
	   return $this->redirect($this->generateUrl("admin_suppliers_index",array("domain"=>$this->get('session')->get('domain'))));
    }

 
 	/**
     * @Route("/updatesupplier/{supplier_master_id}",defaults={"supplier_master_id":""})
     * @Template()
     */
    public function updatesupplierAction($supplier_master_id)
    {
		if(!empty($supplier_master_id)){
			
			//update image
				$supplier_logo = $_FILES['supplier_logo'];
			
				$media_id = $_REQUEST['image_hidden'];
				
				if($supplier_logo['name'] != "" && !empty($supplier_logo['name']))
				{
					$extension = pathinfo($supplier_logo['name'],PATHINFO_EXTENSION);
			
					$media_type_id = $this->mediatype($extension);
					
					if(!empty($media_type_id)){
						$logo = $supplier_logo['name'];					
						$tmpname =$supplier_logo['tmp_name'];						
						$file_path = $this->container->getParameter('file_path');				
						$logo_path = $file_path.'/suppliers';	
						$logo_upload_dir = $this->container->getParameter('upload_dir').'/suppliers/';				
						
						$media_id = $this->mediaremoveAction($logo,$tmpname,$logo_path,$logo_upload_dir,$media_id,$media_type_id);	
					
					}else{
						$this->get("session")->getFlashBag()->set("error_msg","Supplier Logo is Required");
						return $this->redirect($this->generateUrl("admin_suppliers_addsupplier",array("domain"=>$this->get('session')->get('domain'),"supplier_id"=>$supplier_id)));
					}
				}
				
			$supplier = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Suppliermaster')
					   ->findOneBy(array('is_deleted'=>0,'supplier_master_id'=>$supplier_master_id));
			if(!empty($supplier)){
				$supplier->setSupplier_name($_REQUEST['supplier_name']);
				$supplier->setSupplier_description($_REQUEST['supplier_description']);
				$supplier->setPhone_no($_REQUEST['phone_number']);
				$supplier->setMobile_no($_REQUEST['mobile_number']);
				$supplier->setSupplier_logo($media_id);
				$supplier->setStatus($_REQUEST['status']);
				$supplier->setCreated_by($this->get('session')->get('user_id'));
				$supplier->setCreated_date(date('Y-m-d H:i:s'));
				$supplier->setDomain_id($_REQUEST['domain_id']);
				$supplier->setIs_deleted(0);
				$em = $this->getDoctrine()->getManager();
				$em->persist($supplier);
				$em->flush();
			}
			
			$address_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Addressmaster')
					   ->findOneBy(array('is_deleted'=>0,'main_address_id'=>$supplier->getAddress_id(),"language_id"=>$_REQUEST['language_id']));
			if(!empty($address_master))
			{
				$address_master->setAddress_name($_REQUEST['supplier_address']);
				$address_master->setLat($_REQUEST['lat']);
				$address_master->setLng($_REQUEST['long']);
				$em = $this->getDoctrine()->getManager();
				$em->persist($address_master);
				$em->flush();
			}
			
			// Basic Fileds Start
			
			$basic_supplier = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Suppliermaster')
					   ->findBy(array('is_deleted'=>0,'main_supplier_id'=>$supplier->getMain_supplier_id()));
					   
			if(!empty($basic_supplier))
			{
				foreach($basic_supplier as $bkey=>$bval)
				{
					$one_supplier = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:Suppliermaster')
							   ->findOneBy(array('is_deleted'=>0,'supplier_master_id'=>$bval->getSupplier_master_id()));
					$one_supplier->setPhone_no($_REQUEST['phone_number']);
					$one_supplier->setMobile_no($_REQUEST['mobile_number']);
					$one_supplier->setDomain_id($_REQUEST['domain_id']);
					$one_supplier->setStatus($_REQUEST['status']);
					$em = $this->getDoctrine()->getManager();
					$em->persist($one_supplier);
					$em->flush();
				}
			}
			
			$basic_address = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Addressmaster')
					   ->findBy(array('is_deleted'=>0,'main_address_id'=>$supplier->getAddress_id()));
					   
			if(!empty($basic_address))
			{
				foreach($basic_address as $bakey=>$baval)
				{
					$one_address = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:Addressmaster')
							   ->findOneBy(array('is_deleted'=>0,'address_master_id'=>$baval->getAddress_master_id()));
					$one_address->setLat($_REQUEST['lat']);
					$one_address->setLng($_REQUEST['long']);
					$em = $this->getDoctrine()->getManager();
					$em->persist($one_address);
					$em->flush();
				}
			}
			
			// Basic Fileds end
			
		}
		$this->get("session")->getFlashBag()->set("success_msg","Supplier Updated successfully");
	    return $this->redirect($this->generateUrl("admin_suppliers_index",array("domain"=>$this->get('session')->get('domain'))));
	}

	/**
     * @Route("/deletesupplier/{main_supplier_id}",defaults={"main_supplier_id":""})
     * @Template()
     */
    public function deletesupplierAction($main_supplier_id)
    {
		if($main_supplier_id != '0'){
			$supplier_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Suppliermaster')
					   ->findBy(array('is_deleted'=>0,'main_supplier_id'=>$main_supplier_id));
					   
			$address_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Addressmaster')
					   ->findBy(array('is_deleted'=>0,'main_address_id'=>$supplier_master[0]->getAddress_id()));
			if(!empty($address_master)){
				foreach($address_master as $akey=>$aval){
					$address_master_del = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Addressmaster')
					   ->findOneBy(array('is_deleted'=>0,'address_master_id'=>$aval->getAddress_master_id()));
					$address_master_del->setIs_deleted(1);
					$em = $this->getDoctrine()->getManager();
					$em->persist($address_master_del);
					$em->flush();
					
				}
			}

			if(!empty($supplier_master)){
				foreach($supplier_master as $selkey=>$selval){
					$supplier_master_del = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Suppliermaster')
					   ->findOneBy(array('is_deleted'=>0,'supplier_master_id'=>$selval->getSupplier_master_id()));
					$supplier_master_del->setIs_deleted(1);
					$em = $this->getDoctrine()->getManager();
					$em->persist($supplier_master_del);
					$em->flush();
					
				}
			}
			
		}
		$this->get("session")->getFlashBag()->set("success_msg","Suppler Deleted successfully");
	    return $this->redirect($this->generateUrl("admin_suppliers_index",array("domain"=>$this->get('session')->get('domain'))));
	}
   
   /**
     * @Route("/supplierstatus")
     * @Template()
    */
    
    public function supplierstatusAction()
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
			
			$supplier_master_list = $em->getRepository('AdminBundle:Suppliermaster')
								->findBy(
									array(
										'main_supplier_id'=>$id,
										'is_deleted'=>0
									)
								) ;
			if(!empty($supplier_master_list))
			{
				foreach($supplier_master_list as $key=>$val){
					$supplier_master = $em->getRepository('AdminBundle:Suppliermaster')
								->findOneBy(
									array(
										'supplier_master_id'=>$val->getSupplier_master_id(),
										'is_deleted'=>0
									)
								) ;
					$supplier_master->setStatus($status);
					$em->persist($supplier_master) ;
					$em->flush() ;
				}
			}
		}
		return new Response();
	} 
}