<?php

use PrestaShop\Module\Arengu\Auth\LoginRestController;

class ps_arengu_authLoginModuleFrontController extends LoginRestController
{
    public function postProcess()
    {
        $body = $this->parseBody();

        $email = $this->module->utils->getTrimmedString($body, 'email');
        $password = $this->module->utils->getTrimmedString($body, 'password');

        $groups = [];
        if (isset($body['add_groups']) && is_array($body['add_groups'])) {
            $groups = $body['add_groups'];
        }

        $defaultGroup = $this->module->utils->getTrimmedString($body, 'default_group');

        $this->login($email, $password, $groups, $defaultGroup);

        $this->jsonRender([
            'user' => $this->module->utils->presentUser($this->context->customer),
        ]);
    }
}
