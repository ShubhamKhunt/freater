<?php



namespace AdminBundle\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Session\Session;



use AdminBundle\Entity\Languagemaster;

use AdminBundle\Entity\Rightmaster;

/**

* @Route("/{domain}")

*/

class RightController extends BaseController

{

	public function __construct()

    {

		parent::__construct();

        //$obj = new BaseController();

		//$obj->checkSessionAction();

    }

    

    /**

     * @Route("/right")

	 * @Template

     */

    public function indexAction()

    {

		$right_master = $this->getDoctrine()

				   ->getManager()

				   ->getRepository('AdminBundle:Rightmaster')

				   ->findBy(array('is_deleted'=>0));

                   

		return array("right_master"=>$right_master);

    }

	

	/**

     * @Route("/right/addright/{right_master_id}",defaults={"right_master_id":""})

	 * @Template

     */

    public function addrightAction($right_master_id)

    {

		if(!empty($right_master_id))

		{

			$right_master = $this->getDoctrine()

				   ->getManager()

				   ->getRepository('AdminBundle:Rightmaster')

				   ->findOneBy(array('is_deleted'=>0,"right_master_id"=>$right_master_id));

                   

			return array("right_master"=>$right_master);

		}

		return array("right_master"=>'');

	}

	

	/**

     * @Route("/right/saveright")

	 * @Template

     */

    public function saverightAction()

    {

		$display_name = $_REQUEST['display_name'];

		

		$desc = "";

		if(!empty($_REQUEST['description']))

		{

			$desc = $_REQUEST['description'];

		}

		

		$code = $_REQUEST['code'];

		

		if($display_name !="" && $code !="" && !empty($display_name) && !empty($display_name))

		{

			$right_master = new Rightmaster();

			

			$right_master->setCode($code);

			$right_master->setDescription($desc);

			$right_master->setDisplay_name($display_name);

			$right_master->setIs_deleted(0);

			

			$em = $this->getDoctrine()->getManager();

			$em->persist($right_master);

			$em->flush();

			

			$this->get("session")->getFlashBag()->set("success_msg","Right Successfully Add.");

			return $this->redirect($this->generateUrl("admin_right_index" ,array("domain"=>$this->get('session')->get('domain'))));

		}

		else

		{

			 $this->get("session")->getFlashBag()->set("erroe_msg","Right field is required .");

			 return $this->redirect($this->generateUrl("admin_right_addright",array("domain"=>$this->get('session')->get('domain'))));

		}

	}

	

	/**

     * @Route("/right/updateright/{right_master_id}",defaults={"right_master_id":""})

	 * @Template

     */

    public function updaterightAction($right_master_id)

    {

		$display_name = $_REQUEST['display_name'];

		

		$desc = "";

		if(!empty($_REQUEST['description']))

		{

			$desc = $_REQUEST['description'];

		}

		

		$code = $_REQUEST['code'];

		

		if($display_name !="" && $code !="" && !empty($display_name) && !empty($display_name))

		{

			$right_master = $this->getDoctrine()

				   ->getManager()

				   ->getRepository('AdminBundle:Rightmaster')

				   ->findOneBy(array('is_deleted'=>0,"right_master_id"=>$right_master_id));

			

			$right_master->setCode($code);

			$right_master->setDescription($desc);

			$right_master->setDisplay_name($display_name);

			

			$em = $this->getDoctrine()->getManager();

			$em->flush();

			

			$this->get("session")->getFlashBag()->set("success_msg","Right Successfully Update.");

			return $this->redirect($this->generateUrl("admin_right_index",array("domain"=>$this->get('session')->get('domain'))));

		}

		else

		{

			 $this->get("session")->getFlashBag()->set("erroe_msg","Right field is required .");

			 return $this->redirect($this->generateUrl("admin_right_addright",array("domain"=>$this->get('session')->get('domain'))));

		}

	}

	

	/**

     * @Route("/right/deleteright/{right_master_id}",defaults={"right_master_id":""})

	 * @Template

     */

    public function deleterightAction($right_master_id)

    {

		$right_master = $this->getDoctrine()

				   ->getManager()

				   ->getRepository('AdminBundle:Rightmaster')

				   ->findOneBy(array('is_deleted'=>0,"right_master_id"=>$right_master_id));

				   

		$right_master->setIs_deleted(1);

			

		$em = $this->getDoctrine()->getManager();

		$em->flush();

		

		$this->get("session")->getFlashBag()->set("success_msg","Right Successfully Delete.");

		return $this->redirect($this->generateUrl("admin_right_index",array("domain"=>$this->get('session')->get('domain'))));

	}

	/**

     * @Route("/Right/Codecheck")

     * @Template()

     */

    public function codecheckAction()

    {

        $str = "";

            if ($_POST['flag']=="right_master")

			{

				$data = $_POST['data_name'];

				

				$array = explode(" ",$data);

				

				foreach($array as $val)

				{

					$str .= $val[0];

				}

    

            while(1){

                $original = $str;

                

                $str = substr($str,0,2);

                $remaining_len = 4-strlen($str);

                $rem_str =  $this->random_string_only($remaining_len,$str);

                $str .= $rem_str;

                

                $list  = $this->getDoctrine()

				   ->getManager()

				   ->getRepository('AdminBundle:Rightmaster')

				   ->findBy(array('code'=>$str));

                   

                if (count($list)==0){

                    break;

                }else{

                    $str = $original;							

                }

            }

            

            $code = strtoupper($str);

			$content = array('code'=>$code);

			

			return new Response(json_encode($content));

        

        }

    }

    

    public function random_string_only($length = 8,$str)

    {

        mt_srand((double) microtime() * 10000);

    

        for ($i = 0; $i < $length; $i++) {

    

            $x = mt_rand(65, 90);

    

            $str .= chr($x);

        }

        return $str;

    }

}



?>