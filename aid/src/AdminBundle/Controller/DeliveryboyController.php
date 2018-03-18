<?php

namespace AdminBundle\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\RouterInterface;

use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\PropertyAccess\PropertyAccess;



use AdminBundle\Entity\Usermaster;

use AdminBundle\Entity\Addressmaster;

use AdminBundle\Entity\Countrymaster;

use AdminBundle\Entity\Statemaster;

use AdminBundle\Entity\Citymaster;

use AdminBundle\Entity\Areamaster;

use AdminBundle\Entity\Usersetting;



/**

* @Route("/{domain}")

*/



class DeliveryboyController extends BaseController

{

	public function __construct()

    {

		parent::__construct();

        $obj = new BaseController();

		$obj->checkSessionAction();

    }

    /**

     * @Route("/deliveryboylist")

     * @Template()

     */

    public function indexAction()

    {

		$user_id=$this->get('session')->get('user_id');
    	if( $this->get('session')->get('role_id')== '1' or $this->get('session')->get('role_id')== '2')

    	{

			$deliverymanager_list = $this->getDoctrine()

						   ->getManager()

						   ->getRepository('AdminBundle:Usermaster')

						   ->findBy(array('user_role_id'=>6,'is_deleted'=>0));

		}else{

			$deliverymanager_list = $this->getDoctrine()

						   ->getManager()

						   ->getRepository('AdminBundle:Usermaster')

						   ->findBy(array('user_role_id'=>6,'is_deleted'=>0,"parent_user_id"=>$user_id));

		}

		return array("deliverymanager_list"=>$deliverymanager_list);

	}

	/**

     * @Route("/createdeliveryboy/{user_master_id}",defaults = {"user_master_id" = ""})

     * @Template()

     */

    public function createdeliveryboyAction($user_master_id)

    {

		
				$delivery_manager=null;	   
			$user_id=$this->get('session')->get('user_id');
		if( $this->get('session')->get('role_id')== '1'  or $this->get('session')->get('role_id')== '2')

		{

			
			$delivery_manager = $this->getDoctrine()

					   ->getManager()

					   ->getRepository('AdminBundle:Usermaster')

					   ->findBy(array('is_deleted'=>0,'user_role_id'=>3));
					  

		}
		else
		{
			$delivery_manager = $this->getDoctrine()
	
						   ->getManager()
	
						   ->getRepository('AdminBundle:Usermaster')
	
						   ->findBy(array('is_deleted'=>0,'user_role_id'=>3,"user_master_id"=>$user_id));
	
			
		}
		

		

		if(!empty($user_master_id))
		{

		
			$user_master = $this->getDoctrine()

			   ->getManager()

			   ->getRepository('AdminBundle:Usermaster')

			   ->findOneBy(array('user_master_id'=>$user_master_id,'is_deleted'=>0));

			   
			return array("user_master_id"=>$user_master_id,"user_master"=>$user_master,"delivery_manager"=>$delivery_manager);

			

		}

		

		return array("delivery_manager"=>$delivery_manager);

	}

	/**

     * @Route("/savedboy/{user_master_id}",defaults = {"user_master_id" = ""})

     */

    public function savedboyAction($user_master_id)

