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

use AdminBundle\Entity\Servicearearelation;

use AdminBundle\Entity\Servicecountryrelation;

use AdminBundle\Entity\Servicestaterelation;

use AdminBundle\Entity\Servicecityrelation;

use AdminBundle\Entity\Productcityrelation;

use AdminBundle\Entity\Couponcityrelation;



use AdminBundle\Entity\Couponmaster;

use AdminBundle\Entity\Appliedcouponrelation;

use AdminBundle\Entity\Couponappliedlist;

use AdminBundle\Entity\Custassigncopon;



/**

* @Route("/{domain}")

*/



class ServiceareaController extends BaseController

{

	public function __construct()

    {

		parent::__construct();

        $obj = new BaseController();

		$obj->checkSessionAction();

    }

    /**

     * @Route("/servicearealist")

     * @Template()

     */

    public function indexAction()

    {

    	$em = $this->getDoctrine()->getManager();

		$connection = $em->getConnection();

		$statement = $connection->prepare("SELECT service_area_relation.*,area_master.area_name,city_master.city_name,state_master.state_name FROM service_area_relation JOIN area_master ON service_area_relation.main_area_id = area_master.main_area_id JOIN city_master ON service_area_relation.main_city_id = city_master.main_city_id JOIN state_master ON service_area_relation.main_state_id = state_master.main_state_id WHERE area_master.language_id = 1 AND state_master.language_id = 1 AND city_master.language_id = 1 AND service_area_relation.is_deleted = 0 AND area_master.is_deleted = 0 AND state_master.is_deleted = 0 AND city_master.is_deleted = 0 AND service_area_relation.domain_id = '".$this->get('session')->get('domain_id')."'");

		$statement->execute();

		$service_area_list = $statement->fetchAll();



    	return array("service_area_list"=>$service_area_list);

    }

    /**

     * @Route("/addservicearea")

     * @Template()

     */

    public function addserviceareaAction()

    {

    	/*$state_master = $this->getDoctrine()

						   ->getManager()

						   ->getRepository('AdminBundle:Statemaster')

						   ->findBy(array('main_country_id'=>99,'language_id'=>1,'status'=>'active','is_deleted'=>0));*/



		$em = $this->getDoctrine()->getManager();

		$connection = $em->getConnection();

		$statement = $connection->prepare("SELECT service_country_relation.*,country_master.country_title FROM service_country_relation JOIN country_master ON service_country_relation.main_country_id = country_master.main_country_id WHERE country_master.language_id = 1 AND country_master.status = 'active' AND country_master.is_deleted = 0 AND service_country_relation.status = 'active' AND service_country_relation.is_deleted = 0 AND service_country_relation.domain_id = '".$this->get('session')->get('domain_id')."'");

		$statement->execute();

		$service_country_list = $statement->fetchAll();



    	return array("service_country_list"=>$service_country_list);

    }

    /**

     * @Route("/getservicecityoption")

     */

    public function getservicecityoptionAction()

