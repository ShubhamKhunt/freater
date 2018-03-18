<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;

use AdminBundle\Entity\Medialibrarymaster;
use AdminBundle\Entity\Apppushnotificationmaster;
use AdminBundle\Entity\Usermaster ;
use AdminBundle\Entity\Usersetting;
use AdminBundle\Entity\Categorymaster;
use AdminBundle\Entity\Apptypemaster;
use AdminBundle\Entity\Appdetails;
use AdminBundle\Entity\Productlogdetails;



class BaseController extends Controller
{

	public function __construct()
	{
		/*if($_SERVER["HTTPS"] != "on")
		{
			header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
			exit();
		}*/
		date_default_timezone_set("Asia/Calcutta");
	}

	/**
     * @Route("/checkSession")
     */
    function checkSessionAction()
    {
		$session = new Session();
        if($session->get('user_id') == '' && $session->get('role_id') == '' && $session->get('username') == '')
		{
            $hostname = $_SERVER["SERVER_NAME"];
			header("location:http://aidkuwait.com/app.php");
			exit;
        }
    }

	function mediauploadAction($file,$tmpname,$path,$upload_dir,$mediatype_id)
    {

		$clean_image = preg_replace('/\s+/', '', $file);
		$logo_name = date('Y_m_d_H_i_s').'_'.$clean_image;

		if(!file_exists($upload_dir))
		{
			mkdir($upload_dir,0777);
		}
		//logo upload check
		if(move_uploaded_file($tmpname,$upload_dir.$logo_name))
		{
			$medialibrarymaster = new Medialibrarymaster();

			$medialibrarymaster->setMedia_type_id($mediatype_id);
			$medialibrarymaster->setMedia_title($logo_name);
			$medialibrarymaster->setMedia_location($path);
			$medialibrarymaster->setMedia_name($logo_name);
			$medialibrarymaster->setCreated_on(date('Y-m-d H:i:s'));
			$medialibrarymaster->setIs_deleted(0);

			$em = $this->getDoctrine()->getManager();
			$em->persist($medialibrarymaster);
			$em->flush();
			$media_library_master_id = $medialibrarymaster->getMedia_library_master_id();
			return $media_library_master_id;
		}
		else
		{
			return FALSE;
		}
    }
	/**
     * @Route("/updatemedia")
     */
    function updatemediaAction($file,$tmpname,$upload_dir,$media_id)
    {

		$clean_image = preg_replace('/\s+/', '', $file);
		$logo_name = date('Y_m_d_H_i_s').'_'.$clean_image;
		if(!file_exists($upload_dir))
		{
			mkdir($upload_dir,0777);
		}
		//logo upload check
		if(move_uploaded_file($tmpname,$upload_dir.$logo_name))
		{
			$em = $this->getDoctrine()->getManager();
					$update_media = $em->getRepository('AdminBundle:Medialibrarymaster')->find($media_id);
					$update_media->setMedia_filename($logo_name);
					$em->flush();

			return $media_id;
		}
		else
		{
			return FALSE;
		}
    }

