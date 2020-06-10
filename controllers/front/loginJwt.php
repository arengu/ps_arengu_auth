<?php

use PrestaShop\Module\Arengu\Auth\LoginJwtRestController;

class ps_arengu_authLoginJwtModuleFrontController extends LoginJwtRestController
{
    public function postProcess()
    {
      $token = Tools::getValue('token');
      $alg = Tools::getValue('alg');
      $redirect_url = Tools::getValue('redirect_url');

      $this->loginJwt($token, $alg);

      if(filter_var($redirect_url, FILTER_VALIDATE_URL)) {
        Tools::redirect($redirect_url);
      } else {
        Tools::redirect($this->context->link->getPageLink('my-account'));
      }
    }
}
