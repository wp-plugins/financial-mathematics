<?php
// SOURCE: http://kovshenin.com/2012/the-wordpress-settings-api/

add_action( 'admin_menu', 'my_admin_menu' );
function my_admin_menu() {
    add_options_page( 'Financial Mathematics', 'Financial Mathematics', 'manage_options', 'financial-mathematics', 'my_options_page' );
}

add_action( 'admin_init', 'my_admin_init' );
function my_admin_init() {
//    register_setting( 'my-settings-group', 'my-setting' );
//    register_setting( 'my-settings-group', 'my-setting-2' );
//    register_setting( 'my-settings-group', 'my-setting-3' );
//    register_setting( 'my-settings-group', 'my-setting-4' );
    add_settings_section( 'section-one', 'Section One', 'section_one_callback', 'financial-mathematics' );
//    add_settings_field( 'field-one', 'Field One', 'field_one_callback', 'my-plugin', 'section-one' );
//    add_settings_field( 'field-two', 'Field Twe', 'field_two_callback', 'my-plugin', 'section-one' );
//    add_settings_field( 'field-three', 'Field 3', 'field_3_callback', 'my-plugin', 'section-one' );
//    add_settings_field( 'field-four', 'Field 4', 'field_4_callback', 'my-plugin', 'section-one' );
}


function section_one_callback() {
	include 'ct1_plugin_help.html';
}

/*
function field_one_callback() {
    $setting = esc_attr( get_option( 'my-setting' ) );
    echo "<input type='text' name='my-setting' value='$setting' />";
}

function field_two_callback() {
    $setting = esc_attr( get_option( 'my-setting-2' ) );
    echo "<input type='text' name='my-setting-2' value='$setting' />";
}

function field_3_callback() {
    $setting = esc_attr( get_option( 'my-setting-3' ) );
    echo "<select name='my-setting-3'>";
    foreach (array('a','b','c','e') as $o){
?>
    <option value=<?php echo $o?> <?php if ($o==$setting) echo "SELECTED" ?>> <?php echo $o ?></option>
<?php }
}

function field_4_callback() {
    $setting = esc_attr( get_option( 'my-setting-4' ) );
    echo "<input type='checkbox' name='my-setting-4' " ; 
    if ($setting) echo "CHECKED"; 
    echo ">";
}
*/

function my_options_page() {
    ?>
    <div class="wrap">
        <h2>Financial Mathematics Options</h2>
        <form action="options.php" method="POST">
            <?php // settings_fields( 'my-settings-group' ); ?>
            <?php do_settings_sections( 'financial-mathematics' ); ?>
            <?php // submit_button(); ?>
        </form>
    </div>
    <?php
}


