php
<?php
/**
 * Plugin Name:       Simple Content Slider
 * Description:       Displays a simple content slider with predefined items or items from a JSON file, using Slick Carousel.
 * Version:           1.2
 * Author:            Anton Bil
 * Author URI:        https://familiebil.nl/anton
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       simple-content-slider
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define plugin constants
define( 'SCS_PLUGIN_FILE', __FILE__ );
define( 'SCS_PLUGIN_PATH', plugin_dir_path( SCS_PLUGIN_FILE ) );
define( 'SCS_PLUGIN_URL', plugin_dir_url( SCS_PLUGIN_FILE ) );
define( 'SCS_TEXT_DOMAIN', 'simple-content-slider' );

/**
 * Enqueue scripts and styles for the slider.
 */
function scs_enqueue_scripts() {
    // Only load if the shortcode might be used or on specific pages.
    // For now, load on 'is_singular()' and front page for testing.
    if ( is_singular() || is_front_page() ) {

        // Slick Carousel CSS from CDN
        wp_enqueue_style( 'slick-carousel-css', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css', array(), '1.8.1' );
        wp_enqueue_style( 'slick-carousel-theme-css', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css', array('slick-carousel-css'), '1.8.1' );

        // Custom slider styles
        wp_enqueue_style( 'simple-content-slider-styles', SCS_PLUGIN_URL . 'css/simple-content-slider.css', array('slick-carousel-theme-css'), '1.2' );

        // jQuery (WordPress loads this, but Slick needs it as a dependency)
        wp_enqueue_script('jquery');

        // Slick Carousel JS from CDN
        wp_enqueue_script( 'slick-carousel-js', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', array( 'jquery' ), '1.8.1', true );

        //wp_enqueue_style( 'slick-carousel-css', SCS_PLUGIN_URL . '/css/slick.css', array(), '1.10.0' );
        //wp_enqueue_style( 'slick-carousel-theme-css', SCS_PLUGIN_URL . '/css/slick-theme.css', array('slick-carousel-css'), '1.10.0' );

        // Eigen slider styles
        //wp_enqueue_style( 'simple-content-slider-styles', SCS_PLUGIN_URL . '/css/simple-content-slider.css', array('slick-carousel-theme-css'), '1.1' );

        // jQuery (WordPress laadt dit al, maar Slick heeft het nodig)
        //wp_enqueue_script('jquery');

        // Slick Carousel JS
        // Download Slick en plaats slick.min.js in de js map
        //wp_enqueue_script( 'slick-carousel-js', SCS_PLUGIN_URL . '/js/slick.min.js', array( 'jquery' ), '1.10.0', true );
        // Custom slider script
        wp_enqueue_script( 'simple-content-slider-script', SCS_PLUGIN_URL . 'js/simple-content-slider.js', array( 'jquery', 'slick-carousel-js' ), '1.2', true );

        // Localize script for passing data, including translated strings if needed by JS
        wp_localize_script( 'simple-content-slider-script', 'scs_data', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            // Example if your JS needs translated strings:
            // 'i18n' => array(
            //    'some_string' => __( 'Some String for JS', SCS_TEXT_DOMAIN ),
            // )
        ));

        // If your simple-content-slider.js has translatable strings using wp.i18n
        // wp_set_script_translations( 'simple-content-slider-script', SCS_TEXT_DOMAIN, SCS_PLUGIN_PATH . 'languages' );
    }
}
add_action( 'wp_enqueue_scripts', 'scs_enqueue_scripts' );

/**
 * Load plugin text domain for translations.
 */
function scs_load_textdomain() {
    load_plugin_textdomain( SCS_TEXT_DOMAIN, false, basename( dirname( SCS_PLUGIN_FILE ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'scs_load_textdomain' );

/**
 * Shortcode handler for the simple content slider.
 *
 * Usage:
 * [simple_content_slider
 *      slide1_title="Title Slide 1" slide1_subtitle="Subtitle 1" slide1_url="https://url1.com"
 *      slide2_title="Title Slide 2" slide2_subtitle="Subtitle 2" slide2_url="https://url2.com"
 *      slide3_title="Title Slide 3" slide3_url="https://url3.com" // Subtitle is optional
 *      autoplay_speed="3000"
 *      move_target_selector=".some-element-to-move-into"
 * ]
 */
function scs_shortcode_handler( $atts ) {
    // Default attributes for the slider itself (like autoplay_speed)
    $slider_atts = shortcode_atts(
        array(
            'autoplay_speed'       => '4000', // Default autoplay speed in ms
            'move_target_selector' => '',     // CSS selector to move the slider into
            // Example: 'show_dots' => 'true', 'show_arrows' => 'true' (handled by JS if Slick options)
        ),
        $atts,
        'simple_content_slider'
    );

    $slides_data = array();
    // Loop through the attributes to collect slide data
    // Assume a maximum number of slides to prevent infinite loops,
    // or stop when a set of attributes for a slide is not found.
    $max_slides = apply_filters('scs_max_shortcode_slides', 10); // Allow filtering max slides

    for ( $i = 1; $i <= $max_slides; $i++ ) {
        $title_key    = "slide{$i}_title";
        $subtitle_key = "slide{$i}_subtitle";
        $url_key      = "slide{$i}_url";

        // A slide is considered valid if at least a title is present
        if ( isset( $atts[$title_key] ) && ! empty( $atts[$title_key] ) ) {
            $slides_data[] = array(
                'title'    => sanitize_text_field( $atts[$title_key] ),
                'subtitle' => isset( $atts[$subtitle_key] ) ? sanitize_text_field( $atts[$subtitle_key] ) : '', // Subtitle is optional
                'url'      => isset( $atts[$url_key] ) ? esc_url_raw( $atts[$url_key] ) : '', // URL is optional
            );
        } else {
            // If there's no title_key for index 'i', we stop looking for more slides.
            // This prevents iterating through $max_slides if fewer are provided.
            break;
        }
    }

    // If no slide data from shortcode attributes, try loading from JSON
    if ( empty( $slides_data ) ) {
        $json_file_path = apply_filters('scs_json_file_path', SCS_PLUGIN_PATH . '/simple-content-slider.json');

        if ( file_exists( $json_file_path ) ) {
            $json_content = file_get_contents( $json_file_path );
            $decoded_data = json_decode( $json_content, true ); // true for associative array

            if ( json_last_error() === JSON_ERROR_NONE && is_array( $decoded_data ) ) {
                foreach( $decoded_data as $item ) {
                    // Validate if the decoded data has the expected structure
                    if ( isset( $item['title'] ) ) { // Title is minimally required from JSON
                        $slides_data[] = array(
                            'title'    => sanitize_text_field( $item['title'] ),
                            'subtitle' => isset( $item['subtitle'] ) ? sanitize_text_field( $item['subtitle'] ) : '',
                            'url'      => isset( $item['url'] ) ? esc_url_raw( $item['url'] ) : '',
                        );
                    }
                }
            } else {
                // Optional: log an error if JSON is corrupt
                // error_log( SCS_TEXT_DOMAIN . ': Failed to decode JSON or JSON is not an array. Error: ' . json_last_error_msg() );
                // Or return an HTML comment for debugging
                return '<!-- ' . esc_html__( 'Simple Content Slider: Error loading or parsing JSON data.', SCS_TEXT_DOMAIN ) . ' -->';
            }
        } else {
            // Optional: log an error if JSON file is not found
            // error_log( SCS_TEXT_DOMAIN . ': JSON file not found at ' . $json_file_path );
            // Check if any shortcode attributes for slides were even attempted, to avoid showing this if user intends to use attributes.
            $has_slide_attributes = false;
            for ($i = 1; $i <=2; $i++){ // Check for first 2 potential slides as an indicator
                if (isset($atts["slide{$i}_title"])) {
                    $has_slide_attributes = true;
                    break;
                }
            }
            if (!$has_slide_attributes) { // Only show "JSON not found" if no slide attributes were used
                return '<!-- ' . sprintf(
                    /* translators: %s: file path */
                    esc_html__( 'Simple Content Slider: JSON file (%s) not found and no slide attributes provided.', SCS_TEXT_DOMAIN ),
                    esc_html( $json_file_path )
                ) . ' -->';
            }
        }
    }

    // If there's still no slide data (neither from shortcode nor JSON), display nothing or a message
    if ( empty( $slides_data ) ) {
        return '<!-- ' . esc_html__( 'Simple Content Slider: No slides configured.', SCS_TEXT_DOMAIN ) . ' -->'; // Optional message
        // return ''; // Or simply display nothing
    }

    $move_target_selector = sanitize_text_field( $slider_atts['move_target_selector'] );
    $container_id = 'scs-slider-instance-' . uniqid(); // Unique ID for each slider instance
    $style_attr = '';

    // If we are moving the slider, hide the container initially to prevent flashing.
    // The JS will make it visible after moving.
    if ( ! empty( $move_target_selector ) ) {
        $style_attr = 'style="display:none; visibility:hidden;"';
    }

    ob_start();
    ?>
    <div id="<?php echo esc_attr( $container_id ); ?>"
         class="scs-slider-wrapper" <?php /* Wrapper for JS to target for moving */ ?>
         data-autoplay-speed="<?php echo esc_attr( intval( $slider_atts['autoplay_speed'] ) ); ?>"
         <?php if ( ! empty( $move_target_selector ) ) : ?>
             data-move-target-selector="<?php echo esc_attr( $move_target_selector ); ?>"
         <?php endif; ?>
         <?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $style_attr is controlled and simple string ?>
         >

        <div class="scs-slider-container"> <?php /* This is the container Slick Carousel will be applied to */ ?>
            <ul class="scs-slides">
                <?php foreach ( $slides_data as $slide ) : ?>
                    <li class="scs-slide">
                        <div class="scs-slide-content">
                            <?php if ( ! empty( $slide['subtitle'] ) ) : ?>
                                <span class="scs-slide-subtitle"><?php echo esc_html( $slide['subtitle'] ); ?></span>
                            <?php endif; ?>

                            <?php
                            // Determine if we should render an <a> tag or just the title
                            $has_url = ! empty( $slide['url'] );
                            ?>

                            <?php if ( $has_url ) : ?>
                                <div class="wp-block-button is-style-outline is-style-outline--2 scs-slide-button">
                                    <a href="<?php echo esc_url( $slide['url'] ); ?>" class="wp-block-button__link wp-element-button">
                                        <span class="scs-slide-title"><?php echo esc_html( $slide['title'] ); ?></span>
                                    </a>
                                </div>
                            <?php elseif ( ! empty( $slide['title'] ) ) : // Title exists but no URL ?>
                                <span class="scs-slide-title scs-slide-title--no-link"><?php echo esc_html( $slide['title'] ); ?></span>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'simple_content_slider', 'scs_shortcode_handler' );

// Optional: Add a settings link on the plugin page (good practice)

function scs_add_settings_link( $links ) {
    // Check if user can manage options before adding the link
    if ( current_user_can( 'manage_options' ) ) {
        $settings_link = '<a href="options-general.php?page=scs_settings">' . esc_html__( 'Settings', SCS_TEXT_DOMAIN ) . '</a>';
        array_unshift( $links, $settings_link ); // Add to the beginning of the links array
    }
    return $links;
}
add_filter( "plugin_action_links_" . plugin_basename( SCS_PLUGIN_FILE ), 'scs_add_settings_link' );


?>