	function get_hirerachy($lang_id , $parent_hieraerchy_id ,$current_category_id)
	{
		$child_data="";
		$domain_id = $this->get('session')->get('domain_id');

		$single_category = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Categorymaster')
					   ->findOneBy(array('is_deleted'=>0,'main_category_id'=>$current_category_id,'domain_id'=>$domain_id,"language_id"=>$lang_id));

		$all_sub_category = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Categorymaster')
					   ->findBy(array('is_deleted'=>0,'parent_category_id'=>$single_category->getMain_category_id(),'domain_id'=>$domain_id,"language_id"=>$lang_id));

		if(count($all_sub_category) == 0 ){
			if(!empty($single_category)){
				$parent_category_name = $single_category->getCategory_name();
			}
			$data = array(
				"category_master_id"=>$single_category->getCategory_master_id(),
				"category_name"=>$single_category->getCategory_name(),
				"parent_category_id"=>$single_category->getParent_category_id(),
				"parent_category_name"=>$parent_category_name,
				"category_description"=>strip_tags($single_category->getCategory_description()),
				"main_category_id"=>$single_category->getMain_category_id(),
				"category_image_id"=>$single_category->getCategory_image_id(),
				"language_id"=>$single_category->getLanguage_id(),
				"child_data"=>null,
			);
		}else{
			if(!empty($single_category)){
				$parent_category_name = $single_category->getCategory_name();
			}
			$data_temp[]=array(
				"category_master_id"=>$single_category->getCategory_master_id(),
				"category_name"=>$single_category->getCategory_name(),
				"parent_category_id"=>$single_category->getParent_category_id(),
				"parent_category_name"=>$parent_category_name,
				"category_description"=>strip_tags($single_category->getCategory_description()),
				"main_category_id"=>$single_category->getMain_category_id(),
				"category_image_id"=>$single_category->getCategory_image_id(),
				"language_id"=>$single_category->getLanguage_id(),
				"classname"=>"cat ".$single_category->getMain_category_id()

			);

			$data_child='';
			if(count($all_sub_category) > 0 ){
				foreach(array_slice($all_sub_category,0) as $lkey=>$lval){
					$parent_category_name = 'No Parent ' ;

					if($lval->getParent_category_id() == 0){
					}else{

						$data_child[] = $this->get_hirerachy($lang_id,$lval->getParent_category_id(),$lval->getMain_category_id());
					}
				}

			}

			$data = array(
				"category_master_id"=>$data_temp[0]['category_master_id'],
				"category_name"=>$data_temp[0]['category_name'],
				"parent_category_id"=>$data_temp[0]['parent_category_id'],
				"parent_category_name"=>$data_temp[0]['parent_category_name'],
				"category_description"=>$data_temp[0]['category_description'],
				"main_category_id"=>$data_temp[0]['main_category_id'],
				"category_image_id"=>$data_temp[0]['category_image_id'],
				"language_id"=>$data_temp[0]['language_id'],
				"child_data"=>$data_child,
				"classname"=>$data_temp[0]['classname']
			);

		}
		return $data;
	}

//used for media type_id
	public function mediatype($file_type)
	{
		$em = $this->getDoctrine()->getManager();

		$media_type_list = $em->getRepository('AdminBundle:Mediatype')->findBy(array("is_deleted"=>0));

		foreach(array_slice($media_type_list,0) as $pkey=>$pval)
		{
				$type = explode(",",$pval->getMedia_type_allowed());
				foreach($type as $val )
				{
						if($val == $file_type)
						{
						  $media_type_id = $pval->getMedia_type_id();
						}
				}
		}
		if(!empty($media_type_id))
		{
			return $media_type_id;
		}
		else
		{
			return FALSE;
		}
	}

    function mediaremoveAction($file,$tmpname,$path,$upload_dir,$media_id,$media_type_id)
    {

		$clean_image = preg_replace('/\s+/', '', $file);
		$logo_name = date('Y_m_d_H_i_s').'_'.$clean_image;
		if(!file_exists($upload_dir))
		{
			mkdir($upload_dir,0777);
		}
		//logo upload check
		if(move_uploaded_file($tmpname,$upload_dir.$logo_name))
		{
			$em = $this->getDoctrine()->getManager();

			$medialibrarymaster = $em->getRepository('AdminBundle:Medialibrarymaster')->findOneBy(array("media_library_master_id"=>$media_id));

			$medialibrarymaster->setMedia_type_id($media_type_id);
			$medialibrarymaster->setMedia_title($logo_name);
			$medialibrarymaster->setMedia_location($path);
			$medialibrarymaster->setMedia_name($logo_name);
			$medialibrarymaster->setIs_deleted(0);

			$em = $this->getDoctrine()->getManager();
			$em->persist($medialibrarymaster);
			$em->flush();
			$media_library_master_id = $medialibrarymaster->getMedia_library_master_id();

			return $media_library_master_id;
		}
		else
		{
			return FALSE;
		}
    }
	/**
     * @Route("/getmedia/{media_library_master_id}/{live_path}")
     */
    public function getmediaAction($media_library_master_id,$live_path)
    {

		$media_library = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Medialibrarymaster')
				   ->findOneBy(array('media_library_master_id'=>$media_library_master_id,'is_deleted'=>0));
		if(!empty($media_library)){
			return new Response($live_path.$media_library->getMedia_location()."/".$media_library->getMedia_name());
		}
		return new Response("");
	}

