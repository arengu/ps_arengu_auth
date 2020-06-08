<?php

use PrestaShop\Module\Arengu\Auth\RestController;

class ps_arengu_authSignupModuleFrontController extends RestController
{
    public function postProcess()
    {
        $fields = $groups = [];

        $body = $this->parseBody();

        // first extract group settings
        if (isset($body['add_groups']) && is_array($body['add_groups'])) {
            $groups = $body['add_groups'];
            unset($body['add_groups']);
        }

        $defaultGroup = $this->module->utils->getTrimmedString($body, 'default_group');
        unset($body['default_group']);

        // then the rest of the string fields
        foreach (array_keys($body) as $fieldName) {
            $fieldValue = $this->module->utils->getTrimmedString($body, $fieldName);

            if ($fieldValue !== '') {
                $fields[$fieldName] = $fieldValue;
            }
        }

        // keep cart products
        if (isset($body['cart_id'])) {
            $cart_id = $body['cart_id'];
            $this->context->cart = new Cart((int) $cart_id);
        }

        // use a random password when it's absent, for paswordless signup
        if (empty($fields['password'])) {
            $fields['password'] = bin2hex(\Tools::getBytes(32));
        }

        $this->signup($fields, $groups, $defaultGroup);

        $this->jsonRender([
            'user' => $this->module->utils->presentUser($this->context->customer),
        ]);
    }

    private function signup(array $fields, array $groups = [], $defaultGroup = null)
    {
        $form = $this
            ->makeCustomerForm()
            ->fillWith($fields);

        if (!$form->submit()) {
            $this->error($this->module->utils->getFormattedErrors($form));
        }

        $customer = $this->context->customer;

        if ($defaultGroup) {
            $customer->id_default_group = (int) $defaultGroup;
            $customer->update();
        }

        if (count($groups)) {
            $currentGroups = $customer->getGroups();
            $customer->addGroups(array_diff($groups, $currentGroups));
        }

        // log the user in
        $this->context->updateCustomer($customer);
    }
}
