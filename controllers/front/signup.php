<?php

use PrestaShop\Module\Arengu\Auth\RestController;

class ps_arengu_authSignupModuleFrontController extends RestController
{
    public function postProcess()
    {
        $body = $this->parseBody();

        $groupsParams = $this->getGroupsParams($body);
        $tokenParams = $this->getTokenParams($body);

        // allow for potential custom signup fields
        $fields = [];
        foreach (array_keys($body) as $fieldName) {
            $fieldValue = $this->module->utils->getTrimmedString(
                $body,
                $fieldName
            );

            if ($fieldValue !== '') {
                $fields[$fieldName] = $fieldValue;
            }
        }

        // use a random password when it's absent, for passwordless signup
        if (empty($fields['password'])) {
            $fields['password'] = bin2hex(\Tools::getBytes(32));
        }

        $customer = $this->signup(
            $fields,
            $groupsParams['groups'],
            $groupsParams['defaultGroup']
        );

        $token = $this->buildToken($customer, $tokenParams['expiresIn'], $tokenParams['redirectUri']);

        $this->jsonRender($this->buildOutput($customer, $token));
    }

    private function signup(
        array $fields,
        array $groups = [],
        $defaultGroup = null
    )
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

        return $customer;
    }
}
