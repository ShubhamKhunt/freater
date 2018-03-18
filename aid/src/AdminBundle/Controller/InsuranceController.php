<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;

use AdminBundle\Entity\Rightmaster;
use AdminBundle\Entity\Rolerightmaster;
use AdminBundle\Entity\Userrolemaster;
use AdminBundle\Entity\Usermaster;
use AdminBundle\Entity\Insurancemaster;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
* @Route("/admin")
*/
class InsuranceController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
    
	/**
	 * @Route("/insurance/")
     * @Template()
     */
    public function insurancelistAction()
    {
		ini_set('xdebug.var_display_max_depth', 200);
		ini_set('xdebug.var_display_max_children', 256);
		ini_set('xdebug.var_display_max_data', 1024);
		$em = $this->getDoctrine()->getManager();
		$con = $em->getConnection();
		$st = $con->prepare("SELECT main_insurance_master_id FROM insurance_master WHERE is_deleted = 0 GROUP BY main_insurance_master_id");
		$st->execute();
		$insurancelist = $st->fetchAll();
		$data = array();
		if(!empty($insurancelist)){
			foreach($insurancelist as $k=>$v){
				$em = $this->getDoctrine()->getManager();
				$connection = $em->getConnection();
				$statement = $connection->prepare("SELECT * FROM insurance_master WHERE is_deleted = 0 AND main_insurance_master_id = ".$v['main_insurance_master_id']);
				$statement->execute();
				$comp = $statement->fetchAll();
				
				
					for($i=0;$i<count($comp);$i++)
					{
						$get_parent_name = $this->getDoctrine()
							->getManager()
							->getRepository('AdminBundle:Insurancemaster')
							->findOneBy(array(
									'insurance_master_id'=>$comp[$i]['insurance_master_id'],
									'language_id'=>$comp[$i]['language_id'],
									'is_deleted'=>0)
								);
							
					}
					
				
				$data[] = array("insurance_master_id"=>$v['main_insurance_master_id'],"data"=>$comp);
			}
		}
		$live_path=$this->getparams()->live;
		
		$langs = $this->getDoctrine()
				->getManager()
				->getRepository("AdminBundle:Languagemaster")
				->findBy(array("is_deleted"=>0));
					
		return array("insurancelist"=>$data,"languages"=>$langs,"live_path"=>$live_path);
    }
	
    /**
     * @Route("/insurance/add-update/{insurance_master_id}", defaults={"insurance_master_id"=""})
     * @Template()
     */
	 public function addinsuranceAction($insurance_master_id)
	 {	 	
		$langs = $this->getDoctrine()
					->getManager()
					->getRepository("AdminBundle:Languagemaster")
					->findBy(array("is_deleted"=>0));
		
		$get_insurance_list = $this->getDoctrine()
								->getManager()
								->getRepository('AdminBundle:Insurancemaster')
								->findBy(array(
										'is_deleted'=>0)
									);
			
		if(!empty($insurance_master_id)){
			$em = $this->getDoctrine()->getManager();
    		$conn = $em->getConnection();
    		$st = $conn->prepare("SELECT * from insurance_master where insurance_master.main_insurance_master_id = ".$insurance_master_id." AND insurance_master.is_deleted = 0");
    		$st->execute();
    		$load_insurance = $st->fetchAll();
			
			return array('insurance'=>$load_insurance,'insurance_master_id'=>$insurance_master_id,"languages"=>$langs,"insurance_list"=>$get_insurance_list);
		} else {
			return array("languages"=>$langs,"insurance_list"=>$get_insurance_list);
		}
	}
	
	/**
     * @Route("/addinsurancedb")
     * @Template()
     */
	 public function addinsurancedbAction(Request $request){
		$session = new Session();
		$user_id = $session->get("user_id");
		$language_id = $_REQUEST['language_master_id'];
		if(isset($_REQUEST['add'])){
			$insurance_title = $_REQUEST['insurance_title'];
			$insurance_status = $_REQUEST['insurance_status'];
			$insurance_master_id = '';
			
			if(isset($_REQUEST['insurance_master_id']) && !empty($_REQUEST['insurance_master_id']))
			{
				$insurance_master_id = $_REQUEST['insurance_master_id'];
				$get_plan = $this->getDoctrine()
								->getManager()
								->getRepository('AdminBundle:Insurancemaster')
								->findOneBy(array(
										'main_insurance_master_id'=>$insurance_master_id,
										'language_id'=>$language_id,
										'is_deleted'=>0)
									);
				if(count($get_plan) > 0){
					$em = $this->getDoctrine()->getManager();
					$update = $em->getRepository('AdminBundle:Insurancemaster')
								->find($get_plan->getInsurance_master_id());					
					$update->setInsurance_name($insurance_title);
					$update->setStatus($insurance_status);
					
					$em->flush();
					
					$this->get('session')->getFlashBag()->set('success_msg','Insurance Updated Successfuly!');
					
					$referer = $request->headers->get('referer');
					return $this->redirect($referer);
				} else {
					$Insurancemaster = new Insurancemaster();
					$Insurancemaster->setInsurance_name($insurance_title);
					$Insurancemaster->setStatus($insurance_status);
					$Insurancemaster->setLanguage_id($language_id);
					$Insurancemaster->setMain_insurance_master_id($insurance_master_id);
					$Insurancemaster->setIs_deleted(0);
					$Insurancemaster->setCreated_by($user_id);
				
					$em = $this->getDoctrine()->getManager();
					$em->persist($Insurancemaster);
					$em->flush();
					if(isset($insurance_master_id) && !empty($insurance_master_id))
					{
						$this->get('session')->getFlashBag()
							->set('success_msg','Insurance Inserted Successfuly!');
						$referer = $request->headers->get('referer');
						return $this->redirect($referer);
					}
					else {
						$this->get('session')->getFlashBag()->set('error_msg','Insurance Insertion Failed!');
						return $this->redirect($this->generateUrl('admin_insurance_addinsurance'));
					}	
				}
			} else {
				$Insurancemaster = new Insurancemaster();
				$Insurancemaster->setInsurance_name($insurance_title);
				$Insurancemaster->setStatus($insurance_status);
				$Insurancemaster->setLanguage_id($language_id);
				$Insurancemaster->setMain_insurance_master_id(0);
				$Insurancemaster->setIs_deleted(0);
				$Insurancemaster->setCreated_by($user_id);
			
				$em = $this->getDoctrine()->getManager();
				$em->persist($Insurancemaster);
				$em->flush();
					
				$insurance_master_id = $Insurancemaster->getInsurance_master_id();
				
				if(isset($insurance_master_id) && !empty($insurance_master_id))
				{
					$em = $this->getDoctrine()->getManager();
					$update = $em->getRepository('AdminBundle:Insurancemaster')->find($insurance_master_id);
					$update->setMain_Insurance_master_id($insurance_master_id);
					$em->flush();
					$this->get('session')->getFlashBag()->set('success_msg','Insurance Inserted Successfuly!');
					
					$referer = $request->headers->get('referer');
					return $this->redirect($referer);
				} else {
					$this->get('session')->getFlashBag()->set('error_msg','Insurance Insertion Failed!');
					return $this->redirect($this->generateUrl('admin_insurance_addinsurance'));
				}
			}
		}
	}
	
    /**
     * @Route("/insurance/remove/{insurance_master_id}",defaults={"insurance_master_id"=""})
     * @Template()
     */
	 public function deleteinsuranceAction($insurance_master_id){	 	 	
		if(!empty($insurance_master_id)){
			$id = $insurance_master_id;
			$list = $this->getDoctrine()
					->getManager()
					->getRepository("AdminBundle:Insurancemaster")
					->findBy(array("is_deleted"=>0,"main_insurance_master_id"=>$id));
			if(!empty($list)){
				foreach($list as $k=>$v){
					$em = $this->getDoctrine()->getManager();
					$update = $em->getRepository("AdminBundle:Insurancemaster")->find($v->getInsurance_master_id());
					$update->setIs_deleted(1);
					$em->flush();
				}
			}
			$this->get('session')->getFlashBag()->set('success_msg','Insurance Deleted Successfuly!');
			return $this->redirect($this->generateUrl('admin_insurance_insurancelist'));
		}
	}
}