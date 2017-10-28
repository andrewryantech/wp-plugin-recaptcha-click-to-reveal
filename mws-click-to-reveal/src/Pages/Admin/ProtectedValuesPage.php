<?php
/**
 * Author: https://github.com/andrewryantech
 * Created: 1/10/17 4:56 PM
 */
declare(strict_types=1);

namespace ModernWebServices\Plugins\ClickToReveal\Pages\Admin;

use ModernWebServices\Plugins\ClickToReveal;

/**
 * Page where admin can manage their protected name/value pairs
 */
class ProtectedValuesPage
{
    const PAGE_SLUG    = 'mws_click_to_reveal';
    const NONCE_NAME   = 'nonce_mws_click_to_reveal';
    const NONCE_ACTION = 'action_mws_click_to_reveal';

    private $settings;

    /**
     * Options constructor.
     * @param ClickToReveal\Settings $settings
     */
    public function __construct(ClickToReveal\Settings $settings)
    {
        $this->settings = $settings;
        add_action('admin_menu', [$this, 'add_options_page_to_main_menu']);
        add_action('admin_init', [$this, 'enqueue_scripts']);
    }


    /**
     * Add submenu page to the Settings main menu.
     */
    public function add_options_page_to_main_menu(): void
    {
        // Add the menu item and page
        $page_title = 'Settings';
        $menu_title = 'Click-to-reveal';
        $capability = 'edit_pages';
        $slug       = self::PAGE_SLUG;
        $callback   = [$this, 'render_page'];
        $icon       = 'dashicons-hidden';
        $position   = 100;
        add_menu_page( $page_title, $menu_title, $capability, $slug, $callback, $icon, $position );
    }


    public function enqueue_scripts(): void
    {
        if (!isset($_GET['page']) || self::PAGE_SLUG !== $_GET['page']){
            return;
        }

        // Include scripts
        wp_enqueue_script(
            'mws-click-to-reveal-options',
            plugin_dir_url(__FILE__) . '/../../../js/admin/protected-values.js',
            ['jquery'],
            ClickToReveal\Controller::VERSION
        );
    }

    private function handle_form(): bool
    {
        // Check nonce
        $verified = isset($_POST[self::NONCE_NAME]) && 1 === wp_verify_nonce($_POST[self::NONCE_NAME], self::NONCE_ACTION);
        if(!$verified){
            $this->render_error('Sorry, your nonce was not correct. Please try again.');
            return false;
        }

        $keys   = $_POST['keys'] ?? [];
        $values = $_POST['values'] ?? [];
        if(count($keys) !== count($values)){
            $this->render_error('Key count does not match value count. Please try again.');
            return false;
        }

        $data = [];
        foreach($keys as $idx => $key){
            if(!array_key_exists($idx, $values)){
                $this->render_error("No value found for key '$key'. Please try again.");
                return false;
            }
            if(array_key_exists($key, $data)){
                $this->render_error("Name of protected value must be unique. Duplicate '$key'. Please try again.");
                return false;
            }
            $key = trim($key);
            $value = $values[$idx];
            if('' === $key){
                if('' === trim($value)){
                    // Don't save if both empty
                    continue;
                }
                $this->render_error("No key provided for value '$value'. Please try again.");
                return false;
            }
            $data[$key] = $value;
        }
        $this->settings->setProtectedValues($data);
        $this->settings->setDeleteOnUninstall(isset($_POST['delete_data_on_uninstall']));
        return true;
    }

    private function render_error(string $message): void
    {
        echo <<<EOD
            <div class="error">
                <p>{$message}</p>
            </div>
EOD;
    }


    /**
     * Callback to render the options page
     */
    public function render_page(): void
    {
        $templateData['updated'] = false;
        if(isset($_POST['updated']) && $_POST['updated'] === 'true') {
            $templateData['updated'] = $this->handle_form();
        }

        $templateData['protectedItems'] = $this->settings->getProtectedValues([
            'firstName'  => 'firstProtectedValue',
            'SecondName' => 'secondProtectedValue',
        ]);

        $templateData['nonceAction'] = self::NONCE_ACTION;
        $templateData['nonceName']   = self::NONCE_NAME;
        $templateData['deleteOnUninstall'] = $this->settings->getDeleteOnUninstall();

        include __DIR__ . '/../../templates/admin/protected_values.php';
    }
}
