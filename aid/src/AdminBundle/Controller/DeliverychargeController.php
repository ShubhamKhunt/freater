<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;

use AdminBundle\Entity\Domainmaster;
use AdminBundle\Entity\Deliverycharge;

/**
* @Route("/{domain}")
*/

class DeliverychargeController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
	
    /**
     * @Route("/deliverycharge")
     * @Template()
     */
    public function deliverychargeAction()
    {
    	$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare("
		SELECT delivery_charge.*,country_master.country_title,state_master.state_name,city_master.city_name
		FROM 
		delivery_charge 
		JOIN country_master ON delivery_charge.main_country_id = country_master.main_country_id
		JOIN state_master ON delivery_charge.main_state_id = state_master.main_state_id
		JOIN city_master ON delivery_charge.main_city_id = city_master.main_city_id
		WHERE
		delivery_charge.domain_id = '".$this->get('session')->get('domain_id')."' AND delivery_charge.is_deleted = 0 AND
		country_master.language_id = 1 AND country_master.status = 'active' AND country_master.is_deleted = 0 AND
		state_master.language_id = 1 AND state_master.status = 'active' AND state_master.is_deleted = 0 AND
		city_master.language_id = 1 AND city_master.status = 'active' AND city_master.is_deleted = 0
		");
		$statement->execute();
		$delivery_city_list = $statement->fetchAll();
		//var_dump($delivery_city_list);exit;
    	return array("delivery_city_list"=>$delivery_city_list);
    }
    /**
     * @Route("/adddeliverycharge")
     * @Template()
     */
    public function adddeliverychargeAction()
    {
    	$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare("SELECT service_country_relation.*,country_master.country_title FROM service_country_relation JOIN country_master ON service_country_relation.main_country_id = country_master.main_country_id WHERE country_master.language_id = 1 AND country_master.status = 'active' AND country_master.is_deleted = 0 AND service_country_relation.status = 'active' AND service_country_relation.is_deleted = 0 AND service_country_relation.domain_id = '".$this->get('session')->get('domain_id')."'");
		$statement->execute();
		$service_country_list = $statement->fetchAll();
		
    	return array("service_country_list"=>$service_country_list);
    }
    /**
     * @Route("/getdeliverymaincitylist")
     */
    public function getdeliverymaincitylistAction()
    {
    	if(isset($_POST['flag']) && $_POST['flag'] = 'getcity_options' && $_POST['main_state_id'] != "" && $_POST['lang_id'] != "")
    	{
			$city_master = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Citymaster')
						   ->findBy(array('main_state_id'=>$_POST['main_state_id'],'language_id'=>$_POST['lang_id'],'status'=>'active','is_deleted'=>0));
			
			if(!empty($city_master))
			{
				
					?>
					<div class="box box-primary box-solid">
		                <div class="box-header with-border">
		                    <h3 class="box-title">City with delivery charges</h3>
		                </div>
		                <div class="box-body">
		                    <?php
		                   foreach($city_master as $key=>$val)
		                   {
		                   		$delivery_charge = $this->getDoctrine()
									   ->getManager()
									   ->getRepository('AdminBundle:Deliverycharge')
									   ->findOneBy(array('main_city_id'=>$val->getMain_city_id(),'main_state_id'=>$_POST['main_state_id'],'domain_id'=>$this->get('session')->get('domain_id'),'is_deleted'=>0));
		                    ?>
		                    <div class="col-md-4">
				              <div class="box box-default box-solid">
				                <div class="box-header with-border">
				                  <h3 class="box-title"><?php echo $val->getCity_name(); ?></h3>
				                </div>
				                <div class="box-body">
				                	<input type="hidden" name="delivery_city_id[]" value="<?php echo $val->getMain_city_id(); ?>"/>
				                  	<input type="text" class="form-control input-sm" placeholder="charges" name="delivery_charges[]" value="<?php if(!empty($delivery_charge)){echo $delivery_charge->getDelivery_charge();}else{echo 0;} ?>"/>
				                </div>
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
     * @Route("/savedeliverycity")
     */
    public function savedeliverycityAction()
    {
    	if(isset($_POST['save_delivery_city']) && $_POST['save_delivery_city'] == "save_delivery_city")
    	{
			if(!empty($_POST['delivery_city_id']))
			{
				foreach($_POST['delivery_city_id'] as $key=>$val)
				{
					$delivery_charge_info = $this->getDoctrine()
									   ->getManager()
									   ->getRepository('AdminBundle:Deliverycharge')
									   ->findOneBy(array('main_city_id'=>$val,'main_state_id'=>$_POST['main_state_id'],'domain_id'=>$this->get('session')->get('domain_id'),'is_deleted'=>0));
					
					if(empty($delivery_charge_info))
					{
						//insert
						$delivery_charge = new Deliverycharge();
						$delivery_charge->setMain_city_id($val);
						$delivery_charge->setDelivery_charge($_POST['delivery_charges'][$key]);
						$delivery_charge->setMain_country_id($_POST['main_country_id']);
						$delivery_charge->setMain_state_id($_POST['main_state_id']);
						$delivery_charge->setDomain_id($this->get('session')->get('domain_id'));
						$delivery_charge->setCreated_by($this->get('session')->get('user_id'));
						$delivery_charge->setCreate_date(date("Y-m-d H:i:s"));
						$delivery_charge->setIs_deleted(0);
						$em = $this->getDoctrine()->getManager();
						$em->persist($delivery_charge);
						$em->flush();
						
						$this->get('session')->getFlashBag()->set('success_msg','Delivery charges saved successfully.');
					}
					else
					{
						//update
						$delivery_charge_info->setDelivery_charge($_POST['delivery_charges'][$key]);
						$em = $this->getDoctrine()->getManager();
						$em->persist($delivery_charge_info);
						$em->flush();
						
						$this->get('session')->getFlashBag()->set('success_msg','Delivery charges updated successfully.');
					}
				}
				
				
				return $this->redirect($this->generateUrl('admin_deliverycharge_deliverycharge',array("domain"=>$this->get('session')->get('domain'))));
			}
			else
			{
				$this->get('session')->getFlashBag()->set('error_msg','Delivery charges can not save.');
			}
		}
		else
		{
			$this->get('session')->getFlashBag()->set('error_msg','Oops! Something goes wrong! Try again later');
		}
		return $this->redirect($this->generateUrl('admin_deliverycharge_adddeliverycharge',array("domain"=>$this->get('session')->get('domain'))));
    }
    /**
     * @Route("/removedeliverycharge/{delivery_charge_id}")
     */
    public function removedeliverychargeAction($delivery_charge_id)
    {
    	$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare("UPDATE delivery_charge SET is_deleted = 1 WHERE delivery_charge_id = '".$delivery_charge_id."'");
		$statement->execute();
		$this->get('session')->getFlashBag()->set('success_msg','City removed with delivery charge');
		return $this->redirect($this->generateUrl('admin_deliverycharge_deliverycharge',array("domain"=>$this->get('session')->get('domain'))));
    }
}