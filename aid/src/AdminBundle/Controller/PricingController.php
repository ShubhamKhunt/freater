<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Categorymaster;
use AdminBundle\Entity\Domainmaster;
use AdminBundle\Entity\Tagmodulerelation;
use AdminBundle\Entity\Tagmaster;
use AdminBundle\Entity\Pricingrulemaster;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;



/**
* @Route("/{domain}")
*/

 
class PricingController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
	
    /**
     * @Route("/pricing/{domain_id}",defaults={"domain_id":""})
     * @Template()
     */
    public function indexAction($domain_id)
    {
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
		//
		//$pricing_rule_list = $this->getDoctrine()
		//					->getManager()
		//					->getRepository('AdminBundle:Pricingrulemaster')
		//					->findBy(array('is_deleted'=>0));
		
		$query = "SELECT pricing_rule_master.* , language_master.language_name FROM `pricing_rule_master` JOIN language_master ON language_master.language_master_id = pricing_rule_master.language_id  WHERE  pricing_rule_master.is_deleted = 0 ORDER BY `pricing_rule_master`.`main_pricing_rule_id` ASC";
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare($query);
		$statement->execute();
		$pricing_rule_list = $statement->fetchAll();
		 return array("language"=>$language,"pricing_rule_list"=>$pricing_rule_list);
    }
	
	/**
     * @Route("/addpricing/{price_rule_id}",defaults={"price_rule_id":""})
     * @Template()
     */
    public function addpricingAction($price_rule_id)
    {
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
		if(isset($price_rule_id) && !empty($price_rule_id) && $price_rule_id != 0 ){
			$query = "SELECT pricing_rule_master.* , language_master.language_name FROM `pricing_rule_master` JOIN language_master ON language_master.language_master_id = pricing_rule_master.language_id WHERE   pricing_rule_master.main_pricing_rule_id = " . $price_rule_id ;
			$em = $this->getDoctrine()->getManager();
			$connection = $em->getConnection();
			$statement = $connection->prepare($query);
			$statement->execute();
			$pricing_rule_list = $statement->fetchAll();
			
			 return array("pricing_rule_list"=>$pricing_rule_list , "language"=>$language);
		}
	     return array("language"=>$language);
		
		
       
    }
	/**
     * @Route("/savepricingrule/{domain_id}/{rule_id}",defaults={"domain_id":"","rule_id":""})
     */
    public function savepricingruleAction($domain_id,$rule_id)
    {
		if(isset($_REQUEST['basic_language_id']) && !empty($_REQUEST['basic_language_id']) ){
			$pricing_rule = new Pricingrulemaster();
			$pricing_rule->setRule_title($_REQUEST['price_rule_name']);
			$pricing_rule->setPrice_rule_code($_REQUEST['price_rule_code']);
			$pricing_rule->setRule_description($_REQUEST['rule_description']);
			$pricing_rule->setStatus($_REQUEST['status']);
			$pricing_rule->setStart_date($_REQUEST['start_date']);
			$pricing_rule->setEnd_date($_REQUEST['end_date']);
			$pricing_rule->setLanguage_id($_REQUEST['basic_language_id']);
			$pricing_rule->setMain_pricing_rule_id(0);
			$pricing_rule->setIs_deleted(0);
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($pricing_rule);
			$em->flush();
			
			$pricing_rule->setMain_pricing_rule_id($pricing_rule->getPricing_rule_master_id());
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($pricing_rule);
			$em->flush();
			
			$this->get('session')->getFlashBag()->set('success_msg','Basic Rule Details inserted successfully.');
			return $this->redirect($this->generateUrl('admin_pricing_addpricing',array('domain'=>$this->get('session')->get('domain'),'rule_id'=>$pricing_rule->getPricing_rule_master_id())));
		}
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
		 return array("language"=>$language);
	
       
    }

		/**
     * @Route("/updatepricingrule/{main_rule_id}",defaults={"domain_id":"","main_rule_id":""})
     */
    public function updatepricingruleAction($main_rule_id)
    {
		$language_id = $_REQUEST['basic_language_id'];
			
		if($_REQUEST['basic_language_id'] != '0' && $main_rule_id != '0'){
			// check need to edit or update
			
			$pricing_rule_check = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Pricingrulemaster')->findOneBy(array("is_deleted"=>0,"main_pricing_rule_id"=>$main_rule_id,"language_id"=>$_REQUEST['basic_language_id']));
			
			if(!empty($pricing_rule_check)){
				// update
				$pricing_rule_check->setRule_title($_REQUEST['price_rule_name']);
				$pricing_rule_check->setRule_description($_REQUEST['rule_description']);
				
				$em = $this->getDoctrine()->getManager();
				$em->persist($pricing_rule_check);
				$em->flush();
				
				// update status , start date end date rule code in all language
				
				$query = "UPDATE pricing_rule_master SET price_rule_code = '".$_REQUEST['price_rule_code']."' , status = '".$_REQUEST['status']."' , start_date = '".$_REQUEST['start_date']."' , end_date = '".$_REQUEST['end_date']."' where main_pricing_rule_id = '" .$main_rule_id. "'  ";
				$em = $this->getDoctrine()->getManager();
				$connection = $em->getConnection();
				$statement = $connection->prepare($query);
				$statement->execute();
				
			}else{
				
				
				//save as new
				$pricing_rule_check->setRule_title($_REQUEST['price_rule_name']);
				$pricing_rule_check->setRule_description($_REQUEST['rule_description']);
				$pricing_rule_check->setLanguage_id($_REQUEST['basic_language_id']);
				$pricing_rule_check->setIs_deleted(0);
				
				$em = $this->getDoctrine()->getManager();
				$em->persist($pricing_rule_check);
				$em->flush();
				
				$query = "UPDATE pricing_rule_master SET price_rule_code = '".$_REQUEST['price_rule_code']."' , status = '".$_REQUEST['status']."' , start_date = '".$_REQUEST['start_date']."' , end_date = '".$_REQUEST['end_date']."' where main_pricing_rule_id = '" .$main_rule_id. "'  ";
				$em = $this->getDoctrine()->getManager();
				$connection = $em->getConnection();
				$statement = $connection->prepare($query);
				$statement->execute();
			}
		}
		$this->get('session')->getFlashBag()->set('success_msg','Updated successfully.');
		return $this->redirect($this->generateUrl('admin_pricing_addpricing',array('domain'=>$this->get('session')->get('domain'),'price_rule_id'=>$main_rule_id)));
	}

}