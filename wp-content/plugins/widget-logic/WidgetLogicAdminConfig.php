<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WidgetLogicAdminConfig
{
    const OFF = 'off';
    const ON = 'on';
    private static $instance = null;
    protected $options;

    public static function getInstance() {

        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;

    }

    private function __construct() {

        // Add the page to the admin menu
        add_action('admin_menu', array(&$this, 'addPage'));

        // Register page options
        add_action('admin_init', array(&$this, 'registerPageOptions'));

        // Get registered option
        $this->options = get_option('widget_logic_settings_options');

    }

    public function isFullyEnabled()
    {
        return $this->getFullyEnabledValue() === self::ON;
    }

    public function addDescriptionSettingsLink($links) {
        if ($this->isFullyEnabled()) {
            return $links;
        }

        $settings_link = '<a href="options-general.php?page=widget-logic-admin-config">> Enable Gutenberg widgets and new built-in widgets <</a>';

        $links[] = $settings_link;

        return $links;
    }

    public function addPage() {
        add_options_page('Theme Options', 'Widget Logic', 'manage_options', 'widget-logic-admin-config', array($this, 'displayPage'));
    }

    public function displayPage() {
    ?>
    <div class='wrap'>
		<h1><?php esc_html_e('Widget Logic Settings', 'widget_logic_admin_settings'); ?></h1>
		<div id="poststuff" class="metabox-holder">
			<div class="widget">
                <form method="post" action="options.php">
                <?php
                    settings_fields(__FILE__);
                    do_settings_sections(__FILE__);
                ?>
                    <div>
                        <small>
                            When you activate the "Enabled" option, it signifies:<br/>
                            <blockquote>
                                * You’ll enable support of Gutenberg widgets and blocks control <br/>
                                * Also you can add our built-in widgets like Live Match and other that can refer to <a href="https://widgetlogic.org">widgetlogic.org</a> data <br/>
                            </blockquote>
                            On the other hand, if you choose the "Disabled" setting, it conveys:<br/>
                            <blockquote>
                                * You can use Widget Logic functionality only for old Wordpress version and new Gutenberg widgets control are not available <br/>
                                * You can not add our built-in widgets like Live Match and other </br>
                            </blockquote>
                        </small>
                    </div>
                    <?php submit_button(); ?>
                </form>
			</div>
		</div>
	</div>
    <?php
    }

    public function registerPageOptions() {
        // Add Section for option fields
        add_settings_section('widget_logic_section', '', array($this, 'displaySection'), __FILE__);
        add_settings_field('widget_logic_is_fully_enabled', 'Enable Gutenberg support', array($this, 'isEnabledSettingsField'), __FILE__, 'widget_logic_section');

        // Register Settings
        register_setting(__FILE__, 'widget_logic_settings_options', array($this, 'validateOptions'));
    }

    public function validateOptions($fields) {
        $valid_fields = array();

        $isEnabled = trim($fields['widget_logic_is_fully_enabled']);
        $valid_fields['widget_logic_is_fully_enabled'] = wp_strip_all_tags(stripslashes($isEnabled));

        return apply_filters('validateOptions', $valid_fields, $fields);
    }

    public function displaySection() { /* Leave blank */ }

    protected function getFullyEnabledValue()
    {
        return isset($this->options['widget_logic_is_fully_enabled']) ? $this->options['widget_logic_is_fully_enabled'] : self::OFF;
    }

    public function isEnabledSettingsField() {
        $val = $this->getFullyEnabledValue();

        $selected_one=array(self::ON => '', self::OFF => '');
        $selected_one[$val] = 'selected="selected"';
        echo "
        <div>
            <select name='widget_logic_settings_options[widget_logic_is_fully_enabled]'>
                <option value='".esc_attr(self::OFF)."' ". esc_html($selected_one[self::OFF]) .">Disabled</option>
                <option value='".esc_attr(self::ON)."' ". esc_html($selected_one[self::ON]) .">Enabled</option>
            </select>
        </div>
        ";
    }
}
