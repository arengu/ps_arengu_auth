<?php

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use PrestaShop\Module\Arengu\Auth\LoginRestController;

class ps_arengu_authLogin_JwtModuleFrontController extends LoginRestController
{
    public function postProcess()
    {
        try {
            $decodedToken = (array) JWT::decode(
                $this->module->utils->getTrimmedString($_GET, 'token'),
                $this->module->jwtSecret->get(),
                [$this->module->JWT_ALG]
            );
        } catch (ExpiredException $ex) {
            return $this->error(
                $this->l('Sorry, the provided token is expired.')
            );
        } catch (\Exception $ex) {
            return $this->error(
                $this->l('Sorry, the provided token is not valid.')
            );
        }

        $issuer = $this->module->utils->getTrimmedString(
            $decodedToken,
            'iss'
        );

        $email = $this->module->utils->getTrimmedString(
            $decodedToken,
            'email'
        );

        $customerId = $this->module->utils->getTrimmedString(
            $decodedToken,
            'sub'
        );

        $redirectUri = $this->module->utils->getTrimmedString(
            $decodedToken,
            'redirect_uri'
        );

        if ($issuer !== $_SERVER['SERVER_NAME'] || !$email || !$customerId) {
            $this->error($this->l('Sorry, the provided token is not valid.'));
        }

        $customer = $this->login($email, null, [], null);

        // check for freaky coincidence where 2 users somehow managed to
        // exchange email addresses between them after the token was generated
        if ((string) $customer->id !== $customerId) {
            return $this->error($this->l('There was a problem validating your session, please try again.'));
        }

        Tools::redirect($redirectUri ? $redirectUri : $this->context->link->getPageLink('my-account'));
    }

    protected function checkPrivateKey()
    {
        // no key needed, request will be authenticated using the JWT
    }

    protected function error($message, $status = 400)
    {
        $this->setTemplate(
            'module:ps_arengu_auth/views/templates/front/error.tpl'
        );

        $this->context->smarty->assign([
            'message' => $message,
            'home_url' => Tools::getShopDomainSsl(true),
        ]);

        return true;
    }
}
