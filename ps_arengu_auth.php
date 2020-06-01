<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use PrestaShop\Module\Arengu\Auth\PrivateKey;
use PrestaShop\Module\Arengu\Auth\Utils;

class ps_arengu_auth extends Module
{
    public $privateKey;

    public function __construct()
    {
        $this->name = 'ps_arengu_auth';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Arengu';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.5.1'];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Arengu Auth');
        $this->description = $this->l('Enable custom signup, login and passwordless endpoints to interact with your store authentication system from Arengu.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->privateKey = new PrivateKey();
        $this->utils = new Utils();
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        if (!$this->privateKey->renew()) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        $this->privateKey->delete();

        return true;
    }

    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('renew_key')) {
            $this->privateKey->renew();

            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminModules') .
                "&configure={$this->name}"
            );
        }

        return $output . $this->renderForm();
    }

    private function renderForm()
    {
        $output = '';

        $fields[]['form'] = [
            'legend' => [
                'title' => $this->l('Authentication endpoints'),
            ],

            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Signup:'),
                    'name' => 'signup',
                    'readonly' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Normal login:'),
                    'name' => 'normal_login',
                    'readonly' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Passwordless login:'),
                    'name' => 'passwordless_login',
                    'readonly' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Email check:'),
                    'name' => 'email_check',
                    'readonly' => true,
                ],
            ],
        ];

        $fields[]['form'] = [
            'legend' => [
                'title' => $this->l('Renew private key'),
            ],
            'error' => $this->l('This is the key that allows you to use the authentication endpoints.') .
                ' <b>' . $this->l('It allows to impersonate any customer in your store, so you must keep it secret.') . '</b>',
            'warning' => $this->l('Renewing your key will immediately invalidate the previous one, preventing its use.') .
                ' <b>' . $this->l('Make sure you know what you are doing.') . '</b>',
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Current private key:'),
                    'name' => 'current_private_key',
                    'readonly' => true,
                ],
            ],
            'submit' => [
                'title' => $this->l('Renew'),
                'class' => 'btn btn-danger pull-right',
                'icon' => 'icon-warning-sign',
                'name' => 'renew_key',
            ],
        ];

        $helper = new HelperForm();

        $helper->fields_value = [
            'current_private_key' => "Bearer {$this->privateKey->get()}",
            'signup' => $this->context->link->getModuleLink($this->name, 'signup'),
            'normal_login' => $this->context->link->getModuleLink($this->name, 'login'),
            'passwordless_login' => $this->context->link->getModuleLink($this->name, 'passwordless'),
            'email_check' => $this->context->link->getModuleLink($this->name, 'checkemail'),
        ];

        $output .= $helper->generateForm($fields);

        $output .=
            '<script>
                $(function() {
                    $("button[name=renew_key]").click(function(e) {
                        confirm("' .
                            $this->l('Are you sure you want to renew your private key? This is not reversible and will invalidate previous keys.') .
                        '") || e.preventDefault();
                    });
                });
            </script>';

        return $output;
    }
}
