<?php
namespace AdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Generalsettings;
use AdminBundle\Entity\Surveyformquestion;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
* @Route("/{domain}")
*/
class SettingController extends BaseController
{	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
    /**
     * @Route("/setting")
     * @Template()
     */
    public function indexAction()
    {
		$domain_master = $this->getDoctrine()->getManager()
				   ->getRepository('AdminBundle:Domainmaster')
				   ->findOneBy(array('domain_code'=>$this->get('session')->get('domain_id'),'is_deleted'=>0));
		
		$language_master = $this->getDoctrine()
		   ->getManager()
		   ->getRepository('AdminBundle:Languagemaster')
		   ->findBy(array('is_deleted'=>0));		
		$generalsetting = $this->getDoctrine()
		   ->getManager()
		   ->getRepository('AdminBundle:Generalsetting')
		   ->findBy(array('is_deleted'=>0));
		$survey_flag = 'no';
		$amount = 0;
		foreach($generalsetting as $key=>$val)
		{			//amount
			
			
			if($val->getGeneral_setting_key() == 'minimum_order_amount' && $val->getGeneral_setting_id() == 1)
			{
				foreach(json_decode($val->getGeneral_setting_value()) as $gkey=>$gval)
				{
					if($gval->domain_id == $this->get('session')->get('domain_id'))
					{
						$amount = $gval->amount;
					}
				}
			}
			
			if($val->getGeneral_setting_key() == 'delivery_charge' && $val->getGeneral_setting_id() == 3)
			{
				foreach(json_decode($val->getGeneral_setting_value()) as $gkey=>$gval)
				{
					if($gval->domain_id == $this->get('session')->get('domain_id'))
					{
						$delivery_charge = $gval->amount;
					}
				}
			}
			
			if($val->getGeneral_setting_key() == 'phone_no' && $val->getGeneral_setting_id() == 4)
			{
				foreach(json_decode($val->getGeneral_setting_value()) as $gkey=>$gval)
				{
					if($gval->domain_id == $this->get('session')->get('domain_id'))
					{
						$phone_no = $gval->amount;
					}
				}
			}
			
			
			if($val->getGeneral_setting_key() == 'survey' && $val->getGeneral_setting_id() == 2){
				foreach(json_decode($val->getGeneral_setting_value()) as $gkey=>$gval)
				{
					if($gval->domain_id == $this->get('session')->get('domain_id'))
					{
						$survey_flag = $gval->flag;
					}
				}
			}
		}
		$survey_form_question_list = '';
		if($survey_flag == 'yes'){
			//fetch survey questions
			$survey_form_question_list = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Surveyformquestion')->findBy(array("domain_id"=>$this->get('session')->get('domain_id')));
			
			
		}
		return array("survey_flag"=>$survey_flag,"survey_form_question_list"=>$survey_form_question_list,"amount"=>$amount,"domain_text"=>$domain_master->getDomain_name(),"language_master"=>$language_master,"delivery_charge"=>$delivery_charge,"phone_no"=>$phone_no);
     }
	 
	 /**
	  *@Route("/updatesurveyques")
	  */
	 public function updatesurveyquesAction(){
		
		
		$question_cnt = count($_REQUEST['question_id']);
		$em = $this->getDoctrine()->getManager();
		if($question_cnt > 0  ){
			for($i = 0 ; $i < $question_cnt ; $i++){
				$survey_form_question = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Surveyformquestion')->findOneBy(array("survey_form_question_id"=>$_REQUEST['question_id'][$i],"domain_id"=>$this->get('session')->get('domain_id')));
				if(!empty($survey_form_question)){
					$survey_form_question->setQuestion_name($_REQUEST['question'][$i]) ;
					$em->flush();
				}
			}
		}
		return $this->redirect($this->generateUrl('admin_setting_index',array("domain"=>$this->get('session')->get('domain'))));
	 }
	 
	/**
     * @Route("/updatesetting")
     */
    public function updatesettingAction()
    {
    	$content = array("msg"=>"Operation failed!","type"=>"error");
    	if(isset($_REQUEST['flag']) && $_REQUEST['flag'] == "update_setting")
    	{
			
			$generalsetting = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Generalsetting')
				   ->findOneBy(array("general_setting_key"=>$_REQUEST['key'],"general_setting_id"=>$_REQUEST['setting_id'],"is_deleted"=>0));
			
			$json_value = json_decode($generalsetting->getGeneral_setting_value());
			
			$order_amount = array();
			foreach($json_value as $key=>$val)
			{
				if($val->domain_id == $this->get('session')->get('domain_id'))
				{
					$order_amount[] = array("domain_id"=>$val->domain_id,"amount"=>$_REQUEST['setting_value']);
				}
				else
				{
					$order_amount[] = array("domain_id"=>$val->domain_id,"amount"=>$val->amount);
				}
			}
			
		
			$generalsetting->setGeneral_setting_value(json_encode($order_amount));
			$em = $this->getDoctrine()->getManager();
			$em->persist($generalsetting);
			$em->flush();
			
			$content = array("msg"=>"Setting updated","type"=>"success");
		}
    	return new Response(json_encode($content));
    }}