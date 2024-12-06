<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $foxtool_gindex_options;
function foxtool_require_google_api_index() {
    require_once( FOXTOOL_DIR . 'link/google-api/vendor/autoload.php');
}
// check link eror
function foxtool_valid_url($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}
// quan ly json index api
function foxtool_jsonapi_index() {
    global $foxtool_gindex_options;
    foxtool_require_google_api_index();
    $jsonStrings = array(); 
    if (is_array($foxtool_gindex_options) || is_object($foxtool_gindex_options)) {
        foreach ($foxtool_gindex_options as $key => $value) {
            if (preg_match('/^json(\d+)$/', $key, $matches)) {
                $n = $matches[1];
                $jsonStrings[] = sanitize_text_field($value);
            }
        }
    }
    if (empty($jsonStrings)) {
        return false; 
    }
    $randomIndex = array_rand($jsonStrings);
    $selectedJsonString = $jsonStrings[$randomIndex];
    $jsonObject = json_decode($selectedJsonString, true);
    if ($jsonObject === null) {
        return false;
    }
    $client = new Google_Client();
    $client->setAuthConfig($jsonObject);
    $client->addScope('https://www.googleapis.com/auth/indexing');
    return $client->authorize();
}
// xu ly index now and del index
function foxtool_index_now($urls, $action) {
    $result = [];
    $type = $action == 'delete' ? 'URL_DELETED' : 'URL_UPDATED';
    $httpClient = foxtool_jsonapi_index();
	
	if (!$httpClient) {
        $result[] = array(
            'result' => 'error',
            'error' => 'Failed to initialize Google API client'
        );
        return $result;
    }
	
    foreach ($urls as $url) {
        $data = [
            'result' => 'success'
        ];
        if (!foxtool_valid_url($url)) {
            $data['result'] = 'error';
            $data['error'] = 'Invalid URL: ' . $url;
            $result[] = $data;
            continue;
        }
        $endpoint = 'https://indexing.googleapis.com/v3/urlNotifications:publish';
        try {
            if ($action == 'get') {
                $response = $httpClient->get('https://indexing.googleapis.com/v3/urlNotifications/metadata?url=' . urlencode($url));
            } else {
                $content = json_encode([
                    'url' => $url,
                    'type' => $type
                ]);
                $response = $httpClient->post($endpoint, ['body' => $content]);
            }
            $data['body'] = (string) $response->getBody();
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $data['result'] = 'error';
            $data['error'] = 'HTTP Error ' . $statusCode . ': ' . $e->getMessage();
        } catch (\Exception $e) {
            $data['result'] = 'error';
            $data['error'] = $e->getMessage();
        }
        $result[] = $data;
    }
    return $result;
}
// Xử lý ajax index now and del index
function foxtool_index_now_callback() {
    if (!wp_verify_nonce($_POST['ajax_nonce'], 'foxtool_index_now_nonce')) {
        wp_die('Invalid nonce');
    }
    $urls = explode("\n", $_POST['url']); 
    $action = $_POST['ajax_action'];
    // Chia nhỏ mảng URL thành các phần có tối đa 200 URL mỗi phần
    $chunks = array_chunk($urls, 200);
    // Duyệt qua từng phần và xử lý
    foreach ($chunks as $chunk) {
        $result = foxtool_index_now($chunk, $action);
        foreach ($result as $item) {
            if (array_key_exists('body', $item)) {
                $data = json_decode($item['body'], true);
                if ($data && isset($data['urlNotificationMetadata'])) {
                    $latest_update = $data['urlNotificationMetadata']['latestUpdate'] ?? null;
                    $latest_remove = $data['urlNotificationMetadata']['latestRemove'] ?? null;
                    // Xác định trạng thái dựa trên dữ liệu
                    if ($latest_update || $latest_remove) {
                        $latest_update_time = strtotime($latest_update['notifyTime'] ?? '');
                        $latest_remove_time = strtotime($latest_remove['notifyTime'] ?? '');
                        if ($latest_update_time > $latest_remove_time) {
                            $url = $data['urlNotificationMetadata']['url'];
                            $status = __('Already declared', 'foxtool');
                            $time = date('Y-m-d H:i:s', $latest_update_time);
                            $class = '';
                        } else {
                            $url = $data['urlNotificationMetadata']['url'];
                            $status = __('Declaration already deleted', 'foxtool');
                            $time = date('Y-m-d H:i:s', $latest_remove_time);
                            $class = 'ft-index-del';
                        }
                        // Hiển thị thông tin
                        echo '<div class="ft-index '. $class .'">';
                        echo __('URL:', 'foxtool') .' '. $url .'<br>';
                        echo __('Status:', 'foxtool') .' '. $status .'<br>';
                        echo __('Time:', 'foxtool') .' '. $time;
                        echo '</div>';
                        foxtool_index_use_count(); // count user
                    } else {
                        // Thông báo nếu chỉ có 'success' mà không có chi tiết update/remove
                        echo '<div class="ft-index">';
                        echo __('Notification link sent', 'foxtool');
                        echo '</div>';
                    }
                } else {
                    // Xử lý nếu dữ liệu trả về không hợp lệ hoặc không có thông tin
                    echo '<div class="ft-index ft-index-er">';
                    echo __('URL: does not exist', 'foxtool');
                    echo '</div>';
                }
            } else {
                // Xử lý khi thiếu khóa 'body'
                echo '<div class="ft-index ft-index-er">';
                echo __('URL: does not exist', 'foxtool');
                echo '</div>';
            }
        }
    }
    wp_die();
}
add_action('wp_ajax_foxtool_index_now_ajax', 'foxtool_index_now_callback');

