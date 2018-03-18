<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

use AdminBundle\Entity\Userrolemaster;
use AdminBundle\Entity\Rightmaster;
use AdminBundle\Entity\Rolemaster;
use AdminBundle\Entity\Rolerightrelation;

/**
* @Route("/{domain}")
*/

class UserroleController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
	
    /**
     * @Route("/userrole")
	 * @Template
     */
    public function indexAction()
    {
		$role_master = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Rolemaster')
				   ->findBy(array('is_deleted'=>0));
                   
		return array("role_list"=>$role_master);
    }
	
	/**
     * @Route("/addrole/{role_id}",defaults={"role_id":""})
	 * @Template
     */
    public function addroleAction($role_id)
    {
		$user_right_array = '' ;$role_master = '' ; $user_right = '' ;
		if($role_id != '' && $role_id != '0'){
			$role_master = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Rolemaster')
				   ->findOneBy(array('is_deleted'=>0,'role_master_id'=>$role_id));
			$user_right = $this->getDoctrine()
					->getManager()
					->getRepository('AdminBundle:Rolerightrelation')
					->findBy(array('role_master_id'=>$role_id,'is_access'=>1));
			foreach($user_right as $ukey=>$uval){
				$right_master = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Rightmaster')
				   ->findOneBy(array('is_deleted'=>0,'code'=>$uval->getCode()));
				$user_right_array[] = $right_master->getRight_master_id();
			}
			
		}
		
		$right_master = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Rightmaster')
				   ->findBy(array('is_deleted'=>0));
                   
		return array("role_master"=>$role_master,"right_master"=>$right_master,"user_right"=>$user_right_array);
    }
	
	/**
     * @Route("/userrole/saveuserrole/{role_id}",defaults={"role_id":""})
	 * @Template
     */
    public function saveuserroleAction($role_id)
    {
	
		if(isset($_REQUEST['save_role']) && $_REQUEST['save_role'] == 'Save'){
			//-----add role-------------------------------
			$role_master = new Rolemaster();
			$role_master->setRole_name($_REQUEST['name']);
			$role_master->setParent_id(0);
			$role_master->setDescription($_REQUEST['description']);
			$role_master->setStatus($_REQUEST['statusradio']);
			$role_master->setCreate_by($this->get('session')->get('user_id'));
			$role_master->setCreate_date(date("Y-m-d H:i:s"));
			$role_master->setIs_deleted(0);
			$em = $this->getDoctrine()->getManager();
			$em->persist($role_master);
			$em->flush();
			//-----add right per Role-------------------------------
			$right_master = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Rightmaster')
				   ->findBy(array('is_deleted'=>0));
			foreach($right_master as $rkey=>$rval){
				$role_right_relation = new Rolerightrelation();
				$role_right_relation->setRole_master_id($role_master->getRole_master_id());
				$role_right_relation->setCode($rval->getCode());
				if(!empty($_REQUEST['right_checkbox'] ) && in_array($rval->getRight_master_id() , $_REQUEST['right_checkbox'])){
					$role_right_relation->setIs_access(1);
				}
				else{
					$role_right_relation->setIs_access(0);
				}
				$em = $this->getDoctrine()->getManager();
				$em->persist($role_right_relation);
				$em->flush();				
			}
			return $this->redirect($this->generateUrl('admin_userrole_index',array("domain"=>$this->get('session')->get('domain'))));
		}
		elseif(isset($_REQUEST['update_role'])  && $_REQUEST['update_role'] == "Update"){
			
			$role_master = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Rolemaster')
				   ->findOneBy(array('is_deleted'=>0,'role_master_id'=>$role_id));
		
			$role_master->setRole_name($_REQUEST['name']);
			$role_master->setDescription($_REQUEST['description']);
			$role_master->setStatus($_REQUEST['statusradio']);
			$em = $this->getDoctrine()->getManager();
			$em->persist($role_master);
			$em->flush();
			
			//-----Update right per Role-------------------------------
			$right_master = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Rightmaster')
				   ->findBy(array('is_deleted'=>0));
			foreach($right_master as $rkey=>$rval){
				
				$role_right_relation = $this->getDoctrine()
							->getManager()
							->getRepository('AdminBundle:Rolerightrelation')
							->findOneBy(array('code'=>$rval->getCode(),'role_master_id'=>$role_id));
				if(empty($role_right_relation)){
					$role_right_relation = new Rolerightrelation();
				}				
				$role_right_relation->setRole_master_id($role_master->getRole_master_id());
				$role_right_relation->setCode($rval->getCode());
				if(in_array($rval->getRight_master_id() , $_REQUEST['rigth_checkbox'])){
					$role_right_relation->setIs_access(1);
				}
				else{
					$role_right_relation->setIs_access(0);
				}
				$em = $this->getDoctrine()->getManager();
				$em->persist($role_right_relation);
				$em->flush();				
			}
			
			
			return $this->redirect($this->generateUrl('admin_userrole_index',array("domain"=>$this->get('session')->get('domain'))));
		}
		
		
    }
	
	
	/**
     * @Route("/deleterole")
	 * @Template
     */
    public function deleteroleAction()
    {
		$right_master = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Rightmaster')
				   ->findBy(array('is_deleted'=>0));
                   
		return array("right_master"=>$right_master);
    }
}
