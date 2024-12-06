<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } 
global $foxtool_options;
# tu dong dat ten tu dong khi tai len
if(isset($foxtool_options['media-title1'])){
function foxtool_custom_upload_filename($file) {
    $filename = $file['name'];
    $info = pathinfo($filename);
    $ext = !empty($info['extension']) ? '.' . $info['extension'] : '';
    $domain = str_replace('.', '-', parse_url(get_bloginfo('url'), PHP_URL_HOST));
    $base_name = $domain . $ext;
    $upload_dir = wp_upload_dir();
    $unique_filename = wp_unique_filename($upload_dir['path'], $base_name);
    $file['name'] = $unique_filename;
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'foxtool_custom_upload_filename');
}
# bộ lọc ngăn crop hình ảnh tải lên
if(isset($foxtool_options['media-up1'])){
function foxtool_remove_all_image_sizes($sizes) {
    return array();
}
add_filter('intermediate_image_sizes_advanced', 'foxtool_remove_all_image_sizes');
}
// tat cac crop khac nhau
function foxtool_block_specific_image_size($sizes) {
    global $_wp_additional_image_sizes, $foxtool_options;
    if(isset($foxtool_options['main-img']) && !isset($foxtool_options['media-up1'])) {
        foreach ($foxtool_options['main-img'] as $size) {
            unset($sizes[$size]);
        }
    }
    return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'foxtool_block_specific_image_size');
