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
class ReportProblemController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }

    /**
     * @Route("/reportproblem/{domain_id}",defaults={"domain_id":""})
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
				$user_name = 'User Not Found';
				$delivery_user_name = 'User Not Found';
				
				if($val->getDelivery_boy_id() != 0 && !empty($val->getDelivery_boy_id()))
				{
					$delivery_user_name = 'User Not Found';
					$delivery_user_master = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:Usermaster')
							   ->findOneBy(array('is_deleted'=>0,'user_master_id'=>$val->getDelivery_boy_id()));

					if(!empty($delivery_user_master)){
						$delivery_user_name = $delivery_user_master->getUser_firstname();
					}

					$order_number = 'Order Not Found';

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
						"delivery_user_id"=>$val->getDelivery_boy_id(),
						"delivery_name"=>$delivery_user_name,
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
     * @Route("/deletereportproblem/{report_problem_id}",defaults={"report_problem_id":""})
     * @Template()
     */
    public function deletereportproblemAction($report_problem_id)
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

		$this->get("session")->getFlashBag()->set("success_msg","Report Problem Deleted successfully");
	    return $this->redirect($this->generateUrl("admin_reportproblem_index"));
	}
}