	public function getdata($table,$condition){
		$doc = $this->getDoctrine()->getManager()
				->getRepository("AdminBundle:".$table)
				->findBy($condition);
		return $doc;
	}

	public function getonedata($table,$condition){
		$doc = $this->getDoctrine()->getManager()
				->getRepository("AdminBundle:".$table)
				->findOneBy($condition);
		return $doc;
	}

	public function del($table,$id){
		$em = $this->getDoctrine()->getManager();
		$update = $em->getRepository("AdminBundle:".$table)->find($id);
		if($update){
			$update->setIs_deleted(1);
			$em->flush();
			return true;
		}
		return false;
	}
	public function getaddressAction($address_id,$lang_id)
	{
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare("SELECT address_master.*,city_master.city_name,area_master.area_name FROM address_master JOIN city_master ON address_master.city_id = city_master.main_city_id JOIN area_master ON address_master.area_id = area_master.main_area_id WHERE address_master.main_address_id = ".$address_id." AND address_master.language_id = ".$lang_id." AND city_master.language_id = ".$lang_id." AND area_master.language_id = ".$lang_id);
		/*." AND address_master.is_deleted = 0"*/
		$statement->execute();
		$address_info = $statement->fetchAll();

		if(!empty($address_info) && $address_id != 0)
		{
			$address = array(
					"address"=>$address_info[0]['address_name'],
					"base_address_type"=>$address_info[0]['base_address_type'],
					"address_type"=>$address_info[0]['address_type'],
					"city_id"=>$address_info[0]['city_id'],
					"city_name"=>$address_info[0]['city_name'],
					"area_id"=>$address_info[0]['area_id'],
					"area_name"=>$address_info[0]['area_name'],
					"contact_no"=>$address_info[0]['contact_no'],
									"block_no"=>$address_info[0]['block_no'],
									"floor_no"=>$address_info[0]['floor_no'],
									"building_no"=>$address_info[0]['building_no'],
					"street"=>$address_info[0]['street'],
					"flate_house_number"=>$address_info[0]['flate_house_number'],
					"jadda"=>$address_info[0]['jadda'],
					"landmark"=>$address_info[0]['landmark'],
					"pincode"=>$address_info[0]['pincode'],
					"gmap_link"=>$address_info[0]['gmap_link'],
					"lat"=>$address_info[0]['lat'],
					"lng"=>$address_info[0]['lng'],
				);
		}
		else
		{
			$address = "FALSE";
		}
		return $address;
	}