// xy ly index status
function foxtool_index_status($urls) {
    $result = [];
    $httpClient = foxtool_jsonapi_index(); 
    foreach ($urls as $url) {
        $data = [
            'url' => $url,
            'indexed' => null,
            'latest_update' => null, 
            'latest_remove' => null, 
        ];
        if (!foxtool_valid_url($url)) {
            $data['result'] = 'error';
            $data['error'] = 'Invalid URL: ' . $url;
            $result[] = $data;
            continue;
        }
        try {
            $response = $httpClient->get('https://indexing.googleapis.com/v3/urlNotifications/metadata?url=' . urlencode($url));
            $response_body = json_decode($response->getBody(), true);
            if (isset($response_body['latestUpdate'])) {
                $data['latest_update'] = $response_body['latestUpdate'];
            }
            if (isset($response_body['latestRemove'])) {
                $data['latest_remove'] = $response_body['latestRemove'];
            }
            if (isset($response_body['latestUpdate']) || isset($response_body['latestRemove'])) {
                $data['indexed'] = 'yes'; 
            } else {
                $data['indexed'] = 'no'; 
            }
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            if ($statusCode == 404) {
                $data['indexed'] = null;
            } else {
                $data['indexed'] = 'error'; 
                $data['error'] = 'HTTP Error ' . $statusCode . ': ' . $e->getMessage();
            }
        } catch (\Exception $e) {
            $data['indexed'] = 'error';
            $data['error'] = $e->getMessage();
        }
        $result[] = $data;
    }
    return $result;
}
// ajax index status
function foxtool_index_status_callback() {
    if (!wp_verify_nonce($_POST['ajax_nonce'], 'foxtool_index_status_nonce')) {
        wp_die('Invalid nonce');
    }
    $urls = explode("\n", $_POST['url']); 
    // Chia nhỏ mảng URL thành các phần có tối đa 200 URL mỗi phần
    $chunks = array_chunk($urls, 200);
    // Duyệt qua từng phần và xử lý
    foreach ($chunks as $chunk) {
        $url_metadata = foxtool_index_status($chunk); 
        foreach ($url_metadata as $data) {
            $url = isset($data['url']) ? $data['url'] : '';
            $latest_update = isset($data['latest_update']) ? $data['latest_update'] : null;
			$latest_remove = isset($data['latest_remove']) ? $data['latest_remove'] : null;
            if ($data['indexed'] == 'yes') {
                if ($latest_update || $latest_remove) {
                    $latest_update_time = !empty($latest_update['notifyTime']) ? strtotime($latest_update['notifyTime']) : null;
					$latest_remove_time = !empty($latest_remove['notifyTime']) ? strtotime($latest_remove['notifyTime']) : null;
                    if ($latest_update_time > $latest_remove_time) {
                        $status = __('Already declared', 'foxtool');
                        $time = date('Y-m-d H:i:s', $latest_update_time);
                        $class = '';
                    } else {
                        $status = __('Declaration already deleted', 'foxtool');
                        $time = date('Y-m-d H:i:s', $latest_remove_time);
                        $class = 'ft-index-del';
                    }
                }
                // Hiển thị thông tin
                echo '<div class="ft-index '. $class .'">';
                echo __('URL:', 'foxtool') .' '. $url .'<br>';
                echo __('Status:', 'foxtool') .' '. $status .'<br>';
                echo __('Time:', 'foxtool') .' '. $time;
                echo '</div>';
                foxtool_index_use_count(); // count user
            } else {
                echo '<div class="ft-index ft-index-er">';
                echo __('URL: Unverified', 'foxtool');
                echo '</div>';
            }
        }
    }
    wp_die();
}
add_action('wp_ajax_foxtool_index_status_ajax', 'foxtool_index_status_callback');

