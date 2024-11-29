<?php
/*
Plugin Name: دکمه پشتیبانی شناور
Description: این افزونه یک دکمه شناور برای پشتیبانی ایجد می کند که شما می توانید در آن یک لوگوی دلخواه و یک لینک بارگذاری کنید تا در سایت نمایش داده شود
Version: 1.0.0
Author: عبدالرحمان مهدوی
*/

// ایجاد جدول در دیتابیس در هنگام فعال‌سازی افزونه
function fsb_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'support_button';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        logo varchar(255) NOT NULL,
        contact_link varchar(255) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'fsb_create_table');

// افزودن منوی مدیریتی
function fsb_admin_menu() {
    add_menu_page('دکمه شناور پشتیبانی', 'دکمه پشتیبانی', 'manage_options', 'fsb-settings', 'fsb_settings_page');
}
add_action('admin_menu', 'fsb_admin_menu');

// صفحه تنظیمات افزونه
function fsb_settings_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'support_button';
    
    if ($_POST['fsb_save']) {
        $logo = $_POST['fsb_logo'];
        $contact_link = $_POST['fsb_contact_link'];
        
        // ذخیره اطلاعات در دیتابیس
        $wpdb->replace(
            $table_name,
            array(
                'logo' => $logo,
                'contact_link' => $contact_link
            ),
            array(
                '%s',
                '%s'
            )
        );
    }

    // بارگذاری اطلاعات از دیتابیس
    $row = $wpdb->get_row("SELECT * FROM $table_name LIMIT 1");
    $logo = $row ? $row->logo : '';
    $contact_link = $row ? $row->contact_link : '';

    ?>
    <form method="post">
        <h2>تنظیمات دکمه پشتیبانی</h2>
        <label for="fsb_logo">لینک لوگو:</label>
        <input type="text" name="fsb_logo" value="<?php echo esc_attr($logo); ?>" />
        <br>
        <label for="fsb_contact_link">لینک پشتیبانی:</label>
        <input type="text" name="fsb_contact_link" value="<?php echo esc_attr($contact_link); ?>" />
        <br>
        <input type="submit" name="fsb_save" value="ذخیره" />
    </form>
    <?php
}

// افزودن دکمه شناور به سایت
function fsb_floating_button() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'support_button';
    $row = $wpdb->get_row("SELECT * FROM $table_name LIMIT 1");
    
    if ($row) {
        $logo = esc_url($row->logo);
        $contact_link = esc_url($row->contact_link);
        ?>
        <div id="fsb-button" style="position:fixed; bottom:20px; right:20px; width: 55px; height: 55px; cursor: pointer; display: flex; justify-content: center; align-items: center;">
            <a href="<?php echo $contact_link; ?>" target="_blank"><img src="<?php echo $logo; ?>" style="width: 50px; height: 50px;" /></a>
        </div>
        <style>
            #fsb-button img {
                border-radius: 50%;
            }
        </style>
        <?php
    }
}
add_action('wp_footer', 'fsb_floating_button');
