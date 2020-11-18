<?php

namespace PrestaShop\Module\Arengu\Auth;

use Firebase\JWT\JWT;

class RestController extends \ModuleFrontController
{
    public function __construct()
    {
        $this->ajax = true;

        parent::__construct();
    }

    public function init()
    {
        $this->checkPrivateKey();

        parent::init();
    }

    protected function parseBody()
    {
        try {
            return $this->module->utils->parseBody();
        } catch (\Exception $ex) {
            $this->error($ex->getMessage(), 400);
        }
    }

    protected function jsonRender($value, $status = 200)
    {
        @ob_end_clean();

        http_response_code($status);
        header('Content-Type: application/json');

        $this->ajaxRender(json_encode($value));
        exit;
    }

    protected function error($messages, $status = 400)
    {
        if (!is_array($messages)) {
            $messages = ['_request' => $messages];
        }

        $this->jsonRender(['errors' => $messages], $status);
    }

    private function badPrivateKey($msg)
    {
        header('WWW-Authenticate: Bearer', true);
        $this->error($msg, 401);
    }

    protected function checkPrivateKey()
    {
        $receivedHeader = $this->module->utils->getAuthorizationHeader();

        if (!$receivedHeader) {
            $this->badPrivateKey('Authorization header is missing');
        }

        // len('Bearer ') = 7
        $receivedPrefix = substr($receivedHeader, 0, 7);

        if ($receivedPrefix !== 'Bearer ') {
            $this->badPrivateKey('Invalid auth type');
        }

        $receivedKey = substr($receivedHeader, 7);

        if (!$this->module->apiKey->equals($receivedKey)) {
            $this->badPrivateKey('Invalid key');
        }
    }

    protected function buildToken($customer, $expiresIn, $redirectUri)
    {
        $alg = $this->module->JWT_ALG;
        $secret = $this->module->jwtSecret->get();

        $payload = [
            'iss' => $_SERVER['SERVER_NAME'],
            'exp' => $_SERVER['REQUEST_TIME'] + $expiresIn,
            'email' => $customer->email,
            'sub' => (string) $customer->id,
        ];

        if ($redirectUri) {
            $payload['redirect_uri'] = $redirectUri;
        }

        return JWT::encode($payload, $secret, $alg);
    }

    protected function buildOutput($customer, $token)
    {
        return [
            'user' => $this->module->utils->presentUser($customer),
            'token' => $token,
            'login_url' => $this->context->link->getModuleLink(
                $this->module->name,
                'login_jwt',
                ['token' => $token]
            ),
        ];
    }

    protected function getGroupsParams($body)
    {
        $params = [];

        $params['defaultGroup'] = $this->module->utils->getTrimmedString(
            $body,
            'default_group'
        );

        $params['groups'] = [];
        if (isset($body['add_groups']) && is_array($body['add_groups'])) {
            $params['groups'] = $body['add_groups'];
        }

        return $params;
    }

    protected function getTokenParams($body)
    {
        $params = [
            'expiresIn' => (int) $this->module->utils->getTrimmedString(
                $body,
                'expires_in'
            ),

            'redirectUri' => $this->module->utils->getTrimmedString(
                $body,
                'redirect_uri'
            ),
        ];

        if (!$params['expiresIn']) {
            $params['expiresIn'] = $this->module->JWT_EXPIRY;
        }

        return $params;
    }
}
