<?php
/**
 * Author: https://github.com/andrewryantech
 * Created: 2/10/17 2:22 AM
 */
declare(strict_types=1);

namespace ModernWebServices\Plugins\ClickToReveal;

class ShortCode
{

    const TAG            = 'click_to_reveal';
    const FORMAT_DEFAULT = 'default';
    const FORMAT_EMAIL   = 'email';
    const FORMAT_PHONE   = 'phone';

    const FORMATS = [
        self::FORMAT_DEFAULT,
        self::FORMAT_EMAIL,
        self::FORMAT_PHONE,
    ];

    /** @var Settings  */
    private $settings;

    /** @var int */
    private static $nextId = 1;


    /**
     * ShortCode constructor.
     *
     * @param Settings $settings
     */
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

        // Ensure protected value actually exists
        if(!$this->settings->hasProtectedValue($name)){
            if(WP_DEBUG){
                trigger_error(sprintf("No protected value registered with name '%s'", $name));
            }
            return '';
        }

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
    private function render(string $format, string $name, string $public, string $title, string $siteKey): string
    {
        $eType = $this->getElementType($format);

        if(!$eType){
            return '';
        }

        // Ensure the scripts and styles are output to page
        wp_enqueue_script( 'google-recaptcha' );
        wp_enqueue_script('mws-click-to-reveal');
        wp_enqueue_style(Controller::STYLE_FONT_AWESOME);

        $id              = 'google_re_captcha_' . self::$nextId++;
        $extraAttributes = $this->getExtraAttributes($format);
        $debug           = WP_DEBUG ? 'data-debug' : '';

        return  <<<EOD
                    <span class="g-recaptcha" data-sitekey="$siteKey" data-size="invisible" id="$id"></span>
                    <$eType
                        $extraAttributes
                        data-recaptcha-id="$id"
                        data-vendor="modern-web-services"
                        data-plugin="click-to-reveal"
                        data-autoattach
                        data-format="$format"
                        data-name="$name"
                        $debug
                        title="$title">$public<span
                            data-spinner
                            style="display:none;"><i
                                class="fa fa-refresh fa-spin fa-fw"></i
                            ><span class="sr-only">Loading...</span
                        ></span
                    ></$eType>
EOD;
    }


    /**
     * @param string $format
     * @return string
     */
    private function getElementType(string $format): string
    {
        switch($format){
            case self::FORMAT_DEFAULT:
                return 'span';
            case self::FORMAT_EMAIL:
            case self::FORMAT_PHONE:
                return 'a';
            default:
                if(WP_DEBUG){
                    trigger_error("Unrecognised shortcode format: '{$format}'");
                }
                return '';
        }
    }


    /**
     * Generate extra data-attributes for specific formats
     *
     * @param string $format
     * @return string
     */
    private function getExtraAttributes(string $format): string
    {
        switch($format){
            case self::FORMAT_DEFAULT:
                return '';
            case self::FORMAT_EMAIL:
            case self::FORMAT_PHONE:
                return 'href="#"';
            default:
                if(WP_DEBUG){
                    trigger_error("Unrecognised shortcode format: '{$format}'");
                }
                return '';
        }
    }
}
