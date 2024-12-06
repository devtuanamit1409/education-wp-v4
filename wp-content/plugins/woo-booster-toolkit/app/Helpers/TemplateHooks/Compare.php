<?php

namespace WCBT\Helpers\TemplateHooks;

use WCBT\Helpers\Template;

class Compare
{
    public $template;

    public static function instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    protected function __construct()
    {
        $this->template = Template::instance();
        add_action('wcbt/layout/compare/container/before', array( $this, 'section_before_container' ));
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function section_before_container(array $data)
    {
        $sections = apply_filters(
            'wcbt/filter/compare/container/before',
            array(
                'shared/breadcrumb.php',
            )
        );

        $this->template->get_frontend_templates_type_classic($sections, compact('data'));
    }
}
