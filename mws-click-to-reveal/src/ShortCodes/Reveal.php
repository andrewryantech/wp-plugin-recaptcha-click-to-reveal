<?php
/**
 * Author: andy@modernwebservices.com.au
 * Created: 2/10/17 2:22 AM
 */
declare(strict_types=1);

namespace ModernWebServices\Plugins\ClickToReveal\ShortCodes;

use ModernWebServices\Plugins\ClickToReveal\Pages\OptionsPage;
use ModernWebServices\Plugins\ClickToReveal\Settings;

class Reveal
{

    const TAG = 'click_to_reveal';

    const FORMAT_DEFAULT = 'default';
    const FORMAT_EMAIL = 'email';

    const FORMATS = [
        self::FORMAT_DEFAULT,
        self::FORMAT_EMAIL,
    ];

    private $settings;


    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
        add_shortcode(self::TAG, [$this, 'handle']);
    }


    /**
     * @param array  $atts
     * @param string $content
     * @return string
     */
    public function handle(array $atts, string $content): string
    {
        // Ensure we have all required attributes
        if(!isset($atts['name'])){
            if(WP_DEBUG){
                trigger_error('You must specify the name of the protected value you wish to reveal');
            }
            return '';
        }
        $name   = $atts['name'];
        $title  = $atts['title'] ?? 'Click to reveal';
        $format = $atts['format'] ?? self::FORMAT_DEFAULT;

        $siteKey = $this->settings->getSiteKey();

        return $this->render($format, $name, $content, $title, $siteKey);
    }


    /**
     * Return the public html to insert
     *
     * @param string $format
     * @param string $name
     * @param string $public
     * @param string $title
     * @param string $siteKey
     * @return string
     */
    public function render(string $format, string $name, string $public, string $title, string $siteKey): string
    {

        switch($format){
            case self::FORMAT_DEFAULT:
//                return '<button class="g-recaptcha" data-sitekey="'.$siteKey.'" data-callback="xxCallback" data-click-to-reveal="'.$name.'" title="'.$title.'">'.$public.'</button>';
                return '<span class="g-recaptcha" data-sitekey="'.$siteKey.'" data-callback="xxCallback" data-click-to-reveal="'.$name.'" title="'.$title.'">'.$public.'</span>';
            case self::FORMAT_EMAIL:
                return '<a class="g-recaptcha" data-sitekey="'.$siteKey.'" data-callback="xxCallback" href="#" data-click-to-reveal="'.$name.'" title="'.$title.'"><span>'.$public.'</span></a>';
        }
        if(WP_DEBUG){
            trigger_error("Unrecognised shortcode format: '{$format}'");
        }
        return '';
    }
}
