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
use AdminBundle\Entity\Issuetitlemaster;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
* @Route("/admin")
*/
class IssuetitleController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
    
	/**
	 * @Route("/issue-titles/")
     * @Template()
     */
    public function issuetitlelistAction()
    {
		ini_set('xdebug.var_display_max_depth', 200);
		ini_set('xdebug.var_display_max_children', 256);
		ini_set('xdebug.var_display_max_data', 1024);
		$em = $this->getDoctrine()->getManager();
		$con = $em->getConnection();
		$st = $con->prepare("SELECT main_issue_title_master_id FROM issue_title_master WHERE is_deleted = 0 GROUP BY main_issue_title_master_id");
		$st->execute();
		$issue_titlelist = $st->fetchAll();
		$data = array();
		if(!empty($issue_titlelist)){
			foreach($issue_titlelist as $k=>$v){
				$em = $this->getDoctrine()->getManager();
				$connection = $em->getConnection();
				$statement = $connection->prepare("SELECT * FROM issue_title_master WHERE is_deleted = 0 AND main_issue_title_master_id = ".$v['main_issue_title_master_id']);
				$statement->execute();
				$comp = $statement->fetchAll();
				
				for($i=0;$i<count($comp);$i++)
				{
					$get_parent_name = $this->getDoctrine()
						->getManager()
						->getRepository('AdminBundle:Issuetitlemaster')
						->findOneBy(array(
								'issue_title_master_id'=>$comp[$i]['issue_title_master_id'],
								'language_id'=>$comp[$i]['language_id'],
								'is_deleted'=>0)
							);
						
				}
				$data[] = array("issue_title_master_id"=>$v['main_issue_title_master_id'],"data"=>$comp);
			}
		}
		$live_path=$this->getparams()->live;
		
		$langs = $this->getDoctrine()
				->getManager()
				->getRepository("AdminBundle:Languagemaster")
				->findBy(array("is_deleted"=>0));
				
		return array("issue_titlelist"=>$data,"languages"=>$langs,"live_path"=>$live_path);
    }
	 
    /**
     * @Route("/issue-title/add-update/{issue_title_master_id}",defaults={"issue_title_master_id"=""})
     * @Template()
     */
	 public function addissuetitleAction($issue_title_master_id)
	 {	 	
		$langs = $this->getDoctrine()
					->getManager()
					->getRepository("AdminBundle:Languagemaster")
					->findBy(array("is_deleted"=>0));
		
		$get_issue_title_list = $this->getDoctrine()
								->getManager()
								->getRepository('AdminBundle:Issuetitlemaster')
								->findBy(array(
										'is_deleted'=>0)
									);
				
		if(!empty($issue_title_master_id)){
			$em = $this->getDoctrine()->getManager();
    		$conn = $em->getConnection();
    		$st = $conn->prepare("SELECT * from issue_title_master where issue_title_master.main_issue_title_master_id = ".$issue_title_master_id." AND issue_title_master.is_deleted = 0");
    		$st->execute();
    		$load_issue_title = $st->fetchAll();
			
			return array('issue_title'=>$load_issue_title,'issue_title_master_id'=>$issue_title_master_id,"languages"=>$langs,"issue_title_list"=>$get_issue_title_list);
		} else {
			return array("languages"=>$langs,"issue_title_list"=>$get_issue_title_list);
		}
	}
	
	/**
     * @Route("/addissuetitledb")
     * @Template()
     */
	 public function addissuetitledbAction(Request $request){
		$session = new Session();
		$user_id = $session->get("user_id");
		$language_id = $_REQUEST['language_master_id'];
		if(isset($_REQUEST['add']))
		{
			$issue_title_title = $_REQUEST['issue_title_title'];
			$issue_title_status = $_REQUEST['issue_title_status'];
			$issue_type = $_REQUEST['issue_type'];
			$issue_title_master_id = '';
			
			if(isset($_REQUEST['issue_title_master_id']) && !empty($_REQUEST['issue_title_master_id']))
			{
				$issue_title_master_id = $_REQUEST['issue_title_master_id'];
				$get_plan = $this->getDoctrine()
								->getManager()
								->getRepository('AdminBundle:Issuetitlemaster')
								->findOneBy(array(
										'main_issue_title_master_id'=>$issue_title_master_id,
										'language_id'=>$language_id,
										'is_deleted'=>0)
									);
				if(count($get_plan) > 0){
					$em = $this->getDoctrine()->getManager();
					$update = $em->getRepository('AdminBundle:Issuetitlemaster')
								->find($get_plan->getIssue_title_master_id());					
					$update->setIssue_title_name($issue_title_title);
					$update->setIssue_type($issue_type);
					$update->setStatus($issue_title_status);
					
					$em->flush();
			
					$referer = $request->headers->get('referer');
					return $this->redirect($referer);
				} else {
					$Issue_titlemaster = new Issuetitlemaster();
					$Issue_titlemaster->setIssue_title_name($issue_title_title);
					$Issue_titlemaster->setIssue_type($issue_type);
					$Issue_titlemaster->setStatus($issue_title_status);
					$Issue_titlemaster->setLanguage_id($language_id);
					$Issue_titlemaster->setMain_issue_title_master_id($issue_title_master_id);
					$Issue_titlemaster->setIs_deleted(0);
					$Issue_titlemaster->setCreated_by($user_id);
				
					$em = $this->getDoctrine()->getManager();
					$em->persist($Issue_titlemaster);
					$em->flush();
					
					if(isset($issue_title_master_id) && !empty($issue_title_master_id))
					{
						$this->get('session')->getFlashBag()
							->set('success_msg','Issue_title Inserted Successfuly!');
						$referer = $request->headers->get('referer');
						return $this->redirect($referer);
					}
					else {
						$this->get('session')->getFlashBag()->set('error_msg','Issue_title Insertion Failed!');
						return $this->redirect($this->generateUrl('admin_issuetitle_addissuetitle'));
					}	
				}
			} else {
				$Issue_titlemaster = new Issuetitlemaster();
				$Issue_titlemaster->setIssue_title_name($issue_title_title);
				$Issue_titlemaster->setIssue_type($issue_type);
				$Issue_titlemaster->setStatus($issue_title_status);
				$Issue_titlemaster->setLanguage_id($language_id);
				$Issue_titlemaster->setMain_issue_title_master_id(0);
				$Issue_titlemaster->setIs_deleted(0);
				$Issue_titlemaster->setCreated_by($user_id);
			
				$em = $this->getDoctrine()->getManager();
				$em->persist($Issue_titlemaster);
				$em->flush();
					
				$issue_title_master_id = $Issue_titlemaster->getIssue_title_master_id();
				if(isset($issue_title_master_id) && !empty($issue_title_master_id))
				{
					$em = $this->getDoctrine()->getManager();
					$update = $em->getRepository('AdminBundle:Issuetitlemaster')->find($issue_title_master_id);
					$update->setMain_Issue_title_master_id($issue_title_master_id);
					$em->flush();
					$this->get('session')->getFlashBag()->set('success_msg','Issue_title Inserted Successfuly!');
					
					$referer = $request->headers->get('referer');
					return $this->redirect($referer);
				} else {
					$this->get('session')->getFlashBag()->set('error_msg','Issue_title Insertion Failed!');
					return $this->redirect($this->generateUrl('admin_issuetitle_addissuetitle'));
				}
			}
		}
	}
	
    /**
     * @Route("/issue-title/remove/{issue_title_master_id}",defaults={"issue_title_master_id"=""})
     * @Template()
     */
	 public function deleteissuetitleAction($issue_title_master_id){	 	 	
		if(!empty($issue_title_master_id)){
			$id = $issue_title_master_id;
			$list = $this->getDoctrine()
					->getManager()
					->getRepository("AdminBundle:Issuetitlemaster")
					->findBy(array("is_deleted"=>0,"main_issue_title_master_id"=>$id));
			if(!empty($list)){
				foreach($list as $k=>$v){
					$em = $this->getDoctrine()->getManager();
					$update = $em->getRepository("AdminBundle:Issuetitlemaster")->find($v->getIssue_title_master_id());
					$update->setIs_deleted(1);
					$em->flush();
				}
			}
			$this->get('session')->getFlashBag()->set('success_msg','Issue_title Deleted Successfuly!');
			return $this->redirect($this->generateUrl('admin_issuetitle_issuetitlelist'));
		}
	}
}