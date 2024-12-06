<?php
class ThimTwitterOption{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Thim Twitter - Settings', 
            'Thim Twitter', 
            'manage_options', 
            'thim_twitter', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'thim_twitter' );
        ?>
        <div class="wrap">
            <h2>Thim Twitter - Settings</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'thim_twitter_group' );   
                do_settings_sections( 'thim-twitter-admin' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'thim_twitter_group', // Option group
            'thim_twitter', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'application_settings', // ID
            'Application Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'thim-twitter-admin' // Page
        );  

        add_settings_field(
            'consumer_key', // ID
            'Consumer Key (API Key)', // Title 
            array( $this, 'consumer_key_callback' ), // Callback
            'thim-twitter-admin', // Page
            'application_settings' // Section           
        );      

        add_settings_field(
            'consumer_secret', 
            'Consumer Secret (API Secret)', 
            array( $this, 'consumer_secret_callback' ), 
            'thim-twitter-admin', 
            'application_settings'
        );      

        add_settings_field(
            'access_token', 
            'Access Token', 
            array( $this, 'access_token_callback' ), 
            'thim-twitter-admin', 
            'application_settings'
        );  

        add_settings_field(
            'access_token_secret', 
            'Access Token Secret', 
            array( $this, 'access_token_secret_callback' ), 
            'thim-twitter-admin', 
            'application_settings'
        );  

    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['consumer_key'] ) )
            $new_input['consumer_key'] = sanitize_text_field( $input['consumer_key'] );

        if( isset( $input['consumer_secret'] ) )
            $new_input['consumer_secret'] = sanitize_text_field( $input['consumer_secret'] );

        if( isset( $input['access_token'] ) )
            $new_input['access_token'] = sanitize_text_field( $input['access_token'] );

        if( isset( $input['access_token_secret'] ) )
            $new_input['access_token_secret'] = sanitize_text_field( $input['access_token_secret'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info(){
        print 'Create Application in here: <a href="https://apps.twitter.com/" target="_blank">https://apps.twitter.com/</a>';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function consumer_key_callback(){
        printf(
            '<input type="text" id="consumer_key" name="thim_twitter[consumer_key]" value="%s" size="100"/>',
            isset( $this->options['consumer_key'] ) ? esc_attr( $this->options['consumer_key']) : 'fCuXeJBzIhikOjNFmh7FC7Cpz'
        );
    }

    public function consumer_secret_callback(){
        printf(
            '<input type="text" id="consumer_secret" name="thim_twitter[consumer_secret]" value="%s" size="100"/>',
            isset( $this->options['consumer_secret'] ) ? esc_attr( $this->options['consumer_secret']) : 'tLefeE8nyARq1aIAJF7GSIhAoAxQiAMU9aX0RE79F6IVAcfA7J'
        );
    }

    public function access_token_callback(){
        printf(
            '<input type="text" id="access_token" name="thim_twitter[access_token]" value="%s" size="100"/>',
            isset( $this->options['access_token'] ) ? esc_attr( $this->options['access_token']) : '3546925700-hzs7KwBYCqsZxP6sYRtjIS4V1TIMgh0zY0Hlhb5'
        );
    }

    public function access_token_secret_callback(){
        printf(
            '<input type="text" id="access_token_secret" name="thim_twitter[access_token_secret]" value="%s" size="100"/>',
            isset( $this->options['access_token_secret'] ) ? esc_attr( $this->options['access_token_secret']) : 'TmI0MW7QH7KTfdePVX1Swsie7i2K1RziunVc46y0wOALn'
        );
    }

}

if( is_admin() )
    $ThimTwitterOption = new ThimTwitterOption();