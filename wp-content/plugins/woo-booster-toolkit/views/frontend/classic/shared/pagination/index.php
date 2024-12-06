<?php
if ( ! isset( $data ) ) {
	return;
}

use WCBT\Helpers\Template;

Template::instance()->get_frontend_template_type_classic( 'shared/pagination/standard/wrapper.php', compact( 'data' ) );
