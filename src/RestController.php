<?php

namespace PrestaShop\Module\Arengu\Auth;

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
        ob_end_clean();

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

    protected function checkPrivateKey()
    {
        $receivedHeader = $this->module->utils->getAuthorizationHeader();

        if (!$receivedHeader) {
            $this->error('Authorization header is missing', 403);
        }

        // len('Bearer ') = 7
        $receivedPrefix = substr($receivedHeader, 0, 7);

        if ($receivedPrefix !== 'Bearer ') {
            $this->error('Invalid auth type', 403);
        }

        $receivedKey = substr($receivedHeader, 7);

        if (!$this->module->privateKey->equals($receivedKey)) {
            $this->error('Invalid key', 403);
        }
    }
}
