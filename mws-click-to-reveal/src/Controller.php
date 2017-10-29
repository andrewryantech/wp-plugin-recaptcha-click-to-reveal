<?php
/**
 * Author: https://github.com/andrewryantech
 * Created: 30/09/17 2:58 PM
 */
declare(strict_types=1);

namespace ModernWebServices\Plugins\ClickToReveal;

use ModernWebServices\Plugins\ClickToReveal\Pages;

class Controller
{
    const VERSION              = '1.0.0';
    const PLUGIN_NAME          = 'click_to_reveal';
    const FONT_AWESOME_URL     = 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css';
    const STYLE_FONT_AWESOME   = 'font-awesome';
    const INITIAL_EXAMPLE_DATA = [
        'example_private_email'   => 'john@private.com',
        'example_private_address' => '1 Cherry Tree Lane, New York',
        'example_private_phone'   => '415 555 2671',
    ];

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
            add_action('init', [$this, 'addTinyMceButtonsToEditor']);
        } else {
            $this->registerPublicPages();
            add_action('init', [$this, 'check_protected_value_lookup']);
        }
    }


    public function addTinyMceButtonsToEditor(): void
    {
        if ( isset($_GET['action'])  && 'edit' === $_GET['action']){
            // Setup editor with extra button
            add_filter( 'mce_buttons', [$this, 'register_tiny_mce_buttons'] );
            add_filter( 'mce_external_plugins', [$this, 'register_tiny_mce_javascript'] );
            wp_enqueue_style(self::STYLE_FONT_AWESOME, self::FONT_AWESOME_URL, [], self::VERSION, 'screen');

            // Dump the names of all available buttons into page
            add_action('admin_head', [$this, 'list_protected_value_names']);
        }
    }

    public function list_protected_value_names(): void
    {
        $results = [];
        foreach(array_keys($this->settings->getProtectedValues()) as $name){
            $object = new \stdClass();
            $object->text = $name;
            $object->value = $name;
            $results[] = $object;
        }



        /** @noinspection UnknownInspectionInspection */
        /** @noinspection JSUnusedLocalSymbols */
        $script = "<script>var mws_ctr_protected_value_names = [];</script>\n";
        echo str_replace('[]', json_encode($results), $script);
    }

    /**
     * Add Short-code Generator button to Tiny MCE
     *
     * @param array $buttons
     * @return array
     */
    public function register_tiny_mce_buttons( array $buttons ): array
    {
        $buttons[] = self::PLUGIN_NAME;
        return $buttons;
    }

    /**
     * Add Tiny MCE JS plugin
     *
     * @since 1.0.0
     * @param array $plugin_array
     * @return array
     */
    public function register_tiny_mce_javascript( array $plugin_array ): array
    {
        $plugin_array[self::PLUGIN_NAME] = plugins_url( '/src/js/admin/tinymce-plugin.js', __DIR__ );
        return $plugin_array;
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


    /**
     * Registers plugin shortcodes
     */
    private function register_shortcodes(): void
    {
        new ShortCode($this->settings);
    }


    /**
     * Called by WP when user activates the plugin
     *
     * If this is the first time the plugin has been installed, insert example protected values.
     */
    public function activate():void
    {
        if(!$this->settings->getIsInstalled()) {
            $this->settings->setIsInstalled(true);
            $this->settings->setProtectedValues(self::INITIAL_EXAMPLE_DATA);
        }
    }


    /**
     * Called by WP when user deactivates the plugin
     */
    public function deactivate(): void
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
