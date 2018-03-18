<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;

use AdminBundle\Entity\Faq;
use AdminBundle\Entity\Faqtype;
use AdminBundle\Entity\Languagemaster;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
* @Route("/{domain}")
*/

class FaqController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
	
	/**
     * @Route("/Manage-faq")
     * @Template()
     */
	public function faqlistAction()
    {
		
    		$em = $this->getDoctrine()->getManager();
			$con = $em->getConnection();
			$st = $con->prepare("SELECT * FROM faq WHERE is_deleted = 0 group by faq_id");
			$st->execute();
			$faqlist = $st->fetchAll();
			$data = array();
			if(!empty($faqlist)){
				return array("faqtlists"=>$faqlist);
			}
			else
			{
				return array();
			}
			
    }
	
	/**
     * @Route("/Add-Update-faq/{faq_id}",defaults={"faq_id"=""})
     * @Template()
     */
	 public function addfaqAction($faq_id){	 	
		$langs = $this->getDoctrine()
					->getManager()
					->getRepository("AdminBundle:Languagemaster")
					->findBy(array("is_deleted"=>0));
			
		$type = $this->getDoctrine()
					->getManager()
					->getRepository("AdminBundle:Faqtype")
					->findBy(array("is_deleted"=>0));
					
		if(!empty($faq_id)){
			$em = $this->getDoctrine()->getManager();
    		$conn = $em->getConnection();
    		$st = $conn->prepare("SELECT * FROM faq where faq_main_id=".$faq_id." AND is_deleted = 0");
    		$st->execute();
    		$load_faq = $st->fetchAll();
			
			//dump($load_faq);
			//exit;
		/*	return array('advertisement'=>$load_category,'advertisement_master_id'=>$advertisement_master_id,"languages"=>$langs);*/
			return array('faqs'=>$load_faq,'faq_id'=>$faq_id,"languages"=>$langs,"type"=>$type,"view_edit"=>"Edit");
		} else {
			return array("languages"=>$langs,"type"=>$type,"view_edit"=>"Add");
			//return array();
		}
	}
	
	/**
     * @Route("/addfaqdb")
     * @Template()
     */
	 public function addfaqdbAction(){
		$session = new Session();
		$user_id = $session->get("user_id");
		
		
		if(isset($_REQUEST['add'])){
			
			//print_r($_POST);
			//exit;
			$language_id = $_REQUEST['language_master_id'];
			
			$faq_question = $_REQUEST['faq_question'];
			$faq_answer = $_REQUEST['faq_answer'];
		//var_dump($language_id);exit;
			$type = $_REQUEST['type'];
			$status = $_REQUEST['status'];

			
			$faq_id = '';
			if(isset($_REQUEST['faq_id']) && !empty($_REQUEST['faq_id']))
			{
				
				$faq_id = $_REQUEST['faq_id'];
				$get_faq = $this->getDoctrine()
								->getManager()
								->getRepository('AdminBundle:Faq')
								->findOneBy(array(
										'faq_main_id'=>$faq_id,
										'lang_id'=>$language_id,
										'is_deleted'=>0)
									);
			//dump($get_faq);
			//exit;
				if(count($get_faq) > 0){
					$em = $this->getDoctrine()->getManager();
					$update = $em->getRepository('AdminBundle:Faq')
								->find($get_faq->getFaq_id());					
					
					$update->setQuestion($faq_question);
					$update->setAnswer($faq_answer);
					$update->setType_id($type);
					$update->setStatus($status);
					$update->setCreate_date(date('Y-m-d H:i:s'));
					
					$em->flush();
					//-------------------------------- Common Changes -----------------------------
					
					//-------------------------------- Common Changes -----------------------------
					$this->get('session')->getFlashBag()->set('success_msg','FAQ Updated Successfuly!');
					return $this->redirect($this->generateUrl('admin_faq_addfaq',array("domain"=>$this->get('session')->get('domain'))).'/'.$faq_id);
				} else {
					$faqmaster = new Faq();
					
					$faqmaster->setQuestion($faq_question);
					$faqmaster->setAnswer($faq_answer);
					$faqmaster->setType_id($type);
					$faqmaster->setFaq_main_id($faq_id);
					$faqmaster->setLang_id($language_id);
					$faqmaster->setStatus($status);
					$faqmaster->setCreate_date(date('Y-m-d H:i:s'));
					$faqmaster->setIs_deleted(0);
					$em = $this->getDoctrine()->getManager();
					$em->persist($faqmaster);
					$em->flush();
					if(isset($faq_id) && !empty($faq_id)){
						$this->get('session')->getFlashBag()
							->set('success_msg','FAQ Inserted Successfuly!');
						return $this->redirect($this
							->generateUrl('admin_faq_addfaq',array("domain"=>$this->get('session')->get('domain'))).'/'.$faq_id);
					} else {
						$this->get('session')->getFlashBag()->set('error_msg','FAQ Insertion Failed!');
						return $this->redirect($this->generateUrl('admin_faq_addfaq',array("domain"=>$this->get('session')->get('domain'))));
					}	
				}
			} else {
					$faqmaster = new Faq();
					
					$faqmaster->setQuestion($faq_question);
					$faqmaster->setAnswer($faq_answer);
					$faqmaster->setType_id($type);
					$faqmaster->setLang_id($language_id);
					$faqmaster->setFaq_main_id(0);
					$faqmaster->setCreate_date(date('Y-m-d H:i:s'));
					$faqmaster->setStatus($status);
					$faqmaster->setIs_deleted(0);
					$em = $this->getDoctrine()->getManager();
					$em->persist($faqmaster);
					$em->flush();
					$faq_id = $faqmaster->getFaq_id();
				if(isset($faq_id) && !empty($faq_id)){
					$em = $this->getDoctrine()->getManager();
					$update = $em->getRepository('AdminBundle:Faq')->find($faq_id);
					$update->setFaq_main_id($faq_id);
					$em->flush();
					$this->get('session')->getFlashBag()->set('success_msg','FAQ Inserted Successfuly!');
					return $this->redirect($this->generateUrl('admin_faq_addfaq',array("domain"=>$this->get('session')->get('domain'))).'/'.$faq_id);
				} else {
					$this->get('session')->getFlashBag()->set('error_msg','FAQ Insertion Failed!');
					return $this->redirect($this->generateUrl('admin_faq_faqlist',array("domain"=>$this->get('session')->get('domain'))));
				}
				return new Response("succeful");
			}
		}
	 }
	 
	 
	 /**
     * @Route("/DeleteFag/{faq_id}",defaults={"faq_id"=""})
     * @Template()
     */
	 public function deletefaqAction($faq_id){	 	 	
		if(!empty($faq_id)){
			//$faq_id = $_REQUEST['faq_id'];
			//dump($faq_id);
			//exit;
			$id=$faq_id;
			$list = $this->getDoctrine()
					->getManager()
					->getRepository("AdminBundle:Faq")
					->findBy(array("is_deleted"=>0,"faq_main_id"=>$id));
			if(!empty($list)){
				foreach($list as $k=>$v){
					$em = $this->getDoctrine()->getManager();
					$update = $em->getRepository("AdminBundle:Faq")->find($v->getFaq_id());
					$update->setIs_deleted(1);
					$em->flush();
				}
			}
			$this->get('session')->getFlashBag()->set('success_msg','Fag Deleted Successfuly!');
			return $this->redirect($this->generateUrl('admin_faq_faqlist',array("domain"=>$this->get('session')->get('domain'))));
		}
	}
	
	/**
     * @Route("/changestatusfaq")
     * 
     */
    public function changestatusfaqAction()
    {
		if(isset($_POST['flag']) && $_POST['flag'] == 'change_status')
		{
			$faq_id = $_POST['faq_id'];
			$sts = "";
			
			$em = $this->getDoctrine()->getManager();
			$faq_list = $em->getRepository('AdminBundle:Faq')
								->findBy(
									array(
										'faq_main_id'=>$faq_id,
										'is_deleted'=>0
									)
								) ;
								
			if(!empty($faq_list))
			{
				foreach($faq_list as $key=>$val){
					$faq = $em->getRepository('AdminBundle:Faq')
								->findOneBy(
									array(
										'faq_id'=>$val->getFaq_id(),
										'is_deleted'=>0
									)
								) ;
						if($faq->getStatus() == 'inactive')
						{
							$sts = 'active';
						}
						else
						{
							$sts = 'inactive';
						}		
								
								
					$faq->setStatus($sts);
					$em->persist($faq) ;
					$em->flush() ;
				}
			}
			
		}
		return new Response("succeful");
	}
	
	//faq type start
	/**
     * @Route("/Manage-faqtype")
     * @Template()
     */
	public function faqtypelistAction()
    {
		
    		$em = $this->getDoctrine()->getManager();
			$con = $em->getConnection();
			$st = $con->prepare("SELECT * FROM faq_type WHERE is_deleted = 0 group by faq_type_id");
			$st->execute();
			$faqlist = $st->fetchAll();
			$data = array();
			if(!empty($faqlist)){
				return array("faqtlists"=>$faqlist);
			}
			else
			{
				return array();
			}
			
    }
	
	/**
     * @Route("/Add-Update-faqtype/{faq_type_id}",defaults={"faq_type_id"=""})
     * @Template()
     */
	 public function addfaqtypeAction($faq_type_id){	 	
		$langs = $this->getDoctrine()
					->getManager()
					->getRepository("AdminBundle:Languagemaster")
					->findBy(array("is_deleted"=>0));
			
		
		if(!empty($faq_type_id)){
			$em = $this->getDoctrine()->getManager();
    		$conn = $em->getConnection();
    		$st = $conn->prepare("SELECT * FROM faq_type where main_faq_type_id=".$faq_type_id." AND is_deleted = 0");
    		$st->execute();
    		$load_faq = $st->fetchAll();
			
			//dump($load_faq);
			//exit;
		/*	return array('advertisement'=>$load_category,'advertisement_master_id'=>$advertisement_master_id,"languages"=>$langs);*/
			return array('faqs'=>$load_faq,'faq_id'=>$faq_type_id,"languages"=>$langs);
		} else {
			return array("languages"=>$langs);
			//return array();
		}
	}
	
	/**
     * @Route("/addfaqtypedb")
     * @Template()
     */
	 public function addfaqtypedbAction(){
		$session = new Session();
		$user_id = $session->get("user_id");
		
		
		if(isset($_REQUEST['add'])){
			
			//print_r($_POST);
			//exit;
			$language_id = $_REQUEST['language_master_id'];
			
			$faq_question = $_REQUEST['faq_question'];
			
			$status = $_REQUEST['status'];

			
			$faq_id = '';
			if(isset($_REQUEST['faq_id']) && !empty($_REQUEST['faq_id'])){
				
				$faq_id = $_REQUEST['faq_id'];
				$get_faq = $this->getDoctrine()
								->getManager()
								->getRepository('AdminBundle:Faqtype')
								->findOneBy(array(
										'main_faq_type_id'=>$faq_id,
										'language_id'=>$language_id,
										'is_deleted'=>0)
									);
			//dump($get_faq);
			//exit;
				if(count($get_faq) > 0){
					$em = $this->getDoctrine()->getManager();
					$update = $em->getRepository('AdminBundle:Faqtype')
								->find($get_faq->getFaq_type_id());					
					
					$update->setFaq_type_name($faq_question);
					$update->setStatus($status);
					$em->flush();
					//-------------------------------- Common Changes -----------------------------
					
					//-------------------------------- Common Changes -----------------------------
					$this->get('session')->getFlashBag()->set('success_msg','FAQ Type Updated Successfuly!');
					return $this->redirect($this->generateUrl('admin_faq_addfaqtype',array("domain"=>$this->get('session')->get('domain'))).'/'.$faq_id);
				} else {
					$faqmaster = new Faqtype();
					
					$faqmaster->setFaq_type_name($faq_question);
					$faqmaster->setLanguage_id($language_id);
					$faqmaster->setStatus($status);
					$faqmaster->setMain_faq_type_id($faq_id);
					$faqmaster->setIs_deleted(0);
					$em = $this->getDoctrine()->getManager();
					$em->persist($faqmaster);
					$em->flush();
					if(isset($faq_id) && !empty($faq_id)){
						$this->get('session')->getFlashBag()
							->set('success_msg','FAQ Inserted Successfuly!');
						return $this->redirect($this
							->generateUrl('admin_faq_addfaqtype',array("domain"=>$this->get('session')->get('domain'))).'/'.$faq_id);
					} else {
						$this->get('session')->getFlashBag()->set('error_msg','FAQ Type Insertion Failed!');
						return $this->redirect($this->generateUrl('admin_faq_addfaqtype',array("domain"=>$this->get('session')->get('domain'))));
					}	
				}
			} else {
					$faqmaster = new Faqtype();
					
					$faqmaster->setFaq_type_name($faq_question);
				
					$faqmaster->setLanguage_id($language_id);
					$faqmaster->setMain_faq_type_id(0);
					$faqmaster->setStatus($status);
					$faqmaster->setIs_deleted(0);
					$em = $this->getDoctrine()->getManager();
					$em->persist($faqmaster);
					$em->flush();
					$faq_id = $faqmaster->getFaq_type_id();
				if(isset($faq_id) && !empty($faq_id)){
					$em = $this->getDoctrine()->getManager();
					$update = $em->getRepository('AdminBundle:Faqtype')->find($faq_id);
					$update->setMain_faq_type_id($faq_id);
					$em->flush();
					$this->get('session')->getFlashBag()->set('success_msg','FAQ Type Inserted Successfuly!');
					return $this->redirect($this->generateUrl('admin_faq_addfaqtype',array("domain"=>$this->get('session')->get('domain'))).'/'.$faq_id);
				} else {
					$this->get('session')->getFlashBag()->set('error_msg','FAQ Type Insertion Failed!');
					return $this->redirect($this->generateUrl('admin_faq_faqtypelist',array("domain"=>$this->get('session')->get('domain'))));
				}
				return new Response("succeful");
			}
		}
	 }
	 
	 
	 /**
     * @Route("/DeleteFagtype/{faqtype_id}",defaults={"faqtype_id"=""})
     * @Template()
     */
	 public function deletefaqtypeAction($faqtype_id){	 	 	
		if(!empty($faqtype_id)){
			//$faq_id = $_REQUEST['faq_id'];
			//dump($faq_id);
			//exit;
			$id=$faqtype_id;
			$list = $this->getDoctrine()
					->getManager()
					->getRepository("AdminBundle:Faqtype")
					->findBy(array("is_deleted"=>0,"main_faq_type_id"=>$id));
			if(!empty($list)){
				foreach($list as $k=>$v){
					$em = $this->getDoctrine()->getManager();
					$update = $em->getRepository("AdminBundle:Faqtype")->find($v->getFaq_type_id());
					$update->setIs_deleted(1);
					$em->flush();
				}
			}
			$this->get('session')->getFlashBag()->set('success_msg','Fag Type Deleted Successfuly!');
			return $this->redirect($this->generateUrl('admin_faq_faqtypelist',array("domain"=>$this->get('session')->get('domain'))));
		}
		$this->get('session')->getFlashBag()->set('error_msg','Fag Type Deleted Faild!');
		return $this->redirect($this->generateUrl('admin_faq_faqtypelist',array("domain"=>$this->get('session')->get('domain'))));
	}
	
	/**
     * @Route("/changestatusfaqtype")
     * 
     */
    public function changestatusfaqtypeAction()
    {
		if(isset($_POST['flag']) && $_POST['flag'] == 'change_status')
		{
			$faq_id = $_POST['faq_id'];
			$sts = "";
			
			$em = $this->getDoctrine()->getManager();
			$faq_list = $em->getRepository('AdminBundle:Faqtype')
								->findBy(
									array(
										'main_faq_type_id'=>$faq_id,
										'is_deleted'=>0
									)
								) ;
								
			if(!empty($faq_list))
			{
				foreach($faq_list as $key=>$val){
					$faq = $em->getRepository('AdminBundle:Faqtype')
								->findOneBy(
									array(
										'faq_type_id'=>$val->getFaq_type_id(),
										'is_deleted'=>0
									)
								) ;
						if($faq->getStatus() == 'inactive')
						{
							$sts = 'active';
						}
						else
						{
							$sts = 'inactive';
						}		
								
								
					$faq->setStatus($sts);
					$em->persist($faq) ;
					$em->flush() ;
				}
			}
			
		}
		return new Response("succeful");
	}
	
	
}