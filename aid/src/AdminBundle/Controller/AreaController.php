<?php

namespace AdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AdminBundle\Entity\Countrymaster;
use AdminBundle\Entity\Statemaster;
use AdminBundle\Entity\Citymaster;
use AdminBundle\Entity\Areamaster;
use AdminBundle\Entity\Deliveryservicearea;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Bundle\DoctrineBundle\ConnectionFactory;

/**
* @Route("/admin")
*/
class AreaController extends BaseController
{
	public function __construct(){
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
	
    /**
     * @Route("/area")
     * @Template()
     */
    public function indexAction()
    {
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
		$country_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Countrymaster')
					   ->findBy(array('is_deleted'=>0));
		$state_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Statemaster')
					   ->findBy(array('is_deleted'=>0));
		$city_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Citymaster')
					   ->findBy(array('is_deleted'=>0));
		$area_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Areamaster')
					   ->findBy(array('is_deleted'=>0));	

        return array("language"=>$language,"country"=>$country_master,"state"=>$state_master,"city"=>$city_master,"area"=>$area_master);

    }

	/**
     * @Route("/addarea/{main_area_id}",defaults = {"main_area_id" = ""})
     * @Template()
     */
    public function addareaAction($main_area_id)
    {
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));

		$country_master = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Countrymaster')
						   ->findBy(array('is_deleted'=>0));

