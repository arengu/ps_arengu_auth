<?php

use PrestaShop\Module\Arengu\Auth\RestController;

class ps_arengu_authCheck_EmailModuleFrontController extends RestController
{
    public function postProcess()
    {
        $body = $this->parseBody();
        $email = $this->module->utils->getTrimmedString($body, 'email');

        $this->jsonRender([
            'email_exists' => $this->emailExists($email),
        ]);
    }

    private function emailExists($email)
    {
        if (!$email || !\Validate::isEmail($email)) {
            $this->error('You must provide a valid email', 401);
        }

        $customer = (new Customer())->getByEmail($email, null, true);

        return
            !empty($customer) &&
            !empty($customer->id) &&
             empty($customer->is_guest);
    }
}
