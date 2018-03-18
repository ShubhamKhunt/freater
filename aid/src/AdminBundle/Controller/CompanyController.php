<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Companymaster;
use AdminBundle\Entity\Domainmaster;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;



/**
* @Route("/{domain}")
*/

 
class CompanyController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
	
    /**
     * @Route("/comapny/{domain_id}",defaults={"domain_id":""})
     * @Template()
     */
    public function indexAction($domain_id)
    {
		$company_master = $query = '';
		/*if($domain_id == "" || $domain_id == 0 ){*/
		/*if(!empty($this->get('session')->get('domain_id')) && $this->get('session')->get('role_id')== '1')
    	{
			$query = "SELECT company_master.* ,
					 domain_master.domain_name
				  FROM company_master
				  JOIN domain_master
						ON domain_master.domain_code = company_master.domain_id
					WHERE company_master.is_deleted = 0";
			
		}
		else{
			$query = "SELECT company_master.* ,
					 domain_master.domain_name
				  FROM company_master
				  JOIN domain_master
						ON domain_master.domain_code = company_master.domain_id
					WHERE company_master.is_deleted = 0 and company_master.domain_id = '".$this->get('session')->get('domain_id')."' ";
		}
		
					
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare($query);
		$statement->execute();
		$company_master = $statement->fetchAll();*/
		
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
		
		if($this->get('session')->get('role_id')== '1')
    	{
    		$query = "SELECT company_master.* ,
					 domain_master.domain_name
				  FROM company_master
				  JOIN domain_master
						ON domain_master.domain_code = company_master.domain_id
					WHERE company_master.is_deleted = 0 Group by main_company_id";
    	}else{
			$query = "SELECT company_master.* ,
					 domain_master.domain_name
				  FROM company_master
				  JOIN domain_master
						ON domain_master.domain_code = company_master.domain_id
					WHERE company_master.is_deleted = 0 and company_master.domain_id = '".$this->get('session')->get('domain_id')."' Group by main_company_id";
		}
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare($query);
		$statement->execute();
		$company_master = $statement->fetchAll();
		$all_company_details = array();
		if(count($company_master) > 0 ){
			foreach(array_slice($company_master,0) as $ckey=>$cval){
				
				$lang_arr_company_wise = array();
				foreach($language as $key=>$val){
					$lang_company_name = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Companymaster')->findOneBy(array('langauge_id'=>$val->getLanguage_master_id(),'main_company_id'=>$cval['main_company_id']));
					$company_name='';
					if(!empty($lang_company_name)){
						$company_name = $lang_company_name->getCompany_name();
					}	
					$lang_arr_company_wise[] = array(
						"langauge_id"=>$val->getLanguage_master_id(),
						"company_name"=>$company_name
						);
				}
				
				$all_company_details[] = array(
					"company_master_id"=>$cval['company_master_id'],
					"company_name"=>$cval['company_name'],
					"main_company_id"=>$cval['main_company_id'],
					"domain_id"=>$cval['domain_id'],
					"langauge_id"=>$cval['langauge_id'],
					"is_deleted"=>$cval['is_deleted'],
					"lang_arr_company_wise"=>$lang_arr_company_wise
				);
				
			}
		}
		return array("language"=>$language,"company"=>$all_company_details);
    }
	
	/**
     * @Route("/addcompany/{main_company_id}",defaults={"main_company_id":""})
     * @Template()
     */
    public function addcompanyAction($main_company_id)
    {
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
					   
		$domain_master = array();
		if(!empty($this->get('session')->get('domain_id')) && $this->get('session')->get('role_id')== '1')
    	{	   
			$domain_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Domainmaster')
					   ->findBy(array('is_deleted'=>0));
		}else{
			$domain_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Domainmaster')
					   ->findBy(array('is_deleted'=>0,"domain_code"=>$this->get('session')->get('domain_id')));
		}
		
		if(!empty($main_company_id))
		{
			$companymaster = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Companymaster')
					   ->findBy(array('is_deleted'=>0,"main_company_id"=>$main_company_id));
		
			return array("language"=>$language,"domain"=>$domain_master,"company"=>$companymaster);
		}		   
		
		return array("language"=>$language,"domain"=>$domain_master);
	}
	
	/**
     * @Route("/savecompany")
     * @Template()
     */
    public function savecompanyAction()
    {
		$domain_id = $_REQUEST['domain_id'];
		$description = "";
		if(!empty($_REQUEST['company_desc']) && $_REQUEST['company_desc'] != "")
		{
			$description = $_REQUEST['company_desc'];	
		}
		$company_name = $_REQUEST['company_name'];
		$lang_id = $_REQUEST['save'];
		
		if(!empty($domain_id) && !empty($lang_id) && !empty($company_name))
		{
			$company_master = new Companymaster();
			$company_master->setCompany_name($company_name);
			$company_master->setCompany_description($description);
			$company_master->setDomain_id($domain_id);
			$company_master->setLangauge_id($lang_id);
			$company_master->setIs_deleted(0);
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($company_master);
			$em->flush();
			
			$company_master->setMain_company_id($company_master->getCompany_master_id());
			$em->flush();
			
			$this->get("session")->getFlashBag()->set("success_msg","Company Successfully Add .");
			return $this->redirect($this->generateUrl("admin_company_index",array("domain"=>$this->get('session')->get('domain'))));
		}
		else
		{
			$this->get("session")->getFlashBag()->set("erroe_msg","Company field is required .");
			return $this->redirect($this->generateUrl("admin_company_addcompany",array("domain"=>$this->get('session')->get('domain'))));
		}
		
	}
	
	
	
	/**
     * @Route("/deletecompany/{main_company_id}",defaults={"main_company_id":""})
     * @Template()
     */
    public function deletecompanyAction($main_company_id)
    {
		//delete company wise user
		$user_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Usermaster')
					   ->findBy(array('is_deleted'=>0,"company_id"=>$main_company_id));
		if(!empty($user_master))
		{
			foreach($user_master as $val)
			{
				$val->setIs_deleted(1);
				$em = $this->getDoctrine()->getManager();
				$em->flush();
			}
		}
		
		//delete company wise branch
		$branch_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Branchmaster')
					   ->findBy(array('is_deleted'=>0,"company_id"=>$main_company_id));
		if(!empty($branch_master))
		{
			foreach($branch_master as $val)
			{
				$val->setIs_deleted(1);
				$em = $this->getDoctrine()->getManager();
				$em->flush();
			}
		}
		
		//delete company
		$companymaster = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Companymaster')
					   ->findBy(array('is_deleted'=>0,"main_company_id"=>$main_company_id));
		if(!empty($companymaster))
		{
			foreach($companymaster as $val)
			{
				$val->setIs_deleted(1);
				$em = $this->getDoctrine()->getManager();
				$em->flush();
			}
		}
		
		
		$this->get("session")->getFlashBag()->set("success_msg","Company Successfully Delete .");
		return $this->redirect($this->generateUrl("admin_company_index",array("domain"=>$this->get('session')->get('domain'))));
	
	}
	
	
	/**
     * @Route("/updatecompany/{main_company_id}",defaults={"main_company_id":""})
     * @Template()
     */
    public function updatecompanyAction($main_company_id)
    {
		$domain_id = $_REQUEST['domain_id'];
		$description = "";
		if(!empty($_REQUEST['company_desc']) && $_REQUEST['company_desc'] != "")
		{
			$description = $_REQUEST['company_desc'];	
		}
		$company_name = $_REQUEST['company_name'];
		$lang_id = $_REQUEST['save'];
		
		$companymaster = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Companymaster')
					   ->findOneBy(array('is_deleted'=>0,"main_company_id"=>$main_company_id,"langauge_id"=>$lang_id));
		if(!empty($companymaster))
		{
			$companymaster->setCompany_name($company_name);
			$companymaster->setCompany_description($description);
			$companymaster->setDomain_id($domain_id);
			$em = $this->getDoctrine()->getManager();
			$em->flush();
		}
		else{
			
			//add new language wise data
			
			$company_master = new Companymaster();
			$company_master->setCompany_name($company_name);
			$company_master->setCompany_description($description);
			$company_master->setDomain_id($domain_id);
			$company_master->setMain_company_id($main_company_id);
			$company_master->setLangauge_id($lang_id);
			$company_master->setIs_deleted(0);
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($company_master);
			$em->flush();
		}
		
		//comman filed update
		$companymaster_upade = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Companymaster')
					   ->findBy(array('is_deleted'=>0,"main_company_id"=>$main_company_id));
					   
		foreach($companymaster_upade as $val)
		{
			$val->setDomain_id($domain_id);
			$em = $this->getDoctrine()->getManager();
			$em->flush();
		}
		$this->get("session")->getFlashBag()->set("success_msg","Company Successfully Update .");
		return $this->redirect($this->generateUrl("admin_company_index",array("domain"=>$this->get('session')->get('domain'))));
	}
}