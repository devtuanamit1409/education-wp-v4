<?php
$import_form_server = LP_Request::get_param( 'learnpress_import_form_server' );
?>

<div id="error-import-user" style="color: red;"></div>
<form method="post" name="import-user-form" id="import-user-form"
      action="admin.php?page=learnpress-import-export&tab=import" enctype="multipart/form-data">
    <div id="import-form-postbox" class="postbox">
        <h2 class="hndle"><span><?php _e( 'Import Users', 'learnpress-import-export' ); ?></span></h2>
        <div class="inside">
			<?php
			if ( ! empty ( $import_form_server ) ) {
				do_action( 'lpie_import_user_from_server', $import_form_server );
			} else { ?>
                <input type="hidden" name="lpie_import_user_data" value="1"/>
                <input type="hidden" name="step" value="<?php echo $step + 1; ?>"/>
                <input type="hidden" name="action" value="export"/>
                <input type="hidden" name="import-nonce"
                       value="<?php echo wp_create_nonce( 'learnpress-import-export' ); ?>">
				<?php if ( $step ) { ?>
					<?php do_action( 'lpie_import_user_step_' . $step ); ?>
				<?php } else { ?>
                    <div id="import-user-uploader">
                        <div id="import-upload-file"></div>
                        <a id="import-user-uploader-select" href="javascript:;"><?php _e( 'Select file (csv)' ); ?></a>
                        <a id="import-user-start-upload" class="dashicons dashicons-upload" href="javascript:;"></a>
                    </div>
				<?php }
			} ?>
            <p class="download-ex-csv"><?php _e('Download example CSV file ', 'learnpress-import-export') ?><a href="<?php echo plugins_url() . '/learnpress-import-export/dummy-data/demo_import_user.csv'; ?>"><?php _e('here', 'learnpress-import-export') ?></a></p>
        </div>
        
    </div>
</form>
<style>
    p.download-ex-csv a{
        text-decoration: none;
    }
    p.download-ex-csv a:hover{
        text-decoration: underline;
    }
</style>




<script type="text/javascript">
    // Custom example logic
    jQuery(function ($) {
        LP_Importer_User.init({
            url: "<?php echo admin_url( 'admin.php?page=learnpress-import-export&tab=import' );?>"
        })
    })

</script>