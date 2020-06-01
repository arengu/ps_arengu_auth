<?php

namespace PrestaShop\Module\Arengu\Auth;

class PrivateKey
{
    private $name;

    public function __construct($name = 'ARENGU_PRIVATE_KEY')
    {
        $this->name = $name;
    }

    public function renew()
    {
        return $this->set($this->generate());
    }

    public function equals($value)
    {
        return hash_equals($this->get(), $value);
    }

    public function get()
    {
        return \Configuration::get($this->name);
    }

    public function set($value)
    {
        return \Configuration::updateValue($this->name, $value);
    }

    public function delete()
    {
        return \Configuration::deleteByName($this->name);
    }

    private function generate()
    {
        return rtrim(base64_encode(\Tools::getBytes(64)), '=');
    }
}
