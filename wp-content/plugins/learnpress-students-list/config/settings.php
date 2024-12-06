<?php
$students_list_settings = apply_filters(
	'learnpress-addon/course-settings-fields/students-list',
	array(
		array(
			'type'  => 'title',
			'title' => esc_html__( 'Students List Settings', 'learnpress-students-list' ),
			'id'    => 'lp_metabox_students_list_setting',
		),
		array(
			'title'   => esc_html__( 'Students Per Page', 'learnpress-students-list' ),
			'id'      => 'lp_students_per_page',
			'default' => 9,
			'type'    => 'number',
			'desc'    => esc_html__( 'The number of displayed students per page (Enter -1 to display all).', 'learnpress-students-list' ),
		),
		array(
			'type' => 'sectionend',
			'id'   => 'lp_metabox_students_list_setting',
		),
	)
);

return $students_list_settings;
