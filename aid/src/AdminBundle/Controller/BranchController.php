<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Branchmaster;
use AdminBundle\Entity\Companymaster;
use AdminBundle\Entity\Domainmaster;
use AdminBundle\Entity\Languagemaster;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;



/**
* @Route("/{domain}")
*/

 
class BranchController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
	
    /**
     * @Route("/branch/{domain_id}",defaults={"domain_id":""})
     * @Template()
     */
    public function indexAction($domain_id)
    {
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
		
        $query = '';
		
        /*if(!empty($this->get('session')->get('domain_id')) && $this->get('session')->get('role_id')== '1')
    	{
            $query = "SELECT branch_master.* ,
					 company_master.company_name
				  FROM branch_master
				  JOIN company_master
						ON company_master.main_company_id = branch_master.company_id AND
						   company_master.langauge_id = branch_master.language_id
					WHERE branch_master.is_deleted = 0";
        }else{
            $query = "SELECT branch_master.* , 
					 company_master.company_name
				  FROM branch_master
				  JOIN company_master
						ON company_master.main_company_id = branch_master.company_id AND
						   company_master.langauge_id = branch_master.language_id
					WHERE branch_master.is_deleted = 0 and branch_master.domain_id = '".$this->get('session')->get('domain_id')."' ";
        }
		
					
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare($query);
		$statement->execute();
		$branch_master = $statement->fetchAll();*/
		
		if($this->get('session')->get('role_id')== '1')
    	{
    		$query = $query = "SELECT branch_master.* ,
					 company_master.company_name
				  FROM branch_master
				  JOIN company_master
						ON company_master.main_company_id = branch_master.company_id AND
						   company_master.langauge_id = branch_master.language_id
					WHERE branch_master.is_deleted = 0 Group by main_branch_id";
    	}else{
			$query = "SELECT branch_master.* , 
					 company_master.company_name
				  FROM branch_master
				  JOIN company_master
						ON company_master.main_company_id = branch_master.company_id AND
						   company_master.langauge_id = branch_master.language_id
					WHERE branch_master.is_deleted = 0 and branch_master.domain_id = '".$this->get('session')->get('domain_id')."' Group by main_branch_id";
		}

		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare($query);
		$statement->execute();
		$branch_master = $statement->fetchAll();
		
		$all_branch_details = array();
		if(count($branch_master) > 0 ){
		
			foreach(array_slice($branch_master,0) as $bkey=>$bval){
				
				$lang_arr_branch_wise = array();
				foreach($language as $key=>$val){
					$lang_branch_name = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Branchmaster')->findOneBy(array('language_id'=>$val->getLanguage_master_id(),'main_branch_id'=>$bval['main_branch_id']));
					$branch_name='';
					if(!empty($lang_branch_name)){
						$branch_name = $lang_branch_name->getBranch_name();
					}	
					$lang_arr_branch_wise[] = array(
						"langauge_id"=>$val->getLanguage_master_id(),
						"branch_name"=>$branch_name
						);
				}
				
				$all_branch_details[] = array(
					"branch_master_id"=>$bval['branch_master_id'],
					"branch_name"=>$bval['branch_name'],
					"main_branch_id"=>$bval['main_branch_id'],
					"domain_id"=>$bval['domain_id'],
					"language_id"=>$bval['language_id'],
					"is_deleted"=>$bval['is_deleted'],
					"lang_arr_branch_wise"=>$lang_arr_branch_wise
				);
				
			}
		}
		//var_dump($all_branch_details);exit;
		return array("language"=>$language,"branch"=>$all_branch_details);
    }
	
	/**
     * @Route("/branchs/addbranch/{main_branch_id}",defaults={"main_branch_id":""})
     * @Template()
     */
    public function addbranchAction($main_branch_id)
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
		
		if(!empty($main_branch_id))
		{
			$query = "SELECT branch_master.* ,
					 company_master.company_name,domain_master.domain_id,domain_master.domain_code,domain_master.domain_name
				  FROM branch_master
				  JOIN company_master
						ON company_master.main_company_id = branch_master.company_id AND
						   company_master.langauge_id = branch_master.language_id
				  JOIN domain_master
						ON company_master.domain_id = domain_master.domain_code
					WHERE branch_master.is_deleted = 0 AND branch_master.main_branch_id = '" . $main_branch_id . "'";
			
			$em = $this->getDoctrine()->getManager();
			$connection = $em->getConnection();
			$statement = $connection->prepare($query);
			$statement->execute();
			$branchmaster = $statement->fetchAll();
		
			return array("language"=>$language,"domain"=>$domain_master,"branch"=>$branchmaster);
		}
		
		return array("language"=>$language,"domain"=>$domain_master);
	}
	
	/**
     * @Route("/branchs/savebranch")
     * @Template()
     */
    public function savebranchAction()
    {
		$domain_id = $_REQUEST['domain_id'];
		
		$company_id = $_REQUEST['company_id'];
		$description = "";
		if(!empty($_REQUEST['branch_desc']) && $_REQUEST['branch_desc'] != "")
		{
			$description = $_REQUEST['branch_desc'];	
		}
		$branch_name = $_REQUEST['branch_name'];
		$lang_id = $_REQUEST['save'];
		
		if(!empty($company_id) && !empty($lang_id) && !empty($branch_name))
		{
			$branch_master = new Branchmaster();
			$branch_master->setBranch_name($branch_name);
			$branch_master->setBranch_description($description);
			$branch_master->setCompany_id($company_id);
			$branch_master->setDomain_id($domain_id);
			$branch_master->setLanguage_id($lang_id);
			$branch_master->setIs_deleted(0);
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($branch_master);
			$em->flush();
			
			$branch_master->setMain_branch_id($branch_master->getBranch_master_id());
			$em->flush();
			
			$this->get("session")->getFlashBag()->set("success_msg","Branch Successfully Add .");
			return $this->redirect($this->generateUrl("admin_branch_index",array("domain"=>$this->get('session')->get('domain'))));
		}
		else
		{
			$this->get("session")->getFlashBag()->set("erroe_msg","Branch field is required .");
			return $this->redirect($this->generateUrl("admin_branch_addbranch",array("domain"=>$this->get('session')->get('domain'))));
		}
		
	}
	
	/**
     * @Route("/branchs/updatebranch/{main_branch_id}",defaults={"main_branch_id":""})
     * @Template()
     */
    public function updatebranchAction($main_branch_id)
    {
		$domain_id = $_REQUEST['domain_id'];
		$company_id = $_REQUEST['company_id'];
		$description = "";
		if(!empty($_REQUEST['branch_desc']) && $_REQUEST['branch_desc'] != "")
		{
			$description = $_REQUEST['branch_desc'];	
		}
		$branch_name = $_REQUEST['branch_name'];
		$lang_id = $_REQUEST['save'];
		
		$branchmaster = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Branchmaster')
					   ->findOneBy(array('is_deleted'=>0,"main_branch_id"=>$main_branch_id,"language_id"=>$lang_id));
		if(!empty($branchmaster))
		{
			$branchmaster->setBranch_name($branch_name);
			$branchmaster->setBranch_description($description);
			$branchmaster->setCompany_id($company_id);
			$branch_master->setDomain_id($domain_id);
			$em = $this->getDoctrine()->getManager();
			$em->flush();
		}
		else
		{
			$branch_master = new Branchmaster();
			$branch_master->setBranch_name($branch_name);
			$branch_master->setBranch_description($description);
			$branch_master->setCompany_id($company_id);
			$branch_master->setMain_branch_id($main_branch_id);
			$branch_master->setDomain_id($domain_id);
			$branch_master->setLanguage_id($lang_id);
			$branch_master->setIs_deleted(0);
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($branch_master);
			$em->flush();
			
		}
		
		$branch_update = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Branchmaster')
					   ->findBy(array('is_deleted'=>0,"main_branch_id"=>$main_branch_id));
		
		foreach($branch_update as $val)
		{
			$val->setDomain_id($domain_id);
			$val->setCompany_id($company_id);
			$em = $this->getDoctrine()->getManager();
			$em->flush();
		}
		$this->get("session")->getFlashBag()->set("success_msg","Branch Successfully Update .");
		return $this->redirect($this->generateUrl("admin_branch_index",array("domain"=>$this->get('session')->get('domain'))));
	}
	
	/**
     * @Route("/branchs/deletebranch/{main_branch_id}",defaults={"main_branch_id":""})
     * @Template()
     */
    public function deletebranchAction($main_branch_id)
    {
		//delete branch wise user
		$user_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Usermaster')
					   ->findBy(array('is_deleted'=>0,"branch_id"=>$main_branch_id));
		if(!empty($user_master))
		{
			foreach($user_master as $val)
			{
				$val->setIs_deleted(1);
				$em = $this->getDoctrine()->getManager();
				$em->flush();
			}
		}
		
		
		//delete branch
		$branch_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Branchmaster')
					   ->findBy(array('is_deleted'=>0,"main_branch_id"=>$main_branch_id));
		if(!empty($branch_master))
		{
			foreach($branch_master as $val)
			{
				$val->setIs_deleted(1);
				$em = $this->getDoctrine()->getManager();
				$em->flush();
			}
		}
		$this->get("session")->getFlashBag()->set("success_msg","Branch Successfully Delete .");
		return $this->redirect($this->generateUrl("admin_branch_index",array("domain"=>$this->get('session')->get('domain'))));
	}
	/**
     * @Route("/branchs/getcompanys")
     * @Template()
     */
    public function getcompanysAction()
	{
		$html = "";
		if($_POST['flag'] != "" && $_POST['flag'] == "domain_change")
		{
			
			$domain_id = $_POST['domain_id'];
			$lang_id = $_POST['lang_id'];
			
			$company = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Companymaster')
					   ->findBy(array('is_deleted'=>0,"domain_id"=>$domain_id,"langauge_id"=>$lang_id));
			
			if(!empty($company))
			{
				$html .= "<option value=''>Select Company</option>";
				
				foreach($company as $val)
				{
					$html .= "<option value='" . $val->getMain_company_id() . "'>" . $val->getCompany_name() . "</option>";
				}	
			}
		}
		
		return new Response($html);
	}
	
	
}