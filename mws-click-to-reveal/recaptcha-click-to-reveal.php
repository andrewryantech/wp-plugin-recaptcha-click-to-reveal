<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/andrewryantech/wp-plugin-recaptcha-click-to-reveal
 * @package           Plugin_Name
 *
 * @wordpress-plugin
 * Plugin Name:       MWS Click-to-reveal
 * Plugin URI:        https://github.com/andrewryantech/wp-plugin-recaptcha-click-to-reveal
 * Description:       Block web scrapers by requiring users to click on emails to reveal the full address. Authenticates users using Google invisible reCaptcha.
 * Version:           1.0.3
 * Author:            Andy Ryan - Modern Web Services
 * Author URI:        http://modernwebservices.com.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       plugin-name
 * Domain Path:       /languages
 */
declare(strict_types=1);

use ModernWebServices\Plugins\ClickToReveal\Controller;
use ModernWebServices\Plugins\ClickToReveal\Settings;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Composer autoload everything.
require_once __DIR__ . '/vendor/autoload.php';

// Kick things off
$plugin = new Controller(Settings::getInstance(), __FILE__);
