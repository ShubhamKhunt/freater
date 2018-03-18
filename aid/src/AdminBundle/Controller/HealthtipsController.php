<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Usermaster;
use AdminBundle\Entity\Productcategoryrelation;
use AdminBundle\Entity\Productmaster;
use AdminBundle\Entity\Combination;
use AdminBundle\Entity\Combinationrelation;
use AdminBundle\Entity\Gallerymaster;
use AdminBundle\Entity\Medialibrarymaster;
use AdminBundle\Entity\Productsupplierrelation;
use AdminBundle\Entity\Supplierattributerelation;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
* @Route("/{domain}")
*/

class HealthtipsController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
    
    /**
     * @Route("/healthtips")
     * @Template()
     */
    public function indexAction()
    {
		$language = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Languagemaster')
				   ->findBy(array('is_deleted'=>0));
					   
		$product_array='';
		$domain_id = $this->get('session')->get('domain_id');
		return array("language"=>$language);
    }
	/**
     * @Route("/addhealthtip")
     * @Template()
     */
    public function addhealthtipAction()
    {
		$language = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Languagemaster')
				   ->findBy(array('is_deleted'=>0));
					   
		$product_array='';
		$domain_id = $this->get('session')->get('domain_id');
		return array("language"=>$language);
    }
  }