<?php
if ( ! isset( $current_page ) || ! isset( $max_page ) ) {
	return;
}
$current_page = intval( $current_page );
$max_page     = intval( $max_page );

if ( $max_page <= 1 ) {
	return;
}
$next_page = $current_page + 1;
$prev_page = $current_page - 1;
$pages     = array();
// If the max page is smaller than 9 or equal to 9, print all
if ( $max_page <= 9 ) {
	for ( $i = 1; $i <= $max_page; $i ++ ) {
		$pages[] = $i;
	}
} else {
	if ( $current_page <= 3 ) {
		// x is ...
		$pages = [ 1, 2, 3, 4, 5, 'x', $max_page ];
	} elseif ( $current_page <= 5 ) {
		for ( $i = 1; $i <= $current_page; $i ++ ) {
			$pages[] = $i;
		}
		for ( $j = 1; $j <= 2; $j ++ ) {
			$pages[] = $current_page + $j;
		}
		$pages[] = 'x';
		$pages[] = $max_page;
	} else {
		$pages = [ 1, 'x' ];

		for ( $i = 2; $i >= 0; $i -- ) {
			$pages[] = $current_page - $i;
		}

		$current_to_last = $max_page - $current_page;
		if ( $current_to_last <= 5 ) {
			for ( $j = $current_page + 1; $j <= $max_page; $j ++
			) {
				$pages[] = $j;
			}
		} else {
			for ( $j = 1; $j <= 2; $j ++ ) {
				$pages[] = $current_page + $j;
			}
			$pages[] = 'x';
			$pages[] = $max_page;
		}
	}
}

$maximum = count( $pages );
?>
	<nav class="<?php echo esc_attr( apply_filters( 'wcbt/filter/pagination/nav/class', 'wcbt-pagination-nav' ) ); ?>">
		<ul>
			<?php
			$pagination = '';
			if ( $current_page !== 1 ) {
				?>
				<li><a href="#" data-page="<?php echo esc_attr( $prev_page ); ?>"><i class="fa fa-angle-left rp-angle-left"></i></a></li>
				<?php
			}
			for ( $i = 0; $i < $maximum; $i ++ ) {
				if ( intval( $current_page ) === intval( $pages[ $i ] ) ) {
					?>
					<li class="current"><a data-page="<?php echo esc_attr( $pages[ $i ] ); ?>">
							<?php echo esc_html( $pages[ $i ] ); ?>
						</a></li>
					<?php
				} elseif ( $pages[ $i ] === 'x' ) {
					?>
					<li class="disable"><span><i class="rp-solid-ellipsis-h"></i></span></li>
					<?php
				} else {
					?>
					<li><a href="#" data-page="<?php echo esc_attr( $pages[ $i ] ); ?>">
							<?php echo esc_html( $pages[ $i ] ); ?>  </a></li>
					<?php
				}
			}
			if ( $current_page !== $max_page ) {
				?>
				<li><a href="#" data-page="<?php echo esc_attr( $next_page ); ?>"><i class="fa fa-angle-right rp-angle-right"></i></a>
				</li>
				<?php
			}
			?>
		</ul>
	</nav>
<?php
