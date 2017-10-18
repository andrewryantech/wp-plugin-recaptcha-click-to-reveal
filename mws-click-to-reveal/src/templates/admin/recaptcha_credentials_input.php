<?php
/**
 * A template fragment to use on the reCaptcha credentials page when inputting keys
 *
 * @var string   $nonceAction
 * @var string   $nonceName
 */
declare(strict_types=1);
?>

<form method="post">
    <?php wp_nonce_field($nonceAction, $nonceName); ?>
    <input type="hidden" name="action" value="update">
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row"><label for="sitekey">Site Key</label></th>
            <td><input aria-required="true" value="" id="sitekey" name="sitekey" class="regular-text code" type="text"></td>
        </tr>
        <tr>
            <th scope="row"><label for="secret">Secret Key</label></th>
            <td><input aria-required="true" value="" id="secret" name="secret" class="regular-text code" type="text"></td>
        </tr>
        </tbody>
    </table>
    <?php submit_button(); ?>
</form>