    {

		//button click
		$user_id=$this->get('session')->get('user_id');

			if(isset($_POST['save_dboy']) && $_POST['save_dboy'] == "save_dboy")

			{

				if(!empty($user_master_id))
				{

					if($_POST['first_name'] != "" && $_POST['last_name'] != "" && $_POST['email_address'] != "" && $_POST['username'] != "")

					{

						//image upload

						$media_id = 0;

						if(!empty($_FILES['image']))

						{

							$Config_live_site = $this->container->getParameter('live_path') ;

							$file_path = $this->container->getParameter('file_path');

				

							$file = $_FILES['image']['name'];

							// only profile image is allowed

							$extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

							//file extension check

							if($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg')

							{

								$tmpname = $_FILES['image']['tmp_name'];

								$path = $file_path.'/user/';

								$upload_dir = $this->container->getParameter('upload_dir').'/user/';

								$media_id = $this->mediauploadAction($file,$tmpname,$path,$upload_dir,1);

							}

						}

						

							

						$usermaster = $this->getDoctrine()

							->getManager()

							->getRepository('AdminBundle:Usermaster')

							->findOneBy(array('user_master_id'=>$user_master_id,'is_deleted'=>0));

							

						if(!empty($usermaster)){

							

							$usermaster->setUser_firstname($_POST['first_name']);

							$usermaster->setUser_lastname($_POST['last_name']);

							$usermaster->setUsername($_POST['username']);

							if(!empty($_POST['pass'])){

								if($_POST['pass'] == $_POST['confirm_pass']){	

									$usermaster->setPassword(md5($_POST['confirm_pass']));

									

								}

							}

							$usermaster->setUser_emailid($_POST['email_address']);

							if($media_id != 0){

								$usermaster->setUser_image($media_id);	

							

							}

						

							$usermaster->setUser_status($_POST['status']);

							$usermaster->setParent_user_id($_POST['shopadmin']);

							$em = $this->getDoctrine()->getManager();

							$em->persist($usermaster);

							$em->flush();			

						

						$this->get('session')->getFlashBag()->set('success_msg', $_POST['first_name']." ".$_POST['last_name']." saved successfully");	

						}

					}

				}

				else

				{

				//Validation check

				if($_POST['first_name'] != "" && $_POST['last_name'] != "" && !empty($_FILES['image']) && $_POST['email_address'] != "" && $_POST['username'] != "" && $_POST['confirm_pass'] != "" && $_POST['status'] != "")

				{

					//password check

					if($_POST['pass'] == $_POST['confirm_pass'])

					{

						$Config_live_site = $this->container->getParameter('live_path') ;

						$file_path = $this->container->getParameter('file_path');

			

						$file = $_FILES['image']['name'];

						// only profile image is allowed

						$extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

						//file extension check

						if($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg')

						{

							$tmpname = $_FILES['image']['tmp_name'];

							$path = $file_path.'/user/';

							$upload_dir = $this->container->getParameter('upload_dir').'/user/';

							

							$media_id = $this->mediauploadAction($file,$tmpname,$path,$upload_dir,1);

							//media upload check

							if($media_id != "FALSE")

							{

								$usermaster = new Usermaster();

								$usermaster->setUser_role_id(6);

								$usermaster->setUser_firstname($_POST['first_name']);

								$usermaster->setUser_lastname($_POST['last_name']);

								$usermaster->setUsername($_POST['username']);

								$usermaster->setPassword(md5($_POST['confirm_pass']));

								$usermaster->setUser_emailid($_POST['email_address']);

								$usermaster->setUser_image($media_id);

							

								$usermaster->setUser_status($_POST['status']);

								

								$usermaster->setDomain_id("aid001");

						

								$usermaster->setParent_user_id($_POST['shopadmin']);

								$usermaster->setCreated_by($this->get('session')->get('user_id'));

								$usermaster->setCreated_datetime(date("Y-m-d H:i:s"));

								$usermaster->setLast_modified(date("Y-m-d H:i:s"));

								$usermaster->setLast_login(date("Y-m-d H:i:s"));

								$usermaster->setLogin_from('other');

								$usermaster->setIs_deleted(0);

								$em = $this->getDoctrine()->getManager();

								$em->persist($usermaster);

								$em->flush();

								$user_master_id = $usermaster->getUser_master_id();

								

								

								$this->get('session')->getFlashBag()->set('success_msg', $_POST['first_name']." ".$_POST['last_name']." saved successfully");	

							}

							//media upload check else part

							else

							{
								$this->get('session')->getFlashBag()->set('success_msg', "image can not be uploading");	
							}

						}

						//file extension check else part

						else

						{
							$this->get('session')->getFlashBag()->set('success_msg', "extension not matched");	

						}

					}

					//password check else part

					else

					{
						$this->get('session')->getFlashBag()->set('success_msg', "password and confirm password not matched");	
					}

				}

				//Validation check else part

				else

				{
						$this->get('session')->getFlashBag()->set('success_msg', "please fill all entry");	
				}

			}

			return $this->redirect($this->generateUrl('admin_deliveryboy_index',array("domain"=>$this->get('session')->get('domain'))));

		}

	}

	

	/**

     * @Route("/delboyuser/{user_master_id}",defaults = {"user_master_id" = ""})

     */

    public function deluserAction($user_master_id)

    {

		if(!empty($user_master_id)){

			$usermaster = $this->getDoctrine()

					->getManager()

					->getRepository('AdminBundle:Usermaster')

					->findOneBy(array('user_master_id'=>$user_master_id,'is_deleted'=>0));

					

				if(!empty($usermaster)){

					$usermaster->setIs_deleted(1);

					$em = $this->getDoctrine()->getManager();

					$em->persist($usermaster);

					$em->flush();

				}

			$this->get('session')->getFlashBag()->set('success_msg', "User Deleted successfully");	

			return $this->redirect($this->generateUrl('admin_deliveryboy_index',array("domain"=>$this->get('session')->get('domain'))));

		}

	}

	



	

}