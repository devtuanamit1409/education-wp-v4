<?php
if (! isset($data)) {
    return;
}
use WCBT\Helpers\General;

$inner_content_style = '';
if (! empty($data['background_color'])) {
    $inner_content_style = 'background:' . $data['background_color'] . ';';
}

$text_color_style  = '';
if (! empty($data['text_color'])) {
    $text_color_style = 'color:' . $data['text_color'] . ';';
}

$button_style = '';
//if (! empty($data['button_text_color'])) {
//    $button_style .= 'color:' . $data['button_text_color'] . ';';
//}

//if (! empty($data['button_background_color'])) {
//    $button_style .= 'background:' . $data['button_background_color'] . ';';
//}
?>
<div class="wcbt-maxsale-wrapper">
    <div class="wcbt-maxsale-content">
        <div class="inner-content" style="<?php echo esc_attr($inner_content_style); ?>">
            <div class="inner-image">
                <img src="<?php echo esc_url_raw($data['image']); ?>" alt="<?php esc_attr_e('Maxsale'); ?>">
            </div>

            <div class="inner-info">
                <?php
                if (!empty($data['title'])) {
                    ?>
                    <h3 class="inner-title" style="<?php echo esc_attr($text_color_style);?>">
                        <?php
                        echo esc_html($data['title']);
                        ?>
                    </h3>
                    <?php
                }
                if (!empty($data['description'])) {
                    ?>
                    <div class="inner-desc" style="<?php echo esc_attr($text_color_style);?>">
                        <?php
                        echo General::ksesHTML($data['description']);
                        ?>
                    </div>
                    <?php
                }
                ?>
                <div class="input-form">
                    <div class="text-field">
<!--                        <input type="email" placeholder="--><?php //echo esc_attr($data['email_placeholder']); ?><!--">-->
                        <svg width="19" height="14" viewBox="0 0 19 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.6429 0H1.35714C0.997206 0 0.652012 0.1475 0.397498 0.41005C0.142984 0.672601 0 1.0287 0 1.4V12.6C0 12.9713 0.142984 13.3274 0.397498 13.5899C0.652012 13.8525 0.997206 14 1.35714 14H17.6429C18.0028 14 18.348 13.8525 18.6025 13.5899C18.857 13.3274 19 12.9713 19 12.6V1.4C19 1.0287 18.857 0.672601 18.6025 0.41005C18.348 0.1475 18.0028 0 17.6429 0ZM16.15 1.4L9.5 6.146L2.85 1.4H16.15ZM1.35714 12.6V2.037L9.11321 7.574C9.2268 7.65529 9.36175 7.69885 9.5 7.69885C9.63825 7.69885 9.7732 7.65529 9.88679 7.574L17.6429 2.037V12.6H1.35714Z" fill="black"></path></svg>
                    </div>
                    <span class="message"></span>
<!--                    <button class="submit" style="--><?php //echo esc_attr($button_style); ?><!--">--><?php //echo esc_html($data['button']); ?><!--</button>-->
                </div>
            </div>

            <div class="button-close">
                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"
                     style="fill: <?php echo esc_attr($data['text_color']);?>;">
                    <path d="m11.414 10 6.293-6.293a1 1 0 1 0-1.414-1.414l-6.293 6.293-6.293-6.293a1 1 0 0 0-1.414 1.414l6.293 6.293-6.293 6.293a1 1 0 1 0 1.414 1.414l6.293-6.293 6.293 6.293a.998.998 0 0 0 1.707-.707.999.999 0 0 0-.293-.707l-6.293-6.293z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>
