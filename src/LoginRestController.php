<?php

namespace PrestaShop\Module\Arengu\Auth;

class LoginRestController extends RestController
{
    protected function login($email, $password = null, array $groups = [], $defaultGroup = null)
    {
        $translator = $this->getTranslator();

        $loginFormatter =
            $password === null ?
            new PasswordlessLoginFormatter($translator) :
            new \CustomerLoginFormatter($translator);

        $form = new \CustomerLoginForm(
            $this->context->smarty,
            $this->context,
            $this->getTranslator(),
            $loginFormatter,
            $this->getTemplateVarUrls()
        );

        $form->fillWith([
            'email' => $email,
            'password' => $password,
        ]);

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

        return $customer;
    }

    protected function getCustomerFromBody($body, $allowPasswordless)
    {
        $email = $this->module->utils->getTrimmedString($body, 'email');
        $password = $allowPasswordless ? null : $this->module->utils->getTrimmedString($body, 'password');

        $groupsParams = $this->getGroupsParams($body);

        $customer = $this->login(
            $email,
            $password,
            $groupsParams['groups'],
            $groupsParams['defaultGroup']
        );

        return $customer;
    }

    protected function processLogin($allowPasswordless)
    {
        $body = $this->parseBody();

        $customer = $this->getCustomerFromBody($body, $allowPasswordless);

        $params = $this->getTokenParams($body);
        $token = $this->buildToken(
            $customer,
            $params['expiresIn'],
            $params['redirectUri']
        );

        $this->jsonRender(
            $this->buildOutput($customer, $token)
        );
    }
}
