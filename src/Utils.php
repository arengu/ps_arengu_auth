<?php

namespace PrestaShop\Module\Arengu\Auth;

class Utils
{
    public function getAuthorizationHeader()
    {
        // https://bugs.php.net/bug.php?id=72915
        // https://github.com/symfony/symfony/issues/19693

        $header = null;

        if (isset($_SERVER['Authorization'])) {
            $header = $_SERVER['Authorization'];
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $header = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (function_exists('apache_request_headers')) {
            $apacheHeaders = apache_request_headers();

            if (isset($apacheHeaders['Authorization'])) {
                $header = $apacheHeaders['Authorization'];
            }
        }

        return $header;
    }

    public function getFormattedErrors(\FormInterface $form)
    {
        $output = [];

        foreach ($form->getErrors() as $field => $errors) {
            // make generic errors a bit more accessible
            if ($field === '') {
                $field = '_request';
            }

            foreach ($errors as $error) {
                $output[$field] =
                    (isset($output[$field]) ? ' ' : '') .
                    rtrim($error, '.');
            }
        }

        return $output;
    }

    public function presentUser(\Customer $user)
    {
        $groups = array_map(
            function ($group) { return (int) $group['id']; },
            $user->getWsGroups()
        );

        return [
            'id' => (int) $user->id,
            'email' => $user->email,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'birthday' => $user->birthday,
            'id_gender' => $user->id_gender,
            'company' => $user->company,
            'newsletter' => $user->newsletter,
            'optin' => $user->optin,
            'default_group' => (int) $user->id_default_group,
            'groups' => $groups,
        ];
    }

    public function getTrimmedString($arr, $key)
    {
        return
            !empty($arr[$key]) && (
                is_string($arr[$key]) ||
                is_int($arr[$key]) ||
                is_float($arr[$key]) ||
                is_bool($arr[$key])
            ) ?
            (string) trim($arr[$key]) :
            '';
    }

    public function parseBody()
    {
        $body = @file_get_contents('php://input');

        if ($body === false) {
            throw new \Exception('Failed to read POST body');
        }

        $parsed = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to parse JSON data');
        }

        return $parsed;
    }
}
