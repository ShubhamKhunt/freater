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
use AdminBundle\Entity\Combinationimage;
use AdminBundle\Entity\Producttagmaster;
use AdminBundle\Entity\Producttagrelation;
use AdminBundle\Entity\Productattruibutes;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
* @Route("/admin")
*/
class ProductController extends BaseController
{
	public function __construct()
    {
		parent::__construct();
        $obj = new BaseController();
		$obj->checkSessionAction();
    }

    /**
     * @Route("/products")
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
		//$product_list = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Productmaster')->findBy(array("is_deleted"=>0,"domain_id"=>$domain_id));

		$query = "SELECT * FROM `product_master` WHERE is_deleted=0 and domain_id='".$domain_id."' and product_ws_add=0 Group by main_product_master_id ORDER by sort_order ASC";
		$can_insert_flag = 'true';
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare($query);
		$statement->execute();
		$product_list = $statement->fetchAll();
		$lang_arr_product_wise='';
		if(!empty($product_list))
		{
			foreach($product_list as $pkey =>$pval)
			{
				$image_details = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Medialibrarymaster')->findOneBy(array('media_library_master_id'=>$pval['product_logo']));
				$image = '';
				if(!empty($image_details))
				{
					$image = $this->container->getParameter('live_path').$image_details->getMedia_location()."/".$image_details->getMedia_name();
				}
				else
				{
					$image = $this->container->getParameter('live_path').'/bundles/design/images/logo.png';
				}
				$lang_arr_product_wise='';
				// fetch product name in all languages
				foreach($language as $lkey=>$lval){
					$lang_product_name = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Productmaster')->findOneBy(array('language_id'=>$lval->getLanguage_master_id(),'main_product_master_id'=>$pval['main_product_master_id']));
					$product_name='';
					if(!empty($lang_product_name)){
						$product_name = $lang_product_name->getProduct_title();
					}
					$lang_arr_product_wise[] = array(
						"language_id"=>$lval->getLanguage_master_id(),
						"product_name"=>$product_name
						);
				}

				$parent_category_name="";
				$product_category_rel = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Productcategoryrelation')->findBy(array("product_id"=>$pval['main_product_master_id'],"is_deleted"=>0));
			if(!empty($product_category_rel)){

				foreach($product_category_rel as $ppkey=>$ppval)
				{
					 $category_id=$ppval->getCategory_id();
					$parent_category = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:Categorymaster')
							   ->findOneBy(array('is_deleted'=>0,'category_master_id'=>$category_id));
					if(!empty($parent_category)){
						$parent_category_name = $parent_category->getCategory_name();
					}

				}

			}
				$total_sale=0;
			 $em = $this->getDoctrine()->getManager();
			 $connection = $em->getConnection();
			 $statement = $connection->prepare("SELECT SUM(quantity) as total  FROM `cart` where is_deleted = 0 and order_id!=0 and order_placed='true' and product_id='".$pval['main_product_master_id']."' group by product_id");
			 $statement->execute();
			 $view_master_list = $statement->fetch();

			if(!empty($view_master_list))
			{
				$total_sale=$view_master_list['total'];
			}

			$instock=$pval['quantity']-$total_sale;
				$product_array[] = array(
						"product_qty"=>$total_sale,
						'quantity'=>$pval['quantity'],
						"product_master_id"=>$pval['product_master_id'],
						"main_product_id"=>$pval['main_product_master_id'],
						"language_id"=>$pval['language_id'],
						"product_name"=>$pval['product_title'],
						"lang_arr_product_wise"=>$lang_arr_product_wise,
						"image"=>$image,
						"original_price"=>$pval['original_price'],
						"status"=>$pval['status'],
						"parent_category_name"=>$parent_category_name,
						);

			}
		}
	//	var_dump(count($product_array));
	//	print_r($product_array);exit;
    	return array("language"=>$language,"product_list"=>$product_array);
    }

    /**
     * @Route("/addproduct/{main_product_id}/{language_id}",defaults={"main_product_id"="","language_id"=""})
     * @Template()
     */
    public function addproductAction($main_product_id,$language_id)
    {
		/*********** Empty add product page code lines : START *****************/
    	$live_path = $this->container->getParameter('live_path');
    	$domain_id = $this->get('session')->get('domain_id');
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));

		$brand_list = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Brandmaster')
					   ->findBy(array('domain_id'=>$domain_id,'is_deleted'=>0));

			$product_type_list = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Producttypemaster')
					   ->findBy(array('is_deleted'=>0,"status"=>'active'));

		$advanced_pricing = false;
		$advanced_pricing_array = array();

		$tag_list = $this->getDoctrine()->getManager()
				->getRepository("AdminBundle:Producttagmaster")
				->findBy(array("is_deleted"=>0));



		$generalsetting = $this->getDoctrine()
		   ->getManager()
		   ->getRepository('AdminBundle:Generalsetting')
		   ->findOneBy(array('is_deleted'=>0,"general_setting_key"=>'advanced_pricing'));
		if(!empty($generalsetting))
		{
			$value = json_decode($generalsetting->getGeneral_setting_value());

			foreach($value as $val)
			{
				if($val->domain_id == $domain_id && $val->flag=='yes')
				{
					$advanced_pricing = true;
					//var_dump($val->domain_id);
				}

			}
			//exit;
		}
		//var_dump($all_category);exit;
		$supplier_details='';
		$selected_product_category_relation_arr = array();
	    foreach($language as $lgkey=>$lgval)
	    {
			$data = $all_category = '';
			$all_category =$all_category_details= '';
			$all_category = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Categorymaster')
						   ->findBy(array('language_id'=>$lgval->getLanguage_master_id(),'is_deleted'=>0,'parent_category_id'=>0,'domain_id'=>$domain_id));
			if(count($all_category) > 0 )
			{
				foreach(array_slice($all_category,0) as $lkey=>$lval)
				{
					$parent_category_name = 'No Parent ' ;
					$data[]  = $this->get_hirerachy($lgval->getLanguage_master_id(),'',$lval->getMain_category_id());

				}
			}
			//----selected Category--------
			$product_category_rel = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Productcategoryrelation')->findBy(array("product_id"=>$main_product_id,"is_deleted"=>0));
			if(!empty($product_category_rel)){

				foreach($product_category_rel as $ppkey=>$ppval){
					if(!in_array($ppval->getCategory_id() , $selected_product_category_relation_arr ) ){
						$selected_product_category_relation_arr[] = $ppval->getCategory_id();
					}

				}

			}
			$attribute_list_drop_down = $this->getDoctrine()
								 ->getManager()
								 ->getRepository('AdminBundle:Attributemaster')
								 ->findBy(array("is_deleted"=>0,"domain_id"=>$domain_id,"language_id"=>$lgval->getLanguage_master_id()));

			$combination_arr_details = $features_list = $supplier_list = $supplier_attribute_relation = '' ;			$comb_attr_id_un = array() ;
			if($main_product_id != '' && $main_product_id != '0')
			{
				// get selected product attributes
				$combination_list = $this->getDoctrine()
										->getManager()
										->getRepository('AdminBundle:Combination')
										->findBy(array("is_deleted"=>0,"product_id"=>$main_product_id));
				$comb_attr_id_un = array() ;
				if(!empty($combination_list))
				{

					foreach($combination_list as $cmkey=>$cmval)
					{

						$comb_arr_str = '';
						$combination_relation_list = $this->getDoctrine()
										->getManager()
										->getRepository('AdminBundle:Combinationrelation')
										->findBy(array("combination_id"=>$cmval->getCombination_id(),"is_deleted"=>0,"product_id"=>$main_product_id));

						if(!empty($combination_relation_list))
						{
							$query = "SELECT count(*) as cnt FROM combination_relation WHERE is_deleted = 0 AND product_id = '".$main_product_id."'  AND  combination_id = '".$cmval->getCombination_id()."' GROUP BY  combination_id";

							$em = $this->getDoctrine()->getManager();
							$connection = $em->getConnection();
							$statement = $connection->prepare($query);
							$statement->execute();
							$combinationrel_cont_grp = $statement->fetchAll();

							$lang_cont = 0 ;

							foreach($combination_relation_list as $crkey=>$crval)
							{

								$attribute_list = $this->getDoctrine()
									->getManager()
									->getRepository('AdminBundle:Attributemaster')
									->findOneBy(array("is_deleted"=>0,"main_attribute_id"=>$crval->getAttribute_id(),"language_id"=>$lgval->getLanguage_master_id()));
								if(!(in_array($crval->getAttribute_id() , $comb_attr_id_un))){
									$comb_attr_id_un[] = $crval->getAttribute_id() ;
								}


								$attribute_value_list = $this->getDoctrine()
									->getManager()
									->getRepository('AdminBundle:Attributevalue')
									->findOneBy(array("is_deleted"=>0,"main_attribute_value_id"=>$crval->getAttribute_value_id(),"language_id"=>$lgval->getLanguage_master_id()));

								if(!empty($attribute_list) && !empty($attribute_value_list)){
									$lang_cont++ ;
									$comb_arr_str .= ' [ ' .$attribute_list->getAttribute_name() ." : " . $attribute_value_list->getValue() . " ] ";
								}
							}

						}

						$combination_image = $this->getDoctrine()
										->getManager()
										->getRepository('AdminBundle:Combinationimage')
										->findBy(array("combination_id"=>$cmval->getCombination_id(),"is_deleted"=>0,"product_id"=>$main_product_id));
						$combination_images = NULL;
						if(!empty($combination_image))
						{
							foreach($combination_image as $comkey=>$comval)
							{
								$media_library = $this->getDoctrine()
										   ->getManager()
										   ->getRepository('AdminBundle:Medialibrarymaster')
										   ->findOneBy(array('media_library_master_id'=>$comval->getMedia_id(),'is_deleted'=>0));

								$combination_images[] = array(
										"image_path" => $live_path.$media_library->getMedia_location()."/".$media_library->getMedia_name(),
										"combination_image_id" => $comval->getCombination_image_id()
									);
							}
						}
						if(isset($combinationrel_cont_grp) && !empty($combinationrel_cont_grp))
						{
							if($combinationrel_cont_grp[0]['cnt'] == $lang_cont )
							{
								$combination_arr_details[] = array(
											"combination_id"=>$cmval->getCombination_id(),
											"language_id"=>$cmval->getLanguage_id(),
											"combination_market_price"=>$cmval->getComb_market_price(),
											"status"=>$cmval->getStatus(),
											"attribute_value"=>$comb_arr_str,
											"image_gallery"=>$combination_images
										 );
							}
						}


					}
					//var_dump($combination_arr_details);
				}

				if($domain_id == "" || $domain_id == '0'  )
				{
					$features_list = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:Featuresmaster')
							   ->findBy(array('is_deleted'=>0,'status'=>'active',"language_id"=>$lgval->getLanguage_master_id()));

					$supplier_list = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:Suppliermaster')
							   ->findBy(array('is_deleted'=>0,'status'=>'active',"language_id"=>$lgval->getLanguage_master_id()));



				}
				else
				{
					$features_list = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:Featuresmaster')
							   ->findBy(array('is_deleted'=>0,'status'=>'active','domain_id'=>$domain_id,"language_id"=>$lgval->getLanguage_master_id()));

					$supplier_list = $this->getDoctrine()
							   ->getManager()
							   ->getRepository('AdminBundle:Suppliermaster')
							   ->findBy(array('is_deleted'=>0,'status'=>'active','domain_id'=>$domain_id,"language_id"=>$lgval->getLanguage_master_id()));
					$supplier_details = '';
					$advanced_pricing_array = array();
					if(!empty($supplier_list)){
						foreach($supplier_list as $skey=>$sval){

							// check supplier is slected for this product or not
							$supplier_product_relation_list = $this->getDoctrine()
												->getManager()
												->getRepository('AdminBundle:Productsupplierrelation')
												->findBy(array("is_deleted"=>0,"main_product_id"=>$main_product_id,"domain_id"=>$domain_id,"supplier_id"=>$sval->getMain_supplier_id()));
							$sup_available = "false";
							$supp_attr_arr = '';
							//var_dump($supplier_product_relation_list);exit;
							if(!empty($supplier_product_relation_list)){
								$sup_available = "true";
								// if available then fetch all product attributes and values with price and quanities
									$combination_list = $this->getDoctrine()
										->getManager()
										->getRepository('AdminBundle:Combination')
										->findBy(array("is_deleted"=>0,"product_id"=>$main_product_id));

								foreach($supplier_product_relation_list as $suppkey=>$supval){

									$combination_id = $supval->getMain_combination_id();
									$supplier_id = $supval->getSupplier_id();
									$price = $supval->getUnit_price();
									$quantity = $supval->getQuantity();
									$city_id = $supval->getCity_id();
									$supp_attr_arr[] = array (
										"combination_id"=>$combination_id,
										"supplier_id"=>$supplier_id,
										"price"=>$price,
										"city_id"=>$city_id,
										"quantity"=>$quantity
										);
									if($advanced_pricing == true)
									{
											/*
											$advancedpricing_list = $this->getDoctrine()
													->getManager()
													->getRepository('AdminBundle:Advancedpricing')
													->findBy(array("product_id"=>$main_product_id,"is_deleted"=>0,"product_supplier_relation_id"=>$supval->getProduct_supplier_relation_id()));
											*/
											$query2 = "select * from advanced_pricing where product_id='$main_product_id' AND is_deleted='0' AND product_supplier_relation_id='".$supval->getProduct_supplier_relation_id()."' order by apply_from_date asc";

											$em = $this->getDoctrine()->getManager();
											$connection = $em->getConnection();
											$statement = $connection->prepare($query2);
											$statement->execute();
											$advancedpricing_list = $statement->fetchAll();

											if(!empty($advancedpricing_list))
											{
												foreach($advancedpricing_list as $val_adv)
												{
													$advanced_pricing_array[] = array(
													"product_supplier_relation_id"=>$val_adv['product_supplier_relation_id'],
													"ptd"=>$val_adv['ptd'],
													"distributor_margin"=>$val_adv['distributor_margin'],
													"awl_margin"=>$val_adv['awl_margin'],
													"final_app_price"=>$val_adv['final_app_price'],
													"apply_from_date"=>$val_adv['apply_from_date'],
													"updated_date"=>$val_adv['updated_date'],
													);
												}
											}
										//var_dump($cmval->getCombination_id());exit;
									}
								}
							}
						  $supplier_details[] = array(
									"supplier_master_id"=>$sval->getSupplier_master_id(),
									"supplier_name"=>$sval->getSupplier_name(),
									"supplier_description"=>$sval->getSupplier_description(),
									"phone_no"=>$sval->getPhone_no(),
									"mobile_no"=>$sval->getMobile_no(),
									"address_id"=>$sval->getAddress_id(),
									"supplier_logo"=>$sval->getSupplier_logo(),
									"status"=>$sval->getStatus(),
									"created_by"=>$sval->getCreated_by(),
									"created_date"=>$sval->getCreated_date(),
									"main_supplier_id"=>$sval->getMain_supplier_id(),
									"language_id"=>$sval->getLanguage_id(),
									"domain_id"=>$sval->getDomain_id(),
									"is_deleted"=>$sval->getIs_deleted(),
									"sup_available"=>$sup_available,
									"supp_attr_arr"=>$supp_attr_arr,
									"advanced_pricing_arr"=>$advanced_pricing_array

							);
					//	  var_dump($advanced_pricing_array);
						}
					}
				}


				// get Selected Suppliers and selected attributes


			}


			$final_array[] = array(
				"langauge_id"=>$lgval->getLanguage_master_id(),
				"category_details"=>$data,
				"attribute_list"=>$attribute_list_drop_down,
				"combination_arr_details"=>$combination_arr_details,
				"features_list"=>$features_list,
				"supplier_list"=>$supplier_details,
				"comb_attr_id_un"=>$comb_attr_id_un,
				"selected_category"=>$selected_product_category_relation_arr
				);

	    }
	    /*********** Empty add product page code lines : END *****************/


	    /*********** Edit add product page code lines : START *****************/

	    if(isset($main_product_id) && $main_product_id != "")
	    {
			//get edited product info from product_master table
			$product_info = $this->getDoctrine()
								 ->getManager()
								 ->getRepository('AdminBundle:Productmaster')
								 ->findBy(array("main_product_master_id"=>$main_product_id,"is_deleted"=>0,"domain_id"=>$domain_id));

			 $total_sale=0;
			 $em = $this->getDoctrine()->getManager();
			 $connection = $em->getConnection();
			 $statement = $connection->prepare("SELECT SUM(quantity) as total  FROM `cart` where is_deleted = 0 and order_id!=0 and order_placed='true' and product_id=$main_product_id group by product_id");
			 $statement->execute();
			 $view_master_list = $statement->fetch();

			if(!empty($view_master_list))
			{
				$total_sale=$view_master_list['total'];
			}

			$instock=!empty($product_info)?$product_info[0]->getQuantity()-$total_sale:'';


			$media_library_master_id = !empty($product_info)?$product_info[0]->getMain_product_master_id():'';

			$image_gallery = $this->getDoctrine()
								 ->getManager()
								 ->getRepository('AdminBundle:Gallerymaster')
								 ->findBy(array("module_name"=>"product","module_primary_id"=>$media_library_master_id,"is_deleted"=>0));

			$media_library = array();
			if(!empty($image_gallery))
			{
				foreach($image_gallery as $key=>$val)
				{
					$media_library_master = $this->getDoctrine()
									 ->getManager()
									 ->getRepository('AdminBundle:Medialibrarymaster')
									 ->findOneBy(array("media_library_master_id"=>$val->getMedia_library_master_id(),"is_deleted"=>0));
					if(!empty($media_library_master))
					{
						//var_dump($val);exit;
						$media_library[] = array(
								"media_library_master_id"=>$media_library_master->getMedia_library_master_id(),
								"media_title"=>$media_library_master->getMedia_type_id(),
								"media_location"=>$media_library_master->getMedia_location(),
								"media_name"=>$media_library_master->getMedia_name(),
								"created_on"=>$media_library_master->getCreated_on(),
								"gallery_master_id"=>$val->getGallery_master_id(),
								"module_name"=>$val->getModule_name(),
								"module_primary_id"=>$val->getModule_primary_id()
							);
					}
				}
			}
			
			$product_city_relation_data = $this->getDoctrine()
											   ->getManager()
											   ->getRepository('AdminBundle:Productcityrelation')
											   ->findBy(array("product_id"=>$main_product_id,"is_deleted"=>0,"domain_id"=>$domain_id,"status"=>'active'));


			$product_city_rel = array();
			if(!empty($product_city_relation_data))
			{
				foreach($product_city_relation_data as $key=>$val)
				{

					$city_master_data = $this->getDoctrine()
											   ->getManager()
											   ->getRepository('AdminBundle:Citymaster')
											   ->findBy(array("city_master_id"=>$val->getCity_id()));
					
					$product_city_rel[$key]['product_city_relation_id'] = $val->getProduct_city_relation_id();
					$product_city_rel[$key]['product_id'] = $val->getProduct_id();
					$product_city_rel[$key]['city_id'] = $val->getCity_id();
					$product_city_rel[$key]['country_idd'] = $val->getCountry_id();
					$product_city_rel[$key]['state_id'] = $val->getState_id();
					$product_city_rel[$key]['status'] = $val->getStatus();
					$product_city_rel[$key]['domain_id'] = $val->getDomain_id();
					$product_city_rel[$key]['create_date'] = $val->getCreate_date();
					$product_city_rel[$key]['is_deleted'] = $val->getIs_deleted();
					$product_city_rel[$key]['city_name'] = $city_master_data[0]->getCity_name();
				}
			}

			
			$selected_tag = $this->getDoctrine()->getManager()
					->getRepository("AdminBundle:Producttagrelation")
					->findBy(array("product_id"=>$main_product_id,"is_deleted"=>0));
			
			return array("advanced_pricing"=>$advanced_pricing,"advanced_pricing_array"=>$advanced_pricing_array,"language"=>$language,"all_final_category"=>$final_array,"brand_list"=>$brand_list,"product_type_list"=>$product_type_list,"product_info"=>$product_info,"main_product_id"=>$main_product_id,"media_library_master"=>$media_library,"product_city_rel"=>$product_city_rel,"tag_list"=>$tag_list,"selected_tag"=>$selected_tag,"total_sale"=>$total_sale,"instock"=>$instock);
		}
	    /*********** Edit add product page code lines : END *****************/

		return array("advanced_pricing"=>$advanced_pricing,"advanced_pricing_array"=>$advanced_pricing_array,"language"=>$language,"all_final_category"=>$final_array,"brand_list"=>$brand_list,"product_type_list"=>$product_type_list,"product_city_rel"=>"","tag_list"=>$tag_list);

	}

	/**
     * @Route("/addproduct2")
     * @Template()
     */
    public function addproduct2Action()
    {
		/*********** Empty add product page code lines : START *****************/

    	$domain_id = $this->get('session')->get('domain_id');
		$language = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Languagemaster')
					   ->findBy(array('is_deleted'=>0));

		$brand_list = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Brandmaster')
					   ->findBy(array('domain_id'=>$domain_id,'is_deleted'=>0));

		//var_dump($all_category);exit;
	    foreach($language as $lgkey=>$lgval)
	    {
			$data = $all_category = '';
			$all_category =$all_category_details= '';
			$all_category = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Categorymaster')
						   ->findBy(array('language_id'=>$lgval->getLanguage_master_id(),'is_deleted'=>0,'parent_category_id'=>0,'domain_id'=>$domain_id));
			if(count($all_category) > 0 )
			{
				foreach(array_slice($all_category,0) as $lkey=>$lval)
				{
					$parent_category_name = 'No Parent ' ;
					$data[]  = $this->get_hirerachy($lgval->getLanguage_master_id(),'',$lval->getCategory_master_id());

				}
			}
			$attribute_list = $this->getDoctrine()
								 ->getManager()
								 ->getRepository('AdminBundle:Attributemaster')
								 ->findBy(array("is_deleted"=>0,"domain_id"=>$domain_id,"language_id"=>$lgval->getLanguage_master_id()));

			$final_array[] = array(
				"langauge_id"=>$lgval->getLanguage_master_id(),
				"category_details"=>$data,
				"attribute_list"=>$attribute_list
				);
	    }
	    /*********** Empty add product page code lines : END *****************/


	    /*********** Edit add product page code lines : START *****************/

	    if(isset($main_product_id) && $main_product_id != "")
	    {
	    	//get edited product info from product_master table
			$product_info = $this->getDoctrine()
								 ->getManager()
								 ->getRepository('AdminBundle:Productmaster')
								 ->findBy(array("main_product_master_id"=>$main_product_id,"is_deleted"=>0,"domain_id"=>$domain_id));

			//return values to view page
			return array("language"=>$language,"all_final_category"=>$final_array,"brand_list"=>$brand_list,"product_info"=>$product_info);
		}
	    /*********** Edit add product page code lines : END *****************/


		return array("language"=>$language,"all_final_category"=>$final_array,"brand_list"=>$brand_list);

	}

	/**
     * @Route("ajaxproductunit")
     *
     */
	public function ajaxproductunit()
	{
		if(isset($_POST['flag']) && $_POST['flag'] == 'change_status')
		{

			$main_product_id = $_POST['product_id'];
			$unit_id = $_POST['unit_id'];

			//dump($user_id);
			//exit;
			$sts = "";
			$em = $this->getDoctrine()->getManager();
			$pro_list = $em->getRepository('AdminBundle:Productmaster')
								->findBy(
									array(
										'main_product_master_id'=>$main_product_id,
									)
								) ;
								//var_dump($pro_list);
			if(!empty($pro_list))
			{
				foreach($pro_list as $val)
				{
					if($unit_id <= 0)
					{
						$unit_id = 0;
						//$val->setStatus('inactive');
					}
					$val->setQuantity($unit_id);
					$em->persist($val) ;
					$em->flush() ;
				}
			}
		}
		return new Response("successful");
	}

	/**
     * @Route("/product/saveproductbasic/{lang_id}")
     * @Template()
     */
    public function saveproductbasicAction($lang_id)
    {
    	$product_title_exist_check = $this->getDoctrine()
								 ->getManager()
								 ->getRepository('AdminBundle:Productmaster')
								 ->findBy(array("product_title"=>$_POST['product_title_english'],"is_deleted"=>0));

    	//basic details save button clicked
    	if(isset($_POST['save_basic_info']) && $_POST['save_basic_info'] == "save_basic_info" && $lang_id != "")
    	{
    		//server side validation check
			if($_POST['product_title_english'] != "" && $_POST['refrence_code'] != ""  && $_POST['price'] != "" )
			{
				if(count($product_title_exist_check)==0)
				{
					$product_title_english = $_POST['product_title_english'];
					$product_title_arbic = $_POST['product_title_arbic'];
					$refrence_code = $_POST['refrence_code'];
					$brand_id = 0;
					$product_type_id = $_POST['product_type_id'];
					$price = $_POST['price'];
					$mprice = $_POST['mprice'];
					$stock = $_POST['quantity'];
					$max_order = $_POST['max_order'];
					$unit_size = $_POST['unit_size'];
					$status = !empty($_POST['status'])?$_POST['status']:'active';
					$short_description_en = '';
					$description_en = $_POST['description_en'];
					$short_description_ar = '';
					$description_ar = $_POST['description_ar'];

					$ddl_tag = isset($_POST['ddl_tag']) ? $_POST['ddl_tag'] : null;
					//fetch product by refrence code for check refrence code is already exist or not


					//if refrence code not exist then insert in loop

					$product_master = new Productmaster();

					$product_master->setProduct_title($product_title_english);
					$product_master->setRefrence_code($refrence_code);
					$product_master->setBrand_id($brand_id);
					$product_master->setProduct_type_id($product_type_id);
					$product_master->setShort_description($short_description_en);
					$product_master->setDescription($description_en);
					$product_master->setProduct_logo(0);
					$product_master->setStatus($status);
					$product_master->setQuantity($stock);
					$product_master->setMax_allowed_qty_per_order($max_order);
					$product_master->setUnit_size($unit_size);
					$product_master->setMarket_price($mprice);
					$product_master->setOriginal_price($price);
					$product_master->setSku(0);
					$product_master->setOn_sale("no");
					$product_master->setAdditional_shipping_charge(0);
					$product_master->setLanguage_id(1);
					$product_master->setMain_product_master_id(0);
					$product_master->setProduct_ws_add(0);
					$product_master->setCreated_by($this->get('session')->get('user_id'));
					$product_master->setCreated_date(date("Y-m-d H:i:s"));
					$product_master->setLast_updated(date("Y-m-d H:i:s"));
					$product_master->setDomain_id($this->get('session')->get('domain_id'));
					$product_master->setIs_deleted(0);

					$em = $this->getDoctrine()->getManager();
					$em->persist($product_master);
					$em->flush();
					$product_master1 = new Productmaster();

					$product_master1->setProduct_title($product_title_arbic);
					$product_master1->setRefrence_code($refrence_code);
					$product_master1->setBrand_id($brand_id);
					$product_master1->setProduct_type_id($product_type_id);
					$product_master1->setShort_description($short_description_ar);
					$product_master1->setDescription($description_ar);
					$product_master1->setProduct_logo(0);
					$product_master1->setStatus($status);
					$product_master1->setQuantity($stock);
					$product_master1->setMax_allowed_qty_per_order($max_order);
					$product_master1->setUnit_size($unit_size);
					$product_master1->setMarket_price($mprice);
					$product_master1->setOriginal_price($price);
					$product_master1->setSku(0);
					$product_master1->setOn_sale("no");
					$product_master1->setAdditional_shipping_charge(0);
					$product_master1->setLanguage_id(2);
					$product_master1->setMain_product_master_id(0);
					$product_master1->setCreated_by($this->get('session')->get('user_id'));
					$product_master1->setCreated_date(date("Y-m-d H:i:s"));
					$product_master1->setLast_updated(date("Y-m-d H:i:s"));
					$product_master1->setDomain_id($this->get('session')->get('domain_id'));
					$product_master1->setIs_deleted(0);

					$em = $this->getDoctrine()->getManager();
					$em->persist($product_master1);
					$em->flush();
					$product_master1->setMain_product_master_id($product_master->getProduct_master_id());
					$product_master1->setSort_order($product_master->getProduct_master_id());
					$em->flush();
					$product_master->setMain_product_master_id($product_master->getProduct_master_id());
					$product_master->setSort_order($product_master->getProduct_master_id());
					$em->flush();

					$prod=$product_master->getProduct_master_id();

					if(isset($ddl_tag) && $ddl_tag !=null && isset($prod) && $prod !=0 )
					{
						//var_dump($ddl_tag);exit;
						//product_tag_master

						//product_tag_relation

						$em  = $this->getDoctrine()->getManager();
						$conn = $em->getConnection() ;
						//delete privious pending transactions
						$query = "UPDATE product_tag_relation SET is_deleted=1 WHERE product_id='".$prod."' ";
						$st = $conn->prepare($query);
						$st->execute() ;

						if(!empty($ddl_tag))
						{
							foreach($ddl_tag as $val){
								$id = $val;
								$tag_master = $this->getDoctrine()->getManager()
									->getRepository("AdminBundle:Producttagmaster")
									->findOneBy(array("is_deleted"=>0,"tag_id"=>$val));
								if(empty($tag_master))
								{
									$tag_master_text = $this->getDoctrine()->getManager()
										->getRepository("AdminBundle:Producttagmaster")
										->findOneBy(array("is_deleted"=>0,"tag"=>$val));
									if(empty($tag_master_text))
									{
										//inserst
										$ptm = new Producttagmaster();
										$ptm->setTag($val);
										$ptm->setIs_deleted(0);
										$em = $this->getDoctrine()->getManager();
										$em->persist($ptm);
										$em->flush();

										$id = $ptm->getTag_id();
									}
									else{
										//use
										$id = $tag_master_text->getTag_id();
									}
								}

								$ptr = new Producttagrelation();
								$ptr->setProduct_id($prod);
								$ptr->setTag_id($id);
								$ptr->setIs_deleted(0);
								$em = $this->getDoctrine()->getManager();
								$em->persist($ptr);
								$em->flush();
								//insert to
							}
						}
					}
				} else {
					$this->get("session")->getFlashBag()->set("error_msg","Product Title already exist!");
					return $this->redirect($this->generateUrl("admin_product_addproduct"));
				}

				$this->get("session")->getFlashBag()->set("success_msg","Product basic details saved");
				return $this->redirect($this->generateUrl("admin_product_addproduct",array("main_product_id"=>$product_master->getProduct_master_id())));

			}
			//server side validation check else part
			else
			{
				$this->get("session")->getFlashBag()->set("error_msg","Please fill all required fields!");
				return $this->redirect($this->generateUrl("admin_product_addproduct"));
			}
		}
		//basic details save button clicked else part
		else
		{
			$this->get("session")->getFlashBag()->set("error_msg","Oops! Something goes wrong! Try again later.");
			return $this->redirect($this->generateUrl("admin_product_addproduct"));
		}
    }

    /**
     * @Route("/product/updateproductbasic/{lang_id}/{main_product_id}")
     * @Template()
     */
    public function updateproductbasicAction($lang_id,$main_product_id)
    {
    	//if product update button clicked
    	if(isset($_POST['update_basic_info']) && $_POST['update_basic_info'] == "update_basic_info" && $lang_id != "" && $main_product_id != "")
    	{
    		if($_POST['product_title_english'] != "" && $_POST['refrence_code'] != ""  && $_POST['price'] != "" && $_POST['mprice'] != "" && $_POST['status'] != "")
			{
				$ddl_tag = isset($_POST['ddl_tag']) ? $_POST['ddl_tag'] : null;

				$product_info = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Productmaster')
					   ->findOneBy(array('main_product_master_id'=>$main_product_id,'language_id'=>1,'is_deleted'=>0));

				if(!empty($product_info))
				{
					$product_info1 = $this->getDoctrine()
					   ->getManager()
					   ->getRepository('AdminBundle:Productmaster')
					   ->findOneBy(array('main_product_master_id'=>$main_product_id,'language_id'=>2,'is_deleted'=>0));

					if(!empty($product_info1))
					{
							$em = $this->getDoctrine()->getManager();
						//$product_update = $em->getRepository('AdminBundle:Productmaster')->find($product_info->getProduct_master_id());
						$product_info1->setProduct_title($_POST['product_title_arbic']);
						$product_info1->setRefrence_code($_POST['refrence_code']);
						$product_info1->setBrand_id(0);
						$product_info1->setProduct_type_id($_POST['product_type_id']);
						$product_info1->setShort_description('');
						$product_info1->setDescription($_POST['description_ar']);
						$product_info1->setStatus($_POST['status']);
						$product_info1->setMarket_price($_POST['mprice']);
						$product_info1->setOriginal_price($_POST['price']);
						$product_info1->setQuantity($_POST['quantity']);
						$product_info1->setMax_allowed_qty_per_order($_POST['max_order']);
						$product_info1->setUnit_size($_POST['unit_size']);
						$product_info1->setOn_sale("no");
						$product_info1->setAdditional_shipping_charge(0);
						$product_info1->setLast_updated(date("Y-m-d H:i:s"));
						$product_info1->setIs_deleted(0);
						$em->flush();
					}else{
						$product_master1 = new Productmaster();

						$product_master1->setProduct_title($_POST['product_title_arbic']);
						$product_master1->setRefrence_code($_POST['refrence_code']);
						$product_master1->setBrand_id(0);
						$product_master1->setProduct_type_id($_POST['product_type_id']);
						$product_master1->setShort_description('');
						$product_master1->setDescription($_POST['description_ar']);
						$product_master1->setProduct_logo(0);
						$product_master1->setStatus($_POST['status']);
						$product_master1->setQuantity($_POST['quantity']);
						$product_master1->setMax_allowed_qty_per_order($_POST['max_order']);
						$product_master1->setUnit_size($_POST['unit_size']);
						$product_master1->setMarket_price($_POST['mprice']);
						$product_master1->setOriginal_price($_POST['price']);
						$product_master1->setSku(0);
						$product_master1->setOn_sale("no");
						$product_master1->setAdditional_shipping_charge(0);
						$product_master1->setLanguage_id(2);
						$product_master1->setMain_product_master_id($main_product_id);
						$product_master1->setCreated_by($this->get('session')->get('user_id'));
						$product_master1->setCreated_date(date("Y-m-d H:i:s"));
						$product_master1->setLast_updated(date("Y-m-d H:i:s"));
						$product_master1->setDomain_id($this->get('session')->get('domain_id'));
						$product_master1->setIs_deleted(0);
						$em = $this->getDoctrine()->getManager();
						$em->persist($product_master1);
						
						$em->flush();
					}
					$em = $this->getDoctrine()->getManager();
					$product_update = $em->getRepository('AdminBundle:Productmaster')->find($product_info->getProduct_master_id());
					$product_update->setProduct_title($_POST['product_title_english']);
					$product_update->setRefrence_code($_POST['refrence_code']);
					$product_update->setBrand_id(0);
					$product_update->setProduct_type_id($_POST['product_type_id']);
					$product_update->setShort_description('');
					$product_update->setDescription($_POST['description_en']);
					$product_update->setStatus($_POST['status']);
					$product_update->setMarket_price($_POST['mprice']);
					$product_update->setOriginal_price($_POST['price']);
					$product_update->setQuantity($_POST['quantity']);
					$product_update->setMax_allowed_qty_per_order($_POST['max_order']);
					$product_update->setUnit_size($_POST['unit_size']);
					$product_update->setOn_sale("no");
					$product_update->setAdditional_shipping_charge(0);
					$product_update->setLast_updated(date("Y-m-d H:i:s"));
					$product_update->setIs_deleted(0);
					$em->flush();
					$em = $this->getDoctrine()->getManager();
					$product_info = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Productmaster')
						   ->findBy(array('main_product_master_id'=>$main_product_id));
					foreach ($product_info as $key => $value) {
						$value->setStatus($_POST['status']);
						$value->setMarket_price($_POST['mprice']);
						$value->setOriginal_price($_POST['price']);
						$value->setQuantity($_POST['quantity']);
						$value->setMax_allowed_qty_per_order($_POST['max_order']);
						$value->setUnit_size($_POST['unit_size']);
						$em->flush();
					}
					$this->get("session")->getFlashBag()->set("success_msg","Product basic info updated successfully.");
				}
				else
				{
					$product_title = $_POST['product_title'];
					$refrence_code = $_POST['refrence_code'];
					$brand_id = 0;
					$product_type_id = $_POST['product_type_id'];
					$price = $_POST['price'];
					$mprice = $_POST['mprice'];
					$status = $_POST['status'];
					$short_description = $_POST['short_description'];
					$description = $_POST['description'];
					$stock = $_POST['quantity'];
					$max_order=$_POST['max_order'];
					$unit_size = $_POST['unit_size'];

					$product_master = new Productmaster();

					$product_master->setProduct_title($product_title);
					$product_master->setRefrence_code($refrence_code);
					$product_master->setBrand_id($brand_id);
					$product_master->setProduct_type_id($product_type_id);
					$product_master->setShort_description($short_description);
					$product_master->setDescription($description);
					$product_master->setProduct_logo(0);
					$product_master->setStatus($status);
					$product_master->setQuantity($stock);
					$product_master->setMax_allowed_qty_per_order($max_order);
					$product_master->setUnit_size($unit_size);
					$product_master->setMarket_price($mprice);
					$product_master->setOriginal_price($price);
					$product_master->setSku(0);
					$product_master->setOn_sale("no");
					$product_master->setAdditional_shipping_charge(0);
					$product_master->setLanguage_id($lang_id);
					$product_master->setMain_product_master_id($main_product_id);
					$product_master->setSort_order($main_product_id);
					$product_master->setCreated_by($this->get('session')->get('user_id'));
					$product_master->setCreated_date(date("Y-m-d H:i:s"));
					$product_master->setLast_updated(date("Y-m-d H:i:s"));
					$product_master->setDomain_id($this->get('session')->get('domain_id'));
					$product_master->setIs_deleted(0);

					$em = $this->getDoctrine()->getManager();
					$em->persist($product_master);
					$em->flush();

					$em = $this->getDoctrine()->getManager();
					$product_info = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Productmaster')
						   ->findBy(array('main_product_master_id'=>$main_product_id));
					foreach ($product_info as $key => $value) {
						$value->setStatus($_POST['status']);
						$value->setMarket_price($_POST['mprice']);
						$value->setOriginal_price($_POST['price']);
						$value->setQuantity($_POST['quantity']);
						$value->setMax_allowed_qty_per_order($_POST['max_order']);
						$value->setUnit_size($_POST['unit_size']);
						$em->flush();

						# code...
					}

					$this->get("session")->getFlashBag()->set("success_msg","Product basic details saved");
				}


				$prod=$main_product_id;

				if(isset($ddl_tag) && $ddl_tag !=null && isset($prod) && $prod !=0 )
				{
					$em  = $this->getDoctrine()->getManager();
					$conn = $em->getConnection() ;
					//delete privious pending transactions
					$query = "UPDATE product_tag_relation SET is_deleted=1 WHERE product_id='".$prod."' ";
					$st = $conn->prepare($query);
					$st->execute() ;

					if(!empty($ddl_tag))
					{
						foreach($ddl_tag as $val){
							$id = $val;
							$tag_master = $this->getDoctrine()->getManager()
								->getRepository("AdminBundle:Producttagmaster")
								->findOneBy(array("is_deleted"=>0,"tag_id"=>$val));
							if(empty($tag_master))
							{
								$tag_master_text = $this->getDoctrine()->getManager()
									->getRepository("AdminBundle:Producttagmaster")
									->findOneBy(array("is_deleted"=>0,"tag"=>$val));
									if(empty($tag_master_text))
									{
										//inserst
										$ptm = new Producttagmaster();
										$ptm->setTag($val);
										$ptm->setIs_deleted(0);
										$em = $this->getDoctrine()->getManager();
										$em->persist($ptm);
										$em->flush();

										$id = $ptm->getTag_id();
									}
									else{
										//use
										$id = $tag_master_text->getTag_id();
									}
							}

							$ptr = new Producttagrelation();
							$ptr->setProduct_id($prod);
							$ptr->setTag_id($id);
							$ptr->setIs_deleted(0);
							$em = $this->getDoctrine()->getManager();
							$em->persist($ptr);
							$em->flush();
							//insert to
						}
					}
				}
			} else {
				$this->get("session")->getFlashBag()->set("error_msg","All fields are required, please fill all required fields.");
			}
		}
		//if product update button clicked else part
		else
		{
			$this->get("session")->getFlashBag()->set("error_msg","Oops! Something goes wrong! Try again later.");
		}
		return $this->redirect($this->generateUrl("admin_product_addproduct",array("main_product_id"=>$main_product_id)));
    }

	/**
    * @Route("/product/downitem/{main_product_id}",defaults = {"item_id" = "","main_product_id"=""})
    * Template()
    */
    public function downitemAction($main_product_id) {
        $em = $this->getDoctrine()->getManager();
        $con = $em->getConnection();
        $query = $con->prepare("SELECT * FROM product_master WHERE is_deleted = 0  group by main_product_master_id  ORDER BY sort_order ASC");
        $query->execute();
        $itemmaster = $query->fetchAll();
        $itemmaster = array_reverse($itemmaster);
        foreach (array_slice($itemmaster, 0) as $key => $val) {
            if ($val['main_product_master_id'] == $main_product_id) {
                $cur_step_order = $val['sort_order'];
                if ($key + 1 >= count($itemmaster)) {
                    $stekey = 0;
                } else {
                    $stekey = $key + 1;
                }
                $query = "Update product_master SET sort_order='" . $cur_step_order . "' where main_product_master_id= '" . $itemmaster[$stekey]['main_product_master_id'] . "' and is_deleted='0'";
                $em = $this->getDoctrine()->getManager();
                $connection = $em->getConnection();
                $statement = $connection->prepare($query);
                $statement->execute();
                $query = "Update product_master SET sort_order='" . $itemmaster[$stekey]['sort_order'] . "' where main_product_master_id='" . $main_product_id . "' and is_deleted='0'";
                $em = $this->getDoctrine()->getManager();
                $connection = $em->getConnection();
                $statement = $connection->prepare($query);
                $statement->execute();
                break;
            }
        }
        $this->get('session')->getFlashBag()->set('success_msg', 'Sort Order Updated');
       return $this->redirect($this->generateUrl("admin_product_index",array("domain"=>$this->get('session')->get('domain'))));
    }

    /**
     * @Route("/product/upitem/{main_product_id}",defaults = {"item_id" = "","main_product_id" = ""})
     * Template()
     */
    public function upitemAction($main_product_id) {
        $em = $this->getDoctrine()->getManager();
        $con = $em->getConnection();
        $query = $con->prepare("SELECT * FROM product_master WHERE is_deleted = 0  group by main_product_master_id  ORDER BY sort_order ASC");
        $query->execute();
        $itemmaster = $query->fetchAll();
        //print_r($itemmaster);exit;
        foreach (array_slice($itemmaster, 0) as $key => $val) {
            if ($val['main_product_master_id'] == $main_product_id) {
                $cur_step_order = $val['sort_order'];
                if ($key + 1 >= count($itemmaster)) {
                    $stekey = 0;
                } else {
                    $stekey = $key + 1;
                }
                $query = "Update product_master SET sort_order='" . $cur_step_order . "' where main_product_master_id= '" . $itemmaster[$stekey]['main_product_master_id'] . "' and is_deleted='0'";
                $em = $this->getDoctrine()->getManager();
                $connection = $em->getConnection();
                $statement = $connection->prepare($query);
                $statement->execute();
                $query = "Update product_master SET sort_order='" . $itemmaster[$stekey]['sort_order'] . "' where main_product_master_id='" . $main_product_id . "' and is_deleted='0'";
                $em = $this->getDoctrine()->getManager();
                $connection = $em->getConnection();
                $statement = $connection->prepare($query);
                $statement->execute();
                break;
            }
        }
        $this->get('session')->getFlashBag()->set('success_msg', 'Sort Order Updated');
       return $this->redirect($this->generateUrl("admin_product_index",array("domain"=>$this->get('session')->get('domain'))));
    }

	/**
     * @Route("/saveproductcategory")
     */
    public function saveproductcategoryAction()
    {
		if(isset($_REQUEST['language_id']) && $_REQUEST['language_id'] != '' && isset($_REQUEST['category_selected']) && !empty($_REQUEST['category_selected']) && isset($_REQUEST['main_product_id']) && !empty($_REQUEST['main_product_id'])){
			$language_id = $_REQUEST['language_id'];

			// get all product category relation and delete it
			 $all_product_category = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('AdminBundle:Productcategoryrelation')
						   ->findBy(array('language_id'=>$language_id,'is_deleted'=>0,'product_id'=>$_REQUEST['main_product_id']));
			if(!empty($all_product_category)){
				foreach($all_product_category as $akey=>$aval){
					$aval->setIs_deleted(1);
					$em = $this->getDoctrine()->getManager();
					$em->persist($aval);
					$em->flush();
				}

			}
			for($i = 0 ; $i < count($_REQUEST['category_selected']) ; $i++ ){
				$product_category = new Productcategoryrelation();
				$product_category->setProduct_id($_REQUEST['main_product_id']);
				$product_category->setCategory_id($_REQUEST['category_selected'][$i]);
				$product_category->setCreated_by($this->get('session')->get('user_id'));
				$product_category->setCreate_date(date("Y-m-d H:i:s"));
				$product_category->setDomain_id(1);
				$product_category->setLanguage_id($language_id);
				$product_category->setIs_deleted(0);

				$em = $this->getDoctrine()->getManager();
				$em->persist($product_category);
				$em->flush();


			}
		}
		$this->get("session")->getFlashBag()->set("success_msg","Category saved .");
		return $this->redirect($this->generateUrl("admin_product_addproduct",array("main_product_id"=>$_REQUEST['main_product_id'])));
	}

	/**
     * @Route("/getattributevalue/{attribute_id}/{language_id}")
     */
	public function getattributevalueAction($attribute_id,$language_id){
		 $domain_id = $this->get('session')->get('domain_id');
		$attr = '';
		$attribute_value_list  = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Attributevalue')->findBy(array("language_id"=>$language_id,"attribute_master_id"=>$attribute_id,"domain_id"=>$domain_id,"is_deleted"=>0));
		if(!empty($attribute_value_list)){
			foreach($attribute_value_list as $akey=>$aval){
				$attr[] = array(
						"main_attribute_value_id"=>$aval->getMain_attribute_value_id(),
						"attribute_master_id"=>$aval->getAttribute_master_id(),
						"attribute_value"=>$aval->getValue(),
						"language_id"=>$language_id
								);
			}
		}
		return new Response(json_encode($attr));
	}
    /**
     * @Route("/checkattributevalue/{attribute_arr}/{language_id}/{product_id}")
     */
	public function checkattributevalueAction($attribute_arr,$language_id,$product_id){
		$domain_id = $this->get('session')->get('domain_id');
		$attr = '';
		$attribute_arr = explode(",",$attribute_arr);

		$where_str ='';
		for($i = 0 ; $i < count($attribute_arr) ; $i++ ){
			$single_arr = explode(":",$attribute_arr[$i]);

			if($where_str == ''){
				$where_str.='(attribute_id = '.$single_arr[0].' and attribute_value_id = '.$single_arr[1].' )';
			}
			else{
				$where_str.=' OR (attribute_id = '.$single_arr[0].' and attribute_value_id = '.$single_arr[1].' )';
			}

		}
		$query = "SELECT distinct combination_id FROM combination_relation WHERE is_deleted = 0 AND product_id = '".$product_id."' AND ( " .$where_str. " )  GROUP BY combination_id";

		$can_insert_flag = 'true';
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare($query);
		$statement->execute();
		$selected_combination = $statement->fetchAll();
		$arr_pair = $arr_attribute_value = $arr_attribute = $comb_arr='';
		if(count($selected_combination) > 0 ){
			foreach($selected_combination as $ckey=>$cval){
				$arr_pair = $arr_attribute_value = $arr_attribute = '';
				$comb_arr[] = $cval['combination_id'];
				$query = "SELECT attribute_id , attribute_value_id  FROM combination_relation WHERE is_deleted = 0 AND product_id = '".$product_id."'  AND  combination_id = '".$cval['combination_id']."'";

				$em = $this->getDoctrine()->getManager();
				$connection = $em->getConnection();
				$statement = $connection->prepare($query);
				$statement->execute();
				$single_combination = $statement->fetchAll();

				foreach($single_combination as $skey=>$sval){
					$arr_attribute[] = $sval['attribute_id'];
					$arr_attribute_value[] = $sval['attribute_value_id'];
					$arr_pair[] = "". $sval['attribute_id'] .":".$sval['attribute_value_id'];
				}

				$arraysAreEqual = ($arr_pair === $attribute_arr);
				$result=array_diff($arr_pair,$attribute_arr);
				if(count($result) == 0){
					$can_insert_flag = 'false';
				}


			}
		}
		return new Response($can_insert_flag);
	}


	/**
     * @Route("/saveproductattribute")
     * @Template()
     */
	public function saveproductattributeAction(Request $request)
    {
		if($request->request->all()){
			
			$attribute = new Productattruibutes();
			$attribute->setSku($request->get('sku'));
			$attribute->setBrand($request->get('brand'));
			$attribute->setColor($request->get('color'));
			$attribute->setSize($request->get('size'));
			$attribute->setLength($request->get('length'));
			$attribute->setWeight($request->get('weight'));
			$attribute->setHeight($request->get('height'));
			$attribute->setProduct_id($request->get('main_product_id'));
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($attribute);
			$em->flush();
			
			$this->get("session")->getFlashBag()->set("success_msg","Product Attributes saved");
		}
		
		$referer = $request->headers->get('referer');
		return $this->redirect($referer);
	}
	
    public function saveproductattribute1Action()
    {
		// save

		if(isset($_REQUEST['language_id']) && !empty($_REQUEST['language_id']) && isset($_REQUEST['main_product_id']) && isset($_REQUEST['generate']) && ($_REQUEST['generate'] == 'Generate') && isset($_REQUEST['comb_market_price']) && !empty($_REQUEST['comb_market_price'])){


			//-----------------------
			$attr_id = $_REQUEST['attr_selected_ids_'.$_REQUEST['language_id']];
			function combinations($arrays, $i = 0) {
				if (!isset($arrays[$i])) {
					return array();
				}
				if ($i == count($arrays) - 1) {
					return $arrays[$i];
				}
				// get combinations from subsequent arrays
				$tmp = combinations($arrays, $i + 1);
				$result = array();
				// concat each array from tmp with each element from $arrays[$i]
				foreach ($arrays[$i] as $v) {
					foreach ($tmp as $t) {
						$result[] = is_array($t) ?
							array_merge(array($v), $t) :
							array($v, $t);
					}
				}
				return $result;
			}


			$str='';
			for($i = 0 ; $i < count($attr_id) ; $i++ ){
				for($j = 0 ; $j < count($_REQUEST['attr_selected_vals_'.$_REQUEST['language_id'].'_'.$attr_id[$i]]) ; $j++){
					if($j == count($_REQUEST['attr_selected_vals_'.$_REQUEST['language_id'].'_'.$attr_id[$i]] )){
						$str .= $attr_id[$i] . ":" . $_REQUEST['attr_selected_vals_'.$_REQUEST['language_id'].'_'.$attr_id[$i]][$j] . ",";
					}else{
						$str .= $attr_id[$i] . ":" . $_REQUEST['attr_selected_vals_'.$_REQUEST['language_id'].'_'.$attr_id[$i]][$j] .",";
					}
				}
				$new_arr[] = $_REQUEST['attr_selected_vals_'.$_REQUEST['language_id'].'_'.$attr_id[$i]];
			}
			$str = rtrim($str ,",");
			$arr_result = combinations($new_arr);
			for($i = 0 ; $i < count($arr_result) ; $i++ ){
				//------------check combination is available or not -------------
				//get combination of this product from combination list
				$where_str = '' ;
				for($j = 0 ; $j < count($arr_result[$i]) ; $j++ ){
					if($where_str == ''){
						$where_str.='( attribute_value_id = '.$arr_result[$i][$j].' )';
					}
					else{
						$where_str.=' OR (attribute_value_id = '.$arr_result[$i][$j].' )';
					}
				}
				$query = "SELECT distinct combination_id FROM combination_relation WHERE is_deleted = 0 AND product_id = '".$_REQUEST['main_product_id']."' AND  ( " .$where_str. " )  GROUP BY combination_id";
				/*var_dump($query);*/
				$can_insert_flag = 'true';
				$em = $this->getDoctrine()->getManager();
				$connection = $em->getConnection();
				$statement = $connection->prepare($query);
				$statement->execute();
				$selected_combination = $statement->fetchAll();

				if(count($selected_combination) > 0 ){
					foreach($selected_combination as $ckey=>$cval){
						$arr_pair = $arr_attribute_value = $arr_attribute = '';
						$comb_arr[] = $cval['combination_id'];
						$query = "SELECT attribute_id , attribute_value_id  FROM combination_relation WHERE is_deleted = 0 AND product_id = '".$_REQUEST['main_product_id']."' AND  combination_id = '".$cval['combination_id']."'";

						$em = $this->getDoctrine()->getManager();
						$connection = $em->getConnection();
						$statement = $connection->prepare($query);
						$statement->execute();
						$single_combination = $statement->fetchAll();

						foreach($single_combination as $skey=>$sval){

							$arr_attribute_value[] = $sval['attribute_value_id'];
							$arr_pair[] = $sval['attribute_value_id'];
						}

						$arraysAreEqual = ($arr_pair === $arr_result[$i]);
						$result=array_diff($arr_pair,$arr_result[$i]);
						if(count($arr_pair) != count($arr_result[$i])){
							$can_insert_flag = 'false';
						}else{
							if(count($result) == 0 ){
								$can_insert_flag = 'false';
							}
						}
					}

				}

				if($can_insert_flag == 'true'){
					$combination = new Combination();
					$combination->setProduct_id($_REQUEST['main_product_id']);
					$combination->setLanguage_id(0);
					$combination->setComb_market_price($_REQUEST['comb_market_price']);
					$combination->setStatus('active');
					$combination->setIs_deleted('0');
					$em = $this->getDoctrine()->getManager();
					$em->persist($combination);
					$em->flush();
					$combination_id = $combination->getCombination_id();
					for($j = 0 ; $j < count($arr_result[$i]) ; $j++ ){

						//---get attribute value id-----------
						$attr_value = $this->getDoctrine()->getManager()
									->getRepository("AdminBundle:Attributevalue")
									->findOneBy(array("is_deleted"=>0,"attribute_value_id"=>$arr_result[$i][$j]));
						 if (count($arr_result) == count($arr_result, COUNT_RECURSIVE))
						{

						 $attr_value = $this->getDoctrine()->getManager()
									->getRepository("AdminBundle:Attributevalue")
									->findOneBy(array("is_deleted"=>0,"attribute_value_id"=>$arr_result[$i]));
						}
						else
						{

						 $attr_value = $this->getDoctrine()->getManager()
									->getRepository("AdminBundle:Attributevalue")
									->findOneBy(array("is_deleted"=>0,"attribute_value_id"=>$arr_result[$i][$j]));
						}
						//------------------------------------------
						$combinationrelation = new Combinationrelation();
						$combinationrelation->setProduct_id($_REQUEST['main_product_id']);
						$combinationrelation->setLanguage_id(0);
						$combinationrelation->setAttribute_id($attr_value->getAttribute_master_id());
						$combinationrelation->setAttribute_value_id($attr_value->getMain_attribute_value_id());
						$combinationrelation->setCombination_id($combination_id);
						$combinationrelation->setIs_deleted(0);
						$em = $this->getDoctrine()->getManager();
						$em->persist($combinationrelation);
						$em->flush();
					}
				}
				//-------------------------complete-----------------------------
			}
		}


		// Update
		if(isset($_REQUEST['language_id']) && !empty($_REQUEST['language_id']) && isset($_REQUEST['main_product_id']) && isset($_REQUEST['updategenerate']) && ($_REQUEST['updategenerate'] == 'Update')){
			//-----------------------
			$attr_id = $_REQUEST['attr_selected_ids_'.$_REQUEST['language_id']];

			function combinations($arrays, $i = 0) {
				if (!isset($arrays[$i])) {
					return array();
				}
				if ($i == count($arrays) - 1) {
					return $arrays[$i];
				}
				// get combinations from subsequent arrays
				$tmp = combinations($arrays, $i + 1);
				$result = array();
				// concat each array from tmp with each element from $arrays[$i]
				foreach ($arrays[$i] as $v) {
					foreach ($tmp as $t) {
						$result[] = is_array($t) ?
							array_merge(array($v), $t) :
							array($v, $t);
					}
				}
				return $result;
			}


			for($i = 0 ; $i < count($attr_id) ; $i++ ){
				$new_arr[] = $_REQUEST['attr_selected_vals_'.$_REQUEST['language_id'].'_'.$attr_id[$i]];
			}
			$arr_result = combinations($new_arr);
			//--------------------------------

		}

		$this->get("session")->getFlashBag()->set("success_msg","Product Attributes saved");
		return $this->redirect($this->generateUrl("admin_product_addproduct",array("domain"=>$this->get('session')->get('domain'),"main_product_id"=>$_REQUEST['main_product_id'])));
	}
	/**
     * @Route("/saveproductgallery")
	 * @Template
     */
    public function saveproductgalleryAction()
    {
    	//product save image flag check
    	if(isset($_POST['flag']) && $_POST['flag'] == 'save_gallery_image' && isset($_POST['main_product_id']) && $_POST['main_product_id'] != "")
    	{
    		$em = $this->getDoctrine()->getManager();

			$extension = pathinfo($_POST['img_name'],PATHINFO_EXTENSION);
			$media_type_id = $this->mediatype($extension);
			if($media_type_id == 1){
    		$media_library_master = new Medialibrarymaster();
    		$media_library_master->setMedia_type_id($_POST['media_type_id']);
    		$media_library_master->setMedia_title($_POST['img_name']);
    		$media_library_master->setMedia_location("/bundles/design/uploads/product_image");
    		$media_library_master->setMedia_name($_POST['img_name']);
    		$media_library_master->setCreated_on(date("Y-m-d H:i:s"));
    		$media_library_master->setIs_Deleted(0);
			$em->persist($media_library_master);
			$em->flush();

			$gallery_master = new Gallerymaster();
			$gallery_master->setModule_name("product");
			$gallery_master->setModule_primary_id($_POST['main_product_id']);
			$gallery_master->setMedia_library_master_id($media_library_master->getMedia_library_master_id());
			$gallery_master->setCreated_by($this->get('session')->get('user_id'));
			$gallery_master->setCreate_date(date("Y-m-d H:i:s"));
			$gallery_master->setIs_deleted(0);
			$em->persist($gallery_master);
			$em->flush();

			$media_file = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Medialibrarymaster')
				   ->findOneBy(array('media_library_master_id'=>$media_library_master->getMedia_library_master_id(),'is_deleted'=>0));
			if(!empty($media_file))
			{
				$content = array(
					"name"=>$media_file->getMedia_name(),
					"path"=>$this->container->getParameter('live_path').$media_file->getMedia_location()."/".$media_file->getMedia_name(),
					"thumbnail_path"=>$this->container->getParameter('live_path').$media_file->getMedia_location()."/thumbnail/".$media_file->getMedia_name(),
					"media_library_master_id"=>$media_library_master->getMedia_library_master_id(),
					"gallery_master_id"=>$gallery_master->getGallery_master_id()
				);
				return new Response(json_encode($content));
			}
			else
			{
				return new Response(false);
			}
			}else
			{
				return new Response(false);
			}
		}
    }
    /**
     * @Route("/removeproductimage")
     */
    public function removeproductimageAction()
    {
    	if(isset($_POST['flag']) && $_POST['flag'] == "delete_image" && $_POST['media_library_master_id'] != "" && $_POST['gallery_master_id'] != "")
    	{
			$media_file = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Medialibrarymaster')
				   ->findOneBy(array('media_library_master_id'=>$_POST['media_library_master_id'],'is_deleted'=>0));

			$gallery_file = $this->getDoctrine()
				   ->getManager()
				   ->getRepository('AdminBundle:Gallerymaster')
				   ->findOneBy(array('gallery_master_id'=>$_POST['gallery_master_id'],'is_deleted'=>0));

			if(!empty($media_file) && !empty($gallery_file))
			{
				$media_file->setIs_deleted(1);
				$em = $this->getDoctrine()->getManager();
				$em->flush();

				$gallery_file->setIs_deleted(1);
				$em = $this->getDoctrine()->getManager();
				$em->flush();

				return new Response("true");
			}
			else
			{
				return new Response("false");
			}
		}
    }
    /**
     * @Route("/setdefaultimage")
     */
    public function setdefaultimageAction()
    {
    	if(isset($_POST['flag']) && $_POST['flag'] == "set_default_product_image" && $_POST['main_product_master_id'] != "" && $_POST['media_library_master_id'] != "")
    	{
			$em = $this->getDoctrine()->getManager();
			$connection = $em->getConnection();
			$statement = $connection->prepare("UPDATE product_master SET product_logo = '".$_POST['media_library_master_id']."' WHERE main_product_master_id = '".$_POST['main_product_master_id']."' AND is_deleted = 0");
			$statement->execute();

			return new Response("true");
		}
    }

	/**
     * @Route("/savesupplierproduct")
     * @Template()
     */
    public function savesupplierproductAction()
    {
		if(isset($_REQUEST['language_id']) && !empty($_REQUEST['language_id']) && isset($_REQUEST['main_product_id'])){
			$lang=$_REQUEST['language_id'];
			$product_id = trim($_REQUEST['main_product_id']);
			$domain_id = $this->get('session')->get('domain_id');
			$user_id = $this->get('session')->get('user_id');

			$combination_list = $this->getDoctrine()
										->getManager()
										->getRepository('AdminBundle:Combination')
										->findBy(array("is_deleted"=>0,"product_id"=>$product_id));

			$supplier_product_relation_list = $this->getDoctrine()
												->getManager()
												->getRepository('AdminBundle:Productsupplierrelation')
												->findBy(array("is_deleted"=>0,"main_product_id"=>$product_id,"domain_id"=>$domain_id));
			if(!empty($supplier_product_relation_list)){
				foreach($supplier_product_relation_list as $supdelkey=>$spdelval){
					$supplier_product_relation_info = $this->getDoctrine()
												->getManager()
												->getRepository('AdminBundle:Productsupplierrelation')
												->findOneBy(array("product_supplier_relation_id"=>$spdelval->getProduct_supplier_relation_id()));
					if(!empty($supplier_product_relation_info)){
						$supplier_product_relation_info->setIs_deleted(1);
						$em = $this->getDoctrine()->getManager();
						$em->persist($supplier_product_relation_info);
						$em->flush();
					}
				}
			}
			
			foreach($_REQUEST['supplier'] as $key=>$val)
			{
				 $supplier_city_arr = explode("_",$val);
				 $val = $supplier_city_arr[0];
				 $city_id = $supplier_city_arr[1];
				$city_id1 = $supplier_city_arr[2];
				$checkbox="supplier_".$lang."_".$val."_".$city_id;

				if(isset($_REQUEST[$checkbox]))
				{
					if(!empty($_REQUEST[$checkbox]) && $_REQUEST[$checkbox] == $checkbox)
					{
						if(count($_REQUEST['product_combinataion_id_'.$val.'_'.$city_id]) > 0 ){
							foreach($_REQUEST['product_combinataion_id_'.$val.'_'.$city_id] as $supkey =>$supval){
								$product_supplier_relation = new Productsupplierrelation();
								$product_supplier_relation->setMain_product_id($product_id);
								$product_supplier_relation->setMain_combination_id($supval);
								$product_supplier_relation->setSupplier_id($val);
								$product_supplier_relation->setDomain_id($domain_id);
								$product_supplier_relation->setCity_id($city_id1);
								$product_supplier_relation->setUnit_price($_REQUEST['product_price_'.$lang.'_'.$val.'_'.$supval.'_'.$city_id]);
								$product_supplier_relation->setQuantity($_REQUEST['produtct_quantity_'.$lang.'_'.$val.'_'.$supval.'_'.$city_id]);
								$product_supplier_relation->setCreated_by($user_id);
								$product_supplier_relation->setCreate_date(date('Y-m-d H:i:s'));
								$product_supplier_relation->setIs_deleted(0);
								$em = $this->getDoctrine()->getManager();
								$em->persist($product_supplier_relation);
								$em->flush();
							}
						}
					}
				}
			}
		}
		
		$this->get("session")->getFlashBag()->set("success_msg","Supplier saved");
		return $this->redirect($this->generateUrl("admin_product_addproduct",array("domain"=>$this->get('session')->get('domain'),"main_product_id"=>$product_id,'language_id'=>$lang)));
	}

	/**
     * @Route("/deleteproduct/{main_product_id}",defaults={"main_product_id"=""})
     * @Template()
     */
    public function deleteproductAction($main_product_id)
    {
		if(!empty($main_product_id) && isset($main_product_id)){

			$product_master_list = $this->getDoctrine()
										->getManager()
										->getRepository('AdminBundle:Productmaster')
										->findBy(array("is_deleted"=>0,"main_product_master_id"=>$main_product_id));

			if(!empty($product_master_list)){
				foreach($product_master_list as $val){
					$product_master_id = $val->getProduct_master_id();
					$product = $this->getDoctrine()
											->getManager()
											->getRepository('AdminBundle:Productmaster')
											->findOneBy(array("is_deleted"=>0,"product_master_id"=>$product_master_id));
					if(!empty($product)){
						$em = $this->getDoctrine()->getManager();
						$product->setIs_deleted(1);
						$em->flush();
					}
				}
			}

			$this->get("session")->getFlashBag()->set("success_msg","Product Deleted successfully");
			return $this->redirect($this->generateUrl("admin_product_index"));
		}
	}

