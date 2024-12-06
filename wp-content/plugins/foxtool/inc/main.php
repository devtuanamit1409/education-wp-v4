<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $foxtool_options;
# add font Awesome
function foxtool_fontawe_assets(){
	global $foxtool_options;
	if (isset($foxtool_options['main-add1'])){
	wp_enqueue_style( 'foxtool-icon', FOXTOOL_URL . 'font/css/all.css', array(), FOXTOOL_VERSION);
	}
}
add_action('wp_enqueue_scripts', 'foxtool_fontawe_assets');
# them hieu ung cho trang web như noel
function foxtool_add_hover_style(){
	global $foxtool_options;
	if(isset($foxtool_options['main-hover1']) && $foxtool_options['main-hover1'] == 'Snow1'){
	wp_enqueue_script( 'hover', FOXTOOL_URL . 'link/hover/hover-style-1.js', array(), FOXTOOL_VERSION);
	}
	
	if(isset($foxtool_options['main-hover1']) && $foxtool_options['main-hover1'] == 'Snow2'){
	wp_enqueue_script( 'hover', FOXTOOL_URL . 'link/hover/hover-style-2.js', array(), FOXTOOL_VERSION);
	}
	
	if(isset($foxtool_options['main-hover1']) && $foxtool_options['main-hover1'] == 'Lunar1'){
	wp_enqueue_script( 'hover', FOXTOOL_URL . 'link/hover/hover-style-lunar-1.js', array(), FOXTOOL_VERSION);
	}
	
	if(isset($foxtool_options['main-hover1']) && $foxtool_options['main-hover1'] == 'Lunar2'){
	wp_enqueue_script( 'hover', FOXTOOL_URL . 'link/hover/hover-style-lunar-2.js', array(), FOXTOOL_VERSION);
	}
	
	if(isset($foxtool_options['main-hover1']) && $foxtool_options['main-hover1'] == 'Vietnam'){
	wp_enqueue_script( 'hover', FOXTOOL_URL . 'link/hover/hover-style-vietnam.js', array(), FOXTOOL_VERSION);
	}
	
	if(isset($foxtool_options['main-hover1']) && $foxtool_options['main-hover1'] == 'Indonesia'){
	wp_enqueue_script( 'hover', FOXTOOL_URL . 'link/hover/hover-style-indonesia.js', array(), FOXTOOL_VERSION);
	}
} 
add_action('wp_footer', 'foxtool_add_hover_style');
# che do darkmode
if (isset($foxtool_options['main-mode1'])){
function foxtool_darkmode_icon1(){
	$icon = '<span id="ft-darkmode-toggle" class="ft-sunmode">
		<svg id="ft-icon-moon" xmlns="http://www.w3.org/2000/svg" width="15px" height="15px" viewBox="0 0 512 512"><path fill="currentColor" d="M264 480A232 232 0 0 1 32 248c0-94 54-178.28 137.61-214.67a16 16 0 0 1 21.06 21.06C181.07 76.43 176 104.66 176 136c0 110.28 89.72 200 200 200c31.34 0 59.57-5.07 81.61-14.67a16 16 0 0 1 21.06 21.06C442.28 426 358 480 264 480Z"/></svg>
		<svg id="ft-icon-sun" style="display:none;" xmlns="http://www.w3.org/2000/svg" width="15px" height="15px" viewBox="0 0 472 472"><path fill="currentColor" d="m123 95l-30 30l-39-38l30-30zM64 216v43H0v-43h64zM256 4v63h-43V4h43zm159 83l-38 38l-30-30l38-38zm-69 292l30-29l39 38l-30 30zm59-163h64v43h-64v-43zM235 109q53 0 90.5 37.5T363 237t-37.5 90.5T235 365t-90.5-37.5T107 237t37.5-90.5T235 109zm-22 362v-63h43v63h-43zM54 388l39-39l30 30l-39 39z"/></svg>
		</span>';
	return $icon;
}
function foxtool_darkmode_icon2(){
    static $counter = 0; 
    $counter++;
    $unique_id = 'ft-darkmode-switch-' . $counter;
    $icon = '<span id="ft-darkmode-toggle-' . $counter . '" class="ft-toggle-switch">
                <input type="checkbox" id="' . $unique_id . '" />
                <label for="' . $unique_id . '" class="ft-switch"></label>
            </span>';
    return $icon;
}
function foxtool_darkmode_js(){
	global $foxtool_options;
	wp_enqueue_script( 'foxdark-js1', FOXTOOL_URL . 'link/darkmode/foxdark1.js', array(), FOXTOOL_VERSION);
	if(isset($foxtool_options['main-mode10']) && $foxtool_options['main-mode10'] == 'Toggle'){
		wp_enqueue_script( 'foxdark-js3', FOXTOOL_URL . 'link/darkmode/foxdark3.js', array(), FOXTOOL_VERSION, true);
	} elseif (isset($foxtool_options['main-mode10']) && $foxtool_options['main-mode10'] == 'System'){
		wp_enqueue_script( 'foxdark-js4', FOXTOOL_URL . 'link/darkmode/foxdark4.js', array(), FOXTOOL_VERSION, true);
	} elseif (isset($foxtool_options['main-mode10']) && $foxtool_options['main-mode10'] == 'Time'){
		wp_enqueue_script( 'foxdark-js5', FOXTOOL_URL . 'link/darkmode/foxdark5.js', array(), FOXTOOL_VERSION, true);
	} else {
		wp_enqueue_script( 'foxdark-js2', FOXTOOL_URL . 'link/darkmode/foxdark2.js', array(), FOXTOOL_VERSION, true);
	}
}
add_action('wp_enqueue_scripts', 'foxtool_darkmode_js');
function foxtool_darkmode_assets() {
	global $foxtool_options;
	// cau hinh vi tri va mua sac
	$color = !empty($foxtool_options['main-mode-c1']) ? $foxtool_options['main-mode-c1'] : '#444444';
	$here = isset($foxtool_options['main-mode11']) && $foxtool_options['main-mode11'] == 'Right' ? 'right' : 'left';
	if(!empty($foxtool_options['main-mode12']) && $foxtool_options['main-mode12'] < 300){
		$bot = $foxtool_options['main-mode12']. 'px';
	} elseif (!empty($foxtool_options['main-mode12']) && $foxtool_options['main-mode12'] >= 300){
		$bot = '50%';
	} else {
		$bot = '30px';
	}
	$lef = !empty($foxtool_options['main-mode13']) ? $foxtool_options['main-mode13']. 'px' : '30px';
	if(isset($foxtool_options['main-mode10']) && $foxtool_options['main-mode10'] == 'Toggle'){ ?>
		<style>
		.ft-toggle-switch input[type="checkbox"] {
				display: none;
		}
		.ft-switch {
			cursor: pointer;
			width: 100%;
			height: 100%;
			background-color: <?php echo $color; ?>;
			border-radius: 20px; 
			transition: background-color 0.3s ease;
			position: relative; 
		}
		.ft-switch::before {
			content: "";
			position: absolute;
			width: 18px; 
			height: 18px; 
			border-radius: 50%;
			background: white;
			z-index:1;
			top: 4px;
			left: 4px;
			transition: transform 0.3s ease;
		}
		.ft-switch:after {
			content: "";
			position: absolute;
			width: 15px; 
			height: 15px; 
			left: 26px;
			top: 50%;
			transform: translateY(-50%);
			background-image: url(data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJjb2xvcjojZmZmZmZmIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGNsYXNzPSJoLWZ1bGwgdy1mdWxsIj48cmVjdCB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgeD0iMCIgeT0iMCIgcng9IjMwIiBmaWxsPSJ0cmFuc3BhcmVudCIgc3Ryb2tlPSJ0cmFuc3BhcmVudCIgc3Ryb2tlLXdpZHRoPSIwIiBzdHJva2Utb3BhY2l0eT0iMTAwJSIgcGFpbnQtb3JkZXI9InN0cm9rZSI+PC9yZWN0Pjxzdmcgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4IiB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgZmlsbD0iI2ZmZmZmZiIgeD0iMCIgeT0iMCIgcm9sZT0iaW1nIiBzdHlsZT0iZGlzcGxheTppbmxpbmUtYmxvY2s7dmVydGljYWwtYWxpZ246bWlkZGxlIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxnIGZpbGw9IiNmZmZmZmYiPjxwYXRoIGQ9Ik00MDEuNCAzNTQuMmMtMi45LjEtNS44LjItOC43LjItNDcuOSAwLTkzLTE4LjktMTI2LjgtNTMuNC0zMy45LTM0LjQtNTIuNS04MC4xLTUyLjUtMTI4LjggMC0yNy43IDYuMS01NC41IDE3LjUtNzguNyAzLjEtNi42IDkuMy0xNi42IDEzLjYtMjMuNCAxLjktMi45LS41LTYuNy0zLjktNi4xLTYgLjktMTUuMiAyLjktMjcuNyA2LjhDMTM1LjEgOTUuNSA4MCAxNjguNyA4MCAyNTVjMCAxMDYuNiA4NS4xIDE5MyAxOTAuMSAxOTMgNTggMCAxMTAtMjYuNCAxNDQuOS02OC4xIDYtNy4yIDExLjUtMTMuOCAxNi40LTIxLjggMS44LTMtLjctNi43LTQuMS02LjEtOC41IDEuNy0xNy4xIDEuOC0yNS45IDIuMnoiIGZpbGw9ImN1cnJlbnRDb2xvciI+PC9wYXRoPjwvZz48L3N2Zz48L3N2Zz4=);
			background-size: contain;
			background-repeat: no-repeat;
		}
		.ft-toggle-switch input[type="checkbox"]:checked + .ft-switch:after {
			content: "";
			position: absolute;
			width: 15px; 
			height: 15px; 
			left: 4px;
			top: 50%;
			transform: translateY(-50%);
			background-image: url(data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJjb2xvcjojZmZmZmZmIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGNsYXNzPSJoLWZ1bGwgdy1mdWxsIj48cmVjdCB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgeD0iMCIgeT0iMCIgcng9IjAiIGZpbGw9InRyYW5zcGFyZW50IiBzdHJva2U9InRyYW5zcGFyZW50IiBzdHJva2Utd2lkdGg9IjAiIHN0cm9rZS1vcGFjaXR5PSIxMDAlIiBwYWludC1vcmRlcj0ic3Ryb2tlIj48L3JlY3Q+PHN2ZyB3aWR0aD0iNTEycHgiIGhlaWdodD0iNTEycHgiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZmZmZiIgeD0iMCIgeT0iMCIgcm9sZT0iaW1nIiBzdHlsZT0iZGlzcGxheTppbmxpbmUtYmxvY2s7dmVydGljYWwtYWxpZ246bWlkZGxlIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxnIGZpbGw9IiNmZmZmZmYiPjxwYXRoIGQ9Ik02Ljk5NSAxMmMwIDIuNzYxIDIuMjQ2IDUuMDA3IDUuMDA3IDUuMDA3czUuMDA3LTIuMjQ2IDUuMDA3LTUuMDA3cy0yLjI0Ni01LjAwNy01LjAwNy01LjAwN1M2Ljk5NSA5LjIzOSA2Ljk5NSAxMnpNMTEgMTloMnYzaC0yem0wLTE3aDJ2M2gtMnptLTkgOWgzdjJIMnptMTcgMGgzdjJoLTN6TTUuNjM3IDE5Ljc3OGwtMS40MTQtMS40MTRsMi4xMjEtMi4xMjFsMS40MTQgMS40MTR6TTE2LjI0MiA2LjM0NGwyLjEyMi0yLjEyMmwxLjQxNCAxLjQxNGwtMi4xMjIgMi4xMjJ6TTYuMzQ0IDcuNzU5TDQuMjIzIDUuNjM3bDEuNDE1LTEuNDE0bDIuMTIgMi4xMjJ6bTEzLjQzNCAxMC42MDVsLTEuNDE0IDEuNDE0bC0yLjEyMi0yLjEyMmwxLjQxNC0xLjQxNHoiIGZpbGw9ImN1cnJlbnRDb2xvciI+PC9wYXRoPjwvZz48L3N2Zz48L3N2Zz4=);
			background-size: contain;
			background-repeat: no-repeat;
		}
		.ft-toggle-switch input[type="checkbox"]:checked + .ft-switch {
			background-color: #777;
		}
		.ft-toggle-switch input[type="checkbox"]:checked + .ft-switch::before {
			transform: translateX(18px); 
		}
		</style>
		<?php if (isset($foxtool_options['main-mode-s1'])){ ?>
			<style>
			.ft-toggle-switch {
				background: none;
				border: none;
				cursor: pointer;
				padding: 0;
				width: 44px;
				height: 26px; 
				display: flex;
				align-items: center;
				justify-content: center;
			}
			</style>
		<?php } else { ?>
			<style>
			.ft-toggle-switch {
				background: none;
				border: none;
				cursor: pointer;
				padding: 0;
				position: fixed; 
				bottom: <?php echo $bot; ?>;
				<?php  echo$here; ?>: <?php echo $lef; ?>;
				width: 44px;
				height: 26px; 
				z-index: 1000; 
				display: flex;
				align-items: center;
				justify-content: center;
			}
			</style>
			<?php
			echo foxtool_darkmode_icon2();
		}
	} elseif (isset($foxtool_options['main-mode10']) && $foxtool_options['main-mode10'] == 'System'){
			echo '';
	} elseif (isset($foxtool_options['main-mode10']) && $foxtool_options['main-mode10'] == 'Time'){
			echo '';
	} else {
		if (isset($foxtool_options['main-mode-s1'])){ ?>
			<style>
			#ft-darkmode-toggle {
				padding:3px;
				margin:0px;
				background:none;
				border:none;
				display: flex;
				justify-content: center;
				align-items: center;
				background-color: <?php echo $color; ?>;
				width:25px;
				height:25px;
				border-radius:100%;
				box-sizing: border-box;
			}
			#ft-darkmode-toggle svg {
				width: 15px;
				height: 15px;
			}
			#ft-darkmode-toggle svg path{fill: #fff;}
			</style>	
		<?php } else { ?>
			<style>
			#ft-darkmode-toggle {
				position: fixed;
				bottom: <?php echo $bot; ?>;
				<?php  echo$here; ?>: <?php echo $lef; ?>;
				width: 40px;
				height: 40px;
				border-radius: 50%;
				background-color: <?php echo $color; ?>;
				border: none;
				cursor: pointer;
				display: flex;
				justify-content: center;
				align-items: center;
				box-shadow: 0px 0px 10px #00000075;
				z-index: 9999;
				padding: 10px;
				box-sizing: border-box;
			}
			#ft-darkmode-toggle svg {
				width: 25px;
				height: 25px;
			}
			#ft-darkmode-toggle svg path{fill: #fff;}
			</style>
			<?php echo foxtool_darkmode_icon1();
		}
	}
}
add_action('wp_footer', 'foxtool_darkmode_assets');
// shortcode
function foxtool_darkmode_shortcode($atts) {
    global $foxtool_options;
    if (isset($foxtool_options['main-mode-s1']) && isset($foxtool_options['main-mode10'])) {
        if ($foxtool_options['main-mode10'] != 'System' && $foxtool_options['main-mode10'] != 'Time') {
            if ($foxtool_options['main-mode10'] == 'Toggle') {
                return foxtool_darkmode_icon2();
            } else {
                return foxtool_darkmode_icon1();
            }
        }
    }
    return;
}
add_shortcode('foxdark', 'foxtool_darkmode_shortcode');
}
# custom mau thanh scroll
function foxtool_add_scroll_style(){
	global $foxtool_options; 
	if (isset($foxtool_options['main-scroll1'])){
	$bg = !empty($foxtool_options['main-scroll11']) ? $foxtool_options['main-scroll11'] : 'none';
	$br = !empty($foxtool_options['main-scroll12']) ? $foxtool_options['main-scroll12'] : '#333';
	$ru = !empty($foxtool_options['main-scroll13']) ? $foxtool_options['main-scroll13'] .'px' : '1px';
	$zi = !empty($foxtool_options['main-scroll14']) ? $foxtool_options['main-scroll14'] .'px' : '10px';
	?>
	<style>
	::-webkit-scrollbar {
		width:<?php echo $zi ?>;
		height:<?php echo $zi ?>;
		background-color:<?php echo $bg ?>;
	}
	::-webkit-scrollbar-thumb {
		background-color:<?php echo $br ?>;
		border-radius:<?php echo $ru ?>;
	}
	::-webkit-scrollbar-track {
		background-color:<?php echo $bg ?>;
		border-radius:<?php echo $ru ?>;
	}
	</style>
	<?php
	}
} 
add_action('wp_head', 'foxtool_add_scroll_style');
