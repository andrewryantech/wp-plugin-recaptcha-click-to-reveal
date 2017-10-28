<?php
/**
 * @var mixed[]  $templateData       All template data passed here, must be extracted.
 * @var int      $mode               Are we displaying reCaptcha credentials, or inputting them?
 * @var bool     $keysUpdated
 * @var bool     $wasInvalid
 * @var bool     $nonceError
 */
declare(strict_types=1);

use ModernWebServices\Plugins\ClickToReveal\Pages\Admin\CredentialsPage;

extract($templateData, EXTR_OVERWRITE);
?>
<div class="wrap">
    <h1>Integration with Other Services</h1>

    <?php if($keysUpdated) : ?>
      <div id="message" class="updated notice is-dismissible"><p><?php _e( 'Keys saved.' ) ?></p></div>
    <?php endif; ?>

    <?php if($wasInvalid) : ?>
      <div class="error"><p><strong>ERROR:</strong> You must supply both keys, or clear both keys.</p></div>
    <?php endif; ?>

    <?php if($nonceError) : ?>
      <div class="error"><p><strong>ERROR:</strong> Invalid nonce. Please try again</p></div>
    <?php endif; ?>


    <div class="card active" id="recaptcha">
        <h2 class="title">reCAPTCHA</h2>
        <div class="infobox">
            CAPTCHA<br>
            <a href="https://www.google.com/recaptcha/intro/index.html">google.com/recaptcha</a></div>
        <br class="clear">

        <div class="inside">
            <p>reCAPTCHA is a free service to protect your website from spam and abuse.</p>
            <?php
            if($mode === CredentialsPage::MODE_DISPLAY){
                include 'recaptcha_credentials_display.php';
            } elseif($mode === CredentialsPage::MODE_INPUT){
                include 'recaptcha_credentials_input.php';
            }
            ?>
        </div>
    </div>
</div>
