<?php

use PrestaShop\Module\Arengu\Auth\LoginRestController;

class ps_arengu_authPasswordless_LoginModuleFrontController extends LoginRestController
{
    public function postProcess()
    {
        $this->processLogin(true);
    }
}
