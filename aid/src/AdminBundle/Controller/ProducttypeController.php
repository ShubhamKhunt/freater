<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;

use AdminBundle\Entity\Rightmaster;
use AdminBundle\Entity\Rolerightmaster;
use AdminBundle\Entity\Userrolemaster;
use AdminBundle\Entity\Usermaster;
use AdminBundle\Entity\Producttypemaster;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
* @Route("/admin")
*/
class ProducttypeController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }
	
	/**
	 * @Route("/product-types/")
     * @Template()
     */
    public function producttypelistAction()
    {
		ini_set('xdebug.var_display_max_depth', 200);
		ini_set('xdebug.var_display_max_children', 256);
		ini_set('xdebug.var_display_max_data', 1024);
    	$em = $this->getDoctrine()->getManager();
		$con = $em->getConnection();
			$st = $con->prepare("SELECT main_product_type_master_id FROM product_type_master WHERE is_deleted = 0 GROUP BY main_product_type_master_id");
			$st->execute();
			$product_typelist = $st->fetchAll();
			$data = array();
			if(!empty($product_typelist)){
				foreach($product_typelist as $k=>$v){
					$em = $this->getDoctrine()->getManager();
					$connection = $em->getConnection();
					$statement = $connection->prepare("SELECT * FROM product_type_master WHERE is_deleted = 0 AND main_product_type_master_id = ".$v['main_product_type_master_id']);
					$statement->execute();
					$comp = $statement->fetchAll();
					
					for($i=0;$i<count($comp);$i++)
					{
						$get_parent_name = $this->getDoctrine()
							->getManager()
							->getRepository('AdminBundle:Producttypemaster')
							->findOneBy(array(
									'product_type_master_id'=>$comp[$i]['product_type_master_id'],
									'language_id'=>$comp[$i]['language_id'],
									'is_deleted'=>0)
								);
							
					}
					$data[] = array("product_type_master_id"=>$v['main_product_type_master_id'],"data"=>$comp);
				}
			}
			
			$live_path=$this->getparams()->live;
			$langs = $this->getDoctrine()
					->getManager()
					->getRepository("AdminBundle:Languagemaster")
					->findBy(array("is_deleted"=>0));
					
		return array("product_typelist"=>$data,"languages"=>$langs,"live_path"=>$live_path);
    }

    /**
     * @Route("/product-type/add-update/{product_type_master_id}", defaults={"product_type_master_id"=""})
     * @Template()
     */
	 public function addproducttypeAction($product_type_master_id)
	 {	
		$langs = $this->getDoctrine()
					->getManager()
					->getRepository("AdminBundle:Languagemaster")
					->findBy(array("is_deleted"=>0));
		
		$get_product_type_list = $this->getDoctrine()
								->getManager()
								->getRepository('AdminBundle:Producttypemaster')
								->findBy(array(
										'is_deleted'=>0)
									);
		
		if(!empty($product_type_master_id)){
			$em = $this->getDoctrine()->getManager();
    		$conn = $em->getConnection();
    		$st = $conn->prepare("SELECT * from product_type_master where product_type_master.main_product_type_master_id = ".$product_type_master_id." AND product_type_master.is_deleted = 0");
    		$st->execute();
    		$load_product_type = $st->fetchAll();
			return array('product_type'=>$load_product_type,'product_type_master_id'=>$product_type_master_id,"languages"=>$langs,"product_type_list"=>$get_product_type_list);
		} else {
			return array("languages"=>$langs,"product_type_list"=>$get_product_type_list);
		}
	}
	
	/**
     * @Route("/addproducttypedb")
     * @Template()
     */
	 public function addproducttypedbAction(){
		$session = new Session();
		$user_id = $session->get("user_id");
		$language_id = $_REQUEST['language_master_id'];
		if(isset($_REQUEST['add'])){
			$product_type_title = $_REQUEST['product_type_title'];
			$product_type_status = $_REQUEST['product_type_status'];
			$product_type_master_id = '';
			
			if(isset($_REQUEST['product_type_master_id']) && !empty($_REQUEST['product_type_master_id']))
			{
				$product_type_master_id = $_REQUEST['product_type_master_id'];
				$get_plan = $this->getDoctrine()
								->getManager()
								->getRepository('AdminBundle:Producttypemaster')
								->findOneBy(array(
										'main_product_type_master_id'=>$product_type_master_id,
										'language_id'=>$language_id,
										'is_deleted'=>0)
									);
				if(count($get_plan) > 0){
					$em = $this->getDoctrine()->getManager();
					$update = $em->getRepository('AdminBundle:Producttypemaster')
								->find($get_plan->getProduct_type_master_id());					
					$update->setProduct_type_name($product_type_title);
					$update->setStatus($product_type_status);
					$em->flush();
					
					$this->get('session')->getFlashBag()->set('success_msg','Product_type Updated Successfuly!');
					return $this->redirect($this->generateUrl('admin_producttype_addproducttype',array("domain"=>$this->get('session')->get('domain'))).'/'.$product_type_master_id);
				} else {
					$Product_typemaster = new Producttypemaster();
					$Product_typemaster->setProduct_type_name($product_type_title);
					$Product_typemaster->setStatus($product_type_status);
					
					$Product_typemaster->setLanguage_id($language_id);
					$Product_typemaster->setMain_product_type_master_id($product_type_master_id);
					$Product_typemaster->setIs_deleted(0);
					$Product_typemaster->setCreated_by($user_id);
				
					$em = $this->getDoctrine()->getManager();
					$em->persist($Product_typemaster);
					$em->flush();
					
					if(isset($product_type_master_id) && !empty($product_type_master_id))
					{
						$this->get('session')->getFlashBag()
							->set('success_msg','Product_type Inserted Successfuly!');
						return $this->redirect($this
							->generateUrl('admin_producttype_addproducttype',array("domain"=>$this->get('session')->get('domain'))).'/'.$product_type_master_id);
					}
					else {
						$this->get('session')->getFlashBag()->set('error_msg','Product_type Insertion Failed!');
						return $this->redirect($this->generateUrl('admin_producttype_addproducttype',array("domain"=>$this->get('session')->get('domain'))));
					}	
				}
			} else {
				$Product_typemaster = new Producttypemaster();
				$Product_typemaster->setProduct_type_name($product_type_title);
				$Product_typemaster->setStatus($product_type_status);
				$Product_typemaster->setLanguage_id($language_id);
				$Product_typemaster->setMain_product_type_master_id(0);
				$Product_typemaster->setIs_deleted(0);
				$Product_typemaster->setCreated_by($user_id);
			
				$em = $this->getDoctrine()->getManager();
				$em->persist($Product_typemaster);
				$em->flush();
					
				$product_type_master_id = $Product_typemaster->getProduct_type_master_id();
				
				if(isset($product_type_master_id) && !empty($product_type_master_id))
				{
					$em = $this->getDoctrine()->getManager();
					$update = $em->getRepository('AdminBundle:Producttypemaster')->find($product_type_master_id);
					$update->setMain_Product_type_master_id($product_type_master_id);
					$em->flush();
					$this->get('session')->getFlashBag()->set('success_msg','Product_type Inserted Successfuly!');
					return $this->redirect($this->generateUrl('admin_producttype_addproducttype',array("domain"=>$this->get('session')->get('domain'))).'/'.$product_type_master_id);
				} else {
					$this->get('session')->getFlashBag()->set('error_msg','Product_type Insertion Failed!');
					return $this->redirect($this->generateUrl('admin_producttype_addproducttype'));
				}
			}
		}
	}
	
    /**
     * @Route("/deleteProducttype/{product_type_master_id}", defaults={"product_type_master_id"=""})
     * @Template()
     */
	 public function deleteproducttypeAction($product_type_master_id){	 	 	
		if(!empty($product_type_master_id)){
			$id = $product_type_master_id;
			$list = $this->getDoctrine()
					->getManager()
					->getRepository("AdminBundle:Producttypemaster")
					->findBy(array("is_deleted"=>0,"main_product_type_master_id"=>$id));
			if(!empty($list)){
				foreach($list as $k=>$v){
					$em = $this->getDoctrine()->getManager();
					$update = $em->getRepository("AdminBundle:Producttypemaster")->find($v->getProduct_type_master_id());
					$update->setIs_deleted(1);
					$em->flush();
				}
			}
			$this->get('session')->getFlashBag()->set('success_msg','Product_type Deleted Successfuly!');
			return $this->redirect($this->generateUrl('admin_producttype_producttypelist'));
		}
	}
}
