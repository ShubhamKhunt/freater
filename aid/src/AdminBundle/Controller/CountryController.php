<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Countrymaster;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
* @Route("/{domain}")
*/


class CountryController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
	
    /**
     * @Route("/country")
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
		  
        return array("language"=>$language,"country"=>$country_master);
    }
	/**
     * @Route("/addcountry/{main_country_id}",defaults = {"main_country_id" = ""})
     * @Template()
     */
    public function addcountryAction($main_country_id)
    {
		
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
		
		if(isset($main_country_id) && $main_country_id != "")
		{		   
			$country_master = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Countrymaster')
						   ->findBy(array('main_country_id'=>$main_country_id,'is_deleted'=>0));
			$Config_live_site = $this->container->getParameter('live_path') ;	   
			return array("language"=>$language,"country_master"=>$country_master,"main_country_id"=>$main_country_id,"livepath"=>$Config_live_site);
		}			   
        return array("language"=>$language);
    }
	/**
     * @Route("/savecountry/{language_id}/{main_country_id}",defaults={"main_country_id"=""})
     * @Template()
     */
    public function savecountryAction($language_id,$main_country_id)
    {
		
		if(isset($_POST['save_country']) && $_POST['save_country'] == 'save_country' && $language_id != "")
		{
			if($_POST['country_name'] != "" && $_POST['country_title'] != "" && $_POST['country_code'] != ""  && $_POST['status'] != "")
			{
				$country_name = $_POST['country_name'];
				$country_title = $_POST['country_title'];
				$country_code = $_POST['country_code'];
				$status = $_POST['status'];
	
							
				$Config_live_site = $this->container->getParameter('live_path') ;
				
			
				
				
				if(isset($main_country_id) && $main_country_id != "")
				{
					$country_master = $this->getDoctrine()
							->getManager()
							->getRepository("AdminBundle:Countrymaster")
							->findOneBy(array("main_country_id"=>$main_country_id,"language_id"=>$language_id,"is_deleted"=>0));
					if(!empty($country_master))
					{
						$em = $this->getDoctrine()->getManager();
						$update = $em->getRepository('AdminBundle:Countrymaster')->find($country_master->getCountry_id());					
						$update->setCountry_name($country_name);
						$update->setCountry_title($country_title);
						$update->setcountry_code($country_code);
						$update->setStatus($status);
						$em->flush();
						$this->get('session')->getFlashBag()->set('success_msg','Country Updated successfully.');
						return $this->redirect($this->generateUrl('admin_country_addcountry',array("domain"=>$this->get('session')->get('domain') ,'main_country_id'=>$main_country_id)));
					// till here done
					}
					else
					{
						$countrymaster = new Countrymaster();
						$countrymaster->setCountry_name($country_name);
						$countrymaster->setCountry_title($country_title);
						$countrymaster->setcountry_code($country_code);
						
						$countrymaster->setMain_country_id($main_country_id);
						$countrymaster->setLanguage_id($language_id);
						$countrymaster->setStatus($status);
						$countrymaster->setIs_deleted(0);
						$em = $this->getDoctrine()->getManager();
						$em->persist($countrymaster);
						$em->flush();
						
						$this->get('session')->getFlashBag()->set('success_msg','Country inserted successfully.');
						return $this->redirect($this->generateUrl('admin_country_addcountry',array("domain"=>$this->get('session')->get('domain'),'main_country_id'=>$main_country_id)));
					}
				}
				else
				{
					$countrymaster = new Countrymaster();
					$countrymaster->setCountry_name($country_name);
					$countrymaster->setCountry_title($country_title);
					$countrymaster->setcountry_code($country_code);
					
					$countrymaster->setMain_country_id(0);
					$countrymaster->setLanguage_id($language_id);
					$countrymaster->setStatus($status);
				$countrymaster->setIs_deleted(0);
					$em = $this->getDoctrine()->getManager();
					$em->persist($countrymaster);
					$em->flush();
					$countrymaster->setMain_country_id($countrymaster->getCountry_id());
					$em->flush();
					
					$this->get('session')->getFlashBag()->set('success_msg','Country inserted successfully.');
					return $this->redirect($this->generateUrl('admin_country_index',array("domain"=>$this->get('session')->get('domain'))));
				}
			}
			else
			{
				$this->get('session')->getFlashBag()->set('error_msg','Please fill all required fields.');
				return $this->redirect($this->generateUrl('admin_country_addcountry', array("domain"=>$this->get('session')->get('domain'))));
			}
		}
		return array();
	}
	
	/**
     * @Route("/countrystatus")
     * @Template()
    */
    
    public function countrystatusAction()
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
			
			$country_list = $em->getRepository('AdminBundle:Countrymaster')
								->findBy(
									array(
										'main_country_id'=>$id,
										'is_deleted'=>0
									)
								) ;
			if(!empty($country_list))
			{
				foreach($country_list as $key=>$val){
					$country = $em->getRepository('AdminBundle:Countrymaster')
								->findOneBy(
									array(
										'country_id'=>$val->getCountry_id(),
										'is_deleted'=>0
									)
								) ;
					$country->setStatus($status);
					$em->persist($country) ;
					$em->flush() ;
				}
			}
		}
		return new Response();
	}	
	
}