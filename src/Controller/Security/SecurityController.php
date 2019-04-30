<?php

namespace App\Controller\Security;

use Symfony\Component\HttpFoundation\Request;

class SecurityController extends \FOS\UserBundle\Controller\SecurityController
{
    public function loginAction(Request $request)
    {
        $authChecker = $this->container->get('security.authorization_checker');
        if ($authChecker->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('index');
        }

        return parent::loginAction($request);
    }
}
