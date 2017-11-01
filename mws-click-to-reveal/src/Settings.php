<?php
/**
 * Author: https://github.com/andrewryantech
 * Created: 18/10/17 11:33 PM
 */
declare(strict_types=1);

namespace ModernWebServices\Plugins\ClickToReveal;

/**
 * Singleton Plugin Settings.
 *
 * Get/Set using public convenience methods. 'Set' saves immediately.
 */
class Settings
{

    const SETTINGS_KEY = 'mws_click_to_reveal_settings';

    const KEY_SITE_KEY            = 'site_key';
    const KEY_SECRET_KEY          = 'secret_key';
    const KEY_PROTECTED_VALUES    = 'hidden_values';
    const KEY_DELETE_ON_UNINSTALL = 'delete_on_uninstall';
    const KEY_IS_INSTALLED        = 'is_installed';

    /**
     * @var static
     */
    private static $instance;

    /**
     * @var mixed[]
     */
    private $config;


    /**
     * @return self
     */
    public static function getInstance(): self
    {
        if(!self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }


    /**
     * Settings constructor.
     */
    private function __construct()
    {
        $this->config = get_option(self::SETTINGS_KEY, []);
    }


    /**
     * Delete all settings from database
     *
     * @return bool
     */
    public function deleteAll(): bool
    {
        return delete_option(self::SETTINGS_KEY);
    }


    /**
     * @return string
     */
    public function getSiteKey(): string
    {
        return $this->get(self::KEY_SITE_KEY, '');
    }


    /**
     * @param string $siteKey
     * @return bool
     */
    public function setSiteKey(string $siteKey): bool
    {
        return $this->set(self::KEY_SITE_KEY, $siteKey);
    }


    /**
     * @return string
     */
    public function getSecretKey(): string
    {
        return $this->get(self::KEY_SECRET_KEY, '');
    }


    /**
     * @param string $secretKey
     * @return bool
     */
    public function setSecretKey(string $secretKey): bool
    {
        return $this->set(self::KEY_SECRET_KEY, $secretKey);
    }


    /**
     * @param string[] $defaults
     * @return string[]
     */
    public function getProtectedValues(array $defaults = []): array
    {
        return $this->get(self::KEY_PROTECTED_VALUES, $defaults);
    }


    /**
     * @param string[] $protectedValues
     * @return bool
     */
    public function setProtectedValues(array $protectedValues): bool
    {
        return $this->set(self::KEY_PROTECTED_VALUES, $protectedValues);
    }


    /**
     * Does the specified value exist?
     *
     * @param string $name
     * @return bool
     */
    public function hasProtectedValue(string $name): bool
    {
        return array_key_exists($name, $this->getProtectedValues());
    }


    /**
     * @param string $name
     * @return string
     */
    public function getProtectedValue(string $name): string
    {
        $protectedValues = $this->getProtectedValues();
        if(array_key_exists($name, $protectedValues)) {
            return $protectedValues[$name];
        }
        if(WP_DEBUG) {
            trigger_error(sprintf("No hidden value found with name: '%s'", $name));
        }

        return '';
    }


    /**
     * @return bool
     */
    public function getDeleteOnUninstall(): bool
    {
        return $this->get(self::KEY_DELETE_ON_UNINSTALL, false);
    }


    /**
     * @param bool $deleteOnUninstall
     * @return bool
     */
    public function setDeleteOnUninstall(bool $deleteOnUninstall): bool
    {
        return $this->set(self::KEY_DELETE_ON_UNINSTALL, $deleteOnUninstall);
    }


    /**
     * Is the plugin installed?
     *
     * @return bool
     */
    public function getIsInstalled(): bool
    {
        return $this->get(self::KEY_IS_INSTALLED, false);
    }


    /**
     * @param bool $isInstalled
     * @return bool
     */
    public function setIsInstalled(bool $isInstalled): bool
    {
        return $this->set(self::KEY_IS_INSTALLED, $isInstalled);
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
