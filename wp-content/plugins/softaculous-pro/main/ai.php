<?php

if(!defined('ABSPATH')){
	die('HACKING ATTEMPT');
}

add_action('wp_ajax_softaculous_pro_ai_generation', 'softaculous_pro_ai_generation');
add_action('enqueue_block_editor_assets', 'softaculous_enqueue_ai_script');

// AJAX ACTIONS
add_action('wp_ajax_softaculous_ai_generation', 'softaculous_ai_generation');
add_action('wp_ajax_softaculous_ai_history', 'softaculous_ai_history');


function softaculous_enqueue_ai_script(){
	
	// Checking if we need to delete the history or not.
	$spro_history_transient = get_transient('spro_history_transient');
	
	if(!empty($spro_history_transient)){
		spro_delete_older_history();
	}

	wp_enqueue_script('softaculous_ai_base', SOFTACULOUS_PRO_PLUGIN_URL . 'assets/js/ai/ai.js', time(), ['strategy' => 'defer']);

	$soft_ai = [
		'i18n' => [],
		'nonce' => wp_create_nonce('softaculous_ai_nonce'),
		'ajax_url' => admin_url('admin-ajax.php'),
		'ai_icon_url' => SOFTACULOUS_PRO_PLUGIN_URL .'/assets/images/softaculous-ai.svg',
	];
	
	$tokens = get_option('softaculous_ai_tokens', []);
	if(!empty($tokens)){
		$soft_ai['tokens'] = $tokens;
	}

	wp_localize_script('softaculous_ai_base', 'soft_ai', $soft_ai);

	wp_enqueue_script(
		'softaculous_ai_gb',
		SOFTACULOUS_PRO_PLUGIN_URL . 'assets/js/ai/build/index.js', // Load from the build directory
		array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'react-jsx-runtime', 'softaculous_ai_base'),
		time(),
		['strategy' => 'defer']
    );	
	
	// The API provides better output as markdown compared to html, thats why we need this markdown library to convert the markdown to html.
	wp_enqueue_script(
		'softaculous_ai_marked',
		SOFTACULOUS_PRO_PLUGIN_URL . 'assets/js/marked.min.js',
		array('softaculous_ai_base'),
		time(),
		['strategy' => 'defer']
	);
}

function softaculous_ai_generation(){
	
	global $softaculous_pro, $softaculous_pro_settings;
	
	check_admin_referer('softaculous_ai_nonce', 'nonce');
	
	if(!current_user_can('manage_options')){
		wp_send_json_error(__('You do not have required privilege', 'softaculous-pro'));
	}
	
	$content = wp_kses_post(wp_unslash($_REQUEST['content']));
	$prompt = wp_kses_post(wp_unslash($_REQUEST['prompt']));
	$shortcut = sanitize_text_field(wp_unslash($_REQUEST['shortcut']));
	$license = !empty($softaculous_pro['license']['license']) ? $softaculous_pro['license']['license'] : '';
	
	// We need atleast a prompt or a shortcut
	if(empty($prompt) && empty($shortcut)){
		wp_send_json_error(__('Please write a Prompt, for the AI to generate content', 'softaculous-pro'));
	}
	
	// Shortcut requires content, but a prompt does not,
	// shortcuts are meant to be used to process content,
	// where as a Prompt can be used to process as well as generate content.
	if(!empty($shortcut) && empty($content)){
		wp_send_json_error(__('AI shortcut can not be applied without any content.', 'softaculous-pro'));
	}

	$res = wp_remote_post(SOFTACULOUS_PRO_AI_API, [
		'timeout' => 30,
		'body' => [
			'prompt' => $prompt,
			'shortcut' => $shortcut,
			'content' => $content,
			'license' => $license
		]
	]);

	if(empty($res)){
		wp_send_json_error(__('Unable to complete this request', 'softaculous-pro'));
	}
	
	if(is_wp_error($res)){
		$error_string = $res->get_error_message();
		wp_send_json_error($error_string);
	}
	
	$body = wp_remote_retrieve_body($res);
	
	if(empty($body)){
		wp_send_json_error(__('The AI API responsed with empty response', 'softaculous-pro'));
	}

	$ai_content = json_decode($body, true);
	
	$res_code = wp_remote_retrieve_response_code($res);
	if($res_code > 299){
		wp_send_json_error($ai_content['error']);
	}

	// Bulding the history data
	if(!isset($ai_content['error']) && isset($softaculous_pro_settings['ai_history_duration']) && $softaculous_pro_settings['ai_history_duration'] > 0){
		$history_data = [];
		if(!empty($content)){
			$history['content'] = $content;
			$title = (strlen($content) > 100 ? substr($content, 100) : $content);
		}

		if(!empty($prompt)){
			$history['prompt'] = $prompt;
			$title = (strlen($prompt) > 100 ? substr($prompt, 100) : $prompt);
		}

		if(empty($prompt) && !empty($shortcut)){
			$history['shortcut'] = $shortcut;
			$title = spro_parse_shortcuts($shortcut);
		}

		if(!empty($ai_content['ai'])){
			$history['assistant'] = wp_kses_post(wp_unslash($ai_content['ai']));
		}
		
		// Saving the history
		wp_insert_post([
			'post_title' => $title,
			'post_type' => 'spro_ai_history',
			'post_content' => serialize($history),
		]);
	}

	// Saving token data
	if(!empty($ai_content['used_tokens'])){
		update_option('softaculous_ai_tokens', [
			'used_tokens' => sanitize_text_field($ai_content['used_tokens']),
			'total_tokens' => sanitize_text_field($ai_content['total_tokens'])
		], false);
	}
	
	wp_send_json_success($ai_content);

}

