<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class EntityController extends Controller
{
    /**
     * @Route("/entity")
	 * @Template
     */
    public function indexAction()
    {
		return array();
    }
	/**
     * @Route("/entity/createentity")
	 * @Template
     */
    public function createentityAction()
    {
		$table = $_REQUEST['table'];
		$namespace = $_REQUEST['namespace'];
		
		$em = $this->getDoctrine()->getEntityManager();
		$connection = $em->getConnection();
		$statement = $connection->prepare("DESCRIBE $table");
		$statement->execute();
		$result = $statement->fetchAll();
		//var_dump($result);exit;

		$input = preg_replace("/[^a-zA-Z]+/", "", $table);
		$entity_name = ucfirst($input); 
		$root = $this->get('kernel')->getRootDir()."\../src/AdminBundle/Entity/";
		$myfile = fopen($root.$entity_name.".php", "w");

		$txt = "<?php \n";
		
		$txt .= "namespace ";
		$txt .= $namespace.";\n";
		
		$txt .= "use Doctrine\ORM\Mapping as ORM;\n";
		$txt .= "/**\n* @ORM\Entity\n* @ORM\Table(name='".$table."')\n*/\n";
		
		//class start
		$txt .= "class ".$entity_name."\n{\n\t";
		
		foreach($result as $key=>$val)
		{
			if($val['Key'] == "PRI")
			{
				$txt .= "/**\n\t* @ORM\Column(type='integer')\n\t* @ORM\Id\n\t* @ORM\GeneratedValue(strategy='AUTO')\n\t*/\n\t";
				$txt .= "protected $".$val['Field'].";\n\n\t";	
			}
			else
			{
				if($val['Type'] == "int(11)" || $val['Type'] == "tinyint(4)")
				{
					$txt .= "/**\n\t* @ORM\Column(type='integer')\n\t*/\n\t";
					$txt .= "protected $".$val['Field']."=0;\n\n\t";
				}
				else
				{
					$txt .= "/**\n\t* @ORM\Column(type='string')\n\t*/\n\t";
					$txt .= "protected $".$val['Field']."='';\n\n\t";
				}
				//$txt .= "protected $".$val['Field'].";\n\n\t";
			}
		}
		foreach($result as $key=>$val)
		{
			if($val['Key'] == "PRI")
			{
				$field_name = ucfirst($val['Field']);
				$txt .= "public function get".$field_name."()\n\t{\n\t\treturn $";
				$txt .= "this";
				$txt .= "->".$val['Field'].";\n\t}\n\n\t";
				
			}
			else
			{
				$field_name = ucfirst($val['Field']);
				$txt .= "public function get".$field_name."()\n\t{\n\t\treturn $";
				$txt .= "this";
				$txt .= "->".$val['Field'].";\n\t}\n\t";
				
				
				$txt .= "public function set".$field_name."($".$val['Field'].")\n\t{\n\t\t$";
				$txt .= "this";
				$txt .= "->".$val['Field']." = $".$val['Field'].";\n\t}\n";
				if($val != end($result))
				{
					$txt .= "\n\t";
				}
			}
		}
		
		//class end
		$txt .= "}";
		$entity_text = str_replace("'",'"',$txt);
		fwrite($myfile, $entity_text);
		fclose($myfile);
		
		$this->get('session')->getFlashBag()->set('success', $table.' Entity Created successfully');
		return $this->redirect($this->generateUrl("admin_entity_index"));
    }
}
