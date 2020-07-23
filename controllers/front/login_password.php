<?php

use PrestaShop\Module\Arengu\Auth\LoginRestController;

class ps_arengu_authLogin_PasswordModuleFrontController extends LoginRestController
{
    public function postProcess()
    {
        $this->processLogin(false);
    }
}
