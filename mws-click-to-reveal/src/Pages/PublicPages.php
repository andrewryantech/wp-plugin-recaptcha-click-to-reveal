<?php
/**
 * Author: andy@modernwebservices.com.au
 * Created: 19/10/17 12:09 AM
 */
declare(strict_types=1);

namespace ModernWebServices\Plugins\ClickToReveal\Pages;

use ModernWebServices\Plugins\ClickToReveal\Controller;
use ModernWebServices\Plugins\ClickToReveal\ShortCodes;

/**
 * Invoked whenever a public/front page is requested
 */
class PublicPages
{
    public function __construct()
    {
        add_action('wp_head', [$this, 'wp_head']);
    }

    public function wp_head(): void
    {
        if($this->isPageUsingShortcode()) {
            wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js?render=explicit');
            wp_enqueue_script('mws-click-to-reveal', plugin_dir_url(__FILE__) . '/../../js/public/jquery.click-to-reveal.js', ['jquery', 'google-recaptcha']);
            wp_enqueue_style(Controller::STYLE_FONT_AWESOME, Controller::FONT_AWESOME_URL, [], Controller::VERSION, 'screen');
        }
    }

    private function isPageUsingShortcode(): bool
    {
        global $post;
        return is_a($post, 'WP_Post') && has_shortcode($post->post_content, ShortCodes\Reveal::TAG);
    }
}
