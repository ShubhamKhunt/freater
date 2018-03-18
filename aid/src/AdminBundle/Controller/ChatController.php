<?php
namespace AdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Chatmaster;
use AdminBundle\Entity\Blockusermaster;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;
/**

* @Route("/{domain}")

*/
class ChatController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $obj = new BaseController();
        $obj->checkSessionAction();
        $this->session = new Session();
    }

    /**
    * @Route("/Manlist")
    * @Template()
    */
    public function manindexAction()
    {

      $professional_id = $this->session->get('user_id');
      return array("professional_id"=>$professional_id);
    }
    /**
    * @Route("/chatlist/{professional_id}")
    * @Template()
    */
    public function indexAction($professional_id)
    {

      $role_id = $this->session->get('role_id');

      if($role_id == 6)
      {
        $user_id = $this->session->get('user_id');
        
        if($user_id != $professional_id)
        {
            $professional_id = 0;
        }
      }

      return array("professional_id"=>$professional_id,'live_path'=>$this->container->getParameter('live_path'));
    }

    /**
    * @Route("/managechat/{user_id}/{professional_id}",defaults={"user_id"="","professional_id"=""})
    * @Template()
    */
    public function managechatAction($user_id,$professional_id)
    {
      return array();
    }

}
