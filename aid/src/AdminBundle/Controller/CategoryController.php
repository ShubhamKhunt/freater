<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AdminBundle\Entity\Categorymaster;
use AdminBundle\Entity\Domainmaster;
use AdminBundle\Entity\Tagmodulerelation;
use AdminBundle\Entity\Tagmaster;
use AdminBundle\Entity\Languagemaster;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
* @Route("/admin")
*/
class CategoryController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }

    /**
     * @Route("/category")
     * @Template()
     */
    public function indexAction()
    {
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
		$all_category = '';
		if($this->get('session')->get('role_id')== '1')
    	{
			$query= "select * from category_master where is_deleted = 0 group by main_category_id ";
			$em = $this->getDoctrine()->getManager()->getConnection()->prepare($query);
			$em->execute();
			$all_category = $em->fetchAll();
		}else{
			$query= "select * from category_master where is_deleted = 0 and parent_category_id!=0 group by main_category_id ";

			$em = $this->getDoctrine()->getManager()->getConnection()->prepare($query);
			$em->execute();
			$all_category = $em->fetchAll();
		}

		if(count($all_category) > 0 ){
			foreach(array_slice($all_category,0) as $lkey=>$lval){
				$parent_category_name = 'No Parent ';
				if($lval['parent_category_id'] != 0){
					$parent_category_name = '' ;
					$parent_category = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:Categorymaster')
							   ->findOneBy(array('is_deleted'=>0,'category_master_id'=>$lval['parent_category_id']));
					if(!empty($parent_category)){
						$parent_category_name = $parent_category->getCategory_name();
					}

				}
				$lang_wise_category = "";
				// fetch product name in all languages
				
				foreach($language as $lngkey=>$lngval){
					$lang_category_name = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Categorymaster')->findOneBy(array('language_id'=>$lngval->getLanguage_master_id(),"main_category_id"=>$lval['main_category_id']));
					$category_name='';
					if(!empty($lang_category_name)){
						$category_name = $lang_category_name->getCategory_name();
					}
					$lang_wise_category[] = array(
						"language_id"=>$lngval->getLanguage_master_id(),
						"category_name"=>$category_name
					);
				}
				$all_category_details[] = array(
					"category_master_id"=>$lval['category_master_id'],
					"category_name"=>$lval['category_name'],
					"parent_category_id"=>$lval['parent_category_id'],
					"parent_category_name"=>$parent_category_name,
					"category_description"=>$lval['category_description'],
					"main_category_id"=>$lval['main_category_id'],
					"category_image_id"=>$lval['category_image_id'],
					"language_id"=>$lval['language_id'],
					"lang_wise_category"=>$lang_wise_category
				);

			}

			return array("language"=>$language,"all_category"=>$all_category_details);
		}
		 return array("language"=>$language);
    }

	/**
     * @Route("/addcategory/{category_id}",defaults={"category_id":""})
     * @Template()
     */
    public function addcategoryAction($category_id)
    {
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
		
        $qry = "select * from category_master WHERE parent_category_id='4' AND is_deleted='0'";
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare($qry);
		$statement->execute();
		$parent_category_child = $statement->fetchAll();

		$qry = "select * from category_master limit 6";
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare($qry);
		$statement->execute();
		$parent_category = $statement->fetchAll();
		$selected_parent_category_child = 0;
		$selected_parent_id = 0;
		if($this->get('session')->get('role_id')!= '1')
		{
			$domain_list = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Domainmaster')
					   ->findBy(array('is_deleted'=>0,"domain_code"=>$this->get('session')->get('domain_id')));
		} else {
			$domain_list = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Domainmaster')
					   ->findBy(array('is_deleted'=>0));
		}

		if(!empty($category_id)){
			
			$query = "SELECT category_master.*,
					  media_library_master.media_location,media_library_master.media_name
					  FROM category_master
					LEFT  JOIN media_library_master
						ON category_master.category_image_id = media_library_master.media_library_master_id
					WHERE category_master.is_deleted = 0 AND category_master.main_category_id = '" . $category_id . "'";

			$em = $this->getDoctrine()->getManager();
			$connection = $em->getConnection();
			$statement = $connection->prepare($query);
			$statement->execute();
			$selected_category = $statement->fetchAll();

			if(!empty($selected_category))
			{
				$query = "SELECT category_master.*,media_library_master.media_location,media_library_master.media_name
						  FROM category_master
						  LEFT  JOIN media_library_master
						  ON category_master.category_image_id = media_library_master.media_library_master_id
						  WHERE category_master.is_deleted = 0 AND category_master.main_category_id = '" . $selected_category[0]['parent_category_id'] . "'";
				$selected_parent_id = $selected_category[0]['parent_category_id'];
				$em = $this->getDoctrine()->getManager();
				$connection = $em->getConnection();
				$statement = $connection->prepare($query);
				$statement->execute();
				$selected_category2 = $statement->fetchAll();

				if(!empty($selected_category2)){
					if($selected_category2[0]['parent_category_id'] == '4' || $selected_category2[0]['parent_category_id'] == 4){
						$selected_parent_id = 4;
						$selected_parent_category_child = $selected_category[0]['parent_category_id'];
					}
				}
			}

			return array("language"=>$language,"parent_category"=>$parent_category,"selected_category"=>$selected_category,'parent_category_child'=>$parent_category_child,'selected_parent_category_child'=>$selected_parent_category_child,"domain_list"=>$domain_list,'selected_parent_id'=>$selected_parent_id);
		}
		else{
			 return array("language"=>$language,"parent_category"=>$parent_category,'parent_category_child'=>$parent_category_child,"domain_list"=>$domain_list);
		}

    }

	/**
     * @Route("/savecategory")
     * @Template()
     */
    public function savecategoryAction()
    {
      $media_id = 0;
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));
    $parent_category_1 = 0;
    if(isset($_REQUEST['parent_category_1']))
    {
        $parent_category_1 = $_REQUEST['parent_category_1'];
    }

		if($_REQUEST['save'] == 'save_category' && !empty($_REQUEST['language_id'] )  && !empty($_REQUEST['category_name'])){

			//add image
			$category_logo = $_FILES['category_logo'];
			if(isset($_REQUEST['image_hidden']))
			{
				$media_id = $_REQUEST['image_hidden'];

				if($category_logo['name'] != "" && !empty($category_logo['name']))
				{
					$extension = pathinfo($category_logo['name'],PATHINFO_EXTENSION);

					$media_type_id = $this->mediatype($extension);

					if($media_type_id == 1){
						$logo = $category_logo['name'];
						$tmpname =$category_logo['tmp_name'];
						$file_path = $this->container->getParameter('file_path');
						$logo_path = $file_path.'/category';
						$logo_upload_dir = $this->container->getParameter('upload_dir').'/category/';

						$media_id = $this->mediaremoveAction($logo,$tmpname,$logo_path,$logo_upload_dir,$media_id,$media_type_id);

					}else{
						$this->get("session")->getFlashBag()->set("error_msg","Category Logo is Required");
						return $this->redirect($this->generateUrl("admin_category_addcategory",array("domain"=>$this->get('session')->get('domain'),"category_id"=>$category_id)));
					}
				}
			}
			else{


				if($category_logo['name'] != "" && !empty($category_logo['name']))
				{
					$extension = pathinfo($category_logo['name'],PATHINFO_EXTENSION);

					$media_type_id = $this->mediatype($extension);

					if($media_type_id == 1){
						$logo = $category_logo['name'];

						$tmpname =$category_logo['tmp_name'];

						$file_path = $this->container->getParameter('file_path');

						$logo_path = $file_path.'/category';

						$logo_upload_dir = $this->container->getParameter('upload_dir').'/category/';

						$media_id = $this->mediauploadAction($logo,$tmpname,$logo_path,$logo_upload_dir,$media_type_id);
					}else{
						$this->get("session")->getFlashBag()->set("error_msg","Upload Valid Category Logo");
						return $this->redirect($this->generateUrl("admin_category_addcategory"));
					}
				}
			}
			$date_time = date("Y-m-d H:i:s") ;
			$category = new Categorymaster();
			if($_REQUEST['parent_category']==6){
				$category->setEmail_id($_REQUEST['email_id']); 
			}

			$category->setCategory_name($_REQUEST['category_name']);

			if($_REQUEST['parent_category']== 4 && $parent_category_1 != 0 && !empty($parent_category_1)){
				$category->setParent_category_id($parent_category_1);
			}
			else{
				$category->setParent_category_id($_REQUEST['parent_category']);
			}

			$category->setCategory_description($_REQUEST['category_description']);
			$category->setCategory_image_id($media_id);
			$category->setLanguage_id($_REQUEST['language_id']);
			$category->setDomain_id($_REQUEST['domain_id']);
			$category->setIs_deleted(0);
			$category->setCreated_datetime($date_time);
			if(isset($_REQUEST['popular_count'])){
				$category->setPopular_count($_REQUEST['popular_count']);
			}
			if(!empty($_REQUEST['main_category_id']) && $_REQUEST['main_category_id'] != '0'){
				$category->setMain_category_id($_REQUEST['main_category_id']);
				$category->setCreated_datetime($date_time);
				$em = $this->getDoctrine()->getManager();
				$em->persist($category);
				$em->flush();
			} else {
				$category->setMain_category_id(0);
				$em = $this->getDoctrine()->getManager();
				$em->persist($category);
				$em->flush();
				$main_cat = $category->getCategory_master_id();
				$category->setMain_category_id($main_cat);
				$category->setCreated_datetime($date_time);
				$category->setDomain_id($_REQUEST['domain_id']);
				$em = $this->getDoctrine()->getManager();
				$em->persist($category);
				$em->flush();
			}
		}
       $this->get("session")->getFlashBag()->set("success_msg","Category Added successfully");
	   return $this->redirect($this->generateUrl("admin_category_index"));
    }

	/**
     * @Route("/deletecategory/{category_id}",defaults={"category_id":""})
     * @Template()
     */
    public function deletecategoryAction($category_id)
    {
		if($category_id != '0'){
			$selected_category = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Categorymaster')
					   ->findBy(array('is_deleted'=>0,'main_category_id'=>$category_id));
			if(!empty($selected_category)){
				foreach($selected_category as $selkey=>$selval){
					$selected_category_del = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Categorymaster')
					   ->findOneBy(array('is_deleted'=>0,'category_master_id'=>$selval->getCategory_master_id()));
					$selected_category_del->setIs_deleted(1);
					$em = $this->getDoctrine()->getManager();
					$em->persist($selected_category_del);
					$em->flush();

				}
			}
			$selected_category = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Categorymaster')
					   ->findBy(array('is_deleted'=>0,'parent_category_id'=>$category_id));
			if(!empty($selected_category)){
				foreach($selected_category as $selkey=>$selval){
					$selected_category_del = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Categorymaster')
					   ->findOneBy(array('is_deleted'=>0,'category_master_id'=>$selval->getCategory_master_id()));
					$selected_category_del->setIs_deleted(1);
					$em = $this->getDoctrine()->getManager();
					$em->persist($selected_category_del);
					$em->flush();

				}
			}

		}
		$this->get("session")->getFlashBag()->set("success_msg","Category Deleted successfully");
	    return $this->redirect($this->generateUrl("admin_category_index"));
	}
	/**
     * @Route("/updatecategory/{category_id}",defaults={"category_id":""})
     * @Template()
     */
    public function updatecategoryAction($category_id)
    {
		if(!empty($category_id)){
			$category_logo = false;
			if(isset($_FILES['category_logo'])){
				$category_logo = $_FILES['category_logo'];
			}

			$media_id = $_REQUEST['image_hidden'];
			if(!empty($category_logo) && $category_logo['name'] != "" && !empty($category_logo['name'])){
					$extension = pathinfo($category_logo['name'],PATHINFO_EXTENSION);
					$media_type_id = $this->mediatype($extension);

					if($media_type_id == 1){
						$logo = $category_logo['name'];
						$tmpname =$category_logo['tmp_name'];
						$file_path = $this->container->getParameter('file_path');
						$logo_path = $file_path.'/category';
						$logo_upload_dir = $this->container->getParameter('upload_dir').'/category/';

						$media_id = $this->mediaremoveAction($logo,$tmpname,$logo_path,$logo_upload_dir,$media_id,$media_type_id);

					}else{
						$this->get("session")->getFlashBag()->set("error_msg","Upload Valid Category Logo");
						return $this->redirect($this->generateUrl("admin_category_addcategory",array("domain"=>$this->get('session')->get('domain'),"category_id"=>$category_id)));
					}
				}

			$category = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Categorymaster')
					   ->findOneBy(array('is_deleted'=>0,'category_master_id'=>$category_id));
			if($category_id == 1 || $category_id == 2 || $category_id == 3 || $category_id == 4 || $category_id == 5 || $category_id == 6)	{
				$parentid =0;
			}else{
				$parentid=$_REQUEST['parent_category'];
			}
			
			if($parentid == 4){
				if(isset($_REQUEST['parent_category_1']) && !empty($_REQUEST['parent_category_1'])){
					$parentid=$_REQUEST['parent_category_1'];
				}
			}
			
			if(!empty($category)){
				if($_REQUEST['parent_category']!=6)
					$category->setEmail_id('');
				if($_REQUEST['parent_category']==6)
					$category->setEmail_id($_REQUEST['email_id']);
				$category->setCategory_name($_REQUEST['category_name']);
				$category->setParent_category_id($parentid);
				$category->setCategory_description($_REQUEST['category_description']);
				$category->setCategory_image_id($media_id);
				$category->setDomain_id($_REQUEST['domain_id']);
				$em = $this->getDoctrine()->getManager();
				$em->persist($category);
				$em->flush();
			}
		}
		$this->get("session")->getFlashBag()->set("success_msg","Category Updated successfully");
	    return $this->redirect($this->generateUrl("admin_category_index"));
	}


     /**
	 * @Route("/tags/{category_id}/{language_id}/{tag_module_relation_id}",defaults={"category_id":"","language_id":"","tag_module_relation_id":""})
     * @Template()
     */
    public function tagsAction($category_id,$language_id,$tag_module_relation_id)
    {
		if(!empty($category_id) && !empty($language_id) && !empty($tag_module_relation_id)){


		}
		else{
			$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));

			$tag_master = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Tagmaster')
						   ->findBy(array('is_deleted'=>0,'language_id'=>$language_id));

			$query = "SELECT tag_module_relation.* , tag_master.* FROM tag_module_relation Join tag_master on tag_module_relation.main_tag_id = tag_master.main_tag_id WHERE tag_module_relation.main_category_id ='".$category_id."' and tag_module_relation.is_deleted=0 and tag_master.language_id = '".$language_id."'";

			$em = $this->getDoctrine()->getManager();
			$connection = $em->getConnection();
			$statement = $connection->prepare($query);
			$statement->execute();
			$tag_display = $statement->fetchAll();

			return array("language"=>$language,"tag_master"=>$tag_master,"cat_language_id"=>$language_id,"cat_main_id"=>$category_id,"tag_display"=>$tag_display);
		}
	}

	/**
     * @Route("/savetag")
     * @Template()
     */
    public function savetagAction()
    {
    	$language_id=$_POST['cat_language_id'];
		$category_id=$_POST['cat_main_id'];
		if(isset($_POST['save_category_tag']) && $_POST['save_category_tag'] == 'save_category_tag')
		{

			if($_POST['main_tag_id'] != "" && !empty($_POST['main_tag_id']))
			{

				$tag_module_relation = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Tagmodulerelation')
						   ->findOneBy(array('is_deleted'=>0,'main_tag_id'=>$_POST['main_tag_id'],"main_category_id"=>$category_id));
				if(empty($tag_module_relation))
				{
					$tag_module_relation = new Tagmodulerelation();

					$tag_module_relation->setModule_name('category');

					$tag_module_relation->setMain_tag_id($_POST['main_tag_id']);
					$tag_module_relation->setMain_category_id($category_id);
					$tag_module_relation->setIs_deleted(0);
					$em = $this->getDoctrine()->getManager();
					$em->persist($tag_module_relation);
					$em->flush();
					$this->get('session')->getFlashBag()->set('success_msg', 'Tag Inserted For Category Successfully');
					return $this->redirect($this->generateUrl("admin_category_tags",array("domain"=>$this->get('session')->get('domain'),"category_id"=>$category_id,"language_id"=>$language_id)));
				}else{
					$this->get('session')->getFlashBag()->set('error_msg', 'This Tag already added for this category.');
				return $this->redirect($this->generateUrl("admin_category_tags",array("domain"=>$this->get('session')->get('domain'),"category_id"=>$category_id,"language_id"=>$language_id)));
				}

			}
			else
			{
				$this->get('session')->getFlashBag()->set('error_msg', 'Please! Fill all required fields.');
				return $this->redirect($this->generateUrl("admin_category_tags",array("domain"=>$this->get('session')->get('domain'),"category_id"=>$category_id,"language_id"=>$language_id)));
			}
		}
		else
		{
			$this->get('session')->getFlashBag()->set('error_msg', 'Oops! something goes wrong! try again later');
			return $this->redirect($this->generateUrl("admin_category_tags",array("domain"=>$this->get('session')->get('domain'),"category_id"=>$category_id,"language_id"=>$language_id)));
		}
	}

	/**
	*  @Route ("/deletecategorytag/{tag_module_relation_id}/{category_id}/{language_id}",defaults={"tag_module_relation_id":"","category_id":"","language_id":""})
	*  @Template()
	*/
	public function deletecategorytag($tag_module_relation_id,$category_id,$language_id){
		if(!empty($tag_module_relation_id))
		{
			$tag_module_relation = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Tagmodulerelation')
						   ->findOneBy(array('is_deleted'=>0,'tag_module_relation_id'=>$tag_module_relation_id));
			if(!empty($tag_module_relation)){
				$tag_module_relation->setIs_deleted(1);
				$em = $this->getDoctrine()->getManager();
				$em->persist($tag_module_relation);
				$em->flush();
				$this->get('session')->getFlashBag()->set('success_msg', 'Tag Deleted For Category Successfully');
					return $this->redirect($this->generateUrl("admin_category_tags",array("domain"=>$this->get('session')->get('domain'),"category_id"=>$category_id,"language_id"=>$language_id)));
			}
		}else{
			$this->get('session')->getFlashBag()->set('error_msg', 'Oops! something goes wrong! try again later');
				return $this->redirect($this->generateUrl("admin_category_tags",array("domain"=>$this->get('session')->get('domain'),"category_id"=>$category_id,"language_id"=>$language_id)));
		}
	}

}
