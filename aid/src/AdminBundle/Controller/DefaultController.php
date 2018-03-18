<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
* @Route("/admin")
*/
class DefaultController extends BaseController
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction(){
		return array();
    }
	
	/**
     * @Route("/logincheck")
     * @Template()
     */
    public function logincheckAction(){
		if(isset($_POST['_csrf_token']) && $_POST['_csrf_token'] != "")
		{
			if($_POST['username'] != "" && $_POST['password'] != "" && isset($_REQUEST['domain']) && $_REQUEST['domain'] != "")
			{
				$username = $this->bwiz_security($_POST['username']);
				$password = $this->bwiz_security($_POST['password']);
					$user = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Usermaster')
					   ->findOneBy(array('username'=>$username,'password'=>md5($password),'user_status'=>'active','is_deleted'=>0));
				
				$domain_check ='';
				// check domain ---
				if(!empty($user)){
						$domain_check = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Domainmaster')
						   ->findOneBy(array('domain_name'=>$this->bwiz_security($_POST['domain']),'is_deleted'=>'0'));
						 
				}
			
				
				if(!empty($user) && count($user) == 1 && $user != "" && $user != NULL && count($domain_check) > 0 && ($domain_check->getDomain_code() == $user->getdomain_id()))
				{
						$user_image=$user->getUser_image();
					
							$image_details = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Medialibrarymaster')->findOneBy(array('media_library_master_id'=>$user_image));
						$image = '';
						if(!empty($image_details))
						{
							$user_image = $this->container->getParameter('live_path').$image_details->getMedia_location()."/".$image_details->getMedia_name();
						}
						else
						{
							$user_image = $this->container->getParameter('live_path').'/bundles/design/images/logo.png';
						}
										
					
					$this->get('session')->set('role_id',$user->getUser_role_id());
					$this->get('session')->set('user_id',$user->getUser_master_id());
					$this->get('session')->set('domain_id',$user->getDomain_id());
					$this->get('session')->set('username',$user->getUsername());
					$this->get('session')->set('userimage',$user_image);
					$this->get('session')->set("domain",$_REQUEST['domain']);
					$this->get('session')->set("domain_logo_id",$domain_check->getDomain_logo_id());
					$this->get('session')->set("domain_cover_image_id",$domain_check->getDomain_logo_id());
					$this->get('session')->getFlashBag()->set('success_msg', 'Login successfully');	
					return $this->redirect($this->generateUrl('admin_dashboard_index'));
				}
				else
				{
					$this->get('session')->getFlashBag()->set('error_msg', 'Username or password or Domain is wrong');
				}
			}
			else
			{
				$this->get('session')->getFlashBag()->set('error_msg', 'Username and password is required');
			}
			//return array();
		}
		else
		{
			$this->get('session')->getFlashBag()->set('error_msg', 'Oops! Something goes wrong! Try again later');
		}
		return $this->redirect($this->generateUrl('admin_default_index'));
    }
	
	
	/**
     * @Route("/logout")
     * @Template()
     */
    public function logoutAction()
    {
		$this->get('session')->remove('role_id');
		$this->get('session')->remove('user_id');
		$this->get('session')->remove('domain_id');
		$this->get('session')->remove('username');
		$this->get('session')->remove('domain');
		
		return $this->redirect($this->generateUrl('admin_default_index'));
	}
}