    {

    	if(isset($_POST['flag']) && $_POST['flag'] = 'getcity_options' && $_POST['main_state_id'] != "" && $_POST['lang_id'] != "")

    	{



			$em = $this->getDoctrine()->getManager();

			$connection = $em->getConnection();

			$statement = $connection->prepare("

			SELECT service_city_relation.*,city_master.city_name

			FROM

			service_city_relation

			JOIN city_master ON service_city_relation.main_city_id = city_master.main_city_id

			WHERE

			service_city_relation.domain_id = '".$this->get('session')->get('domain_id')."' AND service_city_relation.is_deleted = 0 AND service_city_relation.status = 'active' AND service_city_relation.main_state_id = '".$_POST['main_state_id']."' AND city_master.language_id = 1 AND city_master.status = 'active' AND city_master.is_deleted = 0

			");

			$statement->execute();

			$service_city_list = $statement->fetchAll();



			$html = '<option value="">Select City</option>';

			if(!empty($service_city_list))

			{

				foreach($service_city_list as $key=>$val)

				{

					$html .= '<option value="'.$val['main_city_id'].'">'.$val['city_name'].'</option>';

				}

			}

			return new Response($html);

		}

    }

    /**

     * @Route("/getservicearealist")

     */

    public function getservicearealistAction()

    {

    	if(isset($_POST['flag']) && $_POST['flag'] = 'getarea_list' && $_POST['main_city_id'] != "" && $_POST['lang_id'] != "")

    	{

			$area_master = $this->getDoctrine()

						   ->getManager()

						   ->getRepository('AdminBundle:Areamaster')

						   ->findBy(array('main_city_id'=>$_POST['main_city_id'],'language_id'=>$_POST['lang_id'],'status'=>'active','is_deleted'=>0));



			$service_area_relation = $this->getDoctrine()

						   ->getManager()

						   ->getRepository('AdminBundle:Servicearearelation')

						   ->findBy(array('main_city_id'=>$_POST['main_city_id'],'domain_id'=>$this->get('session')->get('domain_id'),'is_deleted'=>0));



			?>

			<div class="box box-primary box-solid">

                <div class="box-header with-border">

                    <h3 class="box-title">Area list</h3>

                </div>

                <div class="box-body">





                    <?php

                    foreach($area_master as $key=>$val)

                    {

                    ?>

                    <div class="col-sm-3">

                        <div class="checkbox">

                            <label>

                                <input type="checkbox" value="<?php echo $val->getMain_area_id(); ?>" name="service_area_main_id[]"

                                <?php

                                if(!empty($service_area_relation))

                                {

	                                foreach($service_area_relation as $skey=>$sval)

	                                {

										if($sval->getMain_area_id() == $val->getMain_area_id())

										{

											echo 'checked="checked"';

										}

									}

								}

                                ?>>

                                <?php echo $val->getArea_name(); ?>

                            </label>

                        </div>

                    </div>

                    <?php

                    }

                    ?>





                </div>

            </div>

			<?php



			return new Response();

		}

    }

    /**

     * @Route("/savearealist")

     */

    public function savearealistAction()

    {

    	if(isset($_POST['save_service_area']) && $_POST['save_service_area'] == 'save_service_area')

    	{

			if($_POST['main_state_id'] != "" && $_POST['main_city_id'] != "" && isset($_POST['service_area_main_id']) && !empty($_POST['service_area_main_id']))

			{

				$em = $this->getDoctrine()->getManager();

				$connection = $em->getConnection();

				$statement = $connection->prepare("UPDATE service_area_relation SET is_deleted = 1 WHERE domain_id = '".$this->get('session')->get('domain_id')."' AND main_state_id = '".$_POST['main_state_id']."' AND main_city_id = '".$_POST['main_city_id']."' AND is_deleted = 0");

				$statement->execute();



				foreach($_POST['service_area_main_id'] as $key=>$val)

				{

					$service_area_relation = new Servicearearelation();

					$service_area_relation->setMain_area_id($val);

					$service_area_relation->setMain_city_id($_POST['main_city_id']);

					$service_area_relation->setMain_state_id($_POST['main_state_id']);

					$service_area_relation->setDomain_id($this->get('session')->get('domain_id'));

					$service_area_relation->setStatus('active');

					$service_area_relation->setCreate_date(date("Y-m-d H:i:s"));

					$service_area_relation->setIs_deleted(0);



					$em = $this->getDoctrine()->getManager();

					$em->persist($service_area_relation);

					$em->flush();



				}

				$this->get('session')->getFlashBag()->set('success_msg','Service Area saved successfully');

				return $this->redirect($this->generateUrl('admin_servicearea_index',array("domain"=>$this->get('session')->get('domain'))));

			}

			else

			{

				$this->get('session')->getFlashBag()->set('error_msg','Please fill all required fields!');

			}

		}

		else

		{

			$this->get('session')->getFlashBag()->set('error_msg','Oops! Something goes wrong! Try again later!');

		}

		return $this->redirect($this->generateUrl('admin_servicearea_addservicearea',array("domain"=>$this->get('session')->get('domain'))));

    }

    /**

     * @Route("/removeservicearea/{service_area_relation_id}")

     */

    public function removeserviceareaAction($service_area_relation_id)

    {

    	$em = $this->getDoctrine()->getManager();

		$connection = $em->getConnection();

		$statement = $connection->prepare("UPDATE service_area_relation SET is_deleted = 1 WHERE service_area_relation_id = '".$service_area_relation_id."'");

		$statement->execute();

		$this->get('session')->getFlashBag()->set('success_msg','Service Area deleted successfully');

		return $this->redirect($this->generateUrl('admin_servicearea_index',array("domain"=>$this->get('session')->get('domain'))));

    }

	/**

     * @Route("/servicecountrylist")

     * @Template()

     */

    public function servicecountrylistAction()

    {

    	$country_master= $this->getDoctrine()

					   ->getManager()

					   ->getRepository('AdminBundle:Countrymaster')

					   ->findBy(array('language_id'=>1,'status'=>'active','is_deleted'=>0));



		$service_country_relation = $this->getDoctrine()

					   ->getManager()

					   ->getRepository('AdminBundle:Servicecountryrelation')

					   ->findBy(array('domain_id'=>$this->get('session')->get('domain_id'),'status'=>'active','is_deleted'=>0));



    	return array("country_master"=>$country_master,"service_country_relation"=>$service_country_relation);

    }

    /**

     * @Route("/savecountrylist")

     */

    public function savecountrylistAction()

    {

    	//if save button clicked

    	if(isset($_POST['save_service_country']) && $_POST['save_service_country'] == 'save_service_country')

    	{

    		//form field validation check

			if(isset($_POST['main_country_id']) && !empty($_POST['main_country_id']))

			{

				$em = $this->getDoctrine()->getManager();

				$connection = $em->getConnection();

				$statement = $connection->prepare("UPDATE service_country_relation SET is_deleted = 1 WHERE domain_id = '".$this->get('session')->get('domain_id')."' AND is_deleted = 0");

				$statement->execute();



				foreach($_POST['main_country_id'] as $key=>$val)

				{

					$service_country_relation = new Servicecountryrelation();

					$service_country_relation->setMain_country_id($val);

					$service_country_relation->setDomain_id($this->get('session')->get('domain_id'));

					$service_country_relation->setStatus('active');

					$service_country_relation->setCreate_date(date("Y-m-d H:i:s"));

					$service_country_relation->setIs_deleted(0);

					$em = $this->getDoctrine()->getManager();

					$em->persist($service_country_relation);

					$em->flush();

				}

				$this->get('session')->getFlashBag()->set('success_msg','Countries saved successfully');

				return $this->redirect($this->generateUrl('admin_servicearea_servicecountrylist',array("domain"=>$this->get('session')->get('domain'))));

			}

			//form field validation check else part

			else

			{

				$this->get('session')->getFlashBag()->set('error_msg','Choose atleast one country');

			}

		}

		//if save button clicked else part

		else

		{



		}

    }

	 /**

     * @Route("/servicestatelist")

     * @Template()

     */

    public function servicestatelistAction()

    {

    	$em = $this->getDoctrine()->getManager();

		$connection = $em->getConnection();

		$statement = $connection->prepare("SELECT service_state_relation.*,state_master.state_name,country_master.country_title FROM service_state_relation JOIN state_master ON service_state_relation.main_state_id = state_master.main_state_id JOIN country_master ON service_state_relation.main_country_id = country_master.main_country_id WHERE service_state_relation.domain_id = '".$this->get('session')->get('domain_id')."' AND service_state_relation.is_deleted = 0 AND state_master.is_deleted = 0 AND country_master.is_deleted = 0 AND state_master.language_id = 1 AND country_master.language_id = 1");

		$statement->execute();

		$service_state_list = $statement->fetchAll();



    	return array("service_state_list"=>$service_state_list);

    }

    /**

     * @Route("/addservicestate")

     * @Template()

     */

    public function addservicestateAction()

    {

    	$em = $this->getDoctrine()->getManager();

		$connection = $em->getConnection();

		$statement = $connection->prepare("SELECT service_country_relation.*,country_master.country_title FROM service_country_relation JOIN country_master ON service_country_relation.main_country_id = country_master.main_country_id WHERE country_master.language_id = 1 AND country_master.status = 'active' AND country_master.is_deleted = 0 AND service_country_relation.status = 'active' AND service_country_relation.is_deleted = 0 AND service_country_relation.domain_id = '".$this->get('session')->get('domain_id')."'");

		$statement->execute();

		$service_country_list = $statement->fetchAll();

		//var_dump($service_country_list);exit;

    	return array("service_country_list"=>$service_country_list);

    }

     /**

     * @Route("/getservicestatelist")

     */

    public function getservicestatelistAction()

    {

    	if(isset($_POST['flag']) && $_POST['flag'] = 'getstate_list' && $_POST['main_country_id'] != "" && $_POST['lang_id'] != "")

    	{

			$state_master = $this->getDoctrine()

						   ->getManager()

						   ->getRepository('AdminBundle:Statemaster')

						   ->findBy(array('main_country_id'=>$_POST['main_country_id'],'language_id'=>$_POST['lang_id'],'status'=>'active','is_deleted'=>0));

			$service_state_relation = $this->getDoctrine()

						   ->getManager()

						   ->getRepository('AdminBundle:Servicestaterelation')

						   ->findBy(array('main_country_id'=>$_POST['main_country_id'],'domain_id'=>$this->get('session')->get('domain_id'),'is_deleted'=>0));



			$html = '<option value="">Select City</option>';

			if(!empty($state_master))

			{

				?>

				<div class="box box-primary box-solid">

	                <div class="box-header with-border">

	                    <h3 class="box-title">State list</h3>

	                </div>

	                <div class="box-body">





	                    <?php

	                    foreach($state_master as $key=>$val)

	                    {

	                    ?>

	                    <div class="col-sm-3">

	                        <div class="checkbox">

	                            <label>

	                                <input type="checkbox" value="<?php echo $val->getMain_state_id(); ?>" name="service_state_main_id[]"

	                                <?php

	                                if(!empty($service_state_relation))

	                                {

		                                foreach($service_state_relation as $skey=>$sval)

		                                {

											if($sval->getMain_state_id() == $val->getMain_state_id())

											{

												echo 'checked="checked"';

											}

										}

									}

	                                ?>>

	                                <?php echo $val->getState_name(); ?>

	                            </label>

	                        </div>

	                    </div>

	                    <?php

	                    }

	                    ?>





	                </div>

	            </div>

				<?php



			}



			return new Response();

		}

    }

	/**

     * @Route("/savestatelist")

     */

    public function savestatelistAction()

    {

    	if(isset($_POST['save_service_state']) && $_POST['save_service_state'] == 'save_service_state')

    	{

			if($_POST['main_country_id'] != "" && isset($_POST['service_state_main_id']) && !empty($_POST['service_state_main_id']))

			{

				$em = $this->getDoctrine()->getManager();

				$connection = $em->getConnection();

				$statement = $connection->prepare("UPDATE service_state_relation SET is_deleted = 1 WHERE domain_id = '".$this->get('session')->get('domain_id')."' AND main_country_id = '".$_POST['main_country_id']."' AND is_deleted = 0");

				$statement->execute();



				foreach($_POST['service_state_main_id'] as $key=>$val)

				{

					$service_state_relation = new Servicestaterelation();

					$service_state_relation->setMain_country_id($_POST['main_country_id']);

					$service_state_relation->setMain_state_id($val);

					$service_state_relation->setDomain_id($this->get('session')->get('domain_id'));

					$service_state_relation->setStatus("active");

					$service_state_relation->setCreate_date(date("Y-m-d H:i:s"));

					$service_state_relation->setIs_deleted(0);

					$em = $this->getDoctrine()->getManager();

					$em->persist($service_state_relation);

					$em->flush();



				}

				$this->get('session')->getFlashBag()->set('success_msg','Service State saved successfully');

				return $this->redirect($this->generateUrl('admin_servicearea_servicestatelist',array("domain"=>$this->get('session')->get('domain'))));

			}

			else

			{

				$this->get('session')->getFlashBag()->set('error_msg','Please fill all required fields!');

			}

		}

		else

		{

			$this->get('session')->getFlashBag()->set('error_msg','Oops! Something goes wrong! Try again later!');

		}

		return $this->redirect($this->generateUrl('admin_servicearea_addservicestate',array("domain"=>$this->get('session')->get('domain'))));

    }

    /**

     * @Route("/removeservicestate/{service_state_relation_id}")

     */

    public function removeservicestateAction($service_state_relation_id)

    {

    	$em = $this->getDoctrine()->getManager();

		$connection = $em->getConnection();

		$statement = $connection->prepare("UPDATE service_state_relation SET is_deleted = 1 WHERE service_state_relation_id = '".$service_state_relation_id."'");

		$statement->execute();

		$this->get('session')->getFlashBag()->set('success_msg','Service State deleted successfully');

		return $this->redirect($this->generateUrl('admin_servicearea_servicestatelist',array("domain"=>$this->get('session')->get('domain'))));

    }

	/**

     * @Route("/servicecitylist")

     * @Template()

     */

    public function servicecitylistAction()

    {

    	$em = $this->getDoctrine()->getManager();

		$connection = $em->getConnection();

		$statement = $connection->prepare("

		SELECT service_city_relation.*,country_master.country_title,state_master.state_name,city_master.city_name

		FROM

		service_city_relation

		JOIN country_master ON service_city_relation.main_country_id = country_master.main_country_id

		JOIN state_master ON service_city_relation.main_state_id = state_master.main_state_id

		JOIN city_master ON service_city_relation.main_city_id = city_master.main_city_id

		WHERE

		service_city_relation.domain_id = '".$this->get('session')->get('domain_id')."' AND service_city_relation.is_deleted = 0 AND

		country_master.language_id = 1 AND country_master.status = 'active' AND country_master.is_deleted = 0 AND

		state_master.language_id = 1 AND state_master.status = 'active' AND state_master.is_deleted = 0 AND

		city_master.language_id = 1 AND city_master.status = 'active' AND city_master.is_deleted = 0

		");

		$statement->execute();

		$service_city_list = $statement->fetchAll();



    	return array("service_city_list"=>$service_city_list);

    }

    /**

     * @Route("/addservicecity")

     * @Template()

     */

    public function addservicecityAction()

    {

    	$em = $this->getDoctrine()->getManager();

		$connection = $em->getConnection();

		$statement = $connection->prepare("SELECT service_country_relation.*,country_master.country_title FROM service_country_relation JOIN country_master ON service_country_relation.main_country_id = country_master.main_country_id WHERE country_master.language_id = 1 AND country_master.status = 'active' AND country_master.is_deleted = 0 AND service_country_relation.status = 'active' AND service_country_relation.is_deleted = 0 AND service_country_relation.domain_id = '".$this->get('session')->get('domain_id')."'");

		$statement->execute();

		$service_country_list = $statement->fetchAll();



    	return array("service_country_list"=>$service_country_list);

    }

    /**

     * @Route("/getservicestateoption")

     */

    public function getservicestateoptionAction()

    {

    	if(isset($_POST['flag']) && $_POST['flag'] = 'getstate_options' && $_POST['main_country_id'] != "" && $_POST['lang_id'] != "")

    	{



			$em = $this->getDoctrine()->getManager();

			$connection = $em->getConnection();

			$statement = $connection->prepare("SELECT service_state_relation.*,state_master.state_name FROM service_state_relation JOIN state_master ON service_state_relation.main_state_id = state_master.main_state_id AND service_state_relation.status = 'active' AND service_state_relation.domain_id = '".$this->get('session')->get('domain_id')."' AND service_state_relation.is_deleted = 0 AND state_master.status = 'active' AND state_master.language_id = 1 AND state_master.is_deleted = 0");

			$statement->execute();

			$service_state_list = $statement->fetchAll();



			$html = '<option value="">Select State</option>';

			if(!empty($service_state_list))

			{

				foreach($service_state_list as $key=>$val)

				{

					$html .= '<option value="'.$val['main_state_id'].'">'.$val['state_name'].'</option>';

				}

			}

			return new Response($html);

		}

    }

    /**

     * @Route("/getmaincitylist")

     */

    public function getmaincitylistAction()

    {

    	if(isset($_POST['flag']) && $_POST['flag'] = 'getcity_options' && $_POST['main_state_id'] != "" && $_POST['lang_id'] != "")

    	{

			$city_master = $this->getDoctrine()

						   ->getManager()

						   ->getRepository('AdminBundle:Citymaster')

						   ->findBy(array('main_state_id'=>$_POST['main_state_id'],'language_id'=>$_POST['lang_id'],'status'=>'active','is_deleted'=>0));

			$service_city_relation = $this->getDoctrine()

						   ->getManager()

						   ->getRepository('AdminBundle:Servicecityrelation')

						   ->findBy(array('main_state_id'=>$_POST['main_state_id'],'domain_id'=>$this->get('session')->get('domain_id'),'is_deleted'=>0));



			$html = '<option value="">Select City</option>';

			if(!empty($city_master))

			{



					?>

					<div class="box box-primary box-solid">

		                <div class="box-header with-border">

		                    <h3 class="box-title">City list</h3>

		                </div>

		                <div class="box-body">





		                    <?php

		                    foreach($city_master as $key=>$val)

		                    {

		                    ?>

		                    <div class="col-sm-3">

		                        <div class="checkbox">

		                            <label>

		                                <input type="checkbox" value="<?php echo $val->getMain_city_id(); ?>" name="service_city_main_id[]"

		                                <?php

		                                if(!empty($service_city_relation))

		                                {

			                                foreach($service_city_relation as $skey=>$sval)

			                                {

												if($sval->getMain_city_id() == $val->getMain_city_id())

												{

													echo 'checked="checked"';

												}

											}

										}

		                                ?>>

		                                <?php echo $val->getCity_name(); ?>

		                            </label>

		                        </div>

		                    </div>

		                    <?php

		                    }

		                    ?>





		                </div>

		            </div>

					<?php



			}

			return new Response();

		}

    }
	/**

     * @Route("/getmaincitylist_boy")

     */

    public function getmaincitylist_boyAction()

    {

    	if(isset($_POST['flag']) && $_POST['flag'] = 'getcity_list' && $_POST['main_state_id'] != "" && $_POST['lang_id'] != "")

    	{


			$em = $this->getDoctrine()->getManager();

			$connection = $em->getConnection();

			$statement = $connection->prepare("SELECT service_city_relation.*,city_master.city_name FROM service_city_relation JOIN city_master ON service_city_relation.main_city_id = city_master.main_city_id AND service_city_relation.status = 'active' AND service_city_relation.main_state_id='".$_POST['main_state_id']."' AND service_city_relation.domain_id = '".$this->get('session')->get('domain_id')."' AND service_city_relation.is_deleted = 0 AND city_master.status = 'active' AND city_master.language_id = 1 AND city_master.is_deleted = 0");

			$statement->execute();

			$service_city_list = $statement->fetchAll();
			

			$html = '<option value="">Select City</option>';

			if(!empty($service_city_list))

			{

				foreach($service_city_list as $key=>$val)

				{

					$html .= '<option value="'.$val['main_city_id'].'">'.$val['city_name'].'</option>';

				}

			}

			return new Response($html);

		}

    }
	 /**

     * @Route("/getmainareaoption")

     */

    public function getmainareaoptionAction()

    {

    	if(isset($_POST['flag']) && isset($_POST['user_id']) && $_POST['flag'] = 'getarea_options' && $_POST['main_city_id'] != "" && $_POST['lang_id'] != "")

    	{
/*
			$area_master = $this->getDoctrine()

						   ->getManager()

						   ->getRepository('AdminBundle:Areamaster')

						   ->findBy(array('main_city_id'=>$_POST['main_city_id'],'language_id'=>$_POST['lang_id'],'status'=>'active','is_deleted'=>0));

			$service_area_relation = $this->getDoctrine()

						   ->getManager()

						   ->getRepository('AdminBundle:Servicearearelation')

						   ->findBy(array('main_city_id'=>$_POST['main_city_id'],'domain_id'=>$this->get('session')->get('domain_id'),'is_deleted'=>0));
*/			$delivery_service_area = $this->getDoctrine()
					->getManager()
					->getRepository('AdminBundle:Deliveryservicearea')
					->findBy(array("status"=>'active',"domain_id"=>$this->get('session')->get('domain_id'),"user_master_id"=>$_POST['user_id']));
			
			//var_dump($delivery_service_area);exit;			
			
			$em = $this->getDoctrine()->getManager();

			$connection = $em->getConnection();

			$statement = $connection->prepare("SELECT service_area_relation.*,area_master.area_name FROM service_area_relation JOIN area_master ON service_area_relation.main_area_id = area_master.main_area_id AND service_area_relation.status = 'active' AND service_area_relation.main_city_id='".$_POST['main_city_id']."' AND service_area_relation.domain_id = '".$this->get('session')->get('domain_id')."' AND service_area_relation.is_deleted = 0 AND area_master.status = 'active' AND area_master.language_id = 1 AND area_master.is_deleted = 0");

			$statement->execute();

			$service_area_list = $statement->fetchAll();
			$service_area_relation = null;
			//var_dump($service_area_list);
			
			$html = '<option value="">Select Aera</option>';

			if(!empty($service_area_list))
			{
				?>
					<div class="box box-primary box-solid">
		                <div class="box-header with-border">
		                    <h3 class="box-title">Area list</h3>
		                </div>
		                <div class="box-body">
		                    <?php
		                    foreach($service_area_list as $key=>$val)
		                    {
		                    ?>

		                    <div class="col-sm-3">

		                        <div class="checkbox">

		                            <label>

		                                <input type="checkbox" value="<?php echo $val['main_area_id']; ?>" name="service_area_main_id[]"

		                                <?php

		                                if(!empty($delivery_service_area))

		                                {

			                                foreach($delivery_service_area as $skey=>$sval)

			                                {

												if($sval->getArea_id() == $val['main_area_id'])

												{

													//echo 'checked="checked"';
													echo "disabled=disabled";
													//echo 'disable';

												}

											}

										}

		                                ?>>

		                                <?php echo $val['area_name']; ?>

		                            </label>

		                        </div>

		                    </div>

		                    <?php

		                    }

		                    ?>





		                </div>

		            </div>

					<?php



			}

			return new Response();

		}

    }
	/**

     * @Route("/savecitylist")

     */

	public function savecitylistAction()

    {

    	if(isset($_POST['save_service_city']) && $_POST['save_service_city'] == 'save_service_city')

    	{

			if($_POST['main_country_id'] != "" && $_POST['main_state_id'] != "" && isset($_POST['service_city_main_id']) && !empty($_POST['service_city_main_id']))

			{

				$em = $this->getDoctrine()->getManager();

				$connection = $em->getConnection();

				$statement = $connection->prepare("UPDATE service_city_relation SET is_deleted = 1 WHERE domain_id = '".$this->get('session')->get('domain_id')."' AND main_country_id = '".$_POST['main_country_id']."' AND main_state_id = '".$_POST['main_state_id']."' AND is_deleted = 0");

				$statement->execute();



				foreach($_POST['service_city_main_id'] as $key=>$val)

				{

					$service_city_relation = new Servicecityrelation();

					$service_city_relation->setMain_country_id($_POST['main_country_id']);

					$service_city_relation->setMain_state_id($_POST['main_state_id']);

					$service_city_relation->setMain_city_id($val);

					$service_city_relation->setDomain_id($this->get('session')->get('domain_id'));

					$service_city_relation->setStatus('active');

					$service_city_relation->setCreate_date(date("Y-m-d H:i:s"));

					$service_city_relation->setIs_deleted(0);

					$em = $this->getDoctrine()->getManager();

					$em->persist($service_city_relation);

					$em->flush();



				}

				$this->get('session')->getFlashBag()->set('success_msg','Service City saved successfully');

				return $this->redirect($this->generateUrl('admin_servicearea_servicecitylist',array("domain"=>$this->get('session')->get('domain'))));

			}

			else

			{

				$this->get('session')->getFlashBag()->set('error_msg','Please fill all required fields!');

			}

		}

		else

		{

			$this->get('session')->getFlashBag()->set('error_msg','Oops! Something goes wrong! Try again later!');

		}

		return $this->redirect($this->generateUrl('admin_servicearea_addservicecity',array("domain"=>$this->get('session')->get('domain'))));

    }

    /**

     * @Route("/removeservicecity/{service_city_relation_id}")

     */

    public function removeservicecityAction($service_city_relation_id)

    {

    	$em = $this->getDoctrine()->getManager();

		$connection = $em->getConnection();

		$statement = $connection->prepare("UPDATE service_city_relation SET is_deleted = 1 WHERE service_city_relation_id = '".$service_city_relation_id."'");

		$statement->execute();

		$this->get('session')->getFlashBag()->set('success_msg','Service City deleted successfully');

		return $this->redirect($this->generateUrl('admin_servicearea_servicecitylist',array("domain"=>$this->get('session')->get('domain'))));

    }

    /**

     * @Route("/changestatus")

     */

    public function changestatusAction()

    {

    	if(isset($_POST['flag']) && $_POST['flag'] == 'change_status' && $_POST['pkey'] != "" && $_POST['tbl'] != "")

    	{

    		if($_POST['tbl'] == 'country'){$pkey = "service_country_relation_id";$tbl = "Servicecountryrelation";}

    		if($_POST['tbl'] == 'state'){$pkey = "service_state_relation_id";$tbl = "Servicestaterelation";}

    		if($_POST['tbl'] == 'city'){$pkey = "service_city_relation_id";$tbl = "Servicecityrelation";}

    		if($_POST['tbl'] == 'area'){$pkey = "service_area_relation_id";$tbl = "Servicearearelation";}

    		if($_POST['tbl'] == 'product_city'){$pkey = "product_city_relation_id";$tbl = "Productcityrelation";}



			$relation_table = $this->getDoctrine()

						   ->getManager()

						   ->getRepository('AdminBundle:'.$tbl)

						   ->findOneBy(array($pkey=>$_POST['pkey'],'domain_id'=>$this->get('session')->get('domain_id'),'is_deleted'=>0));



			if(!empty($relation_table))

			{

				if($relation_table->getStatus() == 'active')

				{

					$sts = 'inactive';

				}

				else

				{

					$sts = 'active';

				}



				$relation_table->setStatus($sts);

				$em = $this->getDoctrine()->getManager();

				$em->persist($relation_table);

				$em->flush();



				return new Response(json_encode(array("status"=>$sts)));

			}

		}

    }





    /**

     * @Route("/changestatuscouponcity")

     */

    public function changestatuscouponcityAction()

    {

    	if(isset($_POST['flag']) && $_POST['flag'] == 'change_status_couponcity' && $_POST['pkey'] != "" && $_POST['tbl'] != "")

    	{

    		if($_POST['tbl'] == 'country'){$pkey = "service_country_relation_id";$tbl = "Servicecountryrelation";}

    		if($_POST['tbl'] == 'state'){$pkey = "service_state_relation_id";$tbl = "Servicestaterelation";}

    		if($_POST['tbl'] == 'city'){$pkey = "service_city_relation_id";$tbl = "Servicecityrelation";}

    		if($_POST['tbl'] == 'area'){$pkey = "service_area_relation_id";$tbl = "Servicearearelation";}

    		if($_POST['tbl'] == 'coupon_city'){$pkey = "coupon_city_relation_id";$tbl = "Couponcityrelation";}



			$relation_table = $this->getDoctrine()

						   ->getManager()

						   ->getRepository('AdminBundle:'.$tbl)

						   ->findOneBy(array($pkey=>$_POST['pkey'],'domain_id'=>$this->get('session')->get('domain_id'),'is_deleted'=>0));



			if(!empty($relation_table))

			{

				if($relation_table->getStatus() == 'active')

				{

					$sts = 'inactive';

				}

				else

				{

					$sts = 'active';

				}



				$relation_table->setStatus($sts);

				$em = $this->getDoctrine()->getManager();

				$em->persist($relation_table);

				$em->flush();



				return new Response(json_encode(array("status"=>$sts)));

			}

		}

    }



    /**

     * @Route("/sendnotificationcity")

     */

    public function sendnotificationcityAction()

    {

    	if(isset($_POST['flag']) && $_POST['flag'] == 'send_notification' && $_POST['coupon_id'] != "" && $_POST['city_id'] != "")

    	{

			$coupon_master_id = $coupon_id = $_POST['coupon_id'];

			$city_id = $_POST['city_id'];

			$msg="";

			$visible_all = 'no';

					//start - by naman on 2016-07-25 for coupon notification issue resolution



						//----- Push Notificaiton ---

						$user_array = array();

						if(!empty($this->get('session')->get('domain_id')))

	    				{

	    					if($this->get('session')->get('role_id')== '1')

	    					{

								$user_master = $this->getDoctrine()

									   ->getManager()

									   ->getRepository('AdminBundle:Usermaster')

									   ->findBy(array("user_role_id"=>7,"user_status"=>"active","is_deleted"=>0,"user_type"=>"user"));





								foreach($user_master as $key=>$val1)

								{

									$user_array[] = $val1->getUser_master_id();

								}

							}

							else

							{

									$user_master = $this->getDoctrine()

														->getManager()

														->getRepository('AdminBundle:Usermaster')

														->findBy(array("user_role_id"=>7,"user_status"=>"active","is_deleted"=>0,"domain_id"=>$this->get('session')->get('domain_id'),"user_type"=>"user"));



									foreach($user_master as $key=>$val1)

									{

										$user_array[] = $val1->getUser_master_id();

									}

							}

						}

						//var_dump($user_array);

						//exit;

						$final_user_array =  array();

						foreach($user_array as $val_user){

							//var_dump($val_user);exit;

							$em = $this->getDoctrine()->getManager();

							$connection = $em->getConnection();

							$statement = $connection->prepare("select delivery_address_id from order_master where order_createdby='".$val_user."' order by `order_master_id` desc LIMIT 0,1");

							$statement->execute();

							$delivery_address_id = $statement->fetchAll();

							$address_id=0;

							if(isset($delivery_address_id) && !empty($delivery_address_id))

							{

								$address_id = $delivery_address_id[0]['delivery_address_id'];

							}

							//var_dump($address_id);exit;

							if(!empty($address_id) && $address_id!=0)

							{

								$address_master_id ="";

								$connection = $em->getConnection();

								$statement = $connection->prepare("select `city_id` from address_master where `address_master_id`='".$address_id."' and city_id = '".$city_id."'");

								$statement->execute();

								$address_master_id = $statement->fetchAll();



								if(!empty($address_master_id) && $address_master_id!="")

								{

									//var_dump($address_master_id);exit;

									$final_user_array[] = $val_user;

								}

							}

							//var_dump($order_master_id);exit;



						}

						//var_dump($final_user_array);

						//exit;

						$app_id="CUST";

						try{

							if(!empty($final_user_array))

							{



								$coupon_master = $this->getDoctrine()

									   ->getManager()

									   ->getRepository('AdminBundle:Couponmaster')

									   ->findOneBy(array('coupon_master_id'=>$coupon_master_id,'is_deleted'=>0));





									$visible_all = $coupon_master->getVisible_all();



									   //var_dump($coupon_master);exit;

									   $response = array(

											"coupon_code"=>$coupon_master->getCoupon_code(),

											"start_date"=>$coupon_master->getStart_date(),

											"end_date"=>$coupon_master->getEnd_date(),

											"discount_value"=>$coupon_master->getDiscount_value(),

											"discount_type"=>$coupon_master->getDiscount_type(),

											"min_order_val"=>$coupon_master->getMin_order_amount()

										);



										$message = json_encode(array("detail"=>"New Coupon Code : '".$coupon_master->getCoupon_code()."'","code" => '6', "response" => $response));



								//save information in cust_assign_coupon

								$data['custcounpon']['domain_id'] = $this->get('session')->get('domain_id');

								$data['custcounpon']['app_id']= $app_id;

								$data['custcounpon']['table_name']= 'coupon_master';

								$data['custcounpon']['table_id']= $coupon_master->getCoupon_master_id();

								$this->_savecustomerassigncoupons($data);



								$gcm_regids = $this->find_gcm_regid($final_user_array);

								if(!empty($gcm_regids) && $visible_all!='no')

								{



									if (count($gcm_regids[0])>0)

									{

										//var_dump($gcm_regids);exit;

										$this->send_notification($gcm_regids,"New Coupon",$message,2,$app_id,$this->get('session')->get('domain_id'),"coupon_master",$coupon_master->getCoupon_master_id());

									}

								}



								$apns_regids = $this->find_apns_regid($final_user_array);

								if(!empty($apns_regids) && $visible_all!='no')

								{

									if (count($apns_regids[0])>0)

									{

										//var_dump($gcm_regids);exit;

										$this->send_notification($apns_regids,"New Coupon",$message,1,$app_id,$this->get('session')->get('domain_id'),"coupon_master",$coupon_master->getCoupon_master_id());

									}

								}

							}

							if($visible_all=='no')

							{

								$msg = "Notifications could not be send Becouse Coupon Visibility is not checked!";

							}

							else{

								$msg = "Notifications has been sent successfully.";

							}

						}catch(\Exception $e){

							$msg = "Something is wrong with Notification Service!";

						}

						//----- Push Notificaiton ---





					//end - by naman on 2016-07-25 for coupon notification issue resolution



			return new Response(json_encode(array("msg"=>$msg,"coupon_id"=>$coupon_id,"city_id"=>$city_id)));

		}

    }

    /**

     * @Route("/productcity/{main_product_id}")

     * @Template()

     */

    public function productcityAction($main_product_id)

    {

    	$em = $this->getDoctrine()->getManager();

		$connection = $em->getConnection();

		$statement = $connection->prepare("SELECT service_country_relation.*,country_master.country_title FROM service_country_relation JOIN country_master ON service_country_relation.main_country_id = country_master.main_country_id WHERE country_master.language_id = 1 AND country_master.status = 'active' AND country_master.is_deleted = 0 AND service_country_relation.status = 'active' AND service_country_relation.is_deleted = 0 AND service_country_relation.domain_id = '".$this->get('session')->get('domain_id')."'");

		$statement->execute();

		$service_country_list = $statement->fetchAll();



		$em = $this->getDoctrine()->getManager();

		$connection = $em->getConnection();

		$statement = $connection->prepare("

		SELECT

		product_city_relation.*,

		city_master.city_name,

		state_master.state_name,

		country_master.country_title

		FROM

		product_city_relation

		JOIN city_master ON product_city_relation.city_id = city_master.main_city_id

		JOIN state_master ON product_city_relation.state_id = state_master.main_state_id

		JOIN country_master ON product_city_relation.country_id = country_master.main_country_id

		WHERE

		product_city_relation.product_id = '".$main_product_id."' AND

		product_city_relation.domain_id = '".$this->get('session')->get('domain_id')."' AND

		product_city_relation.is_deleted = 0 AND

		city_master.is_deleted = 0 AND

		city_master.language_id = 1 AND

		city_master.status = 'active' AND



		state_master.is_deleted = 0 AND

		state_master.language_id = 1 AND

		state_master.status = 'active' AND



		country_master.is_deleted = 0 AND

		country_master.language_id = 1 AND

		country_master.status = 'active'

		");

		$statement->execute();

		$city_list = $statement->fetchAll();



    	return array("service_country_list"=>$service_country_list,"main_product_id"=>$main_product_id,"city_list"=>$city_list);

    }



    /**

     * @Route("/couponcity/{coupon_master_id}")

     * @Template()

     */

    public function couponcityAction($coupon_master_id)

    {

    	$em = $this->getDoctrine()->getManager();

		$connection = $em->getConnection();

		$statement = $connection->prepare("SELECT service_country_relation.*,country_master.country_title FROM service_country_relation JOIN country_master ON service_country_relation.main_country_id = country_master.main_country_id WHERE country_master.language_id = 1 AND country_master.status = 'active' AND country_master.is_deleted = 0 AND service_country_relation.status = 'active' AND service_country_relation.is_deleted = 0 AND service_country_relation.domain_id = '".$this->get('session')->get('domain_id')."'");

		$statement->execute();

		$service_country_list = $statement->fetchAll();



		$em = $this->getDoctrine()->getManager();

		$connection = $em->getConnection();

		$statement = $connection->prepare("

		SELECT

			coupon_city_relation.*,

			city_master.city_name,

			state_master.state_name,

			country_master.country_title

		FROM coupon_city_relation

		JOIN city_master ON coupon_city_relation.city_id = city_master.main_city_id

		JOIN state_master ON coupon_city_relation.state_id = state_master.main_state_id

		JOIN country_master ON coupon_city_relation.country_id = country_master.main_country_id

		WHERE coupon_city_relation.coupon_id = '".$coupon_master_id."' AND

			coupon_city_relation.domain_id = '".$this->get('session')->get('domain_id')."' AND

			coupon_city_relation.is_deleted = 0 AND

			city_master.is_deleted = 0 AND

			city_master.language_id = 1 AND

			city_master.status = 'active' AND

			state_master.is_deleted = 0 AND

			state_master.language_id = 1 AND

			state_master.status = 'active' AND

			country_master.is_deleted = 0 AND

			country_master.language_id = 1 AND

			country_master.status = 'active'

		");

		$statement->execute();

		$city_list = $statement->fetchAll();



    	return array("service_country_list"=>$service_country_list,"coupon_master_id"=>$coupon_master_id,"city_list"=>$city_list);

    }





    /**

     * @Route("/getproductcity")

     */

    public function getproductcityAction()

    {

    	if(isset($_POST['flag']) && $_POST['flag'] = 'get_product_city' && $_POST['main_state_id'] != "" && $_POST['lang_id'] != "" && $_POST['product_id'] != "")

    	{

			/*$city_master = $this->getDoctrine()

						   ->getManager()

						   ->getRepository('AdminBundle:Citymaster')

						   ->findBy(array('main_state_id'=>$_POST['main_state_id'],'language_id'=>$_POST['lang_id'],'status'=>'active','is_deleted'=>0));*/



			$em = $this->getDoctrine()->getManager();

			$connection = $em->getConnection();

			$statement = $connection->prepare("

			SELECT service_city_relation.*,city_master.city_name

			FROM

			service_city_relation

			JOIN city_master ON service_city_relation.main_city_id = city_master.main_city_id

			WHERE

			service_city_relation.domain_id = '".$this->get('session')->get('domain_id')."' AND service_city_relation.is_deleted = 0 AND service_city_relation.status = 'active' AND service_city_relation.main_state_id = '".$_POST['main_state_id']."' AND city_master.language_id = 1 AND city_master.status = 'active' AND city_master.is_deleted = 0

			");

			$statement->execute();

			$city_master = $statement->fetchAll();



			$product_city_relation = $this->getDoctrine()

						   ->getManager()

						   ->getRepository('AdminBundle:Productcityrelation')

						   ->findBy(array('state_id'=>$_POST['main_state_id'],'product_id'=>$_POST['product_id'],'domain_id'=>$this->get('session')->get('domain_id'),'is_deleted'=>0));



			$html = '<option value="">Select City</option>';

			if(!empty($city_master))

			{



					?>

					<div class="box box-primary box-solid">

		                <div class="box-header with-border">

		                    <h3 class="box-title">City list</h3>

		                </div>

		                <div class="box-body">





		                    <?php

		                    foreach($city_master as $key=>$val)

		                    {

		                    ?>

		                    <div class="col-sm-3">

		                        <div class="checkbox">

		                            <label>

		                                <input type="checkbox" value="<?php echo $val['main_city_id']; ?>" name="product_city_id[]"

		                                <?php

		                                if(!empty($product_city_relation))

		                                {

			                                foreach($product_city_relation as $skey=>$sval)

			                                {

												if($sval->getCity_id() == $val['main_city_id'])

												{

													echo 'checked="checked"';

												}

											}

										}

		                                ?>>

		                                <?php echo $val['city_name']; ?>

		                            </label>

		                        </div>

		                    </div>

		                    <?php

		                    }

		                    ?>





		                </div>

		            </div>

					<?php



			}

			return new Response();

		}

    }





     /**

     * @Route("/getcouponcity")

     */

    public function getcouponcityAction()

    {

    	if(isset($_POST['flag']) && $_POST['flag'] = 'get_coupon_city' && $_POST['main_state_id'] != "" && $_POST['lang_id'] != "" && $_POST['coupon_id'] != "")

    	{

			$em = $this->getDoctrine()->getManager();

			$connection = $em->getConnection();

			$statement = $connection->prepare("

			SELECT service_city_relation.*,city_master.city_name

			FROM

			service_city_relation

			JOIN city_master ON service_city_relation.main_city_id = city_master.main_city_id

			WHERE

			service_city_relation.domain_id = '".$this->get('session')->get('domain_id')."' AND service_city_relation.is_deleted = 0 AND service_city_relation.status = 'active' AND service_city_relation.main_state_id = '".$_POST['main_state_id']."' AND city_master.language_id = 1 AND city_master.status = 'active' AND city_master.is_deleted = 0

			");

			$statement->execute();

			$city_master = $statement->fetchAll();



			$product_city_relation = $this->getDoctrine()

						   ->getManager()

						   ->getRepository('AdminBundle:Couponcityrelation')

						   ->findBy(array('state_id'=>$_POST['main_state_id'],'coupon_id'=>$_POST['coupon_id'],'domain_id'=>$this->get('session')->get('domain_id'),'is_deleted'=>0));



			$html = '<option value="">Select City</option>';

			if(!empty($city_master))

			{



					?>

					<div class="box box-primary box-solid">

		                <div class="box-header with-border">

		                    <h3 class="box-title">City list</h3>

		                </div>

		                <div class="box-body">





		                    <?php

		                    foreach($city_master as $key=>$val)

		                    {

		                    ?>

		                    <div class="col-sm-3">

		                        <div class="checkbox">

		                            <label>

		                                <input type="checkbox" value="<?php echo $val['main_city_id']; ?>" name="coupon_city_id[]"

		                                <?php

		                                if(!empty($product_city_relation))

		                                {

			                                foreach($product_city_relation as $skey=>$sval)

			                                {

												if($sval->getCity_id() == $val['main_city_id'])

												{

													echo 'checked="checked"';

												}

											}

										}

		                                ?>>

		                                <?php echo $val['city_name']; ?>

		                            </label>

		                        </div>

		                    </div>

		                    <?php

		                    }

		                    ?>





		                </div>

		            </div>

					<?php



			}

			return new Response();

		}

    }



	/**

     * @Route("/saveproductcity/{main_product_id}")

     */

    public function saveproductcityAction($main_product_id)

    {

		if(isset($_POST['save_product_city']) && $_POST['save_product_city'] == 'save_product_city')

		{

			if($_POST['main_country_id'] != "" && $_POST['main_state_id'] != "" && !empty($_POST['product_city_id']))

			{

				$em = $this->getDoctrine()->getManager();

				$connection = $em->getConnection();

				//$statement = $connection->prepare("UPDATE product_city_relation SET is_deleted = 1 WHERE domain_id = '".$this->get('session')->get('domain_id')."' AND product_id = '".$main_product_id."' AND country_id = '".$_POST['main_country_id']."' AND state_id = '".$_POST['main_state_id']."' AND is_deleted = 0");

				$statement = $connection->prepare("DELETE FROM product_city_relation WHERE domain_id = '".$this->get('session')->get('domain_id')."' AND product_id = '".$main_product_id."' AND country_id = '".$_POST['main_country_id']."' AND state_id = '".$_POST['main_state_id']."'");

				$statement->execute();



				foreach($_POST['product_city_id'] as $key=>$val)

				{

					$product_city_relation = new Productcityrelation();



					$product_city_relation->setProduct_id($main_product_id);

					$product_city_relation->setCity_id($val);

					$product_city_relation->setCountry_id($_POST['main_country_id']);

					$product_city_relation->setState_id($_POST['main_state_id']);

					$product_city_relation->setStatus('active');

					$product_city_relation->setDomain_id($this->get('session')->get('domain_id'));

					$product_city_relation->setCreate_date(date("Y-m-d H:i:s"));

					$product_city_relation->setIs_deleted(0);

					$em = $this->getDoctrine()->getManager();

					$em->persist($product_city_relation);

					$em->flush();

				}

				$this->get('session')->getFlashBag()->set('success_msg','Product cities saved successfully.');

			}

			else

			{

				$this->get('session')->getFlashBag()->set('error_msg','All fields are required, please select all fields.');

			}

		}

		else

		{

			$this->get('session')->getFlashBag()->set('error_msg','Oops! Something goes wrong! Try again!');

		}

		return $this->redirect($this->generateUrl('admin_servicearea_productcity',array("domain"=>$this->get('session')->get('domain'),"main_product_id"=>$main_product_id)));

	}



	/**

     * @Route("/savecouponcity/{coupon_master_id}")

     */

    public function savecouponcityAction($coupon_master_id)

    {

		if(isset($_POST['save_coupon_city']) && $_POST['save_coupon_city'] == 'save_coupon_city')

		{

			if($_POST['main_country_id'] != "" && $_POST['main_state_id'] != "" && !empty($_POST['coupon_city_id']))

			{

				$em = $this->getDoctrine()->getManager();

				$connection = $em->getConnection();

				//$statement = $connection->prepare("UPDATE coupon_city_relation SET is_deleted = 1 WHERE domain_id = '".$this->get('session')->get('domain_id')."' AND coupon_id = '".$coupon_master_id."' AND country_id = '".$_POST['main_country_id']."' AND state_id = '".$_POST['main_state_id']."' AND is_deleted = 0");

				$statement = $connection->prepare("DELETE FROM coupon_city_relation WHERE domain_id = '".$this->get('session')->get('domain_id')."' AND coupon_id = '".$coupon_master_id."' AND country_id = '".$_POST['main_country_id']."' AND state_id = '".$_POST['main_state_id']."'");

				$statement->execute();



				//var_dump($_POST['coupon_city_id']);exit;



				foreach($_POST['coupon_city_id'] as $key=>$val)

				{

				$city_id = $val;

						//var_dump($val);exit;

					$product_city_relation = new Couponcityrelation();



					$product_city_relation->setCoupon_id($coupon_master_id);

					$product_city_relation->setCity_id($val);

					$product_city_relation->setCountry_id($_POST['main_country_id']);

					$product_city_relation->setState_id($_POST['main_state_id']);

					$product_city_relation->setStatus('active');

					$product_city_relation->setDomain_id($this->get('session')->get('domain_id'));

					$product_city_relation->setCreate_date(date("Y-m-d H:i:s"));

					$product_city_relation->setIs_deleted(0);

					$em = $this->getDoctrine()->getManager();

					$em->persist($product_city_relation);

					$em->flush();



					$visible_all = 'no';

					//start - by naman on 2016-07-25 for coupon notification issue resolution



						//----- Push Notificaiton ---

						$user_array = array();

						if(!empty($this->get('session')->get('domain_id')))

	    				{

	    					if($this->get('session')->get('role_id')== '1')

	    					{

								$user_master = $this->getDoctrine()

									   ->getManager()

									   ->getRepository('AdminBundle:Usermaster')

									   ->findBy(array("user_role_id"=>7,"user_status"=>"active","is_deleted"=>0,"user_type"=>"user"));





								foreach($user_master as $key=>$val1)

								{

									$user_array[] = $val1->getUser_master_id();

								}

							}

							else

							{

									$user_master = $this->getDoctrine()

														->getManager()

														->getRepository('AdminBundle:Usermaster')

														->findBy(array("user_role_id"=>7,"user_status"=>"active","is_deleted"=>0,"domain_id"=>$this->get('session')->get('domain_id'),"user_type"=>"user"));



									foreach($user_master as $key=>$val1)

									{

										$user_array[] = $val1->getUser_master_id();

									}

							}

						}

						//var_dump($user_array);

						//exit;

						$final_user_array =  array();

						foreach($user_array as $val_user){

							//var_dump($val_user);exit;

							$em = $this->getDoctrine()->getManager();

							$connection = $em->getConnection();

							$statement = $connection->prepare("select delivery_address_id from order_master where order_createdby='".$val_user."' order by `order_master_id` desc LIMIT 0,1");

							$statement->execute();

							$delivery_address_id = $statement->fetchAll();

							$address_id=0;

							if(isset($delivery_address_id) && !empty($delivery_address_id))

							{

								$address_id = $delivery_address_id[0]['delivery_address_id'];

							}

							//var_dump($address_id);exit;

							if(!empty($address_id) && $address_id!=0)

							{

								$address_master_id ="";

								$connection = $em->getConnection();

								$statement = $connection->prepare("select `city_id` from address_master where `address_master_id`='".$address_id."' and city_id = '".$city_id."'");

								$statement->execute();

								$address_master_id = $statement->fetchAll();



								if(!empty($address_master_id) && $address_master_id!="")

								{

									//var_dump($address_master_id);exit;

									$final_user_array[] = $val_user;

								}

							}

							//var_dump($order_master_id);exit;



						}

						//var_dump($final_user_array);

						//exit;

						$app_id="CUST";

						if(!empty($final_user_array))

						{



							$coupon_master = $this->getDoctrine()

								   ->getManager()

								   ->getRepository('AdminBundle:Couponmaster')

								   ->findOneBy(array('coupon_master_id'=>$coupon_master_id,'is_deleted'=>0));





								$visible_all = $coupon_master->getVisible_all();



								   //var_dump($coupon_master);exit;

								   $response = array(

										"coupon_code"=>$coupon_master->getCoupon_code(),

										"start_date"=>$coupon_master->getStart_date(),

										"end_date"=>$coupon_master->getEnd_date(),

										"discount_value"=>$coupon_master->getDiscount_value(),

										"discount_type"=>$coupon_master->getDiscount_type(),

										"min_order_val"=>$coupon_master->getMin_order_amount()

									);



									$message = json_encode(array("detail"=>"New Coupon Code : '".$coupon_master->getCoupon_code()."'","code" => '6', "response" => $response));



							//save information in cust_assign_coupon

							$data['custcounpon']['domain_id'] = $this->get('session')->get('domain_id');

							$data['custcounpon']['app_id']= $app_id;

							$data['custcounpon']['table_name']= 'coupon_master';

							$data['custcounpon']['table_id']= $coupon_master->getCoupon_master_id();

							$this->_savecustomerassigncoupons($data);



							$gcm_regids = $this->find_gcm_regid($final_user_array);

							if(!empty($gcm_regids) && $visible_all!='no')

							{



								if (count($gcm_regids[0])>0)

								{

									//var_dump($gcm_regids);exit;

									$this->send_notification($gcm_regids,"New Coupon",$message,2,$app_id,$this->get('session')->get('domain_id'),"coupon_master",$coupon_master->getCoupon_master_id());

								}

							}



							$apns_regids = $this->find_apns_regid($final_user_array);

							if(!empty($apns_regids) && $visible_all!='no')

							{

								if (count($apns_regids[0])>0)

								{

									//var_dump($gcm_regids);exit;

									$this->send_notification($apns_regids,"New Coupon",$message,1,$app_id,$this->get('session')->get('domain_id'),"coupon_master",$coupon_master->getCoupon_master_id());

								}

							}

						}



						//----- Push Notificaiton ---





					//end - by naman on 2016-07-25 for coupon notification issue resolution

				}

				$this->get('session')->getFlashBag()->set('success_msg','Product cities saved successfully.');

			}

			else

			{

				$this->get('session')->getFlashBag()->set('error_msg','All fields are required, please select all fields.');

			}

		}

		else

		{

			$this->get('session')->getFlashBag()->set('error_msg','Oops! Something goes wrong! Try again!');

		}

		return $this->redirect($this->generateUrl('admin_servicearea_couponcity',array("domain"=>$this->get('session')->get('domain'),"coupon_master_id"=>$coupon_master_id)));

	}



	/**

     * @Route("/removeproductcity/{product_city_relation_id}/{main_product_id}")

     */

    public function removeproductcityAction($product_city_relation_id,$main_product_id)

    {

    	$em = $this->getDoctrine()->getManager();

		$connection = $em->getConnection();

		$statement = $connection->prepare("UPDATE product_city_relation SET is_deleted = 1 WHERE product_city_relation_id = '".$product_city_relation_id."'");

		$statement->execute();

		$this->get('session')->getFlashBag()->set('success_msg','Product City deleted successfully');

		return $this->redirect($this->generateUrl('admin_servicearea_productcity',array("domain"=>$this->get('session')->get('domain'),"main_product_id"=>$main_product_id)));

    }



    /**

     * @Route("/removecouponcity/{coupon_city_relation_id}/{coupon_master_id}")

     */

    public function removecouponcityAction($coupon_city_relation_id,$coupon_master_id)

    {

    	$em = $this->getDoctrine()->getManager();

		$connection = $em->getConnection();

		$statement = $connection->prepare("UPDATE coupon_city_relation SET is_deleted = 1 WHERE coupon_city_relation_id = '".$coupon_city_relation_id."'");

		$statement->execute();

		$this->get('session')->getFlashBag()->set('success_msg','Coupon City deleted successfully');

		return $this->redirect($this->generateUrl('admin_servicearea_couponcity',array("domain"=>$this->get('session')->get('domain'),"coupon_master_id"=>$coupon_master_id)));

    }

	private function _savecustomerassigncoupons($assing_copon_arr)

		{

		$em = $this->getDoctrine()->getManager();

		//prepare for database

		$connection = $em->getConnection();

		//prepare query

		$customer_user = $connection->prepare("SELECT `user_master_id` as user_master_id FROM user_master WHERE  user_role_id=7 And user_type='user' And user_status='active' AND is_deleted=0 AND domain_id Like '".$assing_copon_arr['custcounpon']['domain_id']."'");

		//query exexute

		$customer_user->execute();
		//fetch record into table

		$customer_user_id_list = $customer_user->fetchAll();

		if(isset($customer_user_id_list) && !empty($customer_user_id_list))
		{
			foreach($customer_user_id_list as $customer_user_id_list_key => $customer_user_id_list_value)
			{
				$customer_list[] = $customer_user_id_list_value['user_master_id'];
			}
			$customer_list_slipt = implode(",",$customer_list);

		}
		else
		{
			$customer_list_slipt = "";
		}

		if(isset($customer_list_slipt) && !empty($customer_list_slipt) && !empty($assing_copon_arr))
		{
			$customer_user_ids = $customer_list_slipt;
			$custassigncopon = new Custassigncopon();
			$custassigncopon->setCustomer_user_id($customer_user_ids);
			$custassigncopon->setDomain_id($assing_copon_arr['custcounpon']['domain_id']);
			$custassigncopon->setApp_id($assing_copon_arr['custcounpon']['app_id']);
			$custassigncopon->setTable_name($assing_copon_arr['custcounpon']['table_name']);
			$custassigncopon->setTable_id($assing_copon_arr['custcounpon']['table_id']);
			$custassigncopon->setIs_deleted(0);
			$em->persist($custassigncopon);

			$em->flush();

			return true;

		}

	}

}

