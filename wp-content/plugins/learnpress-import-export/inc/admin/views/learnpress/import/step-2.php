<?php
/**
 * Admin Import step 2 view.
 *
 * @since 3.0.0
 */
?>

<h4><?php _e( 'Importing...', 'learnpress-import-export' ); ?></h4>

<input type="hidden" name="save_import" value="<?php echo LP_Request::get_param('save_import'); ?>"/>
<input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'lpie-import-file' ); ?>"/>
<input type="hidden" name="import-file" value="<?php echo LP_Request::get_param('import-file'); ?>"/>

<script type="text/javascript">
    jQuery(function ($) {
        $('#import-form').submit();
    })
</script>