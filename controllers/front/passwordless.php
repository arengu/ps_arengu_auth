<?php

use PrestaShop\Module\Arengu\Auth\LoginRestController;

class ps_arengu_authPasswordlessModuleFrontController extends LoginRestController
{
    public function postProcess()
    {
        $body = $this->parseBody();

        $email = $this->module->utils->getTrimmedString($body, 'email');
        $cart_id = $this->module->utils->getTrimmedString($body, 'cart_id');

        // keep cart products
        if ($cart_id) {
            $this->context->cart = new Cart((int) $cart_id);
        }

        $groups = [];
        if (isset($body['add_groups']) && is_array($body['add_groups'])) {
            $groups = $body['add_groups'];
        }

        $defaultGroup = $this->module->utils->getTrimmedString($body, 'default_group');

        $customer = $this->login($email, null, $groups, $defaultGroup);

        $this->jsonRender([
            'user' => $this->module->utils->presentUser($customer),
        ]);
    }
}
