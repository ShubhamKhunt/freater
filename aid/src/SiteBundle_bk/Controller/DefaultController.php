<?php

namespace SiteBundle\Controller;

use SiteBundle\Controller\BaseController as Base;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Usermaster;
use AdminBundle\Entity\Accesstoken;
use AdminBundle\Entity\Medialibrarymaster;

class DefaultController extends BaseController
{	
    /**
     * @Route("/")
	 * @Template()
     */
    public function indexAction()
    {
		// get category list for slider
		$category_list = $this->getCategoryCollection();
		
		// get product list for slider
		$product_list = $this->getProductCollection();
		
		// get brand list for slider
		$brand_list = $this->getBrandCollection();
		return array(
				'category_list' => $category_list,
				'product_list' => $product_list,
				'brand_list' => $brand_list
			);
    }
	
	/**
     * @Route("/login")
	 * @Template()
     */
    public function loginAction()
    {
		if(Base::isLoggedIn()){
			return $this->redirectToRoute('site_default_index');
		}
		return;
    }
	
	/**
     * @Route("/logincheck")
	 * @Template()
     */
    public function logincheckAction(Request $request)
    {
		$session = $this->get('session');
		
		$username = $request->get('username');
		$password = $request->get('password');
		if(isset($username) && $username != '' && isset($password) && $password != ''){
			
			## get user
			$repository = $this->getDoctrine()->getRepository(Usermaster::class);
			$user = $repository->findOneBy([
				'username' => $request->get('username'),
				'password' => sha1($request->get('password'))
			]);
			
			if(!empty($user)){
				
				## remove existing tokens
				$token_repository = $this->getDoctrine()->getRepository(Accesstoken::class);
				$access_tokens = $token_repository->findBy(
					array(
						'user_id' => $user->getUser_master_id(),
						'token_status' => 'live'
					)
				);
				
				if(!empty($access_tokens)){
					$em = $this->getDoctrine()->getManager();
					foreach($access_tokens as $_token){
						$token = $this->getDoctrine()->getRepository(Accesstoken::class)->find($_token->getAccess_token_id());
						
						$em->remove($token);
						$em->flush();
					}
				}
				
				## generate a new token
				$accesstoken = new Accesstoken();
				$accesstoken->setUser_id($user->getUser_master_id());
				$accesstoken->setAccess_token(sha1($user->getUser_emailid()));
				$accesstoken->setToken_status('live'); ## live, expired
				$accesstoken->setCreated_datetime(date('Y-m-d h:i:s'));
				
				$em = $this->getDoctrine()->getManager();
				$em->persist($accesstoken);
				$em->flush();
				
				## set the session
				$session->set('user_id',$user->getUser_master_id());
				$session->set('access_id', $accesstoken->getAccess_token_id());
				$session->set('access_token', sha1($user->getUser_emailid()));
				$session->set('role_id',$user->getUser_role_id());
				$session->set('username',$user->getUsername());
				$session->set('firstname',$user->getUser_firstname());
				$session->set('lastname',$user->getUser_lastname());
				$session->set('email',$user->getUser_emailid());
				$session->set('user_profile',$user->getUser_image_url());
				$session->set('login_status', 'logged_in');
				
				return $this->redirectToRoute('site_default_index');
			} else {
				$session->getFlashBag()->add('error','Username and Password does not match');
			}
		} else if($request->get('social_accesstoken')) {
			
			echo $request->get('social_accesstoken');exit;
			
		} else {
			$session->getFlashBag()->add('error','Please enter username and password');
		}
		
		$referer = $request->headers->get('referer');
		return $this->redirect($referer);
    }
	
	/**
     * @Route("/logout")
	 * @Template()
     */
    public function logoutAction()
    {
		$session = $this->get('session');
		if($session->all()){
			
			## remove token
			if($session->has('access_id')){
				$token = $this->getDoctrine()->getRepository('AdminBundle:Accesstoken')->find($session->get('access_id'));
				
				if(!empty($token)){
					$em = $this->getDoctrine()->getManager();
					$em->remove($token);
					$em->flush();
				}
			}
			
			$ses_vars = $session->all();
			foreach ($ses_vars as $key => $value) {
				$session->remove($key);
			}
			$session->getFlashBag()->add('success','Logout Successfully');
			$session->set('login_status', 'logout');
		} else {
			$session->getFlashBag()->add('error','Something went wrong');
		}
		
		return $this->redirectToRoute('site_default_login');
	}
	