// ham cap nhat count
function foxtool_index_use_count() {
    $count = get_transient('foxtool_index_count');
    if (false === $count) {
        $count = 0;
    }
    $count++;
    set_transient('foxtool_index_count', $count, 86400);
}
// tao nut index
function foxtool_quick_index_button($actions, $post) {
	global $foxtool_gindex_options;
	if (isset($foxtool_gindex_options['posttype']) && in_array($post->post_type, $foxtool_gindex_options['posttype'])) {
		if ($post->post_status == 'publish') {
			$actions['index'] = sprintf(
				'<a id="foxindex-%1$s" class="foxindex-btn" data-id="%1$s" data-link="%2$s" href="#" rel="permalink">%3$s</a>',
				$post->ID,
				esc_url(get_permalink($post->ID)),
				__('Index now', 'foxtool')
			);
		}
	}
    return $actions;
}
if(isset($foxtool_gindex_options['posttype'])){
	$main_search_post_types = $foxtool_gindex_options['posttype'];
	foreach ($main_search_post_types as $post_type) {
		$hook_name = $post_type .'_row_actions';
		add_filter($hook_name, 'foxtool_quick_index_button', 10, 2);
		add_filter($hook_name, 'foxtool_quick_index_button', 10, 2);
	}
}
// index ajax o edit
function foxtool_index_post_ajax() {
    if (!wp_verify_nonce($_POST['nonce'], 'foxtool_index_now_nonce')) {
        wp_send_json_error('Invalid nonce');
    }
    $post_id = intval($_POST['post_id']);
    $url = esc_url($_POST['url']);
    if (!$url || !$post_id) {
        wp_send_json_error('Invalid URL or Post ID');
    }
    $result = foxtool_index_now([$url], 'update');
    if (!empty($result[0]['error'])) {
        wp_send_json_error($result[0]['error']);
    }
	foxtool_index_use_count(); // count user
    wp_send_json_success(__('Success', 'foxtool'));
}
add_action('wp_ajax_foxtool_index_post', 'foxtool_index_post_ajax');
// js xu ly index
function foxtool_enqueue_scripts() {
    wp_enqueue_script('jquery');
    ?>
    <script type="text/javascript">
	jQuery(document).ready(function($) {
		$('.foxindex-btn').on('click', function(e) {
			e.preventDefault();
			var postId = $(this).data('id');
			var url = $(this).data('link');
			var button = $(this);
			$.ajax({
				url: ajaxurl, 
				method: 'POST',
				data: {
					action: 'foxtool_index_post',
					post_id: postId,
					url: url,
					nonce: '<?php echo wp_create_nonce('foxtool_index_now_nonce'); ?>'
				},
				beforeSend: function() {
					button.text('<?php _e('Wait...', 'foxtool'); ?>'); 
				},
				success: function(response) {
					if (response.success) {
						button.text('<?php _e('Success', 'foxtool'); ?>'); // Thành công
					} else {
						button.text('<?php _e('Error', 'foxtool'); ?>'); 
					}
				},
				error: function() {
					button.text('<?php _e('Error', 'foxtool'); ?>');
				}
			});
		});
	});
    </script>
    <?php
}
add_action('admin_footer', 'foxtool_enqueue_scripts');
