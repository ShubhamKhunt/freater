<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Countrymaster;
use AdminBundle\Entity\Statemaster;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
* @Route("/{domain}")
*/


class StateController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
    /**
     * @Route("/state")
     * @Template()
     */
    public function indexAction()
    {
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
		$country_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Countrymaster')
					   ->findBy(array('is_deleted'=>0));
		
		$state_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Statemaster')
					   ->findBy(array('is_deleted'=>0));
        return array("language"=>$language,"country"=>$country_master,"state"=>$state_master);
    }
	/**
     * @Route("/addstate/{main_state_id}",defaults = {"main_state_id" = ""})
     * @Template()
     */
    public function addstateAction($main_state_id)
    {
		
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
		$country_master = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Countrymaster')
						   ->findBy(array('is_deleted'=>0));
		   
		if(isset($main_state_id) && $main_state_id != "")
		{		   
			$state_master = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Statemaster')
						   ->findBy(array('main_state_id'=>$main_state_id,'is_deleted'=>0));
			return array("language"=>$language,"country_master"=>$country_master,"state_master"=>$state_master,"main_state_id"=>$main_state_id);
		}			   
        return array("language"=>$language,"country_master"=>$country_master);
    }
	/**
     * @Route("/savestate/{language_id}/{main_state_id}",defaults={"main_state_id"=""})
     * @Template()
     */
    public function savestateAction($language_id,$main_state_id)
    {
		
		if(isset($_POST['save_state']) && $_POST['save_state'] == 'save_state' && $language_id != "")
		{
			if($_POST['main_country_id'] != "" && $_POST['state_name'] != "" && $_POST['state_code'] != "" && $_POST['status'] != "")
			{
				$main_country_id = $_POST['main_country_id'];
				$state_name = $_POST['state_name'];
				$state_code = $_POST['state_code'];
				$status = $_POST['status'];
	
							
				
				
				if(isset($main_state_id) && $main_state_id != "")
				{
					$state_master = $this->getDoctrine()
							->getManager()
							->getRepository("AdminBundle:Statemaster")
							->findOneBy(array("main_state_id"=>$main_state_id,"language_id"=>$language_id,"is_deleted"=>0));
					if(!empty($state_master))
					{
						$em = $this->getDoctrine()->getManager();
						$update = $em->getRepository('AdminBundle:Statemaster')->find($state_master->getState_master_id());					
						$update->setState_name($state_name);
						$update->setState_code($state_code);
						$update->setMain_country_id($main_country_id);
						$update->setStatus($status);
						$em->flush();
						$this->get('session')->getFlashBag()->set('success_msg','State Updated successfully.');
						return $this->redirect($this->generateUrl('admin_state_addstate',array("domain"=>$this->get('session')->get('domain'),'main_state_id'=>$main_state_id)));
					// till here done
					}
					else
					{
						$statemaster = new Statemaster();
						$statemaster->setState_name($state_name);
						$statemaster->setState_code($state_code);
						$statemaster->setMain_country_id($main_country_id);
						$statemaster->setMain_state_id($main_state_id);
						$statemaster->setLanguage_id($language_id);
						$statemaster->setStatus($status);
						$statemaster->setIs_deleted(0);
						$em = $this->getDoctrine()->getManager();
						$em->persist($statemaster);
						$em->flush();
						
						$this->get('session')->getFlashBag()->set('success_msg','State inserted successfully.');
						return $this->redirect($this->generateUrl('admin_state_addstate',array("domain"=>$this->get('session')->get('domain'),'main_state_id'=>$main_state_id)));
					}
				}
				else
				{
					$statemaster = new Statemaster();
					$statemaster->setState_name($state_name);
					$statemaster->setState_code($state_code);
					$statemaster->setMain_country_id($main_country_id);
					$statemaster->setMain_state_id(0);
					$statemaster->setLanguage_id($language_id);
					$statemaster->setStatus($status);
					$statemaster->setIs_deleted(0);
					$em = $this->getDoctrine()->getManager();
					$em->persist($statemaster);
					$em->flush();
					$statemaster->setMain_state_id($statemaster->getState_master_id());
					$em->flush();
					
					$this->get('session')->getFlashBag()->set('success_msg','State inserted successfully.');
					return $this->redirect($this->generateUrl('admin_state_index',array("domain"=>$this->get('session')->get('domain'))));
				}
			}
			else
			{
				$this->get('session')->getFlashBag()->set('error_msg','Please fill all required fields.');
				return $this->redirect($this->generateUrl('admin_state_addstate',array("domain"=>$this->get('session')->get('domain'))));
			}
		}
		return array();
	}

	/**
     * @Route("/statestatus")
     * @Template()
    */
    
    public function statestatusAction()
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
			
			$state_list = $em->getRepository('AdminBundle:Statemaster')
								->findBy(
									array(
										'main_state_id'=>$id,
										'is_deleted'=>0
									)
								) ;
								
			if(!empty($state_list))
			{
				foreach($state_list as $key=>$val){
					$state = $em->getRepository('AdminBundle:Statemaster')
								->findOneBy(
									array(
										'state_master_id'=>$val->getState_master_id(),
										'is_deleted'=>0
									)
								) ;
					$state->setStatus($status);
					$em->persist($state) ;
					$em->flush() ;
				}
			}
		}
		return new Response();
	}
}