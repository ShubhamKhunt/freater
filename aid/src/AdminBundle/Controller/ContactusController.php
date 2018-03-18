<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Reportproblem;
use AdminBundle\Entity\Domainmaster;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;


/**
* @Route("/admin")
*/
class ContactusController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }


    /**
     * @Route("/contactus/{domain_id}",defaults={"domain_id":""})
     * @Template()
     */
    public function indexAction($domain_id)
    {
		$report = '';
		$all_report_details = '';
		if($this->get('session')->get('role_id')== '1')
    	{
			$report = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Reportproblem')
					   ->findBy(array('is_deleted'=>0));

		}else{
			$report = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Reportproblem')
					   ->findBy(array('is_deleted'=>0));
		}

		if(count($report) > 0 ){
			foreach(array_slice($report,0) as $key=>$val){
				$user_name = 'User Not Found ' ;
				$delivery_user_name = 'User Not Found ' ;
				if($val->getUser_master_id() != 0 && !empty($val->getUser_master_id()))
				{
					$user_name = 'User Not Found ' ;
					$user_master = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:Usermaster')
							   ->findOneBy(array('is_deleted'=>0,'user_master_id'=>$val->getUser_master_id()));

					if(!empty($user_master)){
						$user_name = $user_master->getUser_firstname();
					}

					$order_number = 'Order Not Found ' ;
					if($val->getOrder_id() != 0 && !empty($val->getOrder_id())){
						$order_number = 'Order Not Found ' ;
						$order_master = $this->getDoctrine()
								   ->getManager()
								   ->getRepository('AdminBundle:Ordermaster')
								   ->findOneBy(array('is_deleted'=>0,'order_master_id'=>$val->getOrder_id()));

						if(!empty($order_master)){
							$order_number = $order_master->getUnique_no();
						}
					}

					$all_report_details[] = array(
						"report_problem_id"=>$val->getReport_problem_id(),
						"message"=>$val->getMessage(),
						"user_master_id"=>$val->getUser_master_id(),
						"user_name"=>$user_name,
						"order_id"=>$val->getOrder_id(),
						"order_unique_no"=>$order_number,
						"domain_id"=>$val->getDomain_id(),
						"create_date"=>$val->getCreate_date(),
						"is_deleted"=>$val->getIs_deleted(),
					);
				}
			}
		}

		return array("reportlist"=>$all_report_details);
    }

	/**
     * @Route("/deletecontactus/{report_problem_id}",defaults={"report_problem_id":""})
     * @Template()
     */
    public function deletecontactusAction($report_problem_id)
    {
		if($report_problem_id != '0'){
			$report_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Reportproblem')
					   ->findOneBy(array('is_deleted'=>0,'report_problem_id'=>$report_problem_id));

			if(!empty($report_master)){
				$report_master->setIs_deleted(1);
				$em = $this->getDoctrine()->getManager();
				$em->persist($report_master);
				$em->flush();
			}
		}
		
		$this->get("session")->getFlashBag()->set("success_msg","Contact Us Deleted successfully");
	    return $this->redirect($this->generateUrl("admin_contactus_index"));
	}
}