<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;

use AdminBundle\Entity\Attributemaster;
use AdminBundle\Entity\Attributevalue;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
* @Route("/{domain}")
*/

class AttributeController extends BaseController{
	protected $session;
	public function __construct(){
		$this->session = new Session();
	}
	
	/**
	* @Route("/Attributes/{attr}",defaults={"attr"=""})
	* @Template()
	*/
	public function attributesAction($attr){
		$domain_id = $this->get('session')->get('domain_id');
		if(isset($attr) && !empty($attr)){
			$attribute_master = $this->getDoctrine()
						->getManager()
						->getRepository("AdminBundle:Attributemaster")
						->findBy(array("main_attribute_id"=>$attr,"is_deleted"=>0));
			if(!empty($attribute_master)){
				foreach($attribute_master as $key=>$val){
					$attribute = $this->getDoctrine()
						->getManager()
						->getRepository("AdminBundle:Attributemaster")
						->findOneBy(array("attribute_master_id"=>$val->getAttribute_master_id()));
					$em = $this->getDoctrine()->getManager();
					$attribute->setIs_deleted(1);
					$em->persist($attribute);
					$em->flush();
				}
			}
			$this->get("session")->getFlashBag()->set("success_msg","Attribute removed.");
			return $this->redirect($this->generateUrl("admin_attribute_attributes",array("domain"=>$this->get('session')->get('domain'))));
		}else{
			$language = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Languagemaster')
				   ->findBy(array('is_deleted'=>0));
			$query= "SELECT *  FROM `attribute_master` WHERE `domain_id` = '". $domain_id ."' AND `is_deleted` = 0 group by main_attribute_id";
			$em = $this->getDoctrine()->getManager()->getConnection()->prepare($query);
			$em->execute();
			$attribute_master = $em->fetchAll();
			//var_dump($attribute_master);exit;
			$all_attribute = "";
		
			if(!empty($attribute_master))
			{
				foreach(array_slice($attribute_master,0) as $lkey=>$lval)
				{
					$lang_wise_att = "";
					foreach($language as $lngkey=>$lngval){
						$lang_attribute_name = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Attributemaster')
						->findOneBy(array('language_id'=>$lngval->getLanguage_master_id(),"main_attribute_id"=>$lval['main_attribute_id']));
						$attribute_name='';
					
						if(!empty($lang_attribute_name)){
							$attribute_name = $lang_attribute_name->getAttribute_name();
						}	
						$lang_wise_att[] = array(
							"language_id"=>$lngval->getLanguage_master_id(),
							"attribute_name"=>$attribute_name
						);
					}
					$all_attribute[] = array(
						"attribute_master_id"=>$lval['attribute_master_id'],
						"attribute_name"=>$lval['attribute_name'],
						"public_name"=>$lval['public_name'],
						"attribute_field_type_id"=>$lval['attribute_field_type_id'],
						"create_date"=>$lval['create_date'],
						"create_by"=>$lval['create_by'],
						"domain_id"=>$lval['domain_id'],
						"language_id"=>$lval['language_id'],
						"main_attribute_id"=>$lval['main_attribute_id'],
						"is_deleted"=>$lval['is_deleted'],
						"lang_wise_att"=>$lang_wise_att
						);
				}
			}
			return array("attributes"=>$all_attribute,"language"=>$language);
		}
	}
	
	/**
	* @Route("/Attribute/Add-Update-Attribute/{attr}",defaults={"attr"=""})
	* @Template()
	*/
	public function addattrAction($attr){
		if($attr){
			return array("language"=>$this->getdata("Languagemaster",array("is_deleted"=>0)),"field_type"=>$this->getdata("Attributefieldtype",array("is_deleted"=>0)),"attr"=>$this->getdata("Attributemaster",array("is_deleted"=>0,"main_attribute_id"=>$attr)));
		}
		return array("language"=>$this->getdata("Languagemaster",array("is_deleted"=>0)),"field_type"=>$this->getdata("Attributefieldtype",array("is_deleted"=>0)));
	}
	
