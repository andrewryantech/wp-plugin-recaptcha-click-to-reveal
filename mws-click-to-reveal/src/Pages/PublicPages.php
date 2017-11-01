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
     * To test if the footer is using the shortcode, we need to render the sidebars using dynamic_sidebar().
     * This will filter through 'widget_text'. We can hook to this filter and check if the shortcode detected.
     * @var bool
     */
    private $shortCodeUseDetected = false;


    /**
     * PublicPages constructor.
     */
    public function __construct()
    {
        // Defer to enable other plugins (eg ContactForm7) to register with their specific 'onload' query arg
        add_action('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts'], 1000);

        // Enable shortcodes in text widgets
        add_filter('widget_text',[ $this, 'do_shortcode']);
    }

    public function do_shortcode($content){

        // Don't re-check if we've already detected the shortcode in a sidebar
        if(!$this->shortCodeUseDetected){
            $this->shortCodeUseDetected = has_shortcode($content, ShortCode::TAG);
        }
        return do_shortcode($content);
    }


    /**
     * If page is using shortcode, enqueues requisite scripts and styles.
     */
    public function wp_enqueue_scripts(): void
    {
        if($this->isPageUsingShortcode()) {
            // Don't re-register if another plugin has already done so. The other plugin may rely on an 'onload' arg
            if ( ! wp_script_is( 'google-recaptcha', 'registered' ) ) {
                wp_register_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js?render=explicit');
            }
            wp_register_script('mws-click-to-reveal', plugin_dir_url(__FILE__) . '../js/public/jquery.click-to-reveal.js', ['jquery', 'google-recaptcha']);
            wp_register_style(Controller::STYLE_FONT_AWESOME, Controller::FONT_AWESOME_URL, [], Controller::VERSION, 'screen');
        }
    }


    /**
     * Does the current public page content use the plugin shortcode?
     *
     * @return bool
     */
    private function isPageUsingShortcode(): bool
    {
        // Check post content
        global $post;
        if(is_a($post, 'WP_Post') && has_shortcode($post->post_content, ShortCode::TAG)){
            return true;
        }

        // Check sidebar content
        global $wp_registered_sidebars;

        // Capture and discard sidebar content. Short-circuit if we find the short-code
        ob_start();
        foreach($wp_registered_sidebars as $sideBar){
            dynamic_sidebar($sideBar['id']);
            if($this->shortCodeUseDetected){
                break;
            }
        }
        ob_end_clean();

        return $this->shortCodeUseDetected;
    }
}
