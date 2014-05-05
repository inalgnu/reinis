<?php

namespace SensioLabs\JobBoardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use SensioLabs\Connect\Security\Authentication\Token\ConnectToken;

class SlnController extends Controller
{
    /**
     * @Route("/sln_customizer.js", name="sln_customizer")
     * @Template("SensioLabsJobBoardBundle:Connect:customizer.js.twig")
     */
    public function customizationAction()
    {
        $token = $this->get('security.context')->getToken();
        $user = $token instanceof ConnectToken ? $token->getApiUser() : null;

        return array('user' => $user);
    }
}
