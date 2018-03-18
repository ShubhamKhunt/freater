<?php
namespace AdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\RouterInterface;

use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\PropertyAccess\PropertyAccess;



use AdminBundle\Entity\Generalnotification;



/**

* @Route("/{domain}")

*/



class PushnotificationController extends BaseController

{

	public function __construct()

    {

		parent::__construct();

        $obj = new BaseController();

		$obj->checkSessionAction();

    }

    /**

     * @Route("/pushnotification/{domain_id}",defaults={"domain_id":""})

     * @Template()

     */

    public function indexAction($domain_id)

    {

    	if($this->get('session')->get('role_id')== '1')

    	{

	    	$note_list = $this->getDoctrine()

					   ->getManager()

					   ->getRepository('AdminBundle:Generalnotification')

					   ->findBy(array('is_deleted'=>0,'notification_type'=>'general'));

			$health_list = $this->getDoctrine()

					   ->getManager()

					   ->getRepository('AdminBundle:Generalnotification')

					   ->findBy(array('is_deleted'=>0,'notification_type'=>'healthtip'));

		}else{

			$note_list = $this->getDoctrine()

					   ->getManager()

					   ->getRepository('AdminBundle:Generalnotification')

					   ->findBy(array('is_deleted'=>0,'notification_type'=>'general','domain_id'=>$this->get('session')->get('domain_id')));

			$health_list = $this->getDoctrine()

					   ->getManager()

					   ->getRepository('AdminBundle:Generalnotification')

					   ->findBy(array('is_deleted'=>0,'notification_type'=>'healthtip','domain_id'=>$this->get('session')->get('domain_id')));

		}

				   

		return array("note_list"=>$note_list,"health_list"=>$health_list);

	}

	/**

     * @Route("/addpushnotification")

     * @Template()

     */

    public function addpushnotificationAction()

    {

		$country_list = $this->getDoctrine()

				   ->getManager()

				   ->getRepository('AdminBundle:Countrymaster')

				   ->findBy(array('status'=>'active','language_id'=>1,'is_deleted'=>0));





		 $em = $this->getDoctrine()->getManager();

		$connection = $em->getConnection();

		$statement = $connection->prepare("SELECT service_city_relation.*,city_master.city_name FROM service_city_relation JOIN city_master ON service_city_relation.main_city_id = city_master.city_master_id WHERE city_master.language_id = 1 AND city_master.status = 'active' AND city_master.is_deleted = 0 AND service_city_relation.status = 'active' AND service_city_relation.is_deleted = 0 AND service_city_relation.domain_id = '".$this->get('session')->get('domain_id')."'");

		$statement->execute();

		$service_city_list = $statement->fetchAll();

		return array("country_list"=>$country_list,"city_list"=>$service_city_list);

	}

	/**

     * @Route("/sendnotification")

     */

