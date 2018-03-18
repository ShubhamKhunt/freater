<?php
namespace AdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AdminBundle\Entity\Usermaster;
use AdminBundle\Entity\Productcategoryrelation;
use AdminBundle\Entity\Productmaster;
use AdminBundle\Entity\Combination;
use AdminBundle\Entity\Combinationrelation;
use AdminBundle\Entity\Gallerymaster;
use AdminBundle\Entity\Medialibrarymaster;
use AdminBundle\Entity\Productsupplierrelation;
use AdminBundle\Entity\Supplierattributerelation;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;
/**
* @Route("/admin")
*/class ChangepasswordController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
		$arr = array();
	}    /**
     * @Route("/changepassword")
     * @Template()
     */
    public function indexAction()
    {
		$request = Request::createFromGlobals();
		$request->query->get('oldpass');
		if($request->query->get('updatepass')=="Change")
		{
			if(!empty($request->query->get('oldpass')) && !empty($request->query->get('newpass')) && !empty($request->query->get('confpass')))
			{
				$domain_id = $this->get('session')->get('domain_id');
				$user_id = $this->get('session')->get('user_id');
				$query = "SELECT count(*) as cnt FROM `user_master` WHERE is_deleted=0 and domain_id='".$domain_id."' and user_master_id='".$user_id."' and password=MD5('".$request->query->get('oldpass')."')";
				$em = $this->getDoctrine()->getManager();
				$connection = $em->getConnection();
				$statement = $connection->prepare($query);
				$statement->execute();				
				$user_list = $statement->fetchAll();
				if($user_list[0]['cnt']!=0)
				{
					if($request->query->get('newpass') == $request->query->get('confpass'))
					{
						$query = "UPDATE `user_master`  SET `password` = MD5('".$request->query->get('newpass')."'),show_password='".$request->query->get('newpass')."' WHERE is_deleted=0 and domain_id='".$domain_id."' and `user_master_id` = '".$user_id."'";
						$connection = $em->getConnection();
						$statement = $connection->prepare($query);						
						if($statement->execute())
						{
							$this->get("session")->getFlashBag()->set("success_msg","Your Password has been Changed Successfully");
							return $this->redirect($this->generateUrl("admin_changepassword_index"));
						}
						$this->get("session")->getFlashBag()->set("error_msg","Something wrong!");
						return $this->redirect($this->generateUrl("admin_changepassword_index"));					}
					$this->get("session")->getFlashBag()->set("error_msg","Your New password and Confirm password doesn't match!");
					return $this->redirect($this->generateUrl("admin_changepassword_index"));
				}
				else
				{
					$this->get("session")->getFlashBag()->set("error_msg","Your Old Password is wrong. Please Enter Correct Password!");
					
					return $this->redirect($this->generateUrl("admin_changepassword_index"));
				}
				$this->get("session")->getFlashBag()->set("error_msg","Something Wrong!");
				return $this->redirect($this->generateUrl("admin_changepassword_index"));
			}
			$this->get("session")->getFlashBag()->set("error_msg","All the fields are mandatory");
			return $this->redirect($this->generateUrl("admin_changepassword_index"));
		}
    }
}