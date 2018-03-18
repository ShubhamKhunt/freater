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
class SurveyController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
    /**
     * @Route("/surveyquestions")
     * @Template()
     */
    public function surveyquestionsAction()
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
		foreach($generalsetting as $key=>$val)
		{			//amount
			$amount = 0;
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
			$survey_flag='yes';
			
		}
		$survey_form_question_list = '';
		if($survey_flag == 'yes'){
			//fetch survey questions
			$survey_form_question_list = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Surveyformquestion')->findBy(array("domain_id"=>$this->get('session')->get('domain_id'),"is_deleted"=>0));
			
			
		}
		return array("survey_flag"=>$survey_flag,"survey_form_question_list"=>$survey_form_question_list,"amount"=>$amount,"domain_text"=>$domain_master->getDomain_name(),"language_master"=>$language_master);
     }
	  /**
     * @Route("/addquestion/{question_id}",defaults={"question_id":""})
     * @Template()
     */
    public function addquestionAction($question_id)
    {
		if(isset($question_id) && $question_id != "" && !empty($question_id) && $question_id != NULL && $question_id != 0 ){
			$survey_form_question = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Surveyformquestion')->findOneBy(array("survey_form_question_id"=>$question_id,"domain_id"=>$this->get('session')->get('domain_id'),"is_deleted"=>0));
			return array("question"=>$survey_form_question);
		}
		return array();
	}
	 /**
     * @Route("/savequestion")
     * @Template()
     */
    public function savequestionAction()
    {
		//get count of total survey questions 
		$survey_form_question_cnt = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Surveyformquestion')->findBy(array("domain_id"=>$this->get('session')->get('domain_id')));
		$survey_form_question_cnt = count($survey_form_question_cnt) + (int) 1 ;
		$em = $this->getDoctrine()->getManager();
		$survey_form_question = new Surveyformquestion();
		$survey_form_question->setQuestion_name($_REQUEST['question']) ;
		$survey_form_question->setDomain_id($this->get('session')->get('domain_id')) ;
		$survey_form_question->setSort_order($survey_form_question_cnt) ;
		$em->persist($survey_form_question);
		$em->flush();
		return $this->redirect($this->generateUrl('admin_survey_surveyquestions',array("domain"=>$this->get('session')->get('domain'))));
	}
	 /**
	  *@Route("/updatequestion")
	  */
	 public function updatequestionAction(){
		
		
		
		$em = $this->getDoctrine()->getManager();
		
		$survey_form_question = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Surveyformquestion')->findOneBy(array("survey_form_question_id"=>$_REQUEST['question_id'],"domain_id"=>$this->get('session')->get('domain_id')));
		if(!empty($survey_form_question)){
			$survey_form_question->setQuestion_name($_REQUEST['question']) ;
			$em->flush();
		}
			
		return $this->redirect($this->generateUrl('admin_survey_surveyquestions',array("domain"=>$this->get('session')->get('domain'))));
	 }
	 
	  /**
     * @Route("/deletequestion/{question_id}",defaults={"question_id":""})
     * @Template()
     */
    public function deletequestionAction($question_id)
    {
		$em = $this->getDoctrine()->getManager();
		
		$survey_form_question = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Surveyformquestion')->findOneBy(array("survey_form_question_id"=>$question_id,"domain_id"=>$this->get('session')->get('domain_id')));
		if(!empty($survey_form_question)){
			$survey_form_question->setIs_deleted(1) ;
			$em->flush();
		}
			
		return $this->redirect($this->generateUrl('admin_survey_surveyquestions',array("domain"=>$this->get('session')->get('domain'))));
	}
	 
	
}

?>