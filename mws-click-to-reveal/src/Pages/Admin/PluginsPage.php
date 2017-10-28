<?php
/**
 * Author: https://github.com/andrewryantech
 * Created: 1/10/17 5:44 PM
 */
declare(strict_types=1);

namespace ModernWebServices\Plugins\ClickToReveal\Pages\Admin;

/**
 * Tweak the plugins page entry for this plugin
 */
class PluginsPage
{
    public function __construct(string $pluginFile)
    {
        add_filter('plugin_action_links_' . plugin_basename($pluginFile), [$this, 'plugin_add_settings_link']);
    }


    public function plugin_add_settings_link(array $links): array
    {
        $href = esc_url( get_admin_url(null, 'admin.php?page=' . ProtectedValuesPage::PAGE_SLUG) );
        $links[] = '<a href="'. $href .'">'. __('Settings') .'</a>';
        return $links;
    }
}