// tim kiem va xoa anh thu nho
function foxtool_delete_thumbnails($thumbnail_name, $limit = 500) {
    $attachments = get_posts(array(
        'post_type' => 'attachment',
        'post_status' => 'any',
        'numberposts' => -1,
    ));
    $deleted_count = 0; // Biến đếm số lượng hình đã xóa
    foreach ($attachments as $attachment) {
        if ($deleted_count >= $limit) { // Kiểm tra xem đã đạt tới giới hạn chưa
            break; // Nếu đã đạt tới giới hạn thì thoát khỏi vòng lặp
        }
        $attachment_id = $attachment->ID;
        $deleted_count += foxtool_delete_all_thumbnails($attachment_id, $thumbnail_name);
    }
	return $deleted_count; 
}
function foxtool_delete_all_thumbnails($attachment_id, $thumbnail_name) {
    $deleted_count = 0; // Biến đếm số lượng hình đã xóa
    $metadata = wp_get_attachment_metadata($attachment_id);
    if (isset($metadata['sizes'])) {
        $image_sizes = $metadata['sizes'];
        foreach ($image_sizes as $size => $data) {
            if ($size === $thumbnail_name) { // Kiểm tra tên của hình thu nhỏ
                $file_path = wp_get_upload_dir()['basedir'] . '/' . dirname($metadata['file']) . '/' . $data['file'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                    $deleted_count++; // Tăng biến đếm khi xóa hình thành công
                }
                unset($metadata['sizes'][$size]);
            }
        }
        wp_update_attachment_metadata($attachment_id, $metadata);
    }
    return $deleted_count; // Trả về số lượng hình đã xóa
}
# han che tai len file 
if (isset($foxtool_options['media-up2']) && !empty($foxtool_options['media-up21'])){ 
function foxtool_change_upload_size(){
    global $foxtool_options;
    $mlimit_mb = !empty($foxtool_options['media-up21']) ? $foxtool_options['media-up21'] : 1; // Giới hạn mặc định 1MB
    $mlimit_kb = $mlimit_mb * 1024 * 1024; 
    return $mlimit_kb;
}
add_filter('upload_size_limit', 'foxtool_change_upload_size');
}
# Cho phép tải lên file SVG
if(isset($foxtool_options['media-up3'])){
function foxtool_allow_svg_upload( $mimes ) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter( 'upload_mimes', 'foxtool_allow_svg_upload' );
function foxtool_fix_svg_thumb_display() {
    echo '
        <style type="text/css">
            td.media-icon img[src$=".svg"], img[src$=".svg"].attachment-post-thumbnail {
                width: 100% !important;
                height: auto !important;
            }
        </style>
    ';
}
add_action( 'admin_head', 'foxtool_fix_svg_thumb_display' );
}
# cho phep tai len anh jfif
if(isset($foxtool_options['media-up4'])){
function foxtool_allow_jfif_upload( $mime_types ) {
    $mime_types['jfif'] = 'image/jfif';
    $mime_types['jpe'] = 'image/jpe';
    return $mime_types;
}
add_filter( 'upload_mimes', 'foxtool_allow_jfif_upload' );
}
# chuyen anh 8bit qua 32bit de xu ly
$foxtool_keypng = [
    'media-zip1',
    'media-webp1',
    'media-avif2',
    'media-zip2',
	'media-cutop1',
    'media-logo1',
];
if (array_filter($foxtool_keypng, fn($key) => isset($foxtool_options[$key])) && !isset($foxtool_options['media-png-8'])) {
	function foxtool_png_8bit_to_32bit($file) {
    $file_type = $file['type'];
    if ($file_type === 'image/png') {
        $image = imagecreatefrompng($file['tmp_name']);
        $bit_depth = imageistruecolor($image) ? 32 : 8;  
        if ($bit_depth === 8) {
            $width = imagesx($image);
            $height = imagesy($image);
            $image_32bit = imagecreatetruecolor($width, $height); 
            imagealphablending($image_32bit, false);
            imagesavealpha($image_32bit, true);
            $transparent_index = imagecolortransparent($image);
            if ($transparent_index >= 0) {
                $transparent_color = imagecolorsforindex($image, $transparent_index);
                $alpha_color = imagecolorallocatealpha(
                    $image_32bit, 
                    $transparent_color['red'], 
                    $transparent_color['green'], 
                    $transparent_color['blue'], 
                    127 
                );
                imagefill($image_32bit, 0, 0, $alpha_color); 
            } else {
                $alpha_color = imagecolorallocatealpha($image_32bit, 0, 0, 0, 127); 
                imagefill($image_32bit, 0, 0, $alpha_color); 
            }
            imagecopy($image_32bit, $image, 0, 0, 0, 0, $width, $height);
            imagepng($image_32bit, $file['tmp_name']);
            imagedestroy($image_32bit);
        }
        imagedestroy($image);
    }
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'foxtool_png_8bit_to_32bit');
}
# nén hình anh jpg khi tai len
if(isset($foxtool_options['media-zip1']) && (!isset($foxtool_options['media-webp1']) && !isset($foxtool_options['media-avif2']))){
function foxtool_image_compression($file) {
    global $foxtool_options;
    $image_type = exif_imagetype($file['tmp_name']);
    if ($image_type === IMAGETYPE_JPEG) {
		$compression_quality = !empty($foxtool_options['media-zip11']) ? $foxtool_options['media-zip11'] : 60; // Mức nén
        $image = imagecreatefromjpeg($file['tmp_name']);
        imagejpeg($image, $file['tmp_name'], $compression_quality);
        imagedestroy($image);
    } 
	// chuyển png sang jpg
	else if ($image_type === IMAGETYPE_PNG && isset($foxtool_options['media-zip12'])) {
        $image = imagecreatefrompng($file['tmp_name']);
		$bit_depth = imageistruecolor($image) ? 32 : 8;
		if ($bit_depth !== 8) {
			$compression_quality = !empty($foxtool_options['media-zip11']) ? $foxtool_options['media-zip11'] : 60; // Mức nén
			imagejpeg($image, $file['tmp_name'], $compression_quality);
		}
		imagedestroy($image);
    }
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'foxtool_image_compression');
}
# chuyen anh jpg, png sang webp
function foxtool_convert_to_webp($upload) {
    $image_path = $upload['file'];
	global $foxtool_options;
	$compression_quality = !empty($foxtool_options['media-webp11']) ? $foxtool_options['media-webp11'] : 60; // Mức nén
    $supported_mime_types = array(
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    );
    $image_info = getimagesize($image_path);
    if ($image_info !== false && array_key_exists($image_info['mime'], $supported_mime_types)) {
        $image = imagecreatefromstring(file_get_contents($image_path));
        if ($image) {
            // Kiểm tra xem ảnh có phải là truecolor hay không
            if (imageistruecolor($image)) {
                // Tạo tên file WebP ban đầu
				$webp_path = preg_replace('/\.(jpg|jpeg|png)$/', '.webp', $image_path);
				// Tách đường dẫn thư mục và tên file
				$upload_dir = wp_upload_dir();
				$file_dir = dirname($webp_path);
				$file_name = basename($webp_path);
				// Sử dụng wp_unique_filename để đảm bảo tên file là duy nhất
				$unique_file_name = wp_unique_filename($upload_dir['path'], $file_name);
				$unique_webp_path = $file_dir . '/' . $unique_file_name;
                
				imagewebp($image, $unique_webp_path, $compression_quality);
                $upload['file'] = $unique_webp_path;
                $upload['type'] = 'image/webp';
				// xóa ảnh góc
				unlink($image_path);
            } else {
            // Nếu là ảnh 8-bit, bỏ qua không nén
                $upload['file'] = $image_path;
                $upload['type'] = $image_info['mime'];
            }
        }
    }
    return $upload;
}
if(isset($foxtool_options['media-webp1']) && !isset($foxtool_options['media-avif2'])){
function foxtool_convert_to_webp_upload($upload) {
    $upload = foxtool_convert_to_webp($upload); 
    return $upload;
}
add_filter('wp_handle_upload', 'foxtool_convert_to_webp_upload');
}
# chuyen anh jpg, png sang avif
// cho phep avif wp
if(isset($foxtool_options['media-avif1'])){
function foxtool_add_avif_support( $mime_types ) {
    $mime_types['avif'] = 'image/avif';
    $mime_types['avifs'] = 'image/avif-sequence';
    return $mime_types;
}
add_filter( 'upload_mimes', 'foxtool_add_avif_support' );
// check GD ho tro avif
function check_gd_avif_support() {
    if (function_exists('gd_info')) {
        $gd_info = gd_info();
        if (!isset($gd_info['AVIF Support']) || !$gd_info['AVIF Support']) {
            echo '<div class="notice notice-error is-dismissible"><p>'. __('GD Library does not support AVIF images', 'foxtool'). '</p></div>';
        }
    } else {
        echo '<div class="notice notice-error is-dismissible"><p>'. __('GD Library is not installed', 'foxtool'). '</p></div>';
    }
}
add_action('admin_notices', 'check_gd_avif_support');
}
function foxtool_convert_to_avif($upload) {
	if (!function_exists('imageavif')) {
	  return $upload; 
	}
    $image_path = $upload['file'];
	global $foxtool_options;
	$compression_quality = !empty($foxtool_options['media-avif21']) ? $foxtool_options['media-avif21'] : 60; // mức nén
    $supported_mime_types = array(
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    );
    $image_info = getimagesize($image_path);
    if ($image_info !== false && array_key_exists($image_info['mime'], $supported_mime_types)) {
        if (function_exists('imagecreatefromavif')) {
            $image = imagecreatefromstring(file_get_contents($image_path));
            if ($image) {
                if (imageistruecolor($image)) {
                    // Tạo tên file AVIF ban đầu
					$avif_path = preg_replace('/\.(jpg|jpeg|png)$/', '.avif', $image_path);
					// Tách đường dẫn thư mục và tên file
					$upload_dir = wp_upload_dir();
					$file_dir = dirname($avif_path);
					$file_name = basename($avif_path);
					// Sử dụng wp_unique_filename để đảm bảo tên file là duy nhất
					$unique_file_name = wp_unique_filename($upload_dir['path'], $file_name);
					$unique_avif_path = $file_dir . '/' . $unique_file_name;
					
                    imageavif($image, $unique_avif_path, $compression_quality);
                    $upload['file'] = $unique_avif_path;
                    $upload['type'] = 'image/avif';
                    // Xóa ảnh gốc
                    unlink($image_path);
                } else {
                    // Nếu là ảnh 8-bit, bỏ qua không nén
                    $upload['file'] = $image_path;
                    $upload['type'] = $image_info['mime'];
                }
            }
        } 
    }
    return $upload;
}
if(isset($foxtool_options['media-avif2'])){
function foxtool_convert_to_avif_upload($upload) {
    $upload = foxtool_convert_to_avif($upload); 
    return $upload;
}
add_filter('wp_handle_upload', 'foxtool_convert_to_avif_upload');
}
# giới hạn kích thước ảnh png khi tải lên
if(isset($foxtool_options['media-zip2'])){
function foxtool_image_resize($file) {
    global $foxtool_options;
    $image_type = exif_imagetype($file['tmp_name']);
    $allowed_formats = array(
        IMAGETYPE_PNG,
        IMAGETYPE_JPEG,
		IMAGETYPE_WEBP,
    );
	
	// Check for AVIF support
    if (function_exists('imagecreatefromavif') && function_exists('imageavif')) {
        $allowed_formats[] = IMAGETYPE_AVIF;
    }
	
    if (in_array($image_type, $allowed_formats)) {
        $image = null;
        if ($image_type === IMAGETYPE_PNG) {
            $image = imagecreatefrompng($file['tmp_name']);
        } elseif ($image_type === IMAGETYPE_JPEG) {
            $image = imagecreatefromjpeg($file['tmp_name']);
		} elseif ($image_type === IMAGETYPE_WEBP) {
            $image = imagecreatefromwebp($file['tmp_name']);
        } elseif ($image_type === IMAGETYPE_AVIF) {
            $image = imagecreatefromavif($file['tmp_name']);
        }
        if ($image) {
			// bo qua neu anh 8bit
			if (!imageistruecolor($image)) {
                imagedestroy($image); 
                return $file; 
            }
			
			
            $width = imagesx($image);
            $height = imagesy($image);
            $max_width = !empty($foxtool_options['media-zip21']) ? $foxtool_options['media-zip21'] : $width;
            $max_height = !empty($foxtool_options['media-zip22']) ? $foxtool_options['media-zip22'] : $height;
			
			// bo qua neu anh có kich thuoc nho hon
			if ($width <= $max_width && $height <= $max_height) {
                imagedestroy($image);
                return $file; 
            }
			
            $new_width = $width;
            $new_height = $height;

            if ($width > $max_width || $height > $max_height) {
                $ratio = min($max_width / $width, $max_height / $height);
                $new_width = intval($width * $ratio);
                $new_height = intval($height * $ratio);
            }
            $new_image = imagecreatetruecolor($new_width, $new_height);
            if ($image_type === IMAGETYPE_PNG) {
                imagealphablending($new_image, false);
                imagesavealpha($new_image, true);
                $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
                imagefilledrectangle($new_image, 0, 0, $new_width, $new_height, $transparent);
                imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagepng($new_image, $file['tmp_name']);
            } elseif ($image_type === IMAGETYPE_JPEG) {
                imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagejpeg($new_image, $file['tmp_name']);
            } elseif ($image_type === IMAGETYPE_WEBP) {
                imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagewebp($new_image, $file['tmp_name']);
            } elseif ($image_type === IMAGETYPE_AVIF) {
                imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imageavif($new_image, $file['tmp_name']);
            }
            imagedestroy($image);
            imagedestroy($new_image);
        }
    }
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'foxtool_image_resize');
}
# khung thoi ma
function foxtool_toRGBA($mau) {
    if (strpos($mau, 'rgba') === 0) {
        $rgba_values = sscanf($mau, "rgba(%d, %d, %d, %f)");
        $alpha = 127 - round($rgba_values[3] * 127); 
        return [$rgba_values[0], $rgba_values[1], $rgba_values[2], $alpha];
    } elseif (strpos($mau, 'rgb') === 0) {
        $rgb_values = sscanf($mau, "rgb(%d, %d, %d)");
        return array_merge($rgb_values, [0]); 
    } elseif (strpos($mau, '#') === 0) {
        if (strlen($mau) == 7) {
            list($r, $g, $b) = sscanf($mau, "#%02x%02x%02x");
            return [$r, $g, $b, 0]; 
        } elseif (strlen($mau) == 9) {
            list($r, $g, $b, $a) = sscanf($mau, "#%02x%02x%02x%02x");
            $alpha = 127 - round($a / 255 * 127); 
            return [$r, $g, $b, $alpha];
        }
    }
    return [0, 0, 0, 0];
}
if (isset($foxtool_options['media-cutop1'])){
function foxtool_add_borderimg($param1, $param2 = null) {
    global $foxtool_options;
    // Kiểm tra và lấy attachment_id và original_file
    if (is_null($param2)) {
        $attachment_id = $param1;
        $original_file = get_attached_file($attachment_id);
        $metadata = wp_generate_attachment_metadata($attachment_id, $original_file);
    } else {
        $metadata = $param1;
        $attachment_id = $param2;
        $original_file = get_attached_file($attachment_id);
    }
    // Kiểm tra và lấy đường dẫn khung
    if (!empty($foxtool_options['media-cutop11']) && empty($foxtool_options['media-cutop12'])) {
        $logo = esc_url(FOXTOOL_URL . 'img/khung/' . $foxtool_options['media-cutop11'] . '.png');
    } elseif (!empty($foxtool_options['media-cutop12'])) {
        $logo = esc_url($foxtool_options['media-cutop12']);
    } else {
        $logo = esc_url(FOXTOOL_URL . 'img/khung/1.png');
    }
    // Kiểm tra xem logo có hợp lệ không
    $headers = @get_headers($logo);
    if (!$headers || strpos($headers[0], '200') === false) {
        return $metadata;
    }
    // Kiểm tra phần mở rộng của file ảnh
    $extension = pathinfo($logo, PATHINFO_EXTENSION);
    if (!in_array($extension, ['png', 'jpg', 'jpeg'])) {
        return $metadata;
    }
    $watermark_path = $logo;
    // Thêm khung vào ảnh gốc nếu tệp tồn tại
    if ($original_file && file_exists($original_file)) {
        foxtool_apply_add_borderimg($original_file, $watermark_path);
    }
    // Thêm khung vào tất cả các kích thước ảnh khác nếu không phải là add_attachment
    if (!is_null($param2)) {
        foreach ($metadata['sizes'] as $size => $data) {
            $resized_file = str_replace(basename($original_file), $data['file'], $original_file);
            // Kiểm tra xem tệp đã resized có tồn tại không
            if ($resized_file && file_exists($resized_file)) {
                foxtool_apply_add_borderimg($resized_file, $watermark_path);
            }
        }
    }
    return $metadata;
}
// Hàm để áp dụng khung cho ảnh cụ thể
function foxtool_apply_add_borderimg($file, $watermark_path) {
	global $foxtool_options;
    $mime_type = wp_check_filetype($file)['type'];
    if (strpos($mime_type, 'image') !== false) {
        $image = false;
        switch ($mime_type) {
            case 'image/webp':
                $image = @imagecreatefromwebp($file);
                break;
            case 'image/jpeg':
            case 'image/jpg':
                $image = @imagecreatefromjpeg($file);
                break;
            case 'image/png':
                $image = @imagecreatefrompng($file);
                break;
            case 'image/avif':
                if (function_exists('imagecreatefromavif') && function_exists('imageavif')) {
                    $image = @imagecreatefromavif($file);
                }
                break;
        }

        if ($image === false) {
            return;
        }
        // Kích thước ảnh
        $image_width = imagesx($image);
        $image_height = imagesy($image);
		// Kiểm tra xem ảnh có dưới 12-bit không
        if (function_exists('imageistruecolor') && !imageistruecolor($image)) {return;}
        $image_width = imagesx($image);
        $image_height = imagesy($image);
		if (isset($foxtool_options['media-cutop13'])){
			imageflip($image, IMG_FLIP_HORIZONTAL);
		}
		// Tải watermark (border image)
		$watermark = imagecreatefrompng($watermark_path);
		$watermark_width = imagesx($watermark);
		$watermark_height = imagesy($watermark);
		// Tạo watermark mới có kích thước khớp với ảnh upload lên
		$resized_watermark = imagecreatetruecolor($image_width, $image_height);
		imagealphablending($resized_watermark, false);
		imagesavealpha($resized_watermark, true);
		// Thay đổi kích thước watermark để khớp với kích thước ảnh
		imagecopyresampled(
			$resized_watermark, $watermark, 0, 0, 0, 0, 
			$image_width, $image_height, $watermark_width, $watermark_height
		);
		if (!empty($foxtool_options['media-cutop14']) && $foxtool_options['media-cutop14'] != 100) {
			$opacity = $foxtool_options['media-cutop14']; 
			$watermark_width = intval($image_width);
			$watermark_height = intval($image_height);
			// Duyệt qua từng pixel của watermark để điều chỉnh độ trong suốt
			for ($x = 0; $x < $watermark_width; $x++) {
				for ($y = 0; $y < $watermark_height; $y++) {
					if ($x >= 0 && $x < imagesx($resized_watermark) && $y >= 0 && $y < imagesy($resized_watermark)) {
						$color = imagecolorsforindex($resized_watermark, imagecolorat($resized_watermark, $x, $y));
						$alpha = min(127, intval($color['alpha'] + (127 * (100 - $opacity) / 100)));
						$new_color = imagecolorallocatealpha($resized_watermark, $color['red'], $color['green'], $color['blue'], $alpha);
						imagesetpixel($resized_watermark, $x, $y, $new_color);
					}
				}
			}
		}
		// lớp phu mau del
		if (!empty($foxtool_options['media-cutop-c1'])) {
			$mau = $foxtool_options['media-cutop-c1']; 
			list($r, $g, $b, $alpha) = foxtool_toRGBA($mau);
			$alpha = min($alpha + 70, 127);
			$overlay = imagecreatetruecolor($image_width, $image_height);
			$color = imagecolorallocatealpha($overlay, $r, $g, $b, $alpha);
			imagefill($overlay, 0, 0, $color);
			imagecopy($image, $overlay, 0, 0, 0, 0, $image_width, $image_height);
		}
		imagecopy($image, $resized_watermark, 0, 0, 0, 0, $image_width, $image_height);
		if ($mime_type === 'image/webp') {
            imagewebp($image, $file);
        } elseif ($mime_type == 'image/jpeg') {
            imagejpeg($image, $file);
        } elseif ($mime_type == 'image/png') {
            imagepng($image, $file);
        } elseif ($mime_type == 'image/avif'){
			imageavif($image, $file);
		}
		// Giải phóng bộ nhớ
		imagedestroy($image);
		imagedestroy($resized_watermark);
		imagedestroy($watermark);
		// lop phu del
		if (!empty($foxtool_options['media-cutop-c1'])) {
			imagedestroy($overlay); 
		}
    }
}
if (isset($foxtool_options['media-cutop-hook']) && !isset($foxtool_options['media-png-8'])){
	add_filter('wp_generate_attachment_metadata', 'foxtool_add_borderimg', 10, 2);
} else {
	add_action('add_attachment', 'foxtool_add_borderimg');
}
}
# Hàm thêm watermark cho hinh anh tai len
if(isset($foxtool_options['media-logo1'])){
function foxtool_add_watermark($param1, $param2 = null) {
    global $foxtool_options;
    // Kiểm tra và lấy attachment_id và original_file
    if (is_null($param2)) {
        $attachment_id = $param1;
        $original_file = get_attached_file($attachment_id);
        $metadata = wp_generate_attachment_metadata($attachment_id, $original_file);
    } else {
        $metadata = $param1;
        $attachment_id = $param2;
        $original_file = get_attached_file($attachment_id);
    }
    // Kiểm tra tùy chọn watermark
    if (empty($foxtool_options['media-logo10']) && empty($foxtool_options['media-logo11'])) {
        return $metadata;
    }
    $logo = '';
    $logotext = '';
    // Kiểm tra logo
    if (!empty($foxtool_options['media-logo11'])) {
        $logo = $foxtool_options['media-logo11'];
        $headers = @get_headers($logo);
        if (!$headers || strpos($headers[0], '200') === false) {
            $logo = ''; 
        }

        // Kiểm tra phần mở rộng của file ảnh
        $extension = pathinfo($logo, PATHINFO_EXTENSION);
        if (!in_array($extension, ['png', 'jpg', 'jpeg'])) {
            $logo = ''; 
        }
    }
    // Xử lý text watermark nếu không có logo
    if (empty($logo) && !empty($foxtool_options['media-logo10'])) {
        $logotext = $foxtool_options['media-logo10'];
    }
    // Nếu cả logo và text đều rỗng thì return metadata
    if (empty($logo) && empty($logotext)) {
        return $metadata;
    }
    // Gán đường dẫn watermark hoặc xử lý text watermark
    $watermark_path = !empty($logo) ? $logo : '';
    // Thêm watermark vào ảnh gốc nếu tệp tồn tại
    if ($original_file && file_exists($original_file)) {
        foxtool_apply_watermark($original_file, $watermark_path, $logotext);
    }
    // Thêm watermark vào tất cả các kích thước ảnh khác nếu không phải là add_attachment
    if (!is_null($param2)) {
        foreach ($metadata['sizes'] as $size => $data) {
            $resized_file = str_replace(basename($original_file), $data['file'], $original_file);
            // Kiểm tra xem tệp đã resized có tồn tại không
            if ($resized_file && file_exists($resized_file)) {
                foxtool_apply_watermark($resized_file, $watermark_path, $logotext);
            }
        }
    }
    return $metadata;    
}
function foxtool_apply_watermark($file, $watermark_path = '', $logotext = '') {
	global $foxtool_options;
	$mime_type = wp_check_filetype($file)['type'];
    if (strpos($mime_type, 'image') !== false) {
        $image = false;
        switch ($mime_type) {
            case 'image/webp':
                $image = @imagecreatefromwebp($file);
                break;
            case 'image/jpeg':
            case 'image/jpg':
                $image = @imagecreatefromjpeg($file);
                break;
            case 'image/png':
                $image = @imagecreatefrompng($file);
                break;
            case 'image/avif':
                if (function_exists('imagecreatefromavif') && function_exists('imageavif')) {
                    $image = @imagecreatefromavif($file);
                }
                break;
        }

        if ($image === false) {
            return;
        }
		// Kiểm tra xem ảnh có dưới 12-bit không
        if (function_exists('imageistruecolor') && !imageistruecolor($image)) {return;}
        $image_width = imagesx($image);
        $image_height = imagesy($image);
		// neu anh cao nho hon 200 thi khong them logo
		if ($image_height < 200) {return;}
		// Xử lý text watermark
        if (!empty($logotext)) {
            $font_file = FOXTOOL_DIR . 'font/logo.ttf'; 
            $font_size = 50; 
            $text = $logotext;
            $bbox = imagettfbbox($font_size, 0, $font_file, $text);
            $text_width = $bbox[2] - $bbox[0];
            $text_height = $bbox[1] - $bbox[7];
            $watermark_width = $text_width + 30; 
            $watermark_height = $text_height + 30; 
            $watermark = imagecreatetruecolor($watermark_width, $watermark_height);
            imagealphablending($watermark, false);
            imagesavealpha($watermark, true);
            $transparent_color = imagecolorallocatealpha($watermark, 0, 0, 0, 127);
            imagefilledrectangle($watermark, 0, 0, $watermark_width, $watermark_height, $transparent_color);
            $hex_color = !empty($foxtool_options['media-logo10-c1']) ? $foxtool_options['media-logo10-c1'] : '#999999';
			list($r, $g, $b) = foxtool_toRGBA($hex_color);
            $text_color = imagecolorallocate($watermark, $r, $g, $b); 
            $x = round(($watermark_width - $text_width) / 2);
            $y = round(($watermark_height - $text_height) / 2 + $text_height);
            imagettftext($watermark, $font_size, 0, $x, $y, $text_color, $font_file, $text);
        } else {
            // Xử lý logo watermark
            $watermark = imagecreatefrompng($watermark_path);
            $watermark_width = imagesx($watermark);
            $watermark_height = imagesy($watermark);
        } 
		if (!empty($foxtool_options['media-logo13'])) {
			$ratio = $foxtool_options['media-logo13']; 
			$watermark_ratio = $ratio / 100;
			$new_width = round($image_width * $watermark_ratio);
			$new_height = round(($watermark_height / $watermark_width) * $new_width);
			$watermark = imagescale($watermark, $new_width, $new_height);
			$watermark_width = $new_width;
			$watermark_height = $new_height;
		}
		if(isset($foxtool_options['media-logo12']) && $foxtool_options['media-logo12'] == 'Center'){
		// chinh giua khung hình
		$watermark_pos_x =  intval(($image_width - $watermark_width) / 2);
		$watermark_pos_y =  intval(($image_height - $watermark_height) / 2);
		}
		elseif (isset($foxtool_options['media-logo12']) && $foxtool_options['media-logo12'] == 'Top Left'){
		// goc tren trai
		$watermark_pos_x = 10;
		$watermark_pos_y = 10;
		}
		elseif (isset($foxtool_options['media-logo12']) && $foxtool_options['media-logo12'] == 'Top Right'){
		// goc tren phai
		$watermark_pos_x = $image_width - $watermark_width - 10; 
		$watermark_pos_y = 10; 
		}
		elseif (isset($foxtool_options['media-logo12']) && $foxtool_options['media-logo12'] == 'Bottom Left'){
		// goc duoi trai
		$watermark_pos_x = 10; 
		$watermark_pos_y = $image_height - $watermark_height - 10; 
		}
		elseif (isset($foxtool_options['media-logo12']) && $foxtool_options['media-logo12'] == 'Bottom Right'){
		// goc duoi phai
		$watermark_pos_x = $image_width - $watermark_width - 10; 
		$watermark_pos_y = $image_height - $watermark_height - 10; 
		}
		elseif (isset($foxtool_options['media-logo12']) && $foxtool_options['media-logo12'] == 'Top Center'){
		$watermark_pos_x = intval(($image_width - $watermark_width) / 2);
		$watermark_pos_y = 10; 
		}
		elseif (isset($foxtool_options['media-logo12']) && $foxtool_options['media-logo12'] == 'Bottom Center'){
		$watermark_pos_x = intval(($image_width - $watermark_width) / 2);
		$watermark_pos_y = $image_height - $watermark_height - 10;
		}
		else {
		// chinh giua khung hình
		$watermark_pos_x =  intval(($image_width - $watermark_width) / 2);
		$watermark_pos_y =  intval(($image_height - $watermark_height) / 2);	
		}
		// trong
		if (!empty($foxtool_options['media-logo14']) && $foxtool_options['media-logo14'] != 100) {
			$opacity = $foxtool_options['media-logo14']; 
			$watermark_resized = imagecreatetruecolor($watermark_width, $watermark_height);
			imagealphablending($watermark_resized, false);
			imagesavealpha($watermark_resized, true);
			$transparent = imagecolorallocatealpha($watermark_resized, 0, 0, 0, 127);
			imagefilledrectangle($watermark_resized, 0, 0, $watermark_width, $watermark_height, $transparent);
			imagecopy($watermark_resized, $watermark, 0, 0, 0, 0, $watermark_width, $watermark_height);
			$watermark_width = intval($watermark_width);
			$watermark_height = intval($watermark_height);
			for ($x = 0; $x < $watermark_width; $x++) {
				for ($y = 0; $y < $watermark_height; $y++) {
					if ($x >= 0 && $x < imagesx($watermark_resized) && $y >= 0 && $y < imagesy($watermark_resized)) {
						$color = imagecolorsforindex($watermark_resized, imagecolorat($watermark_resized, $x, $y));
						$alpha = min(127, intval($color['alpha'] + (127 * (100 - $opacity) / 100)));
						$new_color = imagecolorallocatealpha($watermark_resized, $color['red'], $color['green'], $color['blue'], $alpha);
						imagesetpixel($watermark_resized, $x, $y, $new_color);
					}
				}
			}
			imagecopy($image, $watermark_resized, $watermark_pos_x, $watermark_pos_y, 0, 0, $watermark_width, $watermark_height);
			imagedestroy($watermark_resized);
		} else {
			imagecopy($image, $watermark, $watermark_pos_x, $watermark_pos_y, 0, 0, $watermark_width, $watermark_height);
		}
        // Lưu hình ảnh mới đã thêm watermark
		if ($mime_type === 'image/webp') {
            imagewebp($image, $file);
        } elseif ($mime_type == 'image/jpeg') {
            imagejpeg($image, $file);
        } elseif ($mime_type == 'image/png') {
            imagepng($image, $file);
        } elseif ($mime_type == 'image/avif'){
			imageavif($image, $file);
		}
    }

}
if (isset($foxtool_options['media-logo-hook']) && !isset($foxtool_options['media-png-8'])){
	add_filter('wp_generate_attachment_metadata', 'foxtool_add_watermark', 10, 2);
} else {
	add_action('add_attachment', 'foxtool_add_watermark');
}
}
# add media adminbar
function foxtool_watermark_checkbox_adminbar($wp_admin_bar) {
    if ( !is_admin() || !current_user_can('manage_options') ) {
        return;
    }
    $foxtool_options = get_option('foxtool_settings', array());
    // Lấy trạng thái của các tùy chọn watermark
    $watermark_enabled1 = isset($foxtool_options['media-zip1']) && $foxtool_options['media-zip1'] == 1;
    $watermark_enabled2 = isset($foxtool_options['media-webp1']) && $foxtool_options['media-webp1'] == 1;
	$watermark_enabled3 = isset($foxtool_options['media-avif1']) && $foxtool_options['media-avif1'] == 1;
    $watermark_enabled4 = isset($foxtool_options['media-avif2']) && $foxtool_options['media-avif2'] == 1;
    $watermark_enabled5 = isset($foxtool_options['media-zip2']) && $foxtool_options['media-zip2'] == 1;
    $watermark_enabled6 = isset($foxtool_options['media-logo1']) && $foxtool_options['media-logo1'] == 1;
	$watermark_enabled7 = isset($foxtool_options['media-cutop1']) && $foxtool_options['media-cutop1'] == 1;
    // Tạo node chính
    $args = array(
        'id'    => 'foxtool-adw',
        'title' => '<span class="ab-icon"></span> '. __('Media', 'foxtool'),
        'href'  => '#',
    );
    $wp_admin_bar->add_node($args);
    // Tạo checkbox 1
    $checkbox_args1 = array(
        'id'    => 'foxtool-awc1',
        'title' => sprintf(
            '<label><input type="checkbox" id="ft-watermark-checkbox1" %s /> '. __('Enable JPG image compression', 'foxtool') .'</label>',
            $watermark_enabled1 ? 'checked="checked"' : ''
        ),
        'parent' => 'foxtool-adw',
    );
    $wp_admin_bar->add_node($checkbox_args1);
    // Tạo checkbox 2
    $checkbox_args2 = array(
        'id'    => 'foxtool-awc2',
        'title' => sprintf(
            '<label><input type="checkbox" id="ft-watermark-checkbox2" %s /> '. __('Enable WEBP image compression', 'foxtool') .'</label>',
            $watermark_enabled2 ? 'checked="checked"' : ''
        ),
        'parent' => 'foxtool-adw',
    );
    $wp_admin_bar->add_node($checkbox_args2);
    // Tạo checkbox 3
    $checkbox_args3 = array(
        'id'    => 'foxtool-awc3',
        'title' => sprintf(
            '<label><input type="checkbox" id="ft-watermark-checkbox3" %s /> '. __('Allows uploading AVIF photos', 'foxtool') .'</label>',
            $watermark_enabled3 ? 'checked="checked"' : ''
        ),
        'parent' => 'foxtool-adw',
    );
    $wp_admin_bar->add_node($checkbox_args3);
	// Tạo checkbox 4
    $checkbox_args4 = array(
        'id'    => 'foxtool-awc4',
        'title' => sprintf(
            '<label><input type="checkbox" id="ft-watermark-checkbox4" %s /> '. __('Enable AVIF image compression', 'foxtool') .'</label>',
            $watermark_enabled4 ? 'checked="checked"' : ''
        ),
        'parent' => 'foxtool-adw',
    );
    $wp_admin_bar->add_node($checkbox_args4);
    // Tạo checkbox 5
    $checkbox_args5 = array(
        'id'    => 'foxtool-awc5',
        'title' => sprintf(
            '<label><input type="checkbox" id="ft-watermark-checkbox5" %s /> '. __('Enable image size limitation', 'foxtool') .'</label>',
            $watermark_enabled5 ? 'checked="checked"' : ''
        ),
        'parent' => 'foxtool-adw',
    );
    $wp_admin_bar->add_node($checkbox_args5);
    // Tạo checkbox 6
    $checkbox_args6 = array(
        'id'    => 'foxtool-awc6',
        'title' => sprintf(
            '<label><input type="checkbox" id="ft-watermark-checkbox6" %s /> '. __('Enable watermarks', 'foxtool') .'</label>',
            $watermark_enabled6 ? 'checked="checked"' : ''
        ),
        'parent' => 'foxtool-adw',
    );
    $wp_admin_bar->add_node($checkbox_args6);
	// Tạo checkbox 7
    $checkbox_args7 = array(
        'id'    => 'foxtool-awc7',
        'title' => sprintf(
            '<label><input type="checkbox" id="ft-watermark-checkbox7" %s /> '. __('Enable picture frame', 'foxtool') .'</label>',
            $watermark_enabled7 ? 'checked="checked"' : ''
        ),
        'parent' => 'foxtool-adw',
    );
    $wp_admin_bar->add_node($checkbox_args7);
}
add_action('admin_bar_menu', 'foxtool_watermark_checkbox_adminbar', 100);
function foxtool_handletoggle_watermark() {
    if ( !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'foxtool_watermark_nonce') ) {
        wp_send_json_error('Invalid nonce');
    }
    if ( isset($_POST['enabled'], $_POST['option_key']) ) {
        $enabled = sanitize_text_field($_POST['enabled']);
        $option_key = sanitize_text_field($_POST['option_key']);
        $foxtool_options = get_option('foxtool_settings', array());
        $foxtool_options[$option_key] = $enabled === 'true' ? 1 : NULL;
        update_option('foxtool_settings', $foxtool_options);
        wp_send_json_success('Option updated');
    }
    wp_send_json_error('Missing parameters');
}
add_action('wp_ajax_toggle_watermark', 'foxtool_handletoggle_watermark');
function foxtool_watermark_admin_footer() { ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#wp-admin-bar-foxtool-adw input[type="checkbox"]').on('change', function() {
            var isChecked = $(this).is(':checked');
            var checkboxId = $(this).attr('id');
            var optionKey = '';
            switch (checkboxId) {
                case 'ft-watermark-checkbox1':
                    optionKey = 'media-zip1';
                    break;
                case 'ft-watermark-checkbox2':
                    optionKey = 'media-webp1';
                    break;
                case 'ft-watermark-checkbox3':
                    optionKey = 'media-avif1';
                    break;
				case 'ft-watermark-checkbox4':
                    optionKey = 'media-avif2';
                    break;
                case 'ft-watermark-checkbox5':
                    optionKey = 'media-zip2';
                    break;
                case 'ft-watermark-checkbox6':
                    optionKey = 'media-logo1';
                    break;
				case 'ft-watermark-checkbox7':
                    optionKey = 'media-cutop1';
                    break;
                default:
                    return; // Không xác định được checkbox, thoát
            }
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'toggle_watermark',
                    nonce: '<?php echo wp_create_nonce('foxtool_watermark_nonce'); ?>',
                    enabled: isChecked ? 'true' : 'false',
                    option_key: optionKey
                },
                success: function(response) {
                    if (response.success) {
                        alert('<?php _e('Settings changed', 'foxtool'); ?>');
                    } else {
                        alert('<?php _e('Failed to update setting:', 'foxtool'); ?> ' + response.data);
                    }
                },
                error: function() {
                    alert('<?php _e('An error occurred', 'foxtool'); ?>');
                }
            });
        });
    });
    </script>
    <?php
}
add_action('admin_footer', 'foxtool_watermark_admin_footer');

