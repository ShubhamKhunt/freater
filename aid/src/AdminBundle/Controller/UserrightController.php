<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Coursecategorymaster;
use AdminBundle\Entity\Domainmaster;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;



/**
* @Route("/{domain}")
*/

 
class UserrightController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
	
    /**
     * @Route("/category/{domain_id}",defaults={"domain_id":""})
     * @Template()
     */
    public function indexAction($domain_id)
    {
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
		$all_category = '';
		if($domain_id == "" || $domain_id == 0 ){
			$all_category = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Coursecategorymaster')
					   ->findBy(array('is_deleted'=>0));
		}else{			
			$all_category = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Coursecategorymaster')
					   ->findBy(array('is_deleted'=>0,'domain_id'=>$domain_id));
		}
		
		
		if(count($all_category) > 0 ){
			foreach(array_slice($all_category,0) as $lkey=>$lval){
				$parent_category_name = 'No Parent ' ;
				
				if($lval->getCourse_parent_category_id() != 0){
					$parent_category_name = '' ; 
					$parent_category = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:Coursecategorymaster')
							   ->findOneBy(array('is_deleted'=>0,'course_category_master_id'=>$lval->getCourse_parent_category_id()));
					if(!empty($parent_category)){
						$parent_category_name = $parent_category->getCourse_category_name();
					}
					
				}
				$all_category_details[] = array(
					"course_category_master_id"=>$lval->getCourse_category_master_id(),
					"course_category_name"=>$lval->getCourse_category_name(),
					"course_parent_category_id"=>$lval->getCourse_parent_category_id(),
					"course_parent_category_name"=>$parent_category_name,
					"course_category_description"=>$lval->getCourse_category_description(),
					"main_course_category_id"=>$lval->getMain_course_category_id(),
					"language_id"=>$lval->getLanguage_id()	
				);
				
							   
			
		
			}
			 return array("language"=>$language,"all_category"=>$all_category_details);
		}
		 return array("language"=>$language);
		//var_dump($all_category_details);exit;
       
    }
	
	/**
     * @Route("/addcategory/{category_id}",defaults={"category_id":""})
     * @Template()
     */
    public function addcategoryAction($category_id)
    {
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
		$parent_category = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Coursecategorymaster')
					   ->findBy(array('is_deleted'=>0,'course_parent_category_id'=>0));
		$domain_list = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Domainmaster')
					   ->findBy(array('is_deleted'=>0));
		if($category_id != '0'){
			$selected_category = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Coursecategorymaster')
					   ->findBy(array('is_deleted'=>0,'main_course_category_id'=>$category_id));
			
			return array("language"=>$language,"parent_category"=>$parent_category,"selected_category"=>$selected_category,"domain_list"=>$domain_list);
		}
		else{
			 return array("language"=>$language,"parent_category"=>$parent_category,"domain_list"=>$domain_list);
		}
        
    }
	
	/**
     * @Route("/savecategory")
     * @Template()
     */
    public function savecategoryAction()
    {
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
					   
		if($_REQUEST['save'] == 'save_category' && !empty($_REQUEST['language_id'] )  && !empty($_REQUEST['category_name'])){
			$course_category = new Coursecategorymaster();
			$course_category->setCourse_category_name($_REQUEST['category_name']);
			$course_category->setCourse_parent_category_id($_REQUEST['parent_category']);
			$course_category->setCourse_category_description($_REQUEST['category_description']);
			$course_category->setLanguage_id($_REQUEST['language_id']);
			$course_category->setIs_deleted(0);
			if(!empty($_REQUEST['main_category_id']) && $_REQUEST['main_category_id'] != '0'){
				$course_category->setMain_course_category_id($_REQUEST['main_category_id']);
				$em = $this->getDoctrine()->getManager();
				$em->persist($course_category);
				$em->flush();
			}
			else{
				$course_category->setMain_course_category_id(0);
				$em = $this->getDoctrine()->getManager();
				$em->persist($course_category);
				$em->flush();
				$main_cat = $course_category->getCourse_category_master_id();
				$course_category->setMain_course_category_id($main_cat);
				$em = $this->getDoctrine()->getManager();
				$em->persist($course_category);
				$em->flush();
			}
		}
       $this->get("session")->getFlashBag()->set("success_msg","Category Added successfully");	
	   return $this->redirect($this->generateUrl("admin_category_index",array("domain"=>$this->get('session')->get('domain'))));
    }

	/**
     * @Route("/deletecategory/{category_id}",defaults={"category_id":""})
     * @Template()
     */
    public function deletecategoryAction($category_id)
    {
		if($category_id != '0'){
			$selected_category = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Coursecategorymaster')
					   ->findBy(array('is_deleted'=>0,'main_course_category_id'=>$category_id));
			if(!empty($selected_category)){
				foreach($selected_category as $selkey=>$selval){
					$selected_category_del = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Coursecategorymaster')
					   ->findOneBy(array('is_deleted'=>0,'course_category_master_id'=>$selval->getCourse_category_master_id()));
					$selected_category_del->setIs_deleted(1);
					$em = $this->getDoctrine()->getManager();
					$em->persist($selected_category_del);
					$em->flush();
					
				}
			}
			$selected_category = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Coursecategorymaster')
					   ->findBy(array('is_deleted'=>0,'course_parent_category_id'=>$category_id));
			if(!empty($selected_category)){
				foreach($selected_category as $selkey=>$selval){
					$selected_category_del = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Coursecategorymaster')
					   ->findOneBy(array('is_deleted'=>0,'course_category_master_id'=>$selval->getCourse_category_master_id()));
					$selected_category_del->setIs_deleted(1);
					$em = $this->getDoctrine()->getManager();
					$em->persist($selected_category_del);
					$em->flush();
					
				}
			}
			
		}
		$this->get("session")->getFlashBag()->set("success_msg","Category Deleted successfully");
	    return $this->redirect($this->generateUrl("admin_category_index",array("domain"=>$this->get('session')->get('domain'))));
	}
	/**
     * @Route("/updatecategory/{category_id}",defaults={"category_id":""})
     * @Template()
     */
    public function updatecategoryAction($category_id)
    {
		if($category_id != '0'){
			$course_category = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Coursecategorymaster')
					   ->findOneBy(array('is_deleted'=>0,'course_category_master_id'=>$category_id));
			if(!empty($course_category)){
				$course_category->setCourse_category_name($_REQUEST['category_name']);
				$course_category->setCourse_parent_category_id($_REQUEST['parent_category']);
				$course_category->setCourse_category_description($_REQUEST['category_description']);
				$em = $this->getDoctrine()->getManager();
				$em->persist($course_category);
				$em->flush();
			}
		}
		$this->get("session")->getFlashBag()->set("success_msg","Category Updated successfully");
	    return $this->redirect($this->generateUrl("admin_category_index",array("domain"=>$this->get('session')->get('domain'))));
	}
	
}