/**
     * @Route("/updateproductattributeprice")
     * @Template()
     */
    public function updateproductattributepriceAction()
    {

		// Update Market Price
		if(isset($_REQUEST['language_id']) && !empty($_REQUEST['language_id']) && isset($_REQUEST['main_product_id']) && !empty($_REQUEST['main_product_id']) && isset($_REQUEST['combination_p_id']) && !empty($_REQUEST['combination_p_id']) && isset($_REQUEST['combination_market_price']) && !empty($_REQUEST['combination_market_price']) && isset($_REQUEST['update_comb_market_price']))
		{

			/*var_dump($_REQUEST['combination_p_id']);
			var_dump($_REQUEST['combination_market_price']);
			var_dump($_REQUEST['main_product_id']);
			exit;*/
			foreach($_REQUEST['combination_p_id'] as $key=>$val)
			{
				$em = $this->getDoctrine()->getManager();
				$combination_info = $em->getRepository('AdminBundle:Combination')
								->findOneBy(array("combination_id"=>$val,"is_deleted"=>0));

				if(!empty($combination_info))
				{
					foreach($_FILES['attribute_images_'.$val]['name'] as $ikey=>$ival)
					{
						//image upload : START
						$extension = pathinfo($ival,PATHINFO_EXTENSION);
						$media_type_id = $this->mediatype($extension);

						$logo = $ival;
						$tmpname = $_FILES['attribute_images_'.$val]['tmp_name'][$ikey];
						$file_path = $this->container->getParameter('file_path');
						$logo_path = $file_path.'/attributes';
						$logo_upload_dir = $this->container->getParameter('upload_dir').'/attributes/';

						$media_id = $this->mediauploadAction($logo,$tmpname,$logo_path,$logo_upload_dir,$media_type_id);

						if($media_id != FALSE)
						{
							$combination_image = new Combinationimage();
							$combination_image->setProduct_id($_REQUEST['main_product_id']);
							$combination_image->setCombination_id($val);
							$combination_image->setMedia_id($media_id);
							$combination_image->setIs_deleted(0);
							$em = $this->getDoctrine()->getManager();
							$em->persist($combination_image);
							$em->flush();
						}
						//image upload : END
					}

					$combination_info->setComb_market_price($_REQUEST['combination_market_price'][$key]);
					$em->persist($combination_info) ;
					$em->flush() ;
				}
			}
			$this->get("session")->getFlashBag()->set("success_msg","Attribute Market Price Update Successfully");
		}
		else
		{
			$this->get("session")->getFlashBag()->set("error_msg","Attribute Market Price Not Update Successfully");
		}

		return $this->redirect($this->generateUrl("admin_product_addproduct",array("domain"=>$this->get('session')->get('domain'),"main_product_id"=>$_REQUEST['main_product_id'],"language_id"=>$_REQUEST['language_id'])));
	}
	/**
     * @Route("/removecombination")
     */
    public function removecombinationAction()
    {
    	if(isset($_POST['flag']) && $_POST['flag'] == 'remove_attributr' && isset($_POST['product_id']) && $_POST['product_id'] != "" && isset($_POST['comb_id']) && $_POST['comb_id'] != "")
    	{
			$combination_info = $this->getDoctrine()
									->getManager()
									->getRepository('AdminBundle:Combination')
									->findOneBy(array("product_id"=>$_POST['product_id'],"combination_id"=>$_POST['comb_id'],"status"=>"active","is_deleted"=>0));

			if(!empty($combination_info))
			{
				$combination_info->setIs_deleted(1);
				$em = $this->getDoctrine()->getManager();
				$em->persist($combination_info);
				$em->flush();

				$em = $this->getDoctrine()->getManager();
				$connection = $em->getConnection();
				$statement = $connection->prepare("UPDATE combination_relation SET is_deleted = 1 WHERE combination_id = '".$_POST['comb_id']."' AND product_id = '".$_POST['product_id']."'");
				$statement->execute();

				$chk_combination = $this->getDoctrine()
									->getManager()
									->getRepository('AdminBundle:Combination')
									->findOneBy(array("product_id"=>$_POST['product_id'],"combination_id"=>$_POST['comb_id'],"status"=>"active","is_deleted"=>0));

				if(empty($chk_combination))
				{
					return new Response(json_encode(array("msg"=>"Attribute removed successfully.","cls"=>"text-success")));
				}
				else
				{
					return new Response(json_encode(array("msg"=>"Fail to remove attribute.","cls"=>"text-danger")));
				}
			}
		}
    }
	/**
     * @Route("/removecombinationimage/{combination_image_id}/{main_product_id}")
     */
    public function removecombinationimageAction($combination_image_id,$main_product_id)
    {
    	$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare("UPDATE combination_image SET is_deleted = 1 WHERE combination_image_id = '".$combination_image_id."'");
		$statement->execute();
		$this->get('session')->getFlashBag()->set('success_msg','Attribute image removed successfully');
		return $this->redirect($this->generateUrl('admin_product_addproduct',array("domain"=>$this->get('session')->get('domain'),"main_product_id"=>$main_product_id)));
    }

	/**
     * @Route("/product-import")
     * @Template()
     */
	public function productimportAction()
	{
		$language = null;
		return array("language"=>$language);
	}
	
	/**
	* @Route("/save-product-import")
	*/
	public function saveproductimportAction()
	{
		ini_set('max_execution_time', 0);
		ini_set('max_input_time ',"-1");
		ini_set("memory_limit","-1");
		error_reporting( E_ALL );
		ini_set('display_errors', 1);
		$vendor_exists_str = $msg_type = $msg = $vendor_insert_error_str = '';
		$operation = 'product_import' ;
		$allowed =  array('csv');
		$flie_ext_flag = false ;
		$res_str = '';

		$filename = $_FILES['product_csv']['name'];
		$filename_tmp = $_FILES['product_csv']['tmp_name'];
		$created_by = $this->get('session')->get('user_id');
		$created_datetime = date('Y-m-d H:i:s');
		$domain_id = $this->get('session')->get('domain_id');
		$ext = pathinfo($filename, PATHINFO_EXTENSION);

		if(!in_array($ext,$allowed) ) {
			$flie_ext_flag = false ;
			
		}
		else
		{
			$flie_ext_flag = true ;
			$check_vendor_counter = "";
			if($_FILES["product_csv"]["size"] > 0)
			{
				if($operation =='product_import')
				{
					$file = fopen($filename_tmp, "r");
					$res_str = '' ;
					$rec_cnt = 0;
					$flag = false;
					while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE)
					{
						$data = array_map("utf8_encode",$emapData);
						$num = count ($data);
						
						if(!empty($data))
						{
							$items = $data[0];
							$discription = $data[1];
							$price = $data[2];
							$category = $data[3];
							$sub_category1 = $data[4];
							$sub_category2 = $data[5];
							$unit = $data[6];
							if($rec_cnt == 0)
							{
								$flag = true;
								if(strtolower($items) != 'items')
								{
									$flag = false;
								}
								if(strtolower($discription) != 'discription')
								{
									$flag = false;
								}
								if(strtolower($price) != 'price')
								{
									$flag = false;
								}
								if(strtolower($category) != 'category')
								{
									$flag = false;
								}
								if(strtolower($sub_category1) != 'sub category')
								{
									$flag = false;
								}
								if(strtolower($sub_category2) != 'sub category')
								{
									$flag = false;
								}
							}
							if($flag == false)
							{
								//csv formate violate
							}
							if($rec_cnt >= 1 && $flag == true)
							{
								$res_str = 'success_msg';
								$vali1 = true;
								if(empty($items))
								{
									$vali1 = false;
									// error - Warning_Text_to_Display text
									$msg_type = "error_msg";
									$msg .= "Item name can not be empty, at line no : ".($rec_cnt+1)." . " ;
									$vendor_insert_error_str .= "Item name can not be empty, at line no : ".($rec_cnt+1).". <br>" ;
									$flag_log = $this->maintainLogs($operation,"Validation","","Item name can not be empty, at line no : ".($rec_cnt+1)." . ","",$filename);

									$vendor_exists_str .= $items.'  ';
								}
								if(empty($category))
								{
									$vali1 = false;
									// error - Warning_Text_to_Display text
									$msg_type = "error_msg";
									$msg .= "Item main category can not be empty, at line no : ".($rec_cnt+1)." . " ;
									$vendor_insert_error_str .= "Item main category can not be empty, at line no : ".($rec_cnt+1).". <br>" ;
									$flag_log = $this->maintainLogs($operation,"Validation","","Item main category can not be empty, at line no : ".($rec_cnt+1)." . ","",$filename);

									$vendor_exists_str .= $category.'  ';
								}
								if(empty($sub_category1))
								{
									$vali1 = false;
									// error - Warning_Text_to_Display text
									$msg_type = "error_msg";
									$msg .= "Item sub category can not be empty, at line no : ".($rec_cnt+1)." . " ;
									$vendor_insert_error_str .= "Item sub category can not be empty, at line no : ".($rec_cnt+1).". <br>" ;
									$flag_log = $this->maintainLogs($operation,"Validation","","Item sub category can not be empty, at line no : ".($rec_cnt+1)." . ","",$filename);

									$vendor_exists_str .= $sub_category1.'  ';
								}
								if($vali1 == 'true')
								{
									$main_id = 0;
									$pm = new Productmaster();
									$pm->setProduct_title($items);
									$pm->setRefrence_code('');
									$pm->setProduct_type_id(1);
									$pm->setBrand_id(0);
									$pm->setShort_description('');
									$pm->setDescription($discription);
									$pm->setProduct_logo(0);
									$pm->setStatus('active');
									$pm->setQuantity(0);
									$pm->setMax_allowed_qty_per_order(0);
									$pm->setMarket_price($price);
									$pm->setOriginal_price($price);
									$pm->setSku(0);
									$pm->setOn_sale('no');
									$pm->setAdditional_shipping_charge(0);
									$pm->setLanguage_id(1);
									$pm->setMain_product_master_id($main_id);
									$pm->setCreated_by($created_by);
									$pm->setCreated_date($created_datetime);
									$pm->setLast_updated($created_datetime);
									$pm->setDomain_id(1);
									$pm->setFavourite_count(0);
									$pm->setProduct_ws_add(0);
									$pm->setIs_deleted(0);
									$pm->setUnit_size($unit);
									$pm->setSort_order($main_id);
									$em = $this->getDoctrine()->getManager();
									$em->persist($pm);
									$em->flush();

									if($main_id == '0' || $main_id == 0)
									{
										$main_id = $pm->getProduct_master_id();
										$pm->setMain_product_master_id($main_id);
										$pm->setSort_order($main_id);
										$pm->setRefrence_code($main_id);
										$em->flush();
									}
									$cat_name = '';
									if(!empty($sub_category2))
									{
										$cat_name = $sub_category2;
									}
									else if(!empty($sub_category1))
									{
										$cat_name = $sub_category1;
									}
									
									$cat_id = $this->get_cat_id_from_name($category,$sub_category1,$sub_category2,1);
									if(!empty($cat_id) && $cat_id != '0')
									{
										$pcr = new Productcategoryrelation();
										$pcr->setProduct_id($main_id);
										$pcr->setLanguage_id(1);
										$pcr->setCategory_id($cat_id);
										$pcr->setCreated_by($created_by);
										$pcr->setCreate_date($created_datetime);
										$pcr->setDomain_id($domain_id);
										$pcr->setIs_deleted(0);
										$em = $this->getDoctrine()->getManager();
										$em->persist($pcr);
										$em->flush();
									}
								}
							}
						}
						$rec_cnt++;
					}
				}
				$flash_bag = 'success_msg';
				$res_str = "Products imported successfully";
			}
			else{
				$flash_bag = 'error_msg';
				$res_str = "Invalid file type";
			}
		}
		if($flie_ext_flag == true ){

		}
		else{
			$msg_type = "error_msg";
			$msg = "File Extenstion is not Valid ." ;
			$vendor_insert_error_str .= "File Extenstion is not Valid.<br>" ;
			$flag_log = $this->maintainLogs($operation,"File Import","","File Extenstion is not Valid","",$filename);

		}
		$this->get("session")->getFlashBag()->set($msg_type,$msg);
		if($res_str != ''){
			$flag_log = $this->maintainLogs($operation,"File Import",$res_str,"","",$filename);
			$this->get("session")->getFlashBag()->set($flash_bag, $res_str);
		}
		return $this->redirect($this->generateUrl('admin_product_productimport'));
	}
	
	public function get_cat_id_from_name($category,$subcat1,$subcat2,$lang_id)
	{
		$parent_for_next = 0;
		$em = $this->getDoctrine()->getManager();
		$connection = $em->getConnection();
		$need_to_check_parent = false;

		if(!empty($category))
		{
			$sql1 = "SELECT main_category_id from category_master WHERE trim(lower(category_name))='".trim(strtolower($category))."' AND language_id='$lang_id' AND is_deleted='0'";
			$statement = $connection->prepare($sql1);
			$statement->execute();
			$cat_list_chk = $statement->fetch();

			if(!empty($cat_list_chk))
			{
				$parent_for_next = $cat_list_chk['main_category_id'];
				if(!empty($subcat1))
				{
					$sql1 = "SELECT main_category_id from category_master WHERE trim(lower(category_name))='".trim(strtolower($subcat1))."' AND language_id='$lang_id' AND is_deleted='0' AND parent_category_id='$parent_for_next'";
					$statement = $connection->prepare($sql1);
					$statement->execute();
					$cat1_list_chk = $statement->fetch();
					if(!empty($cat1_list_chk))
					{
						$parent_for_next = $cat1_list_chk['main_category_id'];
						if(!empty($subcat2))
						{
							$sql1 = "SELECT main_category_id from category_master WHERE trim(lower(category_name))='".trim(strtolower($subcat2))."' AND language_id='$lang_id' AND is_deleted='0' AND parent_category_id='$parent_for_next'";
							$statement = $connection->prepare($sql1);
							$statement->execute();
							$cat2_list_chk = $statement->fetch();
							if(!empty($cat2_list_chk))
							{
								$parent_for_next = $cat2_list_chk['main_category_id'];
							}else{
								return 0;
							}
						}
					}else{
						return 0;
					}
				}
			}else{
				return 0;
			}
		}
		return $parent_for_next;
	}
}
