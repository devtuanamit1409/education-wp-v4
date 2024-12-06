<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
# nhan nut xoa het ban ghi tam trong csdl làm sach csdl
function foxtool_delete_post_revisions() {
	check_ajax_referer('foxtool_post_revisions', 'security');
    if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'foxtool'));
    }
    global $wpdb;
    $sql = 'DELETE FROM `' . $wpdb->prefix . 'posts` WHERE `post_type` = %s;';
    try {
        $wpdb->query($wpdb->prepare($sql, array('revision')));
		return true;
    } catch (Exception $e) {
        return 'Error! ' . $wpdb->last_error;
    }
}
add_action('wp_ajax_foxtool_delete_revisions', 'foxtool_delete_post_revisions');
# nhan nut xoa ban luu tu dong trong csdl
function foxtool_delete_auto_drafts() {
	check_ajax_referer('foxtool_post_drafts', 'security');
    if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'foxtool'));
    }
    global $wpdb;
    $sql = "DELETE FROM {$wpdb->posts} WHERE `post_status` = 'auto-draft'";
    try {
        $wpdb->query($sql);
        return true;
    } catch (Exception $e) {
        return 'Error! ' . $wpdb->last_error;
    }
}
add_action('wp_ajax_foxtool_delete_auto_drafts', 'foxtool_delete_auto_drafts');
# xoa tat ca bai trong thung rac
function foxtool_delete_all_trashed_posts() {
	check_ajax_referer('foxtool_post_trashed', 'security');
    if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'foxtool'));
    }
    global $wpdb;
    $sql = "DELETE FROM {$wpdb->posts} WHERE `post_status` = 'trash'";
    try {
        $wpdb->query($sql);
        return true;
    } catch (Exception $e) {
        return 'Error! ' . $wpdb->last_error;
    }
}
add_action('wp_ajax_foxtool_delete_all_trashed_posts', 'foxtool_delete_all_trashed_posts');
# xoa binh luan cho
function foxtool_del_comments_pend_ajax() {
	check_ajax_referer('foxtool_del_comenpend_nonce', 'security');
    if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'foxtool'));
    }
    $number = 200;
    $deleted = 0;
    do {
        $comments = get_comments([
            'status' => 'hold',
            'number' => $number,
        ]);
        if (empty($comments)) {
            break;
        }
        foreach ($comments as $comment) {
            wp_trash_comment($comment->comment_ID);
            wp_delete_comment($comment->comment_ID, true);
            $deleted++;
        }
        sleep(1);
    } while (count($comments) == $number);
    wp_send_json_success(array('deleted_count' => $deleted));
}
add_action('wp_ajax_foxtool_del_comenpend', 'foxtool_del_comments_pend_ajax');
# xoa binh luan spam
function foxtool_del_comments_spam_ajax() {
	check_ajax_referer('foxtool_del_comenspam_nonce', 'security');
    if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'foxtool'));
    }
    $number = 200;
    $deleted = 0;
    do {
        $comments = get_comments([
            'status' => 'spam',
            'number' => $number,
        ]);
        if (empty($comments)) {
            break;
        }
        foreach ($comments as $comment) {
            wp_trash_comment($comment->comment_ID);
            wp_delete_comment($comment->comment_ID, true);
            $deleted++;
        }
        sleep(1);
    } while (count($comments) == $number);
    wp_send_json_success(array('deleted_count' => $deleted));
}
add_action('wp_ajax_foxtool_del_comenspam', 'foxtool_del_comments_spam_ajax');
# xoa binh luan thung rac
function foxtool_del_comments_trash_ajax() {
	check_ajax_referer('foxtool_del_comentrash_nonce', 'security');
    if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'foxtool'));
    }
    $number = 200;
    $deleted = 0;
    do {
        $comments = get_comments([
            'status' => 'trash',
            'number' => $number,
        ]);
        if (empty($comments)) {
            break;
        }
        foreach ($comments as $comment) {
            wp_trash_comment($comment->comment_ID);
            wp_delete_comment($comment->comment_ID, true);
            $deleted++;
        }
        sleep(1);
    } while (count($comments) == $number);
    wp_send_json_success(array('deleted_count' => $deleted));
}
add_action('wp_ajax_foxtool_del_comentrash', 'foxtool_del_comments_trash_ajax');
# xoa binh luan chua url
function foxtool_del_comments_link_ajax() {
    check_ajax_referer('foxtool_del_comenlink_nonce', 'security');
    if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'foxtool'));
    }
    $number = 200;
    $deleted = 0;
    do {
        $comments = get_comments([
            'status' => 'all',
            'number' => $number,
        ]);
        if (empty($comments)) {
            break;
        }
        foreach ($comments as $comment) {
            $comment_author_url = $comment->comment_author_url;
            $comment_content = $comment->comment_content;
            // Kiểm tra nếu bình luận chứa thẻ a hoặc đường dẫn HTTP/HTTPS hoặc trường URL
            if (preg_match('/<a\s|http[s]?:\/\/|www\.[^\s]+/i', $comment_content) || !empty($comment_author_url)) {
                wp_trash_comment($comment->comment_ID);
                wp_delete_comment($comment->comment_ID, true);
                $deleted++;
            }
        }
        sleep(1);
        
    } while (count($comments) == $number);
    wp_send_json_success(array('deleted_count' => $deleted));
}
add_action('wp_ajax_foxtool_del_comenlink', 'foxtool_del_comments_link_ajax');
# xóa ảnh 404 trong media
function foxtool_delete_404_attachments() {
    check_ajax_referer('foxtool_media_del', 'security');
    if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'foxtool'));
    }
    $number = 200; 
    $deletedCount = 0;
    $attachments = get_posts(array(
        'post_type' => 'attachment',
        'numberposts' => $number,
        'fields' => 'ids'
    ));
    if ($attachments) {
        foreach ($attachments as $attachmentID) {
            $file_path = get_attached_file($attachmentID);
            if ($file_path && !file_exists($file_path)) {
                wp_delete_attachment($attachmentID, true);
                $deletedCount++;
            }
        }
    }
    wp_send_json_success(array('deleted_count' => $deletedCount));
}
add_action('wp_ajax_foxtool_delete_media', 'foxtool_delete_404_attachments');
# xoa media thum 404
function foxtool_delete_404_thumbnails() {
    check_ajax_referer('foxtool_media_thum_del', 'security');
    if (!current_user_can('manage_options')) {
        wp_die(__('Insufficient permissions', 'foxtool'));
    }
    $number = 200; 
    $deletedCount = 0;
    $attachments = get_posts(array(
        'post_type' => 'attachment',
        'numberposts' => $number,
        'fields' => 'ids'
    ));
    if ($attachments) {
        foreach ($attachments as $attachmentID) {
            $thumbnail_sizes = wp_get_attachment_metadata($attachmentID);
            if ($thumbnail_sizes && isset($thumbnail_sizes['sizes'])) {
                foreach ($thumbnail_sizes['sizes'] as $size => $data) {
                    $thumbnail_path = get_attached_file($attachmentID);
                    $thumbnail_path = str_replace(basename($thumbnail_path), $data['file'], $thumbnail_path);
                    if (!file_exists($thumbnail_path)) {
                        unset($thumbnail_sizes['sizes'][$size]);
                        $deletedCount++;
                    }
                }
                wp_update_attachment_metadata($attachmentID, $thumbnail_sizes);
            }
        }
    }
    wp_send_json_success(array('deleted_count' => $deletedCount));
}
add_action('wp_ajax_foxtool_delete_media_thum', 'foxtool_delete_404_thumbnails');
# ajax xoa file crop
function foxtool_delete_images_by_size_callback() {
    check_ajax_referer('foxtool_delete_crop_nonce', 'security');
    if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'foxtool'));
    }
    $size_name = isset($_POST['size']) ? $_POST['size'] : '';
    if ($size_name !== '') {
        $limit = 500; // Giới hạn số lượng hình muốn xóa
        $deleted_count = foxtool_delete_thumbnails($size_name, $limit);
        wp_send_json_success(__('Deleted', 'foxtool') .' '. $size_name . ': ' . $deleted_count . ' ' . __('images', 'foxtool'));
    } else {
        wp_send_json_error(__('No size', 'foxtool'));
    }
}
add_action('wp_ajax_foxtool_delete_images_by_size', 'foxtool_delete_images_by_size_callback');



