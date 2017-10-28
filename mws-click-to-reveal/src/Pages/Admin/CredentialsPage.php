<?php
/**
 * Author: https://github.com/andrewryantech
 * Created: 17/10/17 11:13 PM
 */
declare(strict_types=1);

namespace ModernWebServices\Plugins\ClickToReveal\Pages\Admin;

use ModernWebServices\Plugins\ClickToReveal\Settings;

/**
 * The credentials page where admin can manage their Google reCaptcha API keys
 */
class CredentialsPage
{
    const MODE_DISPLAY = 1;  // Displays current credentials, with button to Configure/Reset Keys
    const MODE_INPUT   = 2;  // Displays form input to enter Keys

    const PAGE_SLUG      = 'mws_click_to_reveal_credentials';
    const NONCE_NAME     = 'nonce_mws_ctr_credentials';
    const NONCE_ACTION   = 'action_mws_ctr_credentials';

    /** @var Settings */
    private $settings;


    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
        add_action('admin_menu', [$this, 'add_submenu_page']);
        add_action('init', [$this, 'handle_form']);
    }

    public function add_submenu_page(): void
    {
        $parent_slug = ProtectedValuesPage::PAGE_SLUG;
        $page_title = 'Google reCaptcha credentials';
        $menu_title = 'Integration';
        $capability = 'edit_pages';
        $menu_slug = self::PAGE_SLUG;
        $function = [$this, 'render_page'];
        add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
    }

    public function render_page(): void
    {
        $keysUpdated = isset($_GET['updated']) && 'true' === $_GET['updated'];
        $displayForm = isset($_GET['action']) && 'setup' === $_GET['action'];
        $wasInvalid  = isset($_GET['message']) && 'invalid' === $_GET['message'];
        $nonceError  = isset($_GET['message']) && 'nonce' === $_GET['message'];

        /** @noinspection PhpUnusedLocalVariableInspection */
        $templateData = $displayForm
            ? $this->generateTemplateDataForInput($wasInvalid, $nonceError)
            : $this->generateTemplateDataForDisplay($keysUpdated);

        include __DIR__ . '/../../templates/admin/recaptcha_credentials.php';
    }


    /**
     * Checks to see if valid input has been POSTed.
     * If so, updates, and redirects a GET to the display page
     */
    public function handle_form(): void
    {
        // Not this page?
        if(!$this->isCredentialsPage()) {
            return;
        }
        // Not trying to save keys?
        if(!$this->isUpdateRequest()) {
            return;
        }

        // Redirect back to display
        $slug = self::PAGE_SLUG;

        if(!$this->isValidNonce()){
            wp_redirect(get_admin_url() . "admin.php?page={$slug}&action=setup&message=nonce");
            return;
        }

        $siteKey   = trim($_POST['sitekey'] ?? '');
        $secretKey = trim($_POST['secret'] ?? '');

        // Save attempt, and no errors ?
        if($this->validateSubmittedKeys($siteKey, $secretKey)) {
            $this->settings->setSiteKey($siteKey);
            $this->settings->setSecretKey($secretKey);
            wp_redirect(get_admin_url() . "admin.php?page={$slug}&updated=true");
        } else {
            wp_redirect(get_admin_url() . "admin.php?page={$slug}&action=setup&message=invalid");
        }
    }

    private function isValidNonce(): bool
    {
        return isset($_POST[self::NONCE_NAME]) && 1 === wp_verify_nonce($_POST[self::NONCE_NAME], self::NONCE_ACTION);
    }

    private function isCredentialsPage(): bool
    {
        return isset($_REQUEST['page']) && self::PAGE_SLUG === $_REQUEST['page'];
    }


    private function isUpdateRequest(): bool
    {
        return isset($_POST['action']) && $_POST['action'] === 'update';
    }

    private function validateSubmittedKeys(string $siteKey, string $secret): bool
    {
        // Cannot have one empty and one present
        return (bool)strlen($siteKey) === (bool)strlen($secret);
    }


    private function generateTemplateDataForDisplay(bool $keysUpdated): array
    {
        // replace all but last 4 chars of secret key with '*'
        $secretKey       = $this->settings->getSecretKey();
        $maxVisibleChars = 4;
        $length          = strlen($secretKey);
        $maskLength      = max($length - $maxVisibleChars, 0);
        $visibleLength   = min($maxVisibleChars, $length);
        $mask            = str_pad('', $maskLength, '*');

        return [
            'mode'            => self::MODE_DISPLAY,
            'keysUpdated'     => $keysUpdated,
            'wasInvalid'      => false,
            'nonceError'      => false,
            'siteKey'         => $this->settings->getSiteKey(),
            'maskedSecretKey' => $mask . substr($secretKey, -$visibleLength),
        ];
    }


    private function generateTemplateDataForInput(bool $wasInvalid, bool $nonceError): array
    {
        return [
            'mode'        => self::MODE_INPUT,
            'keysUpdated' => false,
            'wasInvalid'  => $wasInvalid,
            'nonceError'  => $nonceError,
            'nonceName'   => self::NONCE_NAME,
            'nonceAction' => self::NONCE_ACTION,
        ];
    }
}