	public function getparams(){ // MP - 03.11.15
		$live_path = $this->container->getParameter("live_path");
		$absolute_path = $this->container->getParameter("absolute_path");
		$upload_dir = $this->container->getParameter("upload_dir");
		$file_path = $this->container->getParameter("file_path");
		$array = array("live"=>$live_path,"abs"=>$absolute_path,"upload_dir"=>$upload_dir,"file_path"=>$file_path);
		return $this->array_to_object($array);
	}
	public function array_to_object($array){ // MP - 03.11.15
		$new_array = json_decode(json_encode($array, false));
		return $new_array;
	}
	public function send_notification($registration_ids,$title,$message,$provider,$app_id,$domain_id,$tablename,$tabledataid)
	{
		//$date_t = strtotime(date("y-m-d H:i:s"));
		ob_start();
		/*
		$app_id= CUST // Customer App
		$app_id = DEL // Delivery App
		$domain_id = domain_code
		*/

		switch($provider)
		{
		case 1:
			$result = "FALSE";
			$development = false;
			$apns_url = NULL; // Set Later
			$pathCk = NULL; // Set Later
			$apns_port = 2195;

			/*if($app_id == 'CUST')
		    {
		    	// Customer App
				if ($development) {
				    $apns_url = 'gateway.sandbox.push.apple.com';

				    $pathCk=$this->container->get('kernel')->locateResource('@WSBundle/Controller/');
				} else {
				    $apns_url = 'gateway.push.apple.com';

				    $pathCk=$this->container->get('kernel')->locateResource('@WSBundle/Controller/');
				}

				$passphrase = '123';
			}
			elseif($app_id == 'DEL')
			{
				// Delivery App

				if ($development) {
				    $apns_url = 'gateway.sandbox.push.apple.com';

				    $pathCk=$this->container->get('kernel')->locateResource('@WSBundle/Controller/');
				} else {
				    $apns_url = 'gateway.push.apple.com';

				    $pathCk=$this->container->get('kernel')->locateResource('@WSBundle/Controller/');
				}

				$passphrase = '123';

			}*/

            $app_type_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Apptypemaster')
					   ->findOneBy(array('is_deleted'=>0,'app_type_code'=>$app_id));

		    if(!empty($app_type_master))
		    {
				$app_details = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Appdetails')
					   ->findOneBy(array('is_deleted'=>0,'app_type_id'=>$app_type_master->getApp_type_id(),"domain_id"=>$domain_id,"status"=>'active'));

					   if(!empty($app_details))
					   {
					   		if($development)
					   		{

								if(!empty($app_details->getApp_apns_certificate_development()) && $app_details->getApp_apns_certificate_development()!= "" && !empty($app_details->getApp_apns_certificate_development_password()) && $app_details->getApp_apns_certificate_development_password()!= "")
								{

									$apns_url = 'gateway.sandbox.push.apple.com';

								    $pathCk = $this->container->get('kernel')->locateResource('@WSBundle/Controller/'.$app_details->getApp_apns_certificate_development());
								    $passphrase = $app_details->getApp_apns_certificate_development_password();
								}
							}
							else
							{
								if(!empty($app_details->getApp_apns_certificate_production()) && $app_details->getApp_apns_certificate_production()!= "" && !empty($app_details->getApp_apns_certificate_production_password()) && $app_details->getApp_apns_certificate_production_password()!= "")
								{
									$apns_url = 'gateway.push.apple.com';

				    				$pathCk=$this->container->get('kernel')->locateResource('@WSBundle/Controller/'.$app_details->getApp_apns_certificate_production());
				    				$passphrase = $app_details->getApp_apns_certificate_production_password();
								}
							}
						}
			}

            if(!empty($apns_url) && $apns_url != "")
            {
				/*
				$ctx = stream_context_create();
		        stream_context_set_option($ctx, 'ssl', 'local_cert', $pathCk);
		        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

			    $apns = stream_socket_client(
			            'ssl://' . $apns_url . ':' . $apns_port, $err,
			        $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
	            */
	            $data = json_decode($message);
				$response = $response1 = "";
				/*
				 *$payload['aps'] = array(
					'alert' => $data->detail,
					'badge' => 1,
					'sound' => 'default',
					'response'=>$data->response
				);
				*/
				$payload['job_id'] = $data->code ;
				$code = $data->code ;
				$detail = $data->detail;
				$response=print_r($data->response, 1);

				// START LOOP
				if($registration_ids != "ALL")
				{
					unset($where);
					if(is_array($registration_ids))
					{
						$registration_ids = implode("','",$registration_ids);
					}

					$em = $this->getDoctrine()->getManager();

					$connection = $em->getConnection();
					$apns_user = $connection->prepare("SELECT * FROM apns_user WHERE apns_regid in ('".$registration_ids."') and is_deleted=0" );

					$apns_user->execute();
					$apns_user_list = $apns_user->fetchAll();

					// specific user

					$em = $this->getDoctrine()->getManager();
					$connection = $em->getConnection();
					$statement = $connection->prepare("UPDATE apns_user SET badge=(badge+1) WHERE apns_regid in ('".$registration_ids."') and is_deleted=0");
					$statement->execute();
				}
				else
				{
					// All user
					$em = $this->getDoctrine()->getManager();
					$connection = $em->getConnection();
					$statement = $connection->prepare("UPDATE apns_user SET badge = (badge+1) WHERE  is_deleted=0");
					$statement->execute();
				}
				$result = "";
				//$res_arr = array();
				$ctx = stream_context_create();
				stream_context_set_option($ctx, 'ssl', 'local_cert', $pathCk);
				stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

				$apns = stream_socket_client(
						'ssl://' . $apns_url . ':' . $apns_port, $err,
					$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
				stream_set_blocking($apns, 0);

				foreach(array_slice($apns_user_list,0) as $key=>$val)
				{
					if ($key == "50" || $key == "100" ||$key == "250" || $key=="500"||$key=="750"||$key=="1000"||$key=="1250"||$key=="1500"||$key=="1750"||
					$key=="2000" || $key =="2250" || $key=="2500" || $key=="2750" || $key == "3000" || $key == "3250" ||
					$key=="3500" || $key == "3750" || $key=="4000" ){
					ob_flush();
					flush();

					}
					$device = $val['apns_regid'];

					//$final_payload = json_encode($payload);
					if(strlen($device) == 64 )
					{
						if(!$apns)
						{
							echo "Failed to connect (stream_socket_client): $err $errstr";
							$result = "Failed to connect : $error $errorString ".PHP_EOL;
							$ctx = stream_context_create();
							stream_context_set_option($ctx, 'ssl', 'local_cert', $pathCk);
							stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

							$apns = stream_socket_client(
									'ssl://' . $apns_url . ':' . $apns_port, $err,
								$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
							stream_set_blocking($apns, 0);
						}
						else
						{
							$payload['aps'] = array(
							'alert' => $data->detail,
							'badge' => 1,
							'sound' => 'default',
							'response'=>$data->response
							);

							$final_payload = json_encode($payload);

							$apnsMessage = chr(0) . pack('n', 32) . pack('H*', str_replace(' ', '', $device)) . pack('n', strlen($final_payload)) . $final_payload;
							$result ='';

							try {
								$result = fwrite($apns, $apnsMessage );
							}
						   catch (\Exception $e) {
							   fclose($apns);
							   $result = $e->getMessage() ;
							   echo('<br>Error sending Device: ' . $device );
							   echo('<br>Error sending payload: ' . $e->getMessage());


							   $ctx = stream_context_create();
							   stream_context_set_option($ctx, 'ssl', 'local_cert', $pathCk);
							   stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

							   $apns = stream_socket_client(
									   'ssl://' . $apns_url . ':' . $apns_port, $err,
								   $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
							   stream_set_blocking($apns, 0);
							}
							//write log start
							$txtf = "iphone_push_file.txt";
							 if (!file_exists($txtf)) {
								fopen($txtf,'w');
							}
							//-----write file to track error---------

							// Open the file to get existing content
							$current = file_get_contents($txtf);
							// Append a new person to the file
							$current .= "\nAPNS :- ".$val['apns_regid']."====>".$result;
							// Write the contents back to the file
							file_put_contents($txtf, $current);

							unset($payload);
							unset($statement);
							unset($final_payload);
							unset($apnsMessage);
							$result = $device = '' ;

						}
						// END LOOP
					}
				}

				foreach(array_slice($apns_user_list,0) as $key=>$val)
				{
					$device = $val['apns_regid'];
					$apppushnotificationmaster = new Apppushnotificationmaster();
					$apppushnotificationmaster->setDevice_name('ios');
					$apppushnotificationmaster->setApp_id($app_id);
					$apppushnotificationmaster->setDomain_id($domain_id);
					$apppushnotificationmaster->setDevice_token($device);

					$connection = $em->getConnection();
					$apns_query = $connection->prepare("SELECT * FROM apns_user WHERE apns_regid = '".$device."' and is_deleted=0 ORDER BY apns_user_id DESC");
					$apns_query->execute();
					$apns_user = $apns_query->fetchAll();
					$user_id = '' ;
					if(!empty($apns_user))
					{
						$user_id = $apns_user[0]['user_id'];
						$device_id = $apns_user[0]['device_id'];

						$em = $this->getDoctrine()->getManager();
						$user_setting = $em->getRepository('AdminBundle:Usersetting')->findOneBy(array("user_id" => $user_id ,"is_deleted" => 0));

						$value = json_decode($user_setting->getSetting_value(),true);

						$lang_id = $value['language'];

						$apppushnotificationmaster->setUser_id($user_id);
						$apppushnotificationmaster->setLanguage_id($lang_id);
						$apppushnotificationmaster->setDevice_id($device_id);
					}

					$apppushnotificationmaster->setData($detail);
					$apppushnotificationmaster->setCode($code);
					$apppushnotificationmaster->setTable_name($tablename);
					$apppushnotificationmaster->setTable_id($tabledataid);
					$apppushnotificationmaster->setResponse($response);
					$apppushnotificationmaster->setDatetime(date("Y-m-d H:i:s"));
					$apppushnotificationmaster->setIs_deleted(0);
					$em = $this->getDoctrine()->getManager();
					$em->persist($apppushnotificationmaster);
					$em->flush();
				}

				// END LOOP


				fclose($apns);
				unset($apns_user_list);
			}

			break;

		case 2:
			$result = "FALSE";
			$title_name=$title;
			$data = array("title"=>$title_name,"message"=>$message);
			$URL = 'https://android.googleapis.com/gcm/send';

			$fields = array(
		        'registration_ids' => $registration_ids,
		        'data' => $data,
		    );

		    $app_type_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Apptypemaster')
					   ->findOneBy(array('is_deleted'=>0,'app_type_code'=>$app_id));

		    if(!empty($app_type_master))
		    {
				$app_details = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Appdetails')
					   ->findOneBy(array('is_deleted'=>0,'app_type_id'=>$app_type_master->getApp_type_id(),"domain_id"=>$domain_id,"status"=>'active'));

					   if(!empty($app_details))
					   {
					   		/*if($app_id == '1')
						    {
						    	// Customer App
								$headers = array(
							        'Authorization: key=#',
							        'Content-Type: application/json'
						    	);
							}
							elseif($app_id == '2')
							{

								// Delivery App
								$headers = array(
							        'Authorization: key=#',
							        'Content-Type: application/json'
						    	);
							}*/
							if(!empty($app_details->getApp_gcm_key()) && $app_details->getApp_gcm_key()!= "")
							{
								$headers = array(
							        'Authorization: key='.$app_details->getApp_gcm_key(),
							        'Content-Type: application/json'
						    	);

								$data1 = json_decode($message);
								$response="";
					            if(isset($data1->response) && !empty($data1->response))
					            {
									$response=print_r($data1->response, 1);
								}
								else
								{
									$response="";
								}

								$code="";
					            if(isset($data1->code) && !empty($data1->code))
					            {
									$code=$data1->code;
								}
								else
								{
									$code="";
								}

							    $registration_ids_array=$registration_ids;
								if(is_array($registration_ids))
								{
										$registration_ids = implode("','",$registration_ids);
								}
								else
								{
									$registration_ids="";
								}

								$detail="";
					            if(isset($data1->detail) && !empty($data1->detail))
					            {
									$detail=$data1->detail;
								}
								else
								{
									$detail="";
								}
								$em = $this->getDoctrine()->getManager();
								//$registration_ids_array = '';

								if(!empty($registration_ids_array))
								{
									foreach($registration_ids_array as $val)
									{

									    $apppushnotificationmaster = new Apppushnotificationmaster();
										$apppushnotificationmaster->setDevice_name('android');
										$apppushnotificationmaster->setApp_id($app_id);
										$apppushnotificationmaster->setDomain_id($domain_id);
										$apppushnotificationmaster->setDevice_token($val);

										$connection = $em->getConnection();
										$gcm = $connection->prepare("SELECT * FROM gcm_user WHERE gcm_regid = '".$val."' and is_deleted=0 ORDER BY gcm_user_id DESC");
										$gcm->execute();
										$gcm_user = $gcm->fetchAll();
										if(!empty($gcm_user))
										{
											$user_id = $gcm_user[0]['user_id'];
											$device_id = $gcm_user[0]['device_id'];

											$em = $this->getDoctrine()->getManager();
											$user_setting = $em->getRepository('AdminBundle:Usersetting')->findOneBy(array("user_id" => $user_id ,"is_deleted" => 0));

											$value = json_decode($user_setting->getSetting_value(),true);

											$lang_id = $value['language'];

											$apppushnotificationmaster->setUser_id($user_id);
											$apppushnotificationmaster->setLanguage_id($lang_id);
											$apppushnotificationmaster->setDevice_id($device_id);
										}

										$apppushnotificationmaster->setData($detail);
										$apppushnotificationmaster->setCode($code);
										$apppushnotificationmaster->setTable_name($tablename);
										$apppushnotificationmaster->setTable_id($tabledataid);
										$apppushnotificationmaster->setResponse($response);
										$apppushnotificationmaster->setDatetime(date("Y-m-d H:i:s"));
										$apppushnotificationmaster->setIs_deleted(0);
										$em = $this->getDoctrine()->getManager();
										$em->persist($apppushnotificationmaster);
										$em->flush();
							    	}
							    }
							    // Open connection
							    $ch = curl_init();

							    // Set the url, number of POST vars, POST data
							    curl_setopt($ch, CURLOPT_URL, $URL);
							    curl_setopt($ch, CURLOPT_POST, true);
							    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
							    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

							    // Disabling SSL Certificate support temporarly
								curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false);
							    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

							    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
								$txtf = "android_push_file.txt";
								if (!file_exists($txtf)) {
									fopen($txtf,'w');
								}
								//-----write file to track error---------

								// Open the file to get existing content
								$current = file_get_contents($txtf);
								// Append a new person to the file
								$current .= http_build_query($fields) ;
								// Write the contents back to the file
								file_put_contents($txtf, $current);

							    // Execute post


							    $result = curl_exec($ch);

							    if ($result === FALSE) {
							        die('Curl failed: ' . curl_error($ch));
							    }
							    // Close connection
							    curl_close($ch);
							}
					   }
			}

			break;
	}

    return $result;

	}

	/*
	 get GCM user device / @param int $user_id / @return mixed
	*/
	public function find_gcm_regid($user_id){

		if(is_array($user_id)){
			$user_id = implode("','",$user_id) ;
		}

		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$query = "SELECT * FROM gcm_user WHERE user_id in ('".$user_id."') and user_type='user' and gcm_regid NOT LIKE ''  and is_deleted = 0" ;



		$gcm_user = $connection->prepare($query);
		$gcm_user->execute();
		$gcm_user_list = $gcm_user->fetchAll();
		//user_type = 'user' and
		if (count($gcm_user_list)>0){
			$reg_ids = array_map(function($sub){
				return $sub['gcm_regid'];
			},$gcm_user_list);
			return $reg_ids ;
		}
		return false ;
	}

	/*
	get APNS user device / @param int $user_id / @return mixed
	*/
	public function find_apns_regid($user_id){

		if(is_array($user_id)){
			$user_id = implode("','",$user_id) ;
		}

		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$apns_user = $connection->prepare("SELECT * FROM apns_user WHERE user_id in ('".$user_id."') and apns_regid NOT LIKE '(null)' and apns_regid NOT LIKE '' and user_type='user' and  is_deleted=0");

		$apns_user->execute();
		$apns_user_list = $apns_user->fetchAll();


		if (count($apns_user_list)>0){
			$reg_ids = array_map(function($sub){
				return $sub['apns_regid'];
			},$apns_user_list);
			return $reg_ids ;
		}
		return false ;
	}

	function checkAttrvaluePossible($attribute_arr,$language_id,$product_id){
		$domain_id = $this->get('session')->get('domain_id');
		$attr = '';
		$attribute_arr = explode(",",$attribute_arr);

		$where_str ='';
		for($i = 0 ; $i < count($attribute_arr) ; $i++ ){
			$single_arr = explode(":",$attribute_arr[$i]);

			if($where_str == ''){
				$where_str.='(attribute_id = '.$single_arr[0].' and attribute_value_id = '.$single_arr[1].' )';
			}
			else{
				$where_str.=' OR (attribute_id = '.$single_arr[0].' and attribute_value_id = '.$single_arr[1].' )';
			}

		}
		$query = "SELECT distinct combination_id FROM combination_relation WHERE is_deleted = 0 AND product_id = '".$product_id."' AND language_id ='".$language_id."' AND ( " .$where_str. " )  GROUP BY combination_id";
		$can_insert_flag = 'true';
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare($query);
		$statement->execute();
		$selected_combination = $statement->fetchAll();
		$arr_pair = $arr_attribute_value = $arr_attribute = $comb_arr='';
		if(count($selected_combination) > 0 ){
			foreach($selected_combination as $ckey=>$cval){
				$arr_pair = $arr_attribute_value = $arr_attribute = '';
				$comb_arr[] = $cval['combination_id'];
				$query = "SELECT attribute_id , attribute_value_id  FROM combination_relation WHERE is_deleted = 0 AND product_id = '".$product_id."' AND language_id ='".$language_id."' AND  combination_id = '".$cval['combination_id']."'";

				$em = $this->getDoctrine()->getManager();
				$connection = $em->getConnection();
				$statement = $connection->prepare($query);
				$statement->execute();
				$single_combination = $statement->fetchAll();

				foreach($single_combination as $skey=>$sval){
					$arr_attribute[] = $sval['attribute_id'];
					$arr_attribute_value[] = $sval['attribute_value_id'];
					$arr_pair[] = "". $sval['attribute_id'] .":".$sval['attribute_value_id'];
				}

				$arraysAreEqual = ($arr_pair === $attribute_arr);
				$result=array_diff($arr_pair,$attribute_arr);
				if(count($result) == 0){
					$can_insert_flag = 'false';
				}


			}
		}
		return $can_insert_flag;
	}
	public function keyEncryptionAction($string)
	{
		if($string != "" && $string != NULL && !empty($string) && ctype_space($string) == false)
		{
			$key = $this->container->getParameter('key');
			$res = '';
			for( $i = 0; $i < strlen($string); $i++)
	            	{
		                $c = ord(substr($string, $i));

		                $c += ord(substr($key, (($i + 1) % strlen($key))));
		                $res .= chr($c & 0xFF);

	            	}
			return base64_encode($res);
		}
		return "";
	}
	public function keyDecryptionAction($string)
	{
		if($string != "" && $string != NULL && !empty($string) && ctype_space($string) == false)
		{
			$key = $this->container->getParameter('key');

			$res = '';
			$string = base64_decode($string);
			for( $i = 0; $i < strlen($string); $i++)
		        {
		                $c = ord(substr($string, $i));

		                $c -= ord(substr($key, (($i + 1) % strlen($key))));
		                $res .= chr(abs($c) & 0xFF);

		        }
			return $res;
		}
		return "";
	}
	public function getstatusAction($id)
	{
		$sts_check = $this->getDoctrine()
						->getManager()
						->getRepository('AdminBundle:Status')
						->findOneBy(array("status_id"=>$id,"is_deleted"=>0));

		return $sts_check->getStatus_name();
	}
	function is_in_array($array, $key, $key_value){

      $within_array = false;
      foreach( $array as $k=>$v ){
        if( is_array($v) ){
            $within_array = $this->is_in_array($v, $key, $key_value);
            if( $within_array == true ){
                break;
            }
        } else {
                if( $v == $key_value && $k == $key ){
                        $within_array = true;
                        break;
                }
        }
      }
      return $within_array;
	}

	/**
	*
	* use for security
	*
	* @param mix $value
	* @param string $type ('display')
	*
	* @return string $value
	*/

	public function bwiz_security($value,$type="")
	{
		//check type
		if(isset($type) && !empty($type) && $type=="display")
		{
			$str  = stripslashes($value);
		}else{
			$str  = addslashes($value);
		}

		return $str;
	}

	//use to calculate memory usage of php
	// for call - echo $this->convert(memory_get_usage()) . "<br>"; // at start and end both
	function convert($size)
	{
		$unit=array('b','kb','mb','gb','tb','pb');
		return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}
	/**
     * @Route("/maintainLogs/{operation}/{description}/{success_msg}/{error_msg}/{ip_address}/{file_name}")
     */
	function maintainLogs($operation,$description,$success_msg,$error_msg,$ip_address,$file_name){
		$user_id = $this->get('session')->get('user_id');
		$created_datetime = date("Y-m-d H:i:s");
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip_address = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip_address = $_SERVER['REMOTE_ADDR'];
		}
		$em = $this->getDoctrine()->getManager();
		if(true || $operation == "product_import" ){
			$user_log_details = new Productlogdetails();
			$user_log_details->setUser_id($user_id);
			$user_log_details->setOperation($operation);
			$user_log_details->setDescription($description);
			$user_log_details->setSuccess_msg($success_msg);
			$user_log_details->setError_msg($error_msg);
			$user_log_details->setCreated_datetime($created_datetime);
			$user_log_details->setIp_address($ip_address);
			$user_log_details->setFile_name($file_name);
			$user_log_details->setIs_deleted(0);
			$em->persist($user_log_details);
			$em->flush();
		}
		else{
			return false ;
		}

	}
}
