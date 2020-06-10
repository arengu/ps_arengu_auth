<?php

namespace PrestaShop\Module\Arengu\Auth;
use \Firebase\JWT\JWT;

class LoginJwtRestController extends RestController
{
    protected function loginJwt($token, $alg)
    {        
        try {
            $secret = $this->module->privateKey->get();
            $decoded_token = JWT::decode($token, $secret, array($alg));
            $email = $decoded_token->sub;

            \Hook::exec('actionAuthenticationBefore');

            $customer = new \Customer();
            $authentication = $customer->getByEmail($email);

            if (!$authentication || !$customer->active){
                return false;
            }

            $this->context->updateCustomer($customer);

            \Hook::exec('actionAuthentication', ['customer' => $this->context->customer]);
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
