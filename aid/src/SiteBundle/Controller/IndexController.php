<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class IndexController extends Controller
{
    /**
     * @Route("/index")
     */
    public function indexAction()
    {
		exit('here');
        return $this->render('SiteBundle:Default:index.html.twig');
    }
}
