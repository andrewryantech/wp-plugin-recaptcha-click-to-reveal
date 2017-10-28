<?php
/**
 * Author: https://github.com/andrewryantech
 * Created: 19/10/17 12:09 AM
 */
declare(strict_types=1);

namespace ModernWebServices\Plugins\ClickToReveal\Pages;

use ModernWebServices\Plugins\ClickToReveal\Controller;
use ModernWebServices\Plugins\ClickToReveal\ShortCode;

/**
 * Invoked whenever a public/front page is requested
 */
class PublicPages
{

    /**
     * PublicPages constructor.
     */
    public function __construct()
    {
        add_action('wp_head', [$this, 'wp_head']);
    }


    /**
     * If page is using shortcode, enqueues requisite scripts and styles.
     */
    public function wp_head(): void
    {
        if($this->isPageUsingShortcode()) {
            // Function for Google captch JS to call when finished loading
            wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js?render=explicit&onload=mwsCtrAutoAttach');
            wp_enqueue_script('mws-click-to-reveal', plugin_dir_url(__FILE__) . '/../../js/public/jquery.click-to-reveal.js', ['jquery', 'google-recaptcha']);
            wp_enqueue_style(Controller::STYLE_FONT_AWESOME, Controller::FONT_AWESOME_URL, [], Controller::VERSION, 'screen');
        }
    }


    /**
     * Does the current public page content use the plugin shortcode?
     *
     * @return bool
     */
    private function isPageUsingShortcode(): bool
    {
        global $post;
        return is_a($post, 'WP_Post') && has_shortcode($post->post_content, ShortCode::TAG);
    }
}
