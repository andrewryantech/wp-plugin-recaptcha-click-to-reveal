<?php

declare(strict_types=1);

/**
 * @var mixed[]  $templateData       All template data passed here, must be extracted.
 * @var string   $nonceAction
 * @var string   $nonceName
 * @var string[] $protectedItems     Current values
 * @var bool     $updated            Did we just save the form?
 * @var string   $siteKey
 * @var string   $secretKey
 * @var bool     $deleteOnUninstall
 */
extract($templateData, EXTR_OVERWRITE);
?>



<div class="wrap">
    <h1>Click-to-reveal</h1>

    <?php if($updated) : ?>
      <div id="message" class="updated notice is-dismissible"><p><?php _e( 'Settings saved.' ) ?></p></div>
    <?php endif; ?>

    <form method="post">

        <p>Items listed here will not be revealed on your site until visitors verify they are human.</p>

        <input type="hidden" name="updated" value="true"/>
        <?php wp_nonce_field($nonceAction, $nonceName); ?>
        <table id="protected_values" class="wp-list-table widefat fixed striped pages">
            <tr><th>Name</th><th>Protected value</th></tr>
              <?php
              $idx = 0;
              foreach($protectedItems as $name => $value) {
                  $idx++;
                  echo <<<EOD
                      <tr>
                          <td>
                              <input title="Bulk Actions" type="checkbox" name="checkbox[{$idx}]" value="1">
                              <input title="Name" type="text" name="keys[{$idx}]" value="{$name}">
                          </td>
                          <td><input title="Value" type="text" name="values[{$idx}]" value="{$value}"></td>
                      </tr>
EOD;
            } ?>
            <tr id="row_template" style="display:none;">
              <td>
                <input title="Bulk Actions" type="checkbox" name="checkbox[IDX]" value="1">
                <input title="Name" type="text" name="keys[IDX]">
              </td>
              <td><input title="Value" type="text" name="values[IDX]"></td>
            </tr>
        </table>
      <table class="wp-list-table widefat fixed striped pages">
        <tr>
          <th>Delete data on uninstall</th>
          <td>
            <input
                type="checkbox"
                <?php if($deleteOnUninstall): ?>checked="checked"<?php endif; ?>
                id="delete_data_on_uninstall"
                name="delete_data_on_uninstall"
                value="1"
            >
            <label for="delete_data_on_uninstall">If checked, all data will be deleted on plugin uninstall.</label>
          </td>
        </tr>
      </table>
      <?php submit_button('Add row', 'primary', 'submit', false, ['id' => 'add_row']); ?>

      <?php submit_button(); ?>
  </form>
</div>
