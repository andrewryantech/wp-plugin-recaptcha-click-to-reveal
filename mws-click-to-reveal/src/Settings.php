<?php
/**
 * Author: andy@modernwebservices.com.au
 * Created: 18/10/17 11:33 PM
 */
declare(strict_types=1);

namespace ModernWebServices\Plugins\ClickToReveal;

/**
 * Singleton Plugin Settings.
 *
 * Get/Set using public convenience methods. Set saves immediately.
 */
class Settings
{

    private const SETTINGS_KEY            = 'mws_click_to_reveal_settings';

    private const KEY_SITE_KEY            = 'site_key';
    private const KEY_SECRET_KEY          = 'secret_key';
    private const KEY_PROTECTED_VALUES    = 'hidden_values';
    private const KEY_DELETE_ON_UNINSTALL = 'delete_on_uninstall';

    private static $instance;

    private $config;


    public static function getInstance()
    {
        if(!self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }


    private function __construct()
    {
        $this->config = get_option(self::SETTINGS_KEY, []);
    }


    public function getSiteKey(): string
    {
        return $this->get(self::KEY_SITE_KEY, '');
    }


    public function setSiteKey(string $siteKey): bool
    {
        return $this->set(self::KEY_SITE_KEY, $siteKey);
    }


    public function getSecretKey(): string
    {
        return $this->get(self::KEY_SECRET_KEY, '');
    }


    public function setSecretKey(string $secretKey): bool
    {
        return $this->set(self::KEY_SECRET_KEY, $secretKey);
    }


    public function getProtectedValues(array $defaults = []): array
    {
        return $this->get(self::KEY_PROTECTED_VALUES, $defaults);
    }


    public function getProtectedValue(string $name): string
    {
        $protectedValues = $this->getProtectedValues();
        if(array_key_exists($name, $protectedValues)) {
            return $protectedValues[$name];
        }
        throw new \InvalidArgumentException("No hidden value found for $name");
    }


    public function setProtectedValues(array $protectedValues): bool
    {
        return $this->set(self::KEY_PROTECTED_VALUES, $protectedValues);
    }


    public function getDeleteOnUninstall(): bool
    {
        return $this->get(self::KEY_DELETE_ON_UNINSTALL, false);
    }


    public function setDeleteOnUninstall(bool $deleteOnUninstall): bool
    {
        return $this->set(self::KEY_DELETE_ON_UNINSTALL, $deleteOnUninstall);
    }


    /**
     * @param string $key
     * @param null   $default
     * @return mixed
     */
    private function get(string $key, $default = null)
    {
        if(array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }

        return $default;
    }


    /**
     * @param string $key
     * @param mixed  $value
     * @return bool
     */
    private function set(string $key, $value): bool
    {
        $this->config[$key] = $value;

        return update_option(self::SETTINGS_KEY, $this->config);
    }
}
