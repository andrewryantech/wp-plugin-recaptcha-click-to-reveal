<?php
/**
 * Author: andy@modernwebservices.com.au
 * Created: 30/09/17 2:58 PM
 */
declare(strict_types=1);

namespace ModernWebServices\Plugins\ClickToReveal;

use ModernWebServices\Plugins\ClickToReveal\Pages;

class Controller
{
    const VERSION        = '1.0.0';

    /** @var string */
    private $pluginFile;

    /** @var Settings */
    private $settings;

    /**
     * Invoked by Wordpress framework
     *
     * @param Settings $settings
     * @param string   $pluginFile
     */
    public function __construct(Settings $settings, string $pluginFile)
    {
        $this->settings   = $settings;
        $this->pluginFile = $pluginFile;
        $this->register_shortcodes();

        if(is_admin()) {
            $this->registerAdminPages();
            $this->modifyPluginsPageEntry();
            $this->registerHooks();
        } else {
            $this->registerPublicPages();
            add_action('init', [$this, 'check_protected_value_lookup']);
        }
    }


    /**
     * Checks if the current request is to lookup a protected value
     */
    public function check_protected_value_lookup(): void
    {
        if(isset($_GET['gRecaptchaResponse'], $_GET['mwsCtrName'])){
            $result = $this->executeProtectedValueRequest($_GET['gRecaptchaResponse'], $_GET['mwsCtrName']);
            die(json_encode($result));
        }
    }

    private function executeProtectedValueRequest($reCaptchaToken, $protectedValueName): array
    {
        $googleResponse = wp_remote_post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'body' => [
                    'secret'   => $this->settings->getSecretKey(),
                    'response' => $reCaptchaToken,
                    'remoteip' => $this->get_visitor_ip(),
                ]
            ]
        );

        if(is_wp_error($googleResponse)) {
            $result = [
                'success' => false,
            ];
            if(WP_DEBUG) {
                $result['message'] = $googleResponse->get_error_message();
            }
        } else {
            $result = [
                'success'     => true,
                'hiddenValue' => $this->settings->getProtectedValue($protectedValueName),
            ];
        }
        return $result;
    }


    private function get_visitor_ip()
    {
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return apply_filters('wpb_get_ip', $ip);
    }


    private function registerPublicPages(): void
    {
        new Pages\PublicPages();
    }


    private function registerAdminPages(): void
    {
        new Pages\Admin\ProtectedValuesPage($this->settings);
        new Pages\Admin\CredentialsPage($this->settings);
    }

    private function modifyPluginsPageEntry(): void
    {
        new Pages\Admin\PluginsPage($this->pluginFile);
    }

    /**
     * Register hooks required on both admin and front pages
     * @since  1.0.0
     */
    private function registerHooks(): void
    {
        register_activation_hook($this->pluginFile, [$this, 'activate']);
        register_deactivation_hook($this->pluginFile, [$this, 'deactivate']);
        register_uninstall_hook($this->pluginFile, [self::class, 'uninstall']);
    }


    private function register_shortcodes(): void
    {
        new ShortCodes\Reveal($this->settings);
    }



    /**
     * Called by WP when user activates the plugin
     *
     * @since 1.0.0
     */
    public function activate()
    {
//        $postType = new CustomPostType();
//        $postType->register();
//        flush_rewrite_rules();
    }
    /**
     * Called by WP when user deactivates the plugin
     */
    public function deactivate()
    {
//        flush_rewrite_rules();
    }

    /**
     * Called when user clicks 'Delete' on plugin page
     *
     * May delete keys and hidden values depending on current setting.
     *
     * @see https://developer.wordpress.org/plugins/the-basics/uninstall-methods/
     */
    public static function uninstall(): void
    {
        $settings = Settings::getInstance();
        if($settings->getDeleteOnUninstall()){
            $settings->deleteAll();
        }
    }
}
