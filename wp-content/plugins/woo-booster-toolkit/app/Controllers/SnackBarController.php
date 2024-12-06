<?php

namespace WCBT\Controllers;

class SnackBarController
{
    public function __construct()
    {
        add_action('wp_footer', array( $this, 'add_snackbar' ));
    }

    public function add_snackbar()
    {
        ?>
        <div id="wcbt-snackbar"></div>
        <?php
    }
}