	/**
     * @Route("/start-session/{access_token}", defaults={"access_token":""})
     */
    public function startSessionAction($access_token)
    {
		$already_logged = false;
		$session = $this->get('session');
		if(isset($access_token) && $access_token != ''){
			
			$repository = $this->getDoctrine()->getRepository(Accesstoken::class);
			$accesstoken = $repository->findOneBy([
				'access_token' => $access_token,
				'token_status' => 'live'
			]);
			
			if(!empty($accesstoken)){
				
				$user = $this->getDoctrine()->getRepository(Usermaster::class)->find($accesstoken->getUser_id());
				
				if(!empty($user)){
					$already_logged = true;
					
					$session->set('access_id', $accesstoken->getAccess_token_id());
					$session->set('access_token', sha1($user->getUser_emailid()));
					$session->set('user_id',$user->getUser_master_id());
					$session->set('role_id',$user->getUser_role_id());
					$session->set('username',$user->getUsername());
					$session->set('firstname',$user->getUser_firstname());
					$session->set('lastname',$user->getUser_lastname());
					$session->set('email',$user->getUser_emailid());
					$session->set('user_profile',$user->getUser_image_url());
					$session->set('login_status', 'logged_in');
				}
			}
		}
		
		if($already_logged == false){
			$session->set('login_status', 'logout');
		}
		
		return $this->redirectToRoute('site_default_index');
	}
	
	/**
     * @Route("/register")
	 * @Template()
     */
    public function registerAction(Request $request)
    {
		if($request->request->all()){
			
			$media_id = '';
			$media_name = $_FILES['image']['name'];
			if(isset($media_name) && $media_name !=''){
				$upload_dir = $this->container->getParameter('upload_dir');
				$media_name = date('Y_m_d_h_i_s').'_'.$media_name;
				$is_upload = move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir.'/user/'.$media_name);
				if($is_upload){
					
					$media = new Medialibrarymaster();
					$media->setMedia_type_id(1);
					$media->setMedia_title($media_name);
					$media->setMedia_location('/bundles/design/user');
					$media->setMedia_name($media_name);
					$media->setCreated_on(date('Y-m-d h:i:s'));
					$media->setIs_deleted(0);
					
					$em = $this->getDoctrine()->getManager();
					$em->persist($media);
					$em->flush();
					
					$media_id = $media->getMedia_library_master_id();
					$media_url = $this->container->getParameter('live_path')."/bundles/design/user/{$media_name}";
				}
			} else {
				$media_url = $this->container->getParameter('live_path')."/bundles/design/user/default-user.png";
			}
			
			$user = new Usermaster();
			$user->setUser_role_id(7); ## 7 for general user [customer]
			$user->setUsername($request->get('username'));
			$user->setPassword(sha1($request->get('password')));
			$user->setUser_firstname($request->get('firstname'));
			$user->setUser_lastname($request->get('lastname'));
			$user->setUser_mobile($request->get('mobile'));
			$user->setUser_emailid($request->get('email'));
			if(isset($media_id) && $media_id != ''){
				$user->setUser_image($media_id);
				$user->setUser_image_url($media_url);
			}
			$user->setUser_status('active');
			$user->setCreated_datetime(date('Y-m-d h:i:s'));
			$user->setIs_deleted(0);
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();
			
			$user_id = $user->getUser_master_id();
			
			if(isset($user_id) && $user_id != ''){
				$session = $this->get('session');
				$session->set('user_id', $user_id);
				$session->set('username', $request->get('username'));
				$session->set('firstname', $request->get('firstname'));
				$session->set('lastname', $request->get('lastname'));
				$session->set('email', $request->get('email'));
				$session->set('user_profile', $media_url);
				
				return $this->redirectToRoute('site_default_index');
			}
		} else {
			$this->get('session')->getFlashBag()->add('error','Something went wrong');
		}
		return;
    }
}
