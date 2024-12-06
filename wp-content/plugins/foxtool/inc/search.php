<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $foxtool_search_options;
# tao api custom post type post and product
if (isset($foxtool_search_options['main-search1'])){
// xóa post khoi json neu xoa
function foxtool_delete_search_auto_when_delete_post($post_id) {
    $upload_dir = wp_upload_dir();
    $file_path = $upload_dir['basedir'] . '/json/data-search.json';
    $existing_data = array();
    if (file_exists($file_path)) {
        $existing_data = json_decode(file_get_contents($file_path), true);
        foreach ($existing_data as $key => $item) {
            if ($item['ID'] == $post_id) {
                unset($existing_data[$key]);
                break; 
            }
        }
        // Reset array keys
        $existing_data = array_values($existing_data);
        file_put_contents($file_path, json_encode($existing_data));
    }
}
add_action('delete_post', 'foxtool_delete_search_auto_when_delete_post');
// them post vào json
function foxtool_add_search_auto_whenpublish($post_id ) {
        global $foxtool_search_options;
		
		// Tránh vô tình cập nhật khi auto-saved
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		
		// Kiểm tra xem main-search-posttype có tồn tại và có dữ liệu hay không
		if (!isset($foxtool_search_options['main-search-posttype']) || !is_array($foxtool_search_options['main-search-posttype']) || empty($foxtool_search_options['main-search-posttype'])) {
			return; 
		}

		// Tránh xử lý cho các bài viết không phải là sản phẩm
		if (!in_array(get_post_type($post_id), $foxtool_search_options['main-search-posttype'])) {
			return;
		}
		
        $post = get_post($post_id);
        $type = get_post_type($post->ID);
        if (isset($foxtool_search_options['main-search-posttype'])) {
            if(count($foxtool_search_options['main-search-posttype'])>0){
                $allowed_post_types = $foxtool_search_options['main-search-posttype'];
                if (in_array($type, $allowed_post_types)) {
                    $filed = array(
                    'ID',
                    'title',
                    'url',
                    'thum',
					'price',
                    'taxonomy'
                );
                    $item = array('type' => $type);
                    foreach ($filed as $field) {
                        switch ($field) {
                            case 'ID':
                                $item[$field] = $post->ID;
                            break;
                            case 'title':
                                $item[$field] = get_the_title($post->ID);
                                break;
                            case 'url':
                                $item[$field] = get_permalink($post->ID);
                                break;
                            case 'thum':
                                $item[$field] = get_the_post_thumbnail_url($post->ID);
                                break;
                            case 'price':
                                if ($type === 'product') {
									if (function_exists('wc_get_product')) {
										$product = wc_get_product($post->ID);
										if ($product->is_type('variable')) {
											$prices = [];
											$available_variations = $product->get_available_variations();
											foreach ($available_variations as $variation) {
												$variation_obj = new WC_Product_Variation($variation['variation_id']);
												$prices[] = $variation_obj->get_price();
											}
											if (!empty($prices)) {
												$min_price = min($prices);
												$max_price = max($prices);
												$item[$field] = wc_price($min_price) . ' – ' . wc_price($max_price);
											} else {
												$item[$field] = wc_price($product->get_price());
											}
										} else {
											$item[$field] = wc_price($product->get_price());
										}
									}
								}
                                break;
                            case 'taxonomy':
                                if ($post->post_type == 'product') {
                                    $taxonomy_terms = wp_get_post_terms($post->ID, 'product_cat');
                                    if ($taxonomy_terms && !is_wp_error($taxonomy_terms)) {
                                        $first_term = reset($taxonomy_terms);
                                        $item[$field] = $first_term->name;
                                    }
                                } else {
                                    $object_taxonomies = get_object_taxonomies($post->post_type);
                                    foreach ($object_taxonomies as $taxonomy_name) {
                                        $taxonomy_terms = get_the_terms($post->ID, $taxonomy_name);
                                        if ($taxonomy_terms && !is_wp_error($taxonomy_terms)) {
                                            $first_term = reset($taxonomy_terms);
                                            $item[$field] = $first_term->name;
                                            break;
                                        }
                                    }
                                }
                                break;
                        }
                    }
                    $newitem[$post->ID] = $item;
                    $upload_dir = wp_upload_dir();
                    $json_dir = $upload_dir['basedir'] . '/json';
                    if (!is_dir($json_dir)) {
                        mkdir($json_dir);
                    }
                    $file_path = $json_dir . '/data-search.json';
                    $existing_data = array();
                    if (file_exists($file_path)) {
                        $existing_data = json_decode(file_get_contents($file_path), true);
                    }
                    $merged_data = foxtool_merged_array($existing_data,$newitem);
                    file_put_contents($file_path, json_encode($merged_data));
                }
            }
        }
}
add_action('wp_insert_post', 'foxtool_add_search_auto_whenpublish');
// lay name tu custom post type
function foxtool_post_type_name($post_type_slug) {
    $post_type_object = get_post_type_object($post_type_slug);
    if ($post_type_object) {
        $post_type_name = $post_type_object->labels->singular_name;
        return $post_type_name; 
    } 
}
// tao mang json
function foxtool_search($page = 1, $posts_per_page = 2000) {
    global $foxtool_search_options;
    if (isset($foxtool_search_options['main-search-posttype'])) {
        if(count($foxtool_search_options['main-search-posttype'])>0){
            foreach ($foxtool_search_options['main-search-posttype'] as $key => $type) {
                $post_types[$type] = array(
                        'type' => $type,
                        'fields' => array(
                            'ID',
                            'title',
                            'url',
                            'thum',
                            'price',
                            'taxonomy'
                        )
                );
            }
        }  
    } 
    $args = array(
        'numberposts' => $posts_per_page,
        'offset'      => ($page - 1) * $posts_per_page,
        'post_type'   => array_keys($post_types),
    );
    $posts = get_posts($args);
    $results = array();
    foreach ($posts as $post) {
        $post_type = $post->post_type;
        if (isset($post_types[$post_type])) {
            $type_info = $post_types[$post_type];
            $type = $type_info['type'];
            $item = array('type' => $type);
            foreach ($type_info['fields'] as $field) {
                switch ($field) {
                    case 'ID':
                        $item[$field] = $post->ID;
                        break;
                    case 'title':
                        $item[$field] = $post->post_title;
                        break;
                    case 'url':
                        $item[$field] = get_permalink($post->ID);
                        break;
                    case 'thum':
                        $item[$field] = get_the_post_thumbnail_url($post->ID);
                        break;
                    case 'price':
                        if ($type === 'product') {
							if (function_exists('wc_get_product')) {
								$product = wc_get_product($post->ID);
								if ($product->is_type('variable')) {
									$prices = [];
									$available_variations = $product->get_available_variations();
									foreach ($available_variations as $variation) {
										$variation_obj = new WC_Product_Variation($variation['variation_id']);
										$prices[] = $variation_obj->get_price();
									}
									if (!empty($prices)) {
										$min_price = min($prices);
										$max_price = max($prices);
										$item[$field] = wc_price($min_price) . ' – ' . wc_price($max_price);
									} else {
										$item[$field] = wc_price($product->get_price());
									}
								} else {
									$item[$field] = wc_price($product->get_price());
								}
							}
						}
                        break;
                    case 'taxonomy':
                        if ($post->post_type == 'product') {
                            $taxonomy_terms = wp_get_post_terms($post->ID, 'product_cat');
                            if ($taxonomy_terms && !is_wp_error($taxonomy_terms)) {
                                $first_term = reset($taxonomy_terms);
                                $item[$field] = $first_term->name;
                            }
                        } else {
                            $object_taxonomies = get_object_taxonomies($post->post_type);
                            foreach ($object_taxonomies as $taxonomy_name) {
                                $taxonomy_terms = get_the_terms($post->ID, $taxonomy_name);
                                if ($taxonomy_terms && !is_wp_error($taxonomy_terms)) {
                                    $first_term = reset($taxonomy_terms);
                                    $item[$field] = $first_term->name;
                                    break;
                                }
                            }
                        }
                        break;
                }
            }
            $results[$post->ID] = $item;
        }
    }
    return $results;
}
// ajax tao file json 
function foxtool_json_file_callback(){
	global $foxtool_search_options;
    check_ajax_referer('foxtool_search_get', 'security');
	if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'foxtool'));
    }
    $page = $_POST['page'];
    $data =  foxtool_search($page);
    if (empty($data)) {
        echo json_encode(array('page' => -1));
        wp_die();
    }
    $upload_dir = wp_upload_dir();
    $json_dir = $upload_dir['basedir'] . '/json';
    if (!is_dir($json_dir)) {
        mkdir($json_dir);
    }
    $file_path = $json_dir . '/data-search.json';
    $existing_data = array();
    if (file_exists($file_path)) {
        $existing_data = json_decode(file_get_contents($file_path), true);
    }
    // Xóa các custom post type không tồn tại trong main-search-posttype
    if (isset($foxtool_search_options['main-search-posttype']) && count($foxtool_search_options['main-search-posttype']) > 0) {
        $allowed_post_types = $foxtool_search_options['main-search-posttype'];
        foreach ($existing_data as $key => $item) {
            if (!in_array($item['type'], $allowed_post_types)) {
                unset($existing_data[$key]);
            }
        }
        $existing_data = array_values($existing_data);
    }
    $merged_data = foxtool_merged_array($existing_data, $data);
    file_put_contents($file_path, json_encode($merged_data));
    $count = count($merged_data);
    echo json_encode(array('page' =>$page+1,'count'=>$count));   
    wp_die();
}
add_action('wp_ajax_foxtool_json_get', 'foxtool_json_file_callback');
// ajax xoa thư mục json
function foxtool_delete_json_folder_callback() {
    check_ajax_referer('foxtool_search_del', 'security');
    if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'foxtool'));
    }
    $upload_dir = wp_upload_dir();
    $json_dir = $upload_dir['basedir'] . '/json';
    if (is_dir($json_dir)) {
        $files = glob("$json_dir/*");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        rmdir($json_dir);
    }
    wp_die();
}
add_action('wp_ajax_foxtool_json_del', 'foxtool_delete_json_folder_callback');
// xu ly du lieu json
function foxtool_merged_array($existing_data, $data) {
    $merged_data = $existing_data;
    foreach ($data as $new_item) {
        $found = false;
        foreach ($merged_data as &$existing_item) {
            if ($existing_item['ID'] == $new_item['ID']) {
                $existing_item = $new_item;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $merged_data[] = $new_item;
        }
    }
    return array_values($merged_data);
}
// duong dan toi json trong plugin
function foxtool_search_url(){
    $upload_dir = wp_upload_dir();
    $json_file = $upload_dir['basedir'] . '/json/data-search.json';
    if (file_exists($json_file)) {
        $absolute_url = $upload_dir['baseurl'] . '/json/data-search.json';
        $relative_url = wp_make_link_relative($absolute_url);
        $random_version = rand(1000, 9999);
        return $relative_url . '?ver=' . $random_version;
    }
    return null;
}
// dua vao website
function foxtool_search_footer(){ 
	global $foxtool_search_options;
	$limit = !empty($foxtool_search_options['main-search-c1']) ? $foxtool_search_options['main-search-c1'] : 10;
	if (isset($foxtool_search_options['main-search-posttype'])) {
		$main_search_post_types = $foxtool_search_options['main-search-posttype'];
		if (in_array('product', $main_search_post_types)) {
			unset($main_search_post_types[array_search('product', $main_search_post_types)]);
			array_unshift($main_search_post_types, 'product');
		}
		$data_types = implode(',', $main_search_post_types);
		$data_labels = [];
		foreach ($main_search_post_types as $id) {
			$data_labels[] = foxtool_post_type_name($id);
		}
		$data_labels_str = implode(',', $data_labels);
	} else {
		$data_types = '';
		$data_labels_str = '';
	}
	?>
		<div class="ft-sbox ft-smodal" id="ft-sbox" style="display:none">
			<form class="ft-sform" action="<?php bloginfo('url'); ?>">
			<?php 
            if (isset($foxtool_search_options['main-search-posttype']) && in_array('product', $foxtool_search_options['main-search-posttype'])) {
				echo '<input type="hidden" name="post_type" value="product">';
			}
			?>
			<input type="text" id="ft-sinput" placeholder="<?php _e('Enter keywords to search', 'foxtool'); ?>" name="s" value="" maxlength="50" required="required">
			<button title="<?php _e('Search', 'foxtool'); ?>" id="ft-ssumit" type="submit">
			<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 42 42"><path fill="currentColor" d="M1 17.838c0 8.747 7.131 15.827 15.94 15.827c8.796 0 15.938-7.08 15.938-15.827S25.736 2 16.94 2C8.131 2 1 9.091 1 17.838zm5.051 0c0-5.979 4.868-10.817 10.89-10.817c6.01 0 10.888 4.839 10.888 10.817c0 5.979-4.878 10.818-10.888 10.818c-6.022 0-10.89-4.84-10.89-10.818zm22.111 14.523l6.855 7.809c1.104 1.102 1.816 1.111 2.938 0l2.201-2.181c1.082-1.081 1.149-1.778 0-2.921l-7.896-6.775l-4.098 4.068z"/></svg>
			</button>
			</form>
			<div class="ft-sbox-main">
				<div id="ft-staxo"></div>
				<ul id="ft-show" data-types="<?php echo esc_attr($data_types); ?>" data-labels="<?php echo esc_attr($data_labels_str); ?>" data-json="<?php echo base64_encode(foxtool_search_url()); ?>" data-full="<?php echo $limit; ?>" data-none="<?php _e("No results found", "foxtool"); ?>"></ul>
			</div>
		</div>
	<?php
	$color = !empty($foxtool_search_options['main-search-c2']) ? '<style>:root{--ftsearch:'. $foxtool_search_options['main-search-c2'] .';}</style>' : NULL;
	$dark = isset($foxtool_search_options['main-search-s1']) && $foxtool_search_options['main-search-s1'] == 'Dark' ? '<style>:root {--ftsearchbg:#121212;--ftsearchbor:#2d2d2d;--ftsearchspan:#191919;--ftsearchsp:#3f3f3f63;--ftsearchimg:#333333;--ftsearchbl:#1c1c1cf2;--ftsearchcolor:#fff;}</style>' : NULL;
	echo $color . $dark;	
}
add_action('wp_footer', 'foxtool_search_footer');
// add css js search web
function foxtool_enqueue_search(){
	wp_enqueue_style('search-css', FOXTOOL_URL . 'link/search/foxsearch.css', array(), FOXTOOL_VERSION);
	wp_enqueue_script('search-js', FOXTOOL_URL . 'link/search/foxsearch.js', array('jquery'), FOXTOOL_VERSION, true);
}
add_action('wp_enqueue_scripts', 'foxtool_enqueue_search');
// shortoce
if (isset($foxtool_search_options['main-search-code1'])){
function foxtool_search_shortcode($atts) {
	global $foxtool_search_options;
	$color = !empty($foxtool_search_options['main-search-code2']) ? 'style="fill:' . $foxtool_search_options['main-search-code2'] .';"' : NULL;
	$icon = '<span class="ftopensearch" style="display:flex;justify-content:center;align-items:center;"><svg xmlns="http://www.w3.org/2000/svg" width="25px" height="25px" viewBox="0 0 26 26"><path '. $color .' fill="currentColor" d="M10 .188A9.812 9.812 0 0 0 .187 10A9.812 9.812 0 0 0 10 19.813c2.29 0 4.393-.811 6.063-2.125l.875.875a1.845 1.845 0 0 0 .343 2.156l4.594 4.625c.713.714 1.88.714 2.594 0l.875-.875a1.84 1.84 0 0 0 0-2.594l-4.625-4.594a1.824 1.824 0 0 0-2.157-.312l-.875-.875A9.812 9.812 0 0 0 10 .188zM10 2a8 8 0 1 1 0 16a8 8 0 0 1 0-16zM4.937 7.469a5.446 5.446 0 0 0-.812 2.875a5.46 5.46 0 0 0 5.469 5.469a5.516 5.516 0 0 0 3.156-1a7.166 7.166 0 0 1-.75.03a7.045 7.045 0 0 1-7.063-7.062c0-.104-.005-.208 0-.312z"/></svg></span>';
	return $icon;
}
add_shortcode('foxsearch', 'foxtool_search_shortcode');
}
}