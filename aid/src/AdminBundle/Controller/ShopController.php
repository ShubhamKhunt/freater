<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Categorymaster;
use AdminBundle\Entity\Domainmaster;
use AdminBundle\Entity\Tagmodulerelation;
use AdminBundle\Entity\Tagmaster;
use AdminBundle\Entity\Languagemaster;
use AdminBundle\Entity\Addressmaster;
use AdminBundle\Entity\Usermaster;
use AdminBundle\Entity\Shopmaster;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
* @Route("/admin")
*/
class ShopController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }

   /**
     * @Route("/shop/manage-shop")
     * @Template()
     */
    public function shoplistAction()
    {
		$this->session = new Session();
		$domain_id = $this->get('session')->get('domain_id') ;
		$role_id = $this->get('session')->get('role_id') ;
		$user_id = $this->get('session')->get('user_id') ;
		$in_query_txt = $in_query = '';
		if($role_id == '3')
		{
				$in_query_txt = " AND user_master.user_master_id='$user_id' ";

		}
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare("SELECT shop_master.*,user_master.user_master_id,user_master.user_firstname,user_master.user_lastname,user_master.user_emailid
			FROM shop_master JOIN user_master
			ON user_master.user_master_id = shop_master.usermaster_id
			WHERE shop_master.is_deleted=0 $in_query_txt");
		$statement->execute();
		$data = $statement->fetchAll();

		return array("shoplist"=>$data);
    }


    /**
     * @Route("/shop/add-update-shop/{shop_master_id}",defaults={"shop_master_id"=""})
     * @Template()
     */
	 public function addshopAction($shop_master_id)
	 {
		if(!empty($shop_master_id))
		{
			$em = $this->getDoctrine()->getManager();
			$con = $em->getConnection();
			$st = $con->prepare("SELECT * FROM city_master WHERE is_deleted = 0 and status='Active' GROUP BY main_city_id");
			$st->execute();
			$citymaster = $st->fetchAll();

			$em = $this->getDoctrine()->getManager();
			$con = $em->getConnection();
			$st = $con->prepare("SELECT * FROM area_master WHERE is_deleted = 0 and status='Active' and main_city_id=3 GROUP BY main_area_id");
			$st->execute();
			$areamaster = $st->fetchAll();

			$load_user_area=null;
			$load_user_city='';
			$data_area='';
			$em = $this->getDoctrine()->getManager();
			$conn = $em->getConnection();
			$st = $conn->prepare("SELECT shop_master.*,CONCAT(m1.media_location, m1.media_name) as ol FROM shop_master LEFT JOIN
								 media_library_master m1 on shop_master.shop_logo=m1.media_library_master_id
								 where shop_master.shop_master_id = ".$shop_master_id." AND shop_master.is_deleted = 0");
			$st->execute();

			$load_shop = $st->fetchAll();


			$em = $this->getDoctrine()->getManager();
    		$conn = $em->getConnection();
    		$st = $conn->prepare("SELECT * FROM user_master where user_master_id = ".$load_shop[0]['usermaster_id']." AND is_deleted = 0 AND user_role_id = 3");

    		$st->execute();
    		$load_user = $st->fetchAll();

			$em = $this->getDoctrine()->getManager();
    		$conn = $em->getConnection();
    		$st = $conn->prepare("SELECT * FROM address_master where address_master_id = ".$load_user[0]['address_master_id']." AND is_deleted = 0");
    		$st->execute();
    		$load_user_address = $st->fetchAll();

    		if(!empty($load_user_address))
    		{
				$em = $this->getDoctrine()->getManager();
	    		$conn = $em->getConnection();
	    		$st = $conn->prepare("SELECT * FROM area_master where main_area_id = ".$load_user_address[0]['area_id']." AND is_deleted = 0");
	    		$st->execute();
	    		$load_user_area = $st->fetchAll();

	    		if(!empty($load_user_area))
	    		{
					$em = $this->getDoctrine()->getManager();
	    			$conn = $em->getConnection();
	    			$st = $conn->prepare("SELECT * FROM city_master where main_city_id = ".$load_user_area[0]['main_city_id']." AND is_deleted = 0");
	    			$st->execute();
	    			$load_user_city = $st->fetchAll();

				}
			}

			$live_path = $this->container->getParameter("live_path");

			return array("live_path"=>$live_path,'shop'=>$load_shop,'shop_master_id'=>$shop_master_id,'users'=>$load_user,"address"=>$load_user_address,
					 'user_master_id'=>$load_user[0]['user_master_id'],"citymaster"=>$citymaster,"areamaster"=>$areamaster,
					 "single_area"=>$load_user_area,
					 "single_city"=>$load_user_city
					);
		}
		else
		{
			$em = $this->getDoctrine()->getManager();
			$con = $em->getConnection();
			$st = $con->prepare("SELECT * FROM city_master WHERE is_deleted = 0 and status='Active' GROUP BY main_city_id");
			$st->execute();
			$citymaster = $st->fetchAll();

			return array("citymaster"=>$citymaster);
		}
	}

	/**
     * @Route("/addshopdb")
     * @Template()
     */
	 public function addshopdbAction()
	 {
		if(isset($_REQUEST['add']))
		{
			$session = new Session();
			$user_id = $session->get("user_id");
			
			$media_id = 0;
			if($_FILES['ol']['name'] != "" ){
				$extension = pathinfo($_FILES['ol']['name'], PATHINFO_EXTENSION);
				
				$media_type_id = $this->mediatype($extension);
					
				if($media_type_id == 2){
					$shop_master_id = !empty($_REQUEST['shop_master_id'])?$_REQUEST['shop_master_id']:'';
					$this->get("session")->getFlashBag()->set("error_msg","Upload Valid Shop Logo");
					if(!empty($shop_master_id)){
						return $this->redirect($this->generateUrl('admin_shop_addshop',array("domain"=>$this->get('session')->get('domain'),"shop_master_id"=>$shop_master_id)));
					}else{
						return $this->redirect($this->generateUrl('admin_shop_addshop',array("domain"=>$this->get('session')->get('domain'))));
					}
				}
				if($media_type_id == 1){
					$logo = $_FILES['ol']['name'];
					$tmpname = $_FILES['ol']['tmp_name'];
					$file_path = $this->container->getParameter('file_path');

					$logo_path = $file_path.'/user/';

					$logo_upload_dir = $this->container->getParameter('upload_dir').'/user/';

					$media_id = $this->mediauploadAction($logo,$tmpname,$logo_path,$logo_upload_dir,$media_type_id);
				}
			}
			
			$shop_name = $_REQUEST['shop_name'];
			$shop_detail = $_REQUEST['shop_detail'];

			$shipping_charge=0;
			if(isset($_REQUEST['shipping_charge'])){
				$shipping_charge=($_REQUEST['shipping_charge']);
			}

			$user_firstname = $_REQUEST['user_firstname'];
			$user_lastname = $_REQUEST['user_lastname'];
			$user_emailid = $_REQUEST['user_emailid'];
			$username = $_REQUEST['username'];
			$password = $_REQUEST['password'];


			$user_status = $_REQUEST['user_status'];
			$address = $_REQUEST['address'];
			$user_mobile = $_REQUEST['user_mobile'];
			$website=$_REQUEST['website_link'];
			$shop_type=$_REQUEST['shop_type'];
			if($website != ''){
				if (strpos($website,'http://') !== false || strpos($website,'https://') !== false){
				    $website = $website;
				} else {
					$website = 'http://'.$website;
				}
			}

			$delivery_time="";
			if(isset($_REQUEST['delivery_time'])){
				$delivery_time = $_REQUEST['delivery_time'];
			}

			if(isset($_REQUEST['shop_master_id']) && !empty($_REQUEST['shop_master_id']))
			{
				$em = $this->getDoctrine()->getManager();
				$shop_master_id = $_REQUEST['shop_master_id'];
				$orginfo = $em->getRepository('AdminBundle:Shopmaster')
								->find($shop_master_id);

				$em = $this->getDoctrine()->getManager();
				$update_address = $this->getDoctrine()
							->getManager()
							->getRepository("AdminBundle:Addressmaster")
							->findOneBy(array("owner_id"=>$_REQUEST['user_master_id'],"is_deleted"=>0));

				if(!empty($update_address))
				{
					$update_address->setOwner_id($_REQUEST['user_master_id']);
					$update_address->setBase_address_type('primary');
					$update_address->setAddress_type('home');
					$update_address->setArea_id($_REQUEST['area']);//$_REQUEST['area']
					$update_address->setBlock_no(0);
					$update_address->setAddress_name($_REQUEST['address']);
					$update_address->setFloor_no(0);
					$update_address->setBuilding_no(0);
					$update_address->setFlate_house_number(0);
					$update_address->setJadda("");
					$update_address->setLandmark("");
					$update_address->setCity_id($_REQUEST['city']);
					$update_address->setLat(0);
					$update_address->setLng(0);
					$update_address->setIs_deleted('0');
					$em->flush();
					$user_address_master_id = $update_address->getAddress_master_id();
				}
				else
				{
					$Useraddressmaster = new Addressmaster();
					$Useraddressmaster->setOwner_id($_REQUEST['user_master_id']);
					$Useraddressmaster->setBase_address_type('primary');
					$Useraddressmaster->setAddress_type('home');
					$Useraddressmaster->setArea_id($_REQUEST['area']);//
					$Useraddressmaster->setBlock_no(0);
					$Useraddressmaster->setAddress_name($_REQUEST['address']);
					$Useraddressmaster->setFloor_no(0);
					$Useraddressmaster->setBuilding_no(0);
					$Useraddressmaster->setFlate_house_number(0);
					$Useraddressmaster->setJadda("");
					$Useraddressmaster->setLandmark("");
					$Useraddressmaster->setCity_id($_REQUEST['city']);
					$Useraddressmaster->setLanguage_id(1);
					$Useraddressmaster->setLat(0);
					$Useraddressmaster->setLng(0);
					$Useraddressmaster->setIs_deleted('0');
					$em = $this->getDoctrine()->getManager();
					$em->persist($Useraddressmaster);
					$em->flush();
					$user_address_master_id = $Useraddressmaster->getAddress_master_id();
					$Useraddressmaster->setMain_address_id($user_address_master_id);
					$em->persist($Useraddressmaster);
					$em->flush();
				}

				//shop update

				$em = $this->getDoctrine()->getManager();
				$update = $em->getRepository('AdminBundle:Shopmaster')
							->find($shop_master_id);
				$update->setShop_name($shop_name);
				$update->setShop_detail($shop_detail);
				$update->setShop_address($user_address_master_id);
				$update->setShop_type($shop_type);
				$update->setShipping_charge($shipping_charge);
				$update->setTax(0);

				$update->setWebsite_link($website);

				$update->setLast_update_on(date('Y-m-d H:i:s'));
				
				if($media_id != 0)
				{
					$update->setShop_logo($media_id);
				}
				$em->flush();

				//user update
				$user_master_id = $_REQUEST['user_master_id'];
				$em = $this->getDoctrine()->getManager();
				$update = $em->getRepository('AdminBundle:Usermaster')
							->find($user_master_id);
				//$update->setProfile_title($user_display_name);
				$update->setUser_firstname($user_firstname);
				$update->setUser_lastname($user_lastname);
				$update->setUser_emailid($user_emailid);
				$update->setUsername($username);
				if($password != NULL)
				{
					$update->setPassword(md5($password));
				}
				else{
					return $this->redirect($this->generateUrl('admin_shop_addshop',array("domain"=>$this->get('session')->get('domain'),"shop_master_id"=>$shop_master_id)));
				}
				$update->setAddress_master_id($user_address_master_id);
				$update->setUser_status($user_status);
				$update->setUser_mobile($user_mobile);
				$update->setUser_image($media_id);

				$em->flush();

				$this->get('session')->getFlashBag()->set('success_msg','Shop Updated Successfuly!');
				return $this->redirect($this->generateUrl('admin_shop_shoplist'));
			} else {
			 	$Usermaster = new Usermaster();

				$Usermaster->setUser_firstname($user_firstname);
				$Usermaster->setUser_lastname($user_lastname);
				$Usermaster->setUser_emailid($user_emailid);
				$Usermaster->setUsername($username);
				if($password != NULL)
				{
					$Usermaster->setPassword(md5($password));
				}
				$Usermaster->setAddress_master_id(0);
				$Usermaster->setUser_image($media_id);
				$Usermaster->setUser_status($user_status);

				$Usermaster->setUser_mobile($user_mobile);


				$Usermaster->setUser_role_id(3);

				$Usermaster->setLogin_from("other");

				$Usermaster->setCreated_by($user_id);
				$Usermaster->setDomain_id('aid001');

				$Usermaster->setIs_deleted(0);
				$em = $this->getDoctrine()->getManager();
				$em->persist($Usermaster);
				$em->flush();
				$user_master_id = $Usermaster->getUser_master_id();

				$em->flush();

				//add default deliver man
				$usermaster = new Usermaster();
				$usermaster->setUser_firstname($user_firstname);
				$usermaster->setUser_lastname($user_lastname);
				$usermaster->setUser_emailid($user_emailid);
				$usermaster->setUsername($username);
				$usermaster->setPassword(md5($password));
				$usermaster->setAddress_master_id(0);

				$usermaster->setUser_status($user_status);

				$usermaster->setUser_mobile($user_mobile);
				$usermaster->setUser_image($media_id);

				$usermaster->setUser_role_id(6);

				$usermaster->setLogin_from("other");

				$usermaster->setCreated_by($user_master_id);
				$usermaster->setDomain_id('aid001');

				$usermaster->setIs_deleted(0);
				$usermaster->setParent_user_id($user_master_id);
				$em = $this->getDoctrine()->getManager();
				$em->persist($usermaster);
				$em->flush();

			 	//shop add
				$Shopmaster = new Shopmaster();
				$Shopmaster->setShop_name($shop_name);
				$Shopmaster->setUsermaster_id($user_master_id);
				$Shopmaster->setShop_detail($shop_detail);
				$Shopmaster->setStatus("");

				$Shopmaster->setShipping_charge($shipping_charge);
				$Shopmaster->setTax(0);

				$Shopmaster->setStart_time("");
				$Shopmaster->setEnd_time("");
				$Shopmaster->setShop_type($shop_type);
				$Shopmaster->setShop_logo($media_id);
				$Shopmaster->setWebsite_link($website);

				$Shopmaster->setCreated_on(date('Y-m-d H:i:s'));

				$Shopmaster->setShop_address(0);	// need to manage
				$Shopmaster->setLast_update_on(date('Y-m-d H:i:s'));
				$Shopmaster->setCreated_by($user_master_id);
				$Shopmaster->setIs_deleted(0);
				$em = $this->getDoctrine()->getManager();
				$em->persist($Shopmaster);
				$em->flush();
				$or_id = $Shopmaster->getShop_master_id();
				//user add

				$Useraddressmaster = new Addressmaster();
				$Useraddressmaster->setOwner_id($user_master_id);
				$Useraddressmaster->setBase_address_type('primary');
				$Useraddressmaster->setAddress_type('home');
				$Useraddressmaster->setArea_id($_REQUEST['area']);//$_REQUEST['area']
				$Useraddressmaster->setBlock_no(0);
				$Useraddressmaster->setAddress_name($_REQUEST['address']);
				$Useraddressmaster->setFloor_no(0);
				$Useraddressmaster->setBuilding_no(0);
				$Useraddressmaster->setFlate_house_number(0);
				$Useraddressmaster->setJadda("");
				$Useraddressmaster->setLandmark("");
				$Useraddressmaster->setCity_id($_REQUEST['city']);
				$Useraddressmaster->setLanguage_id(1);
				$Useraddressmaster->setLat(0);
				$Useraddressmaster->setLng(0);
				$Useraddressmaster->setIs_deleted('0');
				$em = $this->getDoctrine()->getManager();
				$em->persist($Useraddressmaster);
				$em->flush();
				$user_address_master_id = $Useraddressmaster->getAddress_master_id();
				$Useraddressmaster->setMain_address_id($user_address_master_id);
				$em->persist($Useraddressmaster);
				$em->flush();

				$em = $this->getDoctrine()->getManager();
				$update_shop = $this->getDoctrine()
							->getManager()
							->getRepository("AdminBundle:Shopmaster")
							->findOneBy(array("shop_master_id"=>$or_id,"is_deleted"=>0));

				$update_shop->setShop_address($user_address_master_id);
				$em->flush();

				$em = $this->getDoctrine()->getManager();
				$update_usermaster = $this->getDoctrine()
							->getManager()
							->getRepository("AdminBundle:Usermaster")
							->findOneBy(array("user_master_id"=>$user_master_id,"is_deleted"=>0));

				$update_usermaster->setAddress_master_id($user_address_master_id);
				$em->flush();


				$this->get('session')->getFlashBag()->set('success_msg','Shop Inserted Successfuly!');
				return $this->redirect($this->generateUrl('admin_shop_shoplist'));
			}
		}
    }

    /**
     * @Route("/deleteShop/{shop_master_id}",defaults={"shop_master_id"=""})
     * @Template()
     */
	 public function deleteshopAction($shop_master_id){
	 	$session = new Session();

		if(!empty($shop_master_id)){
			$id = $shop_master_id;
			$list = $this->getDoctrine()
					->getManager()
					->getRepository("AdminBundle:Shopmaster")
					->findBy(array("is_deleted"=>0,"shop_master_id"=>$id));
			if(!empty($list)){
				foreach($list as $k=>$v){
					$em = $this->getDoctrine()->getManager();
					$update = $em->getRepository("AdminBundle:Shopmaster")->find($v->getShop_master_id());
					$update->setIs_deleted(1);
					$em->flush();
				}
			}

			$this->get('session')->getFlashBag()->set('success_msg','Shop Deleted Successfuly!');
			return $this->redirect($this->generateUrl('admin_shop_shoplist'));
		}
	}


	/**
     * @Route("/areaajax")
     * @Template()
     */
    public function areaajaxAction()
    {
        if(isset($_REQUEST['flag']) && $_REQUEST['flag'] == 'change_area')
        {
            $main_city_master_id = $_REQUEST['city_master_id'];
           	$em = $this->getDoctrine()->getManager();
			$con = $em->getConnection();
			$st = $con->prepare("SELECT main_area_id FROM area_master WHERE is_deleted = 0 and status='active' and main_city_id='".$main_city_master_id."' GROUP BY main_area_id");
			$st->execute();
			$areamaster = $st->fetchAll();

			$data = array();
			if(!empty($areamaster)){

				foreach($areamaster as $k=>$v){

					$em = $this->getDoctrine()->getManager();
					$connection = $em->getConnection();
					$statement = $connection->prepare("SELECT area_name FROM area_master WHERE is_deleted = 0 and status='active' and  main_area_id = ".$v['main_area_id']);
					$statement->execute();
					$comp = $statement->fetchAll();
					$area_name = "";
					for($i = 0 ; $i < count($comp) ;$i++){
						if($i==0)
						{
							$area_name .=$comp[$i]['area_name'];
						}
						else
						{
							$area_name .= " / ".  $comp[$i]['area_name'];
						}
						/*$area_name .= " / ".  $comp[$i]['area_name'];*/

					}
					$data_area[] = array("area_master_id"=>$v['main_area_id'],"area_name"=>$area_name);
				}
			}
        }

        return new Response(json_encode($data_area));
    }
}
