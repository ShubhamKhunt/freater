<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Feedbackmaster;
use AdminBundle\Entity\Domainmaster;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;



/**
* @Route("/{domain}")
*/

 
class FeedbackController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
	
    /**
     * @Route("/feedbacklist/{domain_id}",defaults={"domain_id":""})
     * @Template()
     */
    public function indexAction($domain_id)
    {
		$feedback = '';
		$all_feedback_details = '';
		if($this->get('session')->get('role_id')== '1')
    	{
			$feedback = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Feedbackmaster')
					   ->findBy(array('is_deleted'=>0));
		}else{			
			$feedback = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Feedbackmaster')
					   ->findBy(array('is_deleted'=>0,'domain_id'=>$this->get('session')->get('domain_id')));
		}
		
		
		if(count($feedback) > 0 ){
			foreach(array_slice($feedback,0) as $fkey=>$fval){
				$user_name = 'User Not Found ' ;
				
				if($fval->getUser_master_id() != 0 && !empty($fval->getUser_master_id())){
					$user_name = 'User Not Found ' ;
					$user_master = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:Usermaster')
							   ->findOneBy(array('is_deleted'=>0,'user_master_id'=>$fval->getUser_master_id()));
					if(!empty($user_master)){
						$user_name = $user_master->getUser_firstname();
					}
					
				}
				
				$order_number = 'Order Not Found ' ;
				if($fval->getOrder_id() != 0 && !empty($fval->getOrder_id())){
					$order_number = 'Order Not Found ' ;
					$order_master = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:Ordermaster')
							   ->findOneBy(array('is_deleted'=>0,'order_master_id'=>$fval->getOrder_id()));
					if(!empty($order_master)){
						$order_number = $order_master->getUnique_no();
					}
					
				}
				
				$all_feedback_details[] = array(
					"feedback_master_id"=>$fval->getFeedback_master_id(),
					"order_id"=>$fval->getOrder_id(),
					"order_unique_no"=>$order_number,
					"customer_service_name"=>$fval->getCustomer_service_name(),
					"message"=>$fval->getMessage(),
					"ratings"=>$fval->getRatings(),
					"user_master_id"=>$fval->getUser_master_id(),
					"user_name"=>$user_name,
					"domain_id"=>$fval->getDomain_id(),
					"create_date"=>$fval->getCreate_date(),
					"is_deleted"=>	$fval->getIs_deleted(),
				);
				
			}
			
		}
		/*var_dump($all_feedback_details);exit;*/
		 return array("feedbacklist"=>$all_feedback_details);
		//var_dump($all_category_details);exit;
       
    }
	
	/**
     * @Route("/deletefeedback/{feedback_master_id}",defaults={"feedback_master_id":""})
     * @Template()
     */
    public function deletefeedbackAction($feedback_master_id)
    {
		if($feedback_master_id != '0'){
			$feedback_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Feedbackmaster')
					   ->findOneBy(array('is_deleted'=>0,'feedback_master_id'=>$feedback_master_id));
			if(!empty($feedback_master)){
				$feedback_master->setIs_deleted(1);
				$em = $this->getDoctrine()->getManager();
				$em->persist($feedback_master);
				$em->flush();
			}
		}
		$this->get("session")->getFlashBag()->set("success_msg","Feedback Deleted successfully");
	    return $this->redirect($this->generateUrl("admin_feedback_index",array("domain"=>$this->get('session')->get('domain'))));
	}
}