<?php

namespace PrestaShop\Module\Arengu\Auth;

class PrivateKey
{
    private $name;

    public function __construct($name)
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
        $key = \Configuration::get($this->name);

        // if the key somehow got deleted from the config it's preferable
        // to break everything immediately instead of allowing empty keys
        if ($key === false) {
            die(\Tools::displayError(
                "The PrivateKey '{$this->name}' cannot be found. " .
                'Please uninstall and then install the module again.'
            ));
        }

        return $key;
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