	/**
	* @Route("/Attribute/Save-Attributes")
	*/
	public function saveattributes(){
		$create_by = $this->get('session')->get('user_id');
		$domain_id = $this->get('session')->get('domain_id');
		$language_id = $_POST['language_id'];
		
		if(isset($_POST['save'])){			
			if(isset($_POST['attr']) && !empty($_POST['attr'])){
				$main_attribute_id = $_POST['attr'];
				
				$attribute_master = $this->getDoctrine()
							->getManager()
							->getRepository("AdminBundle:Attributemaster")
							->findOneBy(array("main_attribute_id"=>$main_attribute_id,"language_id"=>$language_id,"is_deleted"=>0));
				if(!empty($attribute_master))
					{
						$em = $this->getDoctrine()->getManager();
						$update = $em->getRepository("AdminBundle:Attributemaster")->find($attribute_master->getAttribute_master_id());
						$update->setAttribute_name($_POST['name']);
						$update->setPublic_name($_POST['public_name']);
						$update->setAttribute_field_type_id($_POST['attribute_field_type_id']);
						$em->flush();
						$this->get("session")->getFlashBag()->set("success_msg","Attribute updated.");
					}
					else{
						$attribute = new Attributemaster();
						$attribute->setAttribute_name($_POST['name']);
						$attribute->setPublic_name($_POST['public_name']);
						$attribute->setAttribute_field_type_id($_POST['attribute_field_type_id']);
						$attribute->setCreate_date(date('Y-m-d H:i:s'));
						$attribute->setCreate_by($create_by);
						$attribute->setLanguage_id($language_id);
						$attribute->setMain_attribute_id($main_attribute_id);
						$attribute->setDomain_id($domain_id);
						$attribute->setIs_deleted(0);
						$em = $this->getDoctrine()->getManager();
						$em->persist($attribute);
						$em->flush();
						$this->get("session")->getFlashBag()->set("success_msg","Attribute created.");
						return $this->redirect($this->generateUrl("admin_attribute_addattr",array("domain"=>$this->get('session')->get('domain'),"attr"=>$main_attribute_id)));
					}
			} else {
				$attribute = new Attributemaster();
				$attribute->setAttribute_name($_POST['name']);
				$attribute->setPublic_name($_POST['public_name']);
				$attribute->setAttribute_field_type_id($_POST['attribute_field_type_id']);
				$attribute->setCreate_date(date('Y-m-d H:i:s'));
				$attribute->setCreate_by($create_by);
				$attribute->setLanguage_id($language_id);
				$attribute->setMain_attribute_id(0);
				$attribute->setDomain_id($domain_id);
				$attribute->setIs_deleted(0);
				$em = $this->getDoctrine()->getManager();
				$em->persist($attribute);
				$em->flush();
				$attribute->setMain_attribute_id($attribute->getAttribute_master_id());
				$em->flush();
				$this->get("session")->getFlashBag()->set("success_msg","Attribute created.");
			}
		}
		return $this->redirect($this->generateUrl("admin_attribute_attributes",array("domain"=>$this->get('session')->get('domain'))));
	}
	
	/**
	* @Route("/Attribute-Value/{attr}",defaults={"attr"=""})
	* @Template()
	*/
	public function attrvaluesAction($attr){
		$domain_id = $this->get('session')->get('domain_id');
		return array("attrs"=>$this->getdata("Attributemaster",array("domain_id"=>$domain_id,"is_deleted"=>0)),
					"vals"=>$this->getdata("Attributevalue",array("domain_id"=>$domain_id,"attribute_master_id"=>$attr,"is_deleted"=>0)),"attr"=>$attr,"attr_name"=>$this->getonedata("Attributemaster",array("is_deleted"=>0,"attribute_master_id"=>$attr)));
	}
	
	/**
	* @Route("/Delete-Attribute-Value/{attr}/{val}",defaults={"attr"="","val"=""})
	* @Template()
	*/
	public function delattrvaluesAction($attr,$val){
		
		if(isset($val) && !empty($val)){
			$attribute_value_master = $this->getDoctrine()
						->getManager()
						->getRepository("AdminBundle:Attributevalue")
						->findBy(array("main_attribute_value_id"=>$val,"is_deleted"=>0));
			if(!empty($attribute_value_master)){
				foreach($attribute_value_master as $key=>$v){
					$attribute = $this->getDoctrine()
						->getManager()
						->getRepository("AdminBundle:Attributevalue")
						->findOneBy(array("attribute_value_id"=>$v->getAttribute_value_id()));
					$em = $this->getDoctrine()->getManager();
					$attribute->setIs_deleted(1);
					$em->persist($attribute);
					$em->flush();
				}
			}
			$this->get("session")->getFlashBag()->set("success_msg","Value removed.");
			return $this->redirect($this->generateUrl("admin_attribute_attrvalues",array("domain"=>$this->get('session')->get('domain'),"attr"=>$attr)));
		}
	}
	
