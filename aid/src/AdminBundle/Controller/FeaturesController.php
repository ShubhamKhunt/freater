<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Featuresmaster;
use AdminBundle\Entity\Domainmaster;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;



/**
* @Route("/{domain}")
*/

 
class FeaturesController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
	
    /**
     * @Route("/features/{domain_id}",defaults={"domain_id":""})
     * @Template()
     */
    public function indexAction($domain_id)
    {
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
		$all_features = '';
		if($domain_id == "" || $domain_id == 0 ){
			$all_features = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Featuresmaster')
					   ->findBy(array('is_deleted'=>0));
		}else{			
			$all_features = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Featuresmaster')
					   ->findBy(array('is_deleted'=>0,'domain_id'=>$domain_id));
		}
		
		
		if(count($all_features) > 0 ){
			foreach(array_slice($all_features,0) as $lkey=>$lval){
				
				$all_features_details[] = array(
					"features_master_id"=>$lval->getFeatures_master_id(),
					"title"=>$lval->getTitle(),
					"description"=>$lval->getDescription(),
					"language_id"=>$lval->getlanguage_id(),
					"main_features_master_id"=>$lval->getMain_features_master_id(),
					"status"=>$lval->getStatus(),
					"domain_id"=>$lval->getDomain_id(),
					"is_deleted"=>$lval->getIs_deleted(),
				);
				
			}
			
			
			
			return array("language"=>$language,"all_features"=>$all_features_details);
		}
		 return array("language"=>$language);
    }
	
	/**
     * @Route("/addfeature/{feature_id}",defaults={"feature_id":""})
     * @Template()
     */
    public function addfeatureAction($feature_id)
    {
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
		
		$domain_list = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Domainmaster')
					   ->findBy(array('is_deleted'=>0));
		if(!empty($feature_id)){
			
			$query = "SELECT * from features_master WHERE is_deleted=0 and main_features_master_id = '" . $feature_id . "'";
				
				$em = $this->getDoctrine()->getManager();
				$connection = $em->getConnection();
				$statement = $connection->prepare($query);
				$statement->execute();
				$selected_feature = $statement->fetchAll();
			
			return array("language"=>$language,"selected_feature"=>$selected_feature,"domain_list"=>$domain_list);
		}else{
			return array("language"=>$language,"domain_list"=>$domain_list);	
		}		
		
    }
    
    /**
     * @Route("/savefeature")
     * @Template()
     */
    public function savefeatureAction()
    {
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
					   
		if($_REQUEST['save'] == 'save_feature' && !empty($_REQUEST['language_id'] )  && !empty($_REQUEST['feature_name']) && !empty($_REQUEST['status'])){
			
			
			$date_time = date("Y-m-d H:i:s") ; 
			$features = new Featuresmaster();
			$features->setTitle($_REQUEST['feature_name']);
			$features->setDescription($_REQUEST['feature_description']);
			$features->setStatus($_REQUEST['status']);
			$features->setCreated_by($this->get('session')->get('user_id'));
			$features->setCreate_date(date('Y-m-d H:i:s'));
			$features->setLanguage_id($_REQUEST['language_id']);
			$features->setDomain_id($_REQUEST['domain_id']);
			$features->setIs_deleted(0);
			
			if(!empty($_REQUEST['main_feature_id']) && $_REQUEST['main_feature_id'] != '0'){
				
				$features->setMain_features_master_id($_REQUEST['main_feature_id']);
				$em = $this->getDoctrine()->getManager();
				$em->persist($features);
				$em->flush();
				
			 }
			else{
			
				$features->setMain_features_master_id(0);
				$em = $this->getDoctrine()->getManager();
				$em->persist($features);
				$em->flush();
				$main_features = $features->getFeatures_master_id();
				
				$features->setMain_features_master_id($main_features);
				$em = $this->getDoctrine()->getManager();
				$em->persist($features);
				$em->flush();
				
			}
			
			// Basic Fileds Start
			
			$basic_features = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Featuresmaster')
					   ->findBy(array('is_deleted'=>0,'main_features_master_id'=>$features->getMain_features_master_id()));
					   
			if(!empty($basic_features))
			{
				foreach($basic_features as $bkey=>$bval)
				{
					$one_features = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:Featuresmaster')
							   ->findOneBy(array('is_deleted'=>0,'features_master_id'=>$bval->getFeatures_master_id()));
					$one_features->setStatus($_REQUEST['status']);
					$one_features->setDomain_id($_REQUEST['domain_id']);
					$em = $this->getDoctrine()->getManager();
					$em->persist($one_features);
					$em->flush();
				}
			}
			
			// Basic Fileds end
		
		}
       $this->get("session")->getFlashBag()->set("success_msg","Feature Added successfully");	
	   return $this->redirect($this->generateUrl("admin_features_index",array("domain"=>$this->get('session')->get('domain'))));
    }

 
 	/**
     * @Route("/updatefeature/{features_master_id}",defaults={"features_master_id":""})
     * @Template()
     */
    public function updatefeatureAction($features_master_id)
    {
		if(!empty($features_master_id)){
			
			
			$features = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Featuresmaster')
					   ->findOneBy(array('is_deleted'=>0,'features_master_id'=>$features_master_id));
			if(!empty($features)){
				$features->setTitle($_REQUEST['feature_name']);
				$features->setDescription($_REQUEST['feature_description']);
				$features->setStatus($_REQUEST['status']);
				$features->setCreated_by($this->get('session')->get('user_id'));
				$features->setCreate_date(date('Y-m-d H:i:s'));
				$features->setLanguage_id($_REQUEST['language_id']);
				$features->setDomain_id($_REQUEST['domain_id']);
				$features->setIs_deleted(0);
				$em = $this->getDoctrine()->getManager();
				$em->persist($features);
				$em->flush();
			}
			
			
			// Basic Fileds Start
			
			$basic_features = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Featuresmaster')
					   ->findBy(array('is_deleted'=>0,'main_features_master_id'=>$features->getMain_features_master_id()));
					   
			if(!empty($basic_features))
			{
				foreach($basic_features as $bkey=>$bval)
				{
					$one_features = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:Featuresmaster')
							   ->findOneBy(array('is_deleted'=>0,'features_master_id'=>$bval->getFeatures_master_id()));
					$one_features->setStatus($_REQUEST['status']);
					$one_features->setDomain_id($_REQUEST['domain_id']);
					$em = $this->getDoctrine()->getManager();
					$em->persist($one_features);
					$em->flush();
				}
			}
			
			// Basic Fileds end
			
		}
		$this->get("session")->getFlashBag()->set("success_msg","Feature Updated successfully");
	    return $this->redirect($this->generateUrl("admin_features_index",array("domain"=>$this->get('session')->get('domain'))));
	}

	/**
     * @Route("/deletefeature/{main_features_master_id}",defaults={"main_features_master_id":""})
     * @Template()
     */
    public function deletefeatureAction($main_features_master_id)
    {
		if($main_features_master_id != '0'){
			$feature_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Featuresmaster')
					   ->findBy(array('is_deleted'=>0,'main_features_master_id'=>$main_features_master_id));
					   
			if(!empty($feature_master)){
				foreach($feature_master as $key=>$val){
					$feature_master_del = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Featuresmaster')
					   ->findOneBy(array('is_deleted'=>0,'features_master_id'=>$val->getFeatures_master_id()));
					$feature_master_del->setIs_deleted(1);
					$em = $this->getDoctrine()->getManager();
					$em->persist($feature_master_del);
					$em->flush();
					
				}
			}
			
		}
		$this->get("session")->getFlashBag()->set("success_msg","Features Deleted successfully");
	    return $this->redirect($this->generateUrl("admin_features_index",array("domain"=>$this->get('session')->get('domain'))));
	}
   
   /**
     * @Route("/featurestatus")
     * @Template()
    */
    
    public function featurestatusAction()
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
			
			$features_master_list = $em->getRepository('AdminBundle:Featuresmaster')
								->findBy(
									array(
										'main_features_master_id'=>$id,
										'is_deleted'=>0
									)
								) ;
			if(!empty($features_master_list))
			{
				foreach($features_master_list as $key=>$val){
					$features_master = $em->getRepository('AdminBundle:Featuresmaster')
								->findOneBy(
									array(
										'features_master_id'=>$val->getFeatures_master_id(),
										'is_deleted'=>0
									)
								) ;
					$features_master->setStatus($status);
					$em->persist($features_master) ;
					$em->flush() ;
				}
			}
		}
		return new Response();
	} 
}