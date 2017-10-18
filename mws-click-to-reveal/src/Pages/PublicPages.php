<?php
/**
 * Author: andy@modernwebservices.com.au
 * Created: 19/10/17 12:09 AM
 */
declare(strict_types=1);

namespace ModernWebServices\Plugins\ClickToReveal\Pages;

use ModernWebServices\Plugins\ClickToReveal\ShortCodes;

/**
 * Invoked whenever a public/front page is requested
 */
class PublicPages
{
    public function __construct()
    {
        add_action('wp_head', [$this, 'wp_head']);
        add_action('init', [$this, 'enqueue_public_scripts']);
    }

    public function wp_head(): void
    {
        if($this->isPageUsingShortcode()) {
            wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js');
        }
    }

    public function enqueue_public_scripts(): void
    {
        if($this->isPageUsingShortcode()) {
            wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js');
        }

        // Include scripts
        wp_enqueue_script('mws-click-to-reveal', plugin_dir_url(__FILE__) . 'js/test.js', ['jquery', 'google-recaptcha']);
    }

    private function isPageUsingShortcode(): bool
    {
        global $post;
        return is_a($post, 'WP_Post') && has_shortcode($post->post_content, ShortCodes\Reveal::TAG);
    }
}