	/**
	* @Route("/Attribute-Values/Add-Update-Values/{attr}/{val}",defaults={"attr"="","val"=""})
	* @Template()
	*/
	public function addvalAction($attr,$val){
		if($val){
			return array("language"=>$this->getdata("Languagemaster",array("is_deleted"=>0)),"attrs"=>$this->getdata("Attributemaster",array("is_deleted"=>0)),
						"val"=>$this->getdata("Attributevalue",array("is_deleted"=>0,"main_attribute_value_id"=>$val)),"attr"=>$attr,"attr_name"=>$this->getonedata("Attributemaster",array("is_deleted"=>0,"attribute_master_id"=>$attr)));
		}
		return array("language"=>$this->getdata("Languagemaster",array("is_deleted"=>0)),"attrs"=>$this->getdata("Attributemaster",array("is_deleted"=>0)),"attr"=>$attr,"attr_name"=>$this->getonedata("Attributemaster",array("is_deleted"=>0,"attribute_master_id"=>$attr)));
	}
	
	/**
	* @Route("/Attribute-Values/Save-Attribute-Values")
	*/
	public function saveattrval(){
		$create_by = $this->get('session')->get('user_id');
		$domain_id = $this->get('session')->get('domain_id');
		$language_id = $_POST['language_id'];
		
				
		
		if(isset($_POST['save'])){
			if(isset($_POST['attrval']) && !empty($_POST['attrval'])){
				$main_value_id = $_POST['attrval'];
				$attribute_id = $_POST['attribute'];
				$attribute_value_master = $this->getDoctrine()
							->getManager()
							->getRepository("AdminBundle:Attributevalue")
							->findOneBy(array("main_attribute_value_id"=>$main_value_id,"language_id"=>$language_id,"is_deleted"=>0));
				if(!empty($attribute_value_master)){
					$em = $this->getDoctrine()->getManager();
					$update = $em->getRepository("AdminBundle:Attributevalue")->find($attribute_value_master->getAttribute_value_id());
					$update->setValue($_POST['attrvalue']);
					$em->flush();
					$this->get("session")->getFlashBag()->set("success_msg","Attribute Value updated.");
				}else{
					$value = new Attributevalue();
					$value->setAttribute_master_id($_POST['attribute']);
					$value->setValue($_POST['attrvalue']);
					$value->setCreate_date(date('Y-m-d H:i:s'));
					$value->setCreated_by($create_by);
					$value->setDomain_id($domain_id);
					$value->setLanguage_id($language_id);
					$value->setMain_attribute_value_id($main_value_id);
					$value->setIs_deleted(0);
					$em = $this->getDoctrine()->getManager();
					$em->persist($value);
					$em->flush();
					$this->get("session")->getFlashBag()->set("success_msg","Attribute Value inserted.");
					return $this->redirect($this->generateUrl("admin_attribute_addval",array("domain"=>$this->get('session')->get('domain'),"attr"=>$attribute_id,"val"=>$main_value_id)));
				}
			} else {
				$value = new Attributevalue();
				$value->setAttribute_master_id($_POST['attribute']);
				$value->setValue($_POST['attrvalue']);
				$value->setCreate_date(date('Y-m-d H:i:s'));
				$value->setCreated_by($create_by);
				$value->setDomain_id($domain_id);
				$value->setLanguage_id($language_id);
				$value->setMain_attribute_value_id(0);
				$value->setIs_deleted(0);
				$em = $this->getDoctrine()->getManager();
				$em->persist($value);
				$em->flush();
				$value->setMain_attribute_value_id($value->getAttribute_value_id());
				$em->flush();
				$this->get("session")->getFlashBag()->set("success_msg","Attribute Value inserted.");
			}
		}
		return $this->redirect($this->generateUrl("admin_attribute_attrvalues",array("domain"=>$this->get('session')->get('domain'),"attr"=>$_POST['attribute'])));
	}
}