<?php

namespace PrestaShop\Module\Arengu\Auth;

class PasswordlessLoginFormatter extends \CustomerLoginFormatter
{
    public function getFormat()
    {
        $format = parent::getFormat();

        unset($format['password']);

        return $format;
    }
}
