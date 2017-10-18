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
     * @param Settings $settings
     * @param string  $pluginFile
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
        }
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
    private function registerHooks()
    {
        register_activation_hook($this->pluginFile, [$this, 'activate']);
        register_deactivation_hook($this->pluginFile, [$this, 'deactivate']);
        register_uninstall_hook($this->pluginFile, [self::class, 'uninstall']);
    }


    private function register_shortcodes()
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
     *
     * @since 1.0.0
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
     * @since 1.0.0
     */
    public static function uninstall()
    {
        // delete any options we created during activation
        // eg:
        // delete_option('hche_');
//        $posts = get_posts(array(
//            'numberposts' => -1,
//            'post_type'   => BeforeAfterPhotos::POST_TYPE,
//            'post_status' => 'any'
//        ));
//        foreach ($posts as $post) {
//            wp_delete_post($post->ID, true);
//        }
    }
}