    public function sendnotificationAction()
	{
		if(isset($_POST['send_notification']) && $_POST['send_notification'] == "send_notification")
		{
			if($_POST['note_title'] != "" && $_POST['note_message'] != "")
			{
				if(!empty($_POST['notification_type']) && $_POST['notification_type']=='healthtip')
				{
					$code = '7';
				}
				elseif(!empty($_POST['notification_type']) && $_POST['notification_type']=='app_alert')
				{
					$code = '11';
				}
				else
				{
					$code = '10';
				}
				$send_to = $_POST['send_to'];
				$media_id='FALSE';
				if(!empty($_FILES['image']))
				{
					$Config_live_site = $this->container->getParameter('live_path') ;
					$file_path = $this->container->getParameter('file_path');
					$file = $_FILES['image']['name'];					// only profile image is allowed
					$extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
					if($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg')
					{
						$tmpname = $_FILES['image']['tmp_name'];
						$path = $file_path.'/uploads/notification';
						$upload_dir = $this->container->getParameter('upload_dir').'/uploads/notification/';
						$media_id = $this->mediauploadAction($file,$tmpname,$path,$upload_dir,1);
						$Config_live_site = $this->container->getParameter('live_path') ;
						//media upload check

						if($media_id != "FALSE")
						{

							$media_library = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:Medialibrarymaster')
							   ->findOneBy(array('media_library_master_id'=>$media_id,'is_deleted'=>0));

							   $response = array(

									"notification_title"=>$_POST['note_title'],

									"notification_image"=>$Config_live_site.$media_library->getMedia_location()."/".$media_library->getMedia_name()

								);

								

								$message = json_encode(array("detail"=>$_POST['note_message'],"code" => $code, "response" => $response));

								

						}

						else

						{

							$message = json_encode(array("detail"=>$_POST['note_message'],"code" => $code, "response" => $_POST['note_title']));

						}

					}

					else

					{

						$message = json_encode(array("detail"=>$_POST['note_message'],"code" => $code, "response" => $_POST['note_title']));

					}

					

				}

				else
				{
					$message = json_encode(array("detail"=>$_POST['note_message'],"code" => $code, "response" => $_POST['note_title']));
				}
				$general_notification = new Generalnotification();
				$general_notification->setNotification_type($_POST['notification_type']);
				$general_notification->setTitle($_POST['note_title']);
				$general_notification->setMessage($_POST['note_message']);
				if(!empty($_FILES['image']))
				{
					if($media_id != "FALSE")
					{
						$general_notification->setImage_id($media_id);
					}
				}
				$general_notification->setUser_master_id(0);
				$general_notification->setSend_to($_REQUEST['send_to']);
				if(!empty($this->get('session')->get('domain_id')))
    			{
					$general_notification->setDomain_id($this->get('session')->get('domain_id'));
				}
				$general_notification->setCreate_date(date("Y-m-d H:i:s"));
				$general_notification->setIs_deleted(0);
				$em = $this->getDoctrine()->getManager();
				$em->persist($general_notification);
				$em->flush();		

				$notification_id_send = $general_notification->getGeneral_notification_id();			

				/*

				* 1 = Customers

				* 2 = Delivery Boy

				*/

				$domain_id = "";

				$app_id = $send_to;

				if(!empty($this->get('session')->get('domain_id')))

    			{

    				$domain_id = $this->get('session')->get('domain_id');

    			}

				$user_array = array();

				

				

				if(isset($_POST['user']) && !empty($_POST['user'])) 
				{
					foreach($_POST['user'] as $check) 
					{
							$user_array[] = $check;
					}
				}

		
				

				if($send_to == "CUST")
				{if(isset($_POST['sendall']))  // not in use now
					{
						if(!empty($this->get('session')->get('domain_id')))
    					{
    						if($this->get('session')->get('role_id')== '1')
    						{

								$user_master = $this->getDoctrine()

								   ->getManager()

								   ->getRepository('AdminBundle:Usermaster')

								   ->findBy(array("user_role_id"=>7,"user_status"=>"active","user_type"=>"user","is_deleted"=>0));

								   //"user_type"=>"user"

							

								foreach($user_master as $key=>$val)

								{

										

									$user_array[] = $val->getUser_master_id();

								}

								    	

								

							}

							else

							{

								$user_master = $this->getDoctrine()

									   ->getManager()

									   ->getRepository('AdminBundle:Usermaster')

									   ->findBy(array("user_role_id"=>7,"user_status"=>"active","user_type"=>"user","is_deleted"=>0,"domain_id"=>$this->get('session')->get('domain_id')));

									  //"user_type"=>"user" 

								

								foreach($user_master as $key=>$val)

								{

									$user_array[] = $val->getUser_master_id();

								}

							}

							

						}

					}

				}
				if($send_to == "DEL")
				{

					if(isset($_POST['sendall']))

					{

						if(!empty($this->get('session')->get('domain_id')))

    					{

    						if($this->get('session')->get('role_id')== '1')

    						{

								$user_master = $this->getDoctrine()

								   ->getManager()

								   ->getRepository('AdminBundle:Usermaster')

								   ->findBy(array("user_role_id"=>6,"user_status"=>"active","user_type"=>"user","is_deleted"=>0));

								   

							//"user_type"=>"user"

								foreach($user_master as $key=>$val)

								{

									$user_array[] = $val->getUser_master_id();

								}	

							}

							else

							{

								$user_master = $this->getDoctrine()

									   ->getManager()

									   ->getRepository('AdminBundle:Usermaster')

									   ->findBy(array("user_role_id"=>6,"user_status"=>"active","user_type"=>"user","is_deleted"=>0,"domain_id"=>$this->get('session')->get('domain_id')));

									//"user_type"=>"user"   

								

								foreach($user_master as $key=>$val)

								{

									$user_array[] = $val->getUser_master_id();

								}

							}

							

						}

					}

				}

				$gcm_regids = $this->find_gcm_regid($user_array);

				

				if(!empty($gcm_regids))
				{				
					if (count($gcm_regids)>0)
					{
						$this->send_notification($gcm_regids,$_POST['note_title'],$message,2,$app_id,$domain_id,"general_notification",$notification_id_send);					

					}

				}
				

						

				$apns_regids = $this->find_apns_regid($user_array);

				
				
				if(!empty($apns_regids))

				{

					if (count($apns_regids[0])>0)

					{

					$this->send_notification($apns_regids,$_POST['note_title'],$message,1,$app_id,$domain_id,"general_notification",$notification_id_send);

					}

				}

				

				$this->get('session')->getFlashBag()->set('success_msg', "Notification sent successfully");	

				return $this->redirect($this->generateUrl('admin_pushnotification_index',array("domain"=>$this->get('session')->get('domain'))));



			}

			else

			{

				$this->get('session')->getFlashBag()->set('error_msg', "Notification title and message is required");	

			}

		}

		else

		{

			$this->get('session')->getFlashBag()->set('error_msg', 'Oops! Something goes wrong! Try again later');

		}

		return $this->redirect($this->generateUrl('admin_pushnotification_addpushnotification',array("domain"=>$this->get('session')->get('domain'))));

	}

	/**

     * @Route("/getuserlist")

     */

    public function getuserlistAction()
    {
		$html = "";
    	if(isset($_POST['flag']) && $_POST['flag'] == 'getuser' && $_POST['user_type'] != "")
    	{

			$user_type = $_POST['user_type'];
			$city_id = !empty($_POST['city_id'])?$_POST['city_id']:0;
			$user_list = array();
			if($user_type == 'CUST')
			{

				$user_list = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:Usermaster')
							   ->findBy(array("user_role_id"=>7,"user_status"=>"active","is_deleted"=>0,"domain_id"=>$this->get('session')->get('domain_id'),"user_type"=>"user"));

			}

			if($user_type == 'DEL')

			{

				$user_list = $this->getDoctrine()

							   ->getManager()

							   ->getRepository('AdminBundle:Usermaster')

							   ->findBy(array("user_role_id"=>6,"user_status"=>"active","is_deleted"=>0,"domain_id"=>$this->get('session')->get('domain_id'),"user_type"=>"user"));

			}

			

			if(!empty($user_list))

			{

				

				$html .= '<label class="col-sm-2 control-label">&nbsp;</label> 

								<div class="col-md-10">

									<div class="box box-success box-solid">

										<div class="box-header with-border">

											<h3 class="box-title">Users list</h3>

											<input type="checkbox" id="checkAll" class="checkbox pull-right"/>

										</div>

										<div class="box-body" id="userlistbox">';

				foreach($user_list as $key=>$val)

				{

					if(isset($city_id) && !empty($city_id))

					{

							$address_id  = 0;

									$order_master_id = "";

									$em = $this->getDoctrine()->getManager();

										$connection = $em->getConnection();

										$statement = $connection->prepare("select delivery_address_id from order_master where order_createdby='".$val->getUser_master_id()."' order by `order_master_id` desc LIMIT 0,1");

										$statement->execute();

										$order_master_id = $statement->fetchAll();

										if(isset($order_master_id) && !empty($order_master_id))

										{

											$address_id = $order_master_id[0]['delivery_address_id'];

										}

										else

										{

											if($val->getAddress_master_id()=="0")

											{

												//echo "test";

												continue;

											}else{

												$address_id = $val->getAddress_master_id();	

											}

											

										}

										$address_master_id ="";

											$connection = $em->getConnection();

										$statement = $connection->prepare("select `city_id` from address_master where `address_master_id`='".$address_id."'");

										$statement->execute();

										$address_master_id = $statement->fetchAll();

										if(isset($address_master_id) && !empty($address_master_id))

										{

											$get_city_id = $address_master_id[0]['city_id'];

											//if($address_master_id[0]['city_id'])

										}

										if($get_city_id==$city_id){

											$mono = $this->keyDecryptionAction($val->getUser_mobile());
											if($mono != '' && $mono != 0 && $mono != NULL){
												$html .= '<div class="col-md-3"><input type="checkbox" name="user[]" class="checkBoxClass" id="mychk" value="'.$val->getUser_master_id().'"> '.$mono.'&emsp;</div>';
											}
										}

					}

					else

					{

											$mono = $this->keyDecryptionAction($val->getUser_mobile());

											$html .= '<input type="checkbox" name="user[]" class="checkBoxClass" id="mychk" value="'.$val->getUser_master_id().'"> '.$mono.'&emsp;';

					}

				}

				$html .= '</div>

									</div>

								</div>';

				$html .= "<script>$('#checkAll').change(function () {

							$('input:checkbox').prop('checked', $(this).prop('checked'));

						});</script>";

			}

			

		}

		echo $html;
		return new Response;
    }

}