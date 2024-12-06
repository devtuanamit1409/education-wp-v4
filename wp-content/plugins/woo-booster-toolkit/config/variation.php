<?php

use WCBT\Helpers\Fields\FileUpload;
use WCBT\Helpers\Fields\ColorPicker;

return apply_filters(
    'wcbt/filter/config/variation/fields',
    array(
        'color' => array(
            'title'  => esc_html__('Color', 'wcbt'),
            'fields' => array(
                'color' => array(
                    'type'    => new ColorPicker(),
                    'id'      => 'product_attribute_color',
                    'name'    => 'product_attribute_color',
                    'title'   => esc_html__('Color', 'wcbt'),
                    'desc'    => esc_html__('Choose a color', 'wcbt'), // description
                    'class'   => '',
                    'default' => '#904141',
                ),
            ),
        ),
        'image' => array(
            'title'  => esc_html__('Image', 'wcbt'),
            'fields' => array(
                'image' => array(
                    'type'         => new FileUpload(),
                    'id'           => 'product_attribute_image',
                    'name'         => 'product_attribute_image',
                    'title'        => esc_html__('Image', 'wcbt'),
                    'button_title' => esc_html__('Select Image', 'wcbt'),
                    'class'        => '',
                    'default'   => '',
                    'multiple'     => false,
                ),
            ),
        ),
        'text' => array(
            'title'  => esc_html__('Text', 'wcbt'),
            'fields' => array(
            ),
        ),
    )
);
