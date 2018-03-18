<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/site-api/sampledata")
	 * @Template()
     */
    public function indexAction(Request $request)
    {
		$data[] = array('name' => 'Test', 'price' => 100);
		$data[] = array('name' => 'Test2', 'price' => 200);
		
		echo json_encode($data);exit;
    }
}
