<?php

namespace Beeewithus\Auth;
 
use Zend\Authentication\Storage;
 
class AdminAuthStorage extends Storage\Session
{
    public function setRememberMe($rememberMe = 0)
    {
        if ($rememberMe == 1) {
            $time = 60 * 60 * 24 * 365;
            $this->session->getManager()->rememberMe($time);   
        }
    }
     
    public function forgetMe()
    {
        $this->session->getManager()->forgetMe();
    }
}