function softaculous_ai_history(){
	check_admin_referer('softaculous_ai_nonce', 'nonce');
	
	if(!current_user_can('manage_options')){
		wp_send_json_error(__('You do not have required privilege', 'softaculous-pro'));
	}
	
	$history_id = sanitize_text_field(wp_unslash($_POST['history_id']));
	$offset = (int) sanitize_text_field(wp_unslash($_POST['offset']));
	$history = [];
	
	if(!empty($history_id)){
		$query = new WP_Query([
			'post_type' => ['spro_ai_history'],
			'page_id' => $history_id,
		]);
		
		if($query->have_posts()){
			while($query->have_posts()){
				$query->the_post();
				$history = unserialize(get_the_content());
			}
		}
		
		// Restore to original post
		wp_reset_postdata();
		wp_send_json_success($history);
		
	} else {
		$query = new WP_Query([
			'post_type' => ['spro_ai_history'],
			'posts_per_page' => 20,
			'offset' => $offset,
			'orderby' => 'date',
			'order' => 'DESC',
			
		]);
	}

	if($query->have_posts()){
		while($query->have_posts()){
			$query->the_post();
			$single['title'] = get_the_title();
			$single['date'] = get_the_date();
			$single['id'] = get_the_ID();
			
			array_push($history, $single);
			
		}
	}
	
	// Restore to original post
	wp_reset_postdata();
	
	$total_count = wp_count_posts('spro_ai_history')->draft; // Getting the total count
	wp_send_json_success(['history' => $history , 'total' => $total_count]);
}

function spro_parse_shortcuts($shortcut){
	$supported_languages = ['arabic', 'chinese', 'czech', 'danish', 'dutch', 'english', 'finnish', 'french', 'german', 'greek', 'hindi', 'hebrew', 'hungarian', 'indonesian', 'italian', 'japanese', 'korean', 'marathi', 'punjabi', 'persian', 'polish', 'portuguese', 'russian', 'spanish', 'swedish', 'thai', 'turkish', 'vietnamese'];
	$supported_tones = ['casual', 'confidence', 'formal', 'friendly', 'inspirational', 'motivational', 'nostalgic', 'playful', 'professional', 'scientific', 'straightforward', 'witty'];
	$other_shortcuts = ['shorter' => 'Make it shorter', 'longer' => 'Make it longer', 'simplify' => 'Simplify the language', 'grammer' => 'Fix Spelling and Grammer'];
	
	if(in_array($shortcut, $supported_languages)){
		return 'Translate to ' . ucfirst($shortcut);
	}
	
	if(in_array($shortcut, $supported_tones)){
		return 'Change the tone of the content to ' . ucfirst($shortcut);
	}
	
	if(array_key_exists($shortcut, $other_shortcuts)){
		return $other_shortcuts[$shortcut];
	}

	return $shortcut;
}

function spro_delete_older_history($delete_all = false){
	global $softaculous_pro_settings;
	
	$history_duration = (int) $softaculous_pro_settings['ai_history_duration'];

	if($history_duration < 1){
		return;
	}

	$x_days_ago = date('Y-m-d', strtotime('-'.$history_duration.' days'));

	$query = new WP_Query([
		'post_type' => 'spro_ai_history',
		'date_query'=> [
			[
				'column' => 'post_date',
				'before' => $x_days_ago,
			],
		],
		'posts_per_page' => -1,
	]);

	if($query->have_posts()){
		while($query->have_posts()){
			$query->the_post();
			wp_delete_post(get_the_ID(), true);
		}

		wp_reset_postdata();
	}
	
	set_transient('spro_history_transient', time(), DAY_IN_SECONDS);
}