<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Bundle\DoctrineBundle\ConnectionFactory;

use AdminBundle\Entity\Termsconditionsmaster;

/**
* @Route("/{domain}")
*/

class TermsController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
    /**
     * @Route("/termsandcondition")
     * @Template()
     */
    public function termsandconditionAction()
    {
    	$language = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Languagemaster')
				   ->findBy(array('is_deleted'=>0));
		
		$terms_conditions_info = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Termsconditionsmaster')
					   ->findBy(array("domain_code"=>$this->get('session')->get('domain_id'),"is_deleted"=>0));
					   
    	return array("language"=>$language,"terms_conditions_info"=>$terms_conditions_info);
    }
    /**
     * @Route("/saveterms/{lang_id}/{domain_id}")
     * @Template()
     */
    public function savetermsAction($lang_id,$domain_id)
    {
    	if(isset($_POST['save_terms']) && $_POST['save_terms'] == 'save_terms')
    	{
			if($_POST['description'] != "")
			{
				$terms_conditions_info = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Termsconditionsmaster')
					   ->findOneBy(array("language_id"=>$lang_id,"domain_code"=>$domain_id,"is_deleted"=>0));
				
				if(!empty($terms_conditions_info))
				{
					//update terms and conditions
					$terms_conditions_info->setDescription($_POST['description']);
					$terms_conditions_info->setLast_updated(date("Y-m-d H:i:s"));
					$em = $this->getDoctrine()->getManager();
					$em->persist($terms_conditions_info);
					$em->flush();
					$this->get('session')->getFlashBag()->set('success_msg','Terms & Conditions updated successfully.');
				}
				else
				{
					//insert new terms and conditions
					$terms_conditions_master = new Termsconditionsmaster();
					$terms_conditions_master->setDescription($_POST['description']);
					$terms_conditions_master->setLanguage_id($lang_id);
					$terms_conditions_master->setDomain_code($domain_id);
					$terms_conditions_master->setCreate_date(date("Y-m-d H:i:s"));
					$terms_conditions_master->setLast_updated(date("Y-m-d H:i:s"));
					$terms_conditions_master->setIs_deleted(0);
					$em = $this->getDoctrine()->getManager();
					$em->persist($terms_conditions_master);
					$em->flush();
					$this->get('session')->getFlashBag()->set('success_msg','Terms & Conditions inserted successfully.');
				}
			}
			else
			{
				$this->get('session')->getFlashBag()->set('error_msg','Terms & condition is required, please write some text');
			}
		}
		else
		{
			$this->get('session')->getFlashBag()->set('error_msg','Oops! Something goes wrong! Try again later!');
		}
		return $this->redirect($this->generateUrl('admin_terms_termsandcondition',array("domain"=>$this->get('session')->get('domain'))));
    }
}