<?php
/**
 * A template fragment to use on the reCaptcha credentials page when displaying current state
 *
 * @var string   $siteKey
 * @var string   $maskedSecretKey  All but last four chars are replaced with * char
 */
declare(strict_types=1);

use ModernWebServices\Plugins\ClickToReveal\Pages\Admin\CredentialsPage;

// No keys configured?
$isEmptyKeys = '' === $siteKey && '' === $maskedSecretKey;

// Generate link
$slug = CredentialsPage::PAGE_SLUG;
$href = get_admin_url() . "admin.php?page={$slug}&action=setup";
?>

<?php if($isEmptyKeys) : ?>
<p>To use reCAPTCHA, you need to install an API key pair.</p>
<p><a href="<?php echo $href; ?>" class="button">Configure Keys</a></p>
<p>For more details, see <a href="https://contactform7.com/recaptcha/">reCAPTCHA</a>.</p>
<?php else: ?>
<table class="form-table">
    <tbody>
        <tr>
            <th scope="row">Site Key</th>
            <td class="code"><?php echo $siteKey; ?></td>
        </tr>
        <tr>
            <th scope="row">Secret Key</th>
            <td class="code"><?php echo $maskedSecretKey; ?></td>
        </tr>
    </tbody>
</table>
<p><a href="<?php echo $href; ?>" class="button">Reset Keys</a></p>
<?php endif; ?>
