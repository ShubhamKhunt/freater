<?php
namespace AdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;
use AdminBundle\Entity\Usermaster;
use AdminBundle\Entity\Addressmaster;
use AdminBundle\Entity\Countrymaster;
use AdminBundle\Entity\Statemaster;
use AdminBundle\Entity\Citymaster;
use AdminBundle\Entity\Areamaster;
use AdminBundle\Entity\Usersetting;class CustomerController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
    /**
     * @Route("/admin/customerlist")
     * @Template()
     */
    public function indexAction()
    {		$deliverymanager_list = $this->getDoctrine()						   ->getManager()						   ->getRepository('AdminBundle:Usermaster')						   ->findBy(array('user_role_id'=>6,'is_deleted'=>0,"domain_id"=>$this->get('session')->get('domain_id')));
		return array("deliverymanager_list" => $deliverymanager_list);
	}
}