		if(isset($main_area_id) && $main_area_id != "")
		{		   
			$area_master = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Areamaster')
						   ->findBy(array('main_area_id'=>$main_area_id,'is_deleted'=>0));

			$city_master = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Citymaster')
						   ->findBy(array('main_city_id'=>$area_master[0]->getMain_city_id(),'is_deleted'=>0));

			$state_master = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Statemaster')
						   ->findBy(array('main_state_id'=>$city_master[0]->getMain_state_id(),'is_deleted'=>0));

			return array("language"=>$language,"country_master"=>$country_master,"area_master"=>$area_master,"state_master"=>$state_master,"city_master"=>$city_master,"main_country_id"=>$state_master[0]->getMain_country_id(),"main_state_id"=>$city_master[0]->getMain_state_id(),"main_city_id"=>$area_master[0]->getMain_city_id(),"main_area_id"=>$main_area_id);
		}			   
        return array("language"=>$language,"country_master"=>$country_master);
    }

	/**
     * @Route("/savearea/{language_id}/{main_area_id}",defaults={"main_area_id"=""})
     * @Template()
     */
    public function saveareaAction($language_id,$main_area_id)
    {
		if(isset($_POST['save_area']) && $_POST['save_area'] == 'save_area' && $language_id != "")
		{
			if($_POST['area_name'] != "" && $_POST['area_code'] != "" && $_POST['status'] != "")
			{
				//$main_city_id = $_POST['main_city_id'];
				$area_name = $_POST['area_name'];
				$area_code = $_POST['area_code'];
				$status = $_POST['status'];
				if(isset($main_area_id) && $main_area_id != "")
				{
					$area_master = $this->getDoctrine()
							->getManager()
							->getRepository("AdminBundle:Areamaster")
							->findOneBy(array("main_area_id"=>$main_area_id,"language_id"=>$language_id,"is_deleted"=>0));

					if(!empty($area_master))
					{
						$em = $this->getDoctrine()->getManager();
						$update = $em->getRepository('AdminBundle:Areamaster')->find($area_master->getArea_master_id());					
						$update->setArea_name($area_name);

						$update->setArea_code($area_code);

						$update->setMain_city_id(3);

						$update->setStatus($status);

						$em->flush();

						$this->get('session')->getFlashBag()->set('success_msg','Area Updated successfully.');

						return $this->redirect($this->generateUrl('admin_area_addarea',array("domain"=>$this->get('session')->get('domain'),'main_area_id'=>$main_area_id)));

					// till here done

					}

					else

					{

						$areamaster = new Areamaster();

						$areamaster->setArea_name($area_name);

						$areamaster->setArea_code($area_code);

						$areamaster->setMain_city_id(3);

						$areamaster->setMain_area_id($main_area_id);

						$areamaster->setLanguage_id($language_id);

						$areamaster->setStatus($status);

						$areamaster->setIs_deleted(0);

						$em = $this->getDoctrine()->getManager();

						$em->persist($areamaster);

						$em->flush();

						

						$this->get('session')->getFlashBag()->set('success_msg','Area inserted successfully.');

						return $this->redirect($this->generateUrl('admin_area_addarea',array("domain"=>$this->get('session')->get('domain'),'main_area_id'=>$main_area_id)));

					}

				}

				else

				{

					$areamaster = new Areamaster();

					$areamaster->setArea_name($area_name);

					$areamaster->setArea_code($area_code);

					$areamaster->setMain_city_id(3);

					$areamaster->setMain_area_id(0);

					$areamaster->setLanguage_id($language_id);

					$areamaster->setStatus($status);

					$areamaster->setIs_deleted(0);

					$em = $this->getDoctrine()->getManager();

					$em->persist($areamaster);

					$em->flush();

					$areamaster->setMain_area_id($areamaster->getArea_master_id());

					$em->flush();

					

					$this->get('session')->getFlashBag()->set('success_msg','Area inserted successfully.');

					return $this->redirect($this->generateUrl('admin_area_index',array("domain"=>$this->get('session')->get('domain'))));

				}

			}

			else

			{

				$this->get('session')->getFlashBag()->set('error_msg','Please fill all required fields.');

				return $this->redirect($this->generateUrl('admin_area_addarea',array("domain"=>$this->get('session')->get('domain'))));

			}

		}

		return array();

	}

	

	/**

	* @Route("/getcityajax")

	*@Template()

	**/

	public function getcityajaxAction(){

		$html = "<option value=''>Select City</option>" ;
		$method = $this->get('request')->getMethod() ;

		if($method = "POST"){
			$state_id = $_POST['state_id'] ;
			$lang_id = $_POST['lang_id'] ;

			$city_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Citymaster')
					   ->findBy(array('main_state_id'=>$state_id,"language_id"=>$lang_id,'is_deleted'=>0));

			if(!empty($city_master)){
				foreach($city_master as $key=>$val){
					$html .= "<option value='".$val->getMain_city_id()."'>".$val->getCity_name()."</option>";
				}
			}
		}

		$response = new Response() ;
		$response->setContent($html) ;
		return $response ;
	}

	/**
	* @Route("/getareaajax")
	*@Template()
	**/

	public function getareaajaxAction(){

		$html = "<option value=''>Select Area</option>" ;
		$method = $this->get('request')->getMethod() ;

		if($method = "POST"){
			$city_id = $_POST['city_id'] ;
			$lang_id = $_POST['lang_id'] ;
			
			$area_master = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Areamaster')
					   ->findBy(array('main_city_id'=>$city_id,"language_id"=>$lang_id,'is_deleted'=>0));

			if(!empty($area_master)){
				foreach($area_master as $key=>$val){
					$html .= "<option value='".$val->getMain_area_id()."'>".$val->getArea_name()."</option>";
				}
			}
		}

		$response = new Response() ;
		$response->setContent($html) ;
		return $response ;
	}

	/**
	* @Route("/servicearea/{user_id}")
	* @Template()
	**/
	public function serviceareaAction($user_id)
	{
		$country_list = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Countrymaster')
				   ->findBy(array('status'=>'active','language_id'=>1,'is_deleted'=>0));

		$delivery_area_list = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Deliveryservicearea')
				   ->findBy(array('user_master_id'=>$user_id,'status'=>'active','is_deleted'=>0));
				   
		if(!empty($delivery_area_list) && $delivery_area_list != NULL && count($delivery_area_list) >0)
		{
			$area_list = array();	   
			foreach($delivery_area_list as $key=>$val)
			{
				$area_list[] = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Areamaster')
				   ->findOneBy(array('main_area_id'=>$val->getArea_id(),'status'=>'active','language_id'=>1,'is_deleted'=>0));

			}

			$city_info = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Citymaster')
				   ->findOneBy(array('main_city_id'=>$area_list[0]->getMain_city_id(),'status'=>'active','language_id'=>1,'is_deleted'=>0));

			$state_info = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Statemaster')
				   ->findOneBy(array('main_state_id'=>$city_info->getMain_state_id(),'status'=>'active','language_id'=>1,'is_deleted'=>0));

			return array("country_list"=>$country_list,'user_id'=>$user_id,"state_info"=>$state_info,"city_info"=>$city_info,"area_list"=>$area_list);
		}

		return array("country_list"=>$country_list,'user_id'=>$user_id);
	}
	
	/**
	* @Route("/servicearea_boy/{user_id}")
	* @Template()
	**/
	public function servicearea_boyAction($user_id)
	{
		
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare("SELECT service_country_relation.*,country_master.country_name FROM service_country_relation JOIN country_master ON service_country_relation.main_country_id = country_master.main_country_id AND service_country_relation.status = 'active' AND service_country_relation.domain_id = '".$this->get('session')->get('domain_id')."' AND service_country_relation.is_deleted = 0 AND country_master.status = 'active' AND country_master.language_id = 1 AND country_master.is_deleted = 0");
		$statement->execute();
		
		$country_list = $statement->fetchAll();
		$delivery_area_list = $this->getDoctrine()
			   ->getManager()
			   ->getRepository('AdminBundle:Deliveryservicearea')
			   ->findBy(array('user_master_id'=>$user_id,'status'=>'active','is_deleted'=>0));
		
		$statement = $connection->prepare("SELECT cm.city_master_id,cm.city_name,sm.state_name,com.country_name FROM address_master am,city_master cm,state_master sm,country_master com where am.owner_id='".$user_id."' AND am.city_id !='0' AND am.is_deleted = 0
										  
										  AND am.city_id=cm.city_master_id AND cm.main_state_id=sm.state_master_id AND sm.main_country_id=com.country_id
										  ");

		$statement->execute();
		$address_master = $statement->fetch();
		$country_name ="";
		$state_name ="";
		$city_name ="";
		$city_id = "";
		
		if(!empty($address_master))
		{
			$country_name = $address_master['country_name'];
			$state_name = $address_master['state_name'];
			$city_name = $address_master['city_name'];
			$city_id = $address_master['city_master_id'];
		}

		$area_list = array();	   
		$i=0;
		foreach($delivery_area_list as $key=>$val)
		{
			$area_info = $this->getDoctrine()
			   ->getManager()
			   ->getRepository('AdminBundle:Areamaster')
			   ->findOneBy(array('main_area_id'=>$val->getArea_id(),'status'=>'active','language_id'=>1,'is_deleted'=>0));

			$city_info = $this->getDoctrine()
			   ->getManager()
			   ->getRepository('AdminBundle:Citymaster')
			   ->findOneBy(array('main_city_id'=>$area_info->getMain_city_id(),'status'=>'active','language_id'=>1,'is_deleted'=>0));

			$state_info = $this->getDoctrine()
			   ->getManager()
			   ->getRepository('AdminBundle:Statemaster')
			   ->findOneBy(array('main_state_id'=>$city_info->getMain_state_id(),'status'=>'active','language_id'=>1,'is_deleted'=>0));
			   
			   $country_info = $this->getDoctrine()
			   ->getManager()
			   ->getRepository('AdminBundle:Countrymaster')
			   ->findOneBy(array('main_country_id'=>$state_info->getMain_country_id(),'status'=>'active','language_id'=>1,'is_deleted'=>0));
		
			$area_list[$i]['delivery_service_area_id'] = $val->getDelivery_service_area_id();
			$area_list[$i]['area_name'] = $area_info->getArea_name();
			$area_list[$i]['city_name'] = $city_info->getCity_name();
			$area_list[$i]['state_name'] = $state_info->getState_name();
			$area_list[$i]['country_name'] = $country_info->getCountry_name();
			$i++;
		}
		
		$delivery_service_area = $this->getDoctrine()
				->getManager()
				->getRepository('AdminBundle:Deliveryservicearea')
				->findBy(array("status"=>'active',"domain_id"=>$this->get('session')->get('domain_id'),"user_master_id"=>$user_id));
		
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();

		$statement = $connection->prepare("SELECT service_area_relation.*,area_master.area_name FROM service_area_relation JOIN area_master ON service_area_relation.main_area_id = area_master.main_area_id AND service_area_relation.status = 'active' AND service_area_relation.main_city_id='3' AND service_area_relation.is_deleted = 0 AND area_master.status = 'active' AND area_master.language_id = 1 AND area_master.is_deleted = 0");

		$statement->execute();
		$service_area_list = $statement->fetchAll();
		
		return array("country_list"=>$country_list,'user_id'=>$user_id,"area_list"=>$area_list,
				 "country_name"=>$country_name,"state_name"=>$state_name,"city_name"=>$city_name,"city_id"=>$city_id,"service_area_list"=>$service_area_list,"delivery_service_area"=>$delivery_service_area);
	}

	/**
	* @Route("/getstatelist")
	**/
	public function getstatelistAction()
	{
		if(isset($_POST['flag']) && $_POST['flag'] == "get_state_by_country" && $_POST['id'] != "")
		{
			$id = $_POST['id'];
			$state_list = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Statemaster')
				   ->findBy(array('status'=>'active','language_id'=>1,'main_country_id'=>$id,'is_deleted'=>0));

			$html = '<label class="col-sm-2 control-label">State</label>';
			$html .= '<div class="col-sm-4">';  
			$html .= '<select class="form-control" onchange="getcity(this.value);" required>';
            $html .= '<option value="">-- select State --</option>';
			
			foreach($state_list as $key=>$val)
			{
				$html .= '<option value="'.$val->getMain_state_id().'">'.$val->getState_name().'</option>';
			}

            $html .= '</select>';
			$html .= '</div>';
			
			$content = array("html"=>$html);
			return new Response(json_encode($content));
		} 
	}

	/**
	* @Route("/getcitylist")
	**/
	public function getcitylistAction()
	{
		if(isset($_POST['flag']) && $_POST['flag'] == "get_city_by_state" && $_POST['id'] != "")
		{
			$id = $_POST['id'];
			$city_list = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Citymaster')
				   ->findBy(array('status'=>'active','language_id'=>1,'main_state_id'=>$id,'is_deleted'=>0));
				   
			$html = '<label class="col-sm-2 control-label">City</label>';
			$html .= '<div class="col-sm-4">';  	   
			$html .= '<select class="form-control" onchange="getarea(this.value);" required>';
            $html .= '<option value="">-- select city --</option>';

			foreach($city_list as $key=>$val)
			{
				$html .= '<option value="'.$val->getMain_city_id().'">'.$val->getCity_name().'</option>';
			}

            $html .= '</select>';
			$html .= '</div>';
			
			$content = array("html"=>$html);
			return new Response(json_encode($content));
		} 
	}

	/**
	* @Route("/getarealist")
	**/
	public function getarealistAction()
	{
		if(isset($_POST['flag']) && $_POST['flag'] == "get_area_by_city" && $_POST['id'] != "")
		{
			$id = $_POST['id'];
			$user_id = $_POST['user_id'];

			$area_list = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Areamaster')
				   ->findBy(array('status'=>'active','language_id'=>1,'main_city_id'=>$id,'is_deleted'=>0));

			$delivery_service_area = $this->getDoctrine()
					->getManager()
					->getRepository('AdminBundle:Deliveryservicearea')
					->findBy(array("status"=>'active',"domain_id"=>$this->get('session')->get('domain_id'),"user_master_id"=>$user_id));
			$selected_area = '' ;
			if(!empty($delivery_service_area)){
				foreach($delivery_service_area as $dkey=>$dval){
					$selected_area[]  = $dval->getArea_id() ;
				}
			}
			
			if(!empty($area_list))
			{
				$html = '<div class="box box-solid">';
				$html .= '<form action="'.$this->generateUrl('admin_area_saveservicearea',array("domain"=>$this->get('session')->get('domain'))).'" method="post">';
				$html .= '<input type="hidden" name="area_user_id" value="'.$_POST['user_id'].'">';
				$html .= '<div class="box-header with-border">';
				$html .= '<h3 class="box-title">Area list</h3>';
				$html .= '</div>';
				$html .= '<div class="box-body">';
				$html .= '<div class="row">';
						
				foreach($area_list as $key=>$val)
				{
					$html .= '<div class="col-md-4">';
						$html .= '<div class="checkbox">';
							$html .= '<label>';
							
							$selected = "";	
							if(in_array($val->getMain_area_id()	,$selected_area)){
								$selected = "checked";	
							}
							$html .= '<input type="checkbox"  '.$selected.' name="service_area[]" value="'.$val->getMain_area_id().'"> '.$val->getArea_name().'';

							$html .= '</label>';
						$html .= '</div>';
					$html .= '</div>';
				}

				$html .= '</div>';
				$html .= '</div>';
				$html .= '<div class="box-footer">';
                $html .= '<a href="'.$this->generateUrl('admin_deliveryboy_index',array("domain"=>$this->get('session')->get('domain'))).'" class="btn btn-default btn-flat">Cancel</a>';
                $html .= '<button type="submit" name="save_area" value="save_area" class="btn btn-info pull-right btn-flat">Save</button>';
                $html .= '</div>';
				$html .= '</div>';
			} else {
				$html = '<label class="control-label" for="inputError"><i class="fa fa-alert"></i> Area not found!</label>';
			}

			$content = array("html"=>$html);
			return new Response(json_encode($content));
		} 
	}
	
	/**
	* @Route("/saveservicearea")
	**/
	public function saveserviceareaAction()
	{
		if(isset($_POST['save_area']) && $_POST['save_area'] == "save_area")
		{	
			if(isset($_POST['service_area_main_id']) && !empty($_POST['service_area_main_id']))
			{
				$em = $this->getDoctrine()->getManager();
				$connection = $em->getConnection();	
				$usermaster = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Usermaster')
				   ->findOneBy(array('user_master_id'=>$_POST['area_user_id'],'is_deleted'=>0));
				$domain_id=$this->get('session')->get('domain_id');
				if(!empty($usermaster))
				{
					$domain_id=$usermaster->getDomain_id();
				}
				
				$em = $this->getDoctrine()->getManager();
				$delivery_service_area_info = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Deliveryservicearea')->findBy(array("is_deleted"=>0,"user_master_id"=>$_POST['area_user_id']));
				if(!empty($delivery_service_area_info)){
					foreach($delivery_service_area_info as $delivery_service_area_info)
					{
					$delivery_service_area_info->setIs_deleted(1);
					$em->persist($delivery_service_area_info);
					$em->flush();
					}
				}

				foreach($_POST['service_area_main_id'] as $key=>$val)
				{
					$deliveryservicearea = new Deliveryservicearea();
					$deliveryservicearea->setUser_master_id($_POST['area_user_id']);
					$deliveryservicearea->setArea_id($val);
					$deliveryservicearea->setService_available('yes');
					$deliveryservicearea->setStatus('active');
					$deliveryservicearea->setCreate_date(date("Y-m-d H:i:s"));
					$deliveryservicearea->setDomain_id($domain_id);
					//domain_id

					$deliveryservicearea->setCreated_by($this->get('session')->get('user_id'));
					$deliveryservicearea->setIs_deleted(0);
					$em = $this->getDoctrine()->getManager();
					$em->persist($deliveryservicearea);
					$em->flush();
				}

				$this->get('session')->getFlashBag()->set('success_msg','Service Area saved successfully');
				return $this->redirect($this->generateUrl('admin_area_servicearea_boy',array("domain"=>$this->get('session')->get('domain'),'user_id'=>$_POST['area_user_id'])));

			}
			else
			{
				$this->get('session')->getFlashBag()->set('error_msg','Please select at least one service area');
				return $this->redirect($this->generateUrl('admin_area_servicearea_boy',array("domain"=>$this->get('session')->get('domain'),'user_id'=>$_POST['area_user_id'])));
			}
		}
		else
		{
			$this->get('session')->getFlashBag()->set('error_msg','Oops! Something goes wrong! Try again!');
			return $this->redirect($this->generateUrl('admin_area_servicearea_boy',array("domain"=>$this->get('session')->get('domain'),'user_id'=>$_POST['area_user_id'])));
		}
	}

	/**
	* @Route("/getareadrop")
	**/
	public function getareadropAction()
	{
		if(isset($_POST['flag']) && $_POST['flag'] == "get_area_by_city" && $_POST['id'] != "")
		{
			$id = $_POST['id'];
			$area_list = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Areamaster')
				   ->findBy(array('status'=>'active','language_id'=>1,'main_city_id'=>$id,'is_deleted'=>0));

			if(!empty($area_list))
			{
				$html = '<label class="col-sm-2 control-label">Area</label>';
				$html .= '<div class="col-sm-4">';  	   	   
				$html .= '<select class="form-control" name="area" tequired>';
				$html .= '<option value="">-- select city --</option>';

				foreach($area_list as $key=>$val)
				{
					$html .= '<option value="'.$val->getMain_area_id().'">'.$val->getArea_name().'</option>';
				}

				$html .= '</select>';
				$html .= '</div>';
			}
			else
			{
				$html = '<label class="control-label" for="inputError"><i class="fa fa-alert"></i> Area not found!</label>';
			}

			$content = array("html"=>$html);
			return new Response(json_encode($content));
		} 
	}


	/**
	 *@Route("/deleteservicearea/{delivery_service_area_id}/{user_id}")
	 **/
	public function deleteserviceareaAction($delivery_service_area_id,$user_id){	
		$em = $this->getDoctrine()->getManager();
		$delivery_service_area_info = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Deliveryservicearea')->findOneBy(array("is_deleted"=>0,"delivery_service_area_id"=>$delivery_service_area_id));
		if(!empty($delivery_service_area_info)){
			$delivery_service_area_info->setIs_deleted(1);
			$em->persist($delivery_service_area_info);
			$em->flush();
		}
		$this->get('session')->getFlashBag()->set('success_msg', "Deleted successfully");	

		return $this->redirect($this->generateUrl('admin_area_servicearea_boy',array("domain"=>$this->get('session')->get('domain_id'),"user_id"=>$user_id)));
		
	}

}