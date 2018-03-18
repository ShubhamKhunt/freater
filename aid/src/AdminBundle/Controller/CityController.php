<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Countrymaster;
use AdminBundle\Entity\Statemaster;
use AdminBundle\Entity\Citymaster;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;


/**
* @Route("/{domain}")
*/

class CityController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
	
    /**
     * @Route("/city")
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
		$city_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Citymaster')
					   ->findBy(array('is_deleted'=>0));
        return array("language"=>$language,"country"=>$country_master,"state"=>$state_master,"city"=>$city_master);
    }
	/**
     * @Route("/addcity/{main_city_id}",defaults = {"main_city_id" = ""})
     * @Template()
     */
    public function addcityAction($main_city_id)
    {
		
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
		$country_master = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Countrymaster')
						   ->findBy(array('is_deleted'=>0));
						   
		if(isset($main_city_id) && $main_city_id != "")
		{		   
			$city_master = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Citymaster')
						   ->findBy(array('main_city_id'=>$main_city_id,'is_deleted'=>0));
			$state_master = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Statemaster')
						   ->findBy(array('main_state_id'=>$city_master[0]->getMain_state_id(),'is_deleted'=>0));
			return array("language"=>$language,"country_master"=>$country_master,"state_master"=>$state_master,"city_master"=>$city_master,"main_country_id"=>$state_master[0]->getMain_country_id(),"main_state_id"=>$city_master[0]->getMain_state_id(),"main_city_id"=>$main_city_id);
		}			   
        return array("language"=>$language,"country_master"=>$country_master);
    }
	
	/**
     * @Route("/savecity/{language_id}/{main_city_id}",defaults={"main_city_id"=""})
     * @Template()
     */
    public function savecityAction($language_id,$main_city_id)
    {
		
		if(isset($_POST['save_city']) && $_POST['save_city'] == 'save_city' && $language_id != "")
		{
			if($_POST['main_state_id'] != "" && $_POST['city_name'] != "" && $_POST['city_code'] != "" && $_POST['status'] != "")
			{
				$main_state_id = $_POST['main_state_id'];
				$city_name = $_POST['city_name'];
				$city_code = $_POST['city_code'];
				$status = $_POST['status'];
	
							
				
				
				if(isset($main_city_id) && $main_city_id != "")
				{
					$city_master = $this->getDoctrine()
							->getManager()
							->getRepository("AdminBundle:Citymaster")
							->findOneBy(array("main_city_id"=>$main_city_id,"language_id"=>$language_id,"is_deleted"=>0));
					if(!empty($city_master))
					{
						$em = $this->getDoctrine()->getManager();
						$update = $em->getRepository('AdminBundle:Citymaster')->find($city_master->getCity_master_id());					
						$update->setCity_name($city_name);
						$update->setCity_code($city_code);
						$update->setMain_state_id($main_state_id);
						$update->setStatus($status);
						$em->flush();
						$this->get('session')->getFlashBag()->set('success_msg','City Updated successfully.');
						return $this->redirect($this->generateUrl('admin_city_addcity',array('domain'=>$this->get('session')->get('domain'),'main_city_id'=>$main_city_id)));
					// till here done
					}
					else
					{
						$citymaster = new Citymaster();
						$citymaster->setCity_name($city_name);
						$citymaster->setCity_code($city_code);
						$citymaster->setMain_state_id($main_state_id);
						$citymaster->setMain_city_id($main_city_id);
						$citymaster->setLanguage_id($language_id);
						$citymaster->setStatus($status);
						$citymaster->setIs_deleted(0);
						$em = $this->getDoctrine()->getManager();
						$em->persist($citymaster);
						$em->flush();
						
						$this->get('session')->getFlashBag()->set('success_msg','City inserted successfully.');
						return $this->redirect($this->generateUrl('admin_city_addcity',array('domain'=>$this->get('session')->get('domain'),'main_city_id'=>$main_city_id)));
					}
				}
				else
				{
					$citymaster = new Citymaster();
					$citymaster->setCity_name($city_name);
					$citymaster->setCity_code($city_code);
					$citymaster->setMain_state_id($main_state_id);
					$citymaster->setMain_city_id(0);
					$citymaster->setLanguage_id($language_id);
					$citymaster->setStatus($status);
					$citymaster->setIs_deleted(0);
					$em = $this->getDoctrine()->getManager();
					$em->persist($citymaster);
					$em->flush();
					$citymaster->setMain_city_id($citymaster->getCity_master_id());
					$em->flush();
					
					$this->get('session')->getFlashBag()->set('success_msg','City inserted successfully.');
					return $this->redirect($this->generateUrl('admin_city_index',array("domain"=>$this->get('session')->get('domain'))));
				}
			}
			else
			{
				$this->get('session')->getFlashBag()->set('error_msg','Please fill all required fields.');
				return $this->redirect($this->generateUrl('admin_city_addcity',array("domain"=>$this->get('session')->get('domain'))));
			}
		}
		return array();
	}
	
	/**
	* @Route("/getstateajax")
	*@Template()
	**/
	public function getstateajaxAction(){
		$html = "<option value=''>Select State</option>" ;
		$method = $this->get('request')->getMethod() ;
		if($method = "POST"){
			$country_id = $_POST['country_id'] ;
			$lang_id = $_POST['lang_id'] ;
			
			$state_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Statemaster')
					   ->findBy(array('main_country_id'=>$country_id,"language_id"=>$lang_id,'is_deleted'=>0));
			if(!empty($state_master)){
				foreach($state_master as $key=>$val){
					$html .= "<option value='".$val->getMain_state_id()."'>".$val->getState_name()."</option>";
				}
			}
			
		}
		
		$response = new Response() ;
		$response->setContent($html) ;
		return $response ;
	}
	
	
	/**
     * @Route("/citystatus")
     * @Template()
    */
    
    public function citystatusAction()
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
			
			$city_list = $em->getRepository('AdminBundle:Citymaster')
								->findBy(
									array(
										'main_city_id'=>$id,
										'is_deleted'=>0
									)
								) ;
								
			if(!empty($city_list))
			{
				foreach($city_list as $key=>$val){
					$city = $em->getRepository('AdminBundle:Citymaster')
								->findOneBy(
									array(
										'city_master_id'=>$val->getCity_master_id(),
										'is_deleted'=>0
									)
								) ;
					$city->setStatus($status);
					$em->persist($city) ;
					$em->flush() ;
				}
			}
		}
		return new Response();
	}
}