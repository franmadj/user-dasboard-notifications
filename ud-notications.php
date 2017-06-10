<?php

/*
  Plugin Name: User Dashboard Notifications
  Description: Create and manage notifications in admin area for specific or group(Roles) of users
  Version: 1.0.0
  Author: Francisco Mauri
  Text Domain: user-dashboard-notifications
  Domain Path: /languages
  License: GPLv2 or later
 */

define('UD_NOTIFICATIONS_VERSION', '1.0.0');
define('UD_NOTIFICATIONS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('UD_NOTIFICATIONS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('UD_NOTIFICATIONS_DOMAIN', 'user-dashboard-notifications');

//if (!function_exists('dame')) {
//
//    function dame($data) {
//        echo '<pre>';
//        var_dump($data);
//        echo '</pre>';
//    }
//
//}

class User_Dashboard_Notifications {

    function __construct() {

        add_action('admin_menu', [$this, 'create_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'add_scripts']);
        add_action('wp_ajax_notice_dismiss', [$this, 'check_notice']);
        add_action('all_admin_notices', [$this, 'display_notices']);
        add_action('plugins_loaded', [$this, 'load_text_domain']);
    }

    
    /**
     * 
     * Add plugin menu items into admin menu
     *
     */
    function create_admin_menu() {
        add_menu_page('User Dashboard Notifications', __('User Dashboard Notifications', UD_NOTIFICATIONS_DOMAIN), 'manage_options', 'ud-notifications', [$this, 'list_ud_notifications'], 'dashicons-megaphone');
        add_submenu_page('ud-notifications', __('List Notifications', UD_NOTIFICATIONS_DOMAIN), __('List Notifications', UD_NOTIFICATIONS_DOMAIN), 'manage_options', 'ud-notifications', [$this, 'list_ud_notifications']);
        add_submenu_page('ud-notifications', __('Add Notifications', UD_NOTIFICATIONS_DOMAIN), __('Add Notifications', UD_NOTIFICATIONS_DOMAIN), 'manage_options', 'add-ud-notifications', [$this, 'add_ud_notifications']);
    }
    
    /**
     * 
     * Add all CSS and JS
     *
     */
    function add_scripts() {
        wp_register_style('custom_ud_admin_css', UD_NOTIFICATIONS_PLUGIN_URL . '/css/style.css', false, '1.0.0');
        wp_enqueue_style('custom_ud_admin_css');
        wp_register_script('custom_ud_admin_js', UD_NOTIFICATIONS_PLUGIN_URL . '/js/custom.js', [], '1.0.0', true);
        wp_enqueue_script('custom_ud_admin_js');
        wp_localize_script('custom_ud_admin_js', 'udL10n', array(
            'errorRole' => __('Select at least one role', UD_NOTIFICATIONS_DOMAIN),
            'errorUser' => __('Select at least one user', UD_NOTIFICATIONS_DOMAIN),
            'errorNotification' => __('You must provide a notification text', UD_NOTIFICATIONS_DOMAIN)
        ));
    }

    /**
     * 
     * Update notification as checked by ajax
     *
     */
    function check_notice() {
        $key = explode('-', $_POST['key']);
        $user_id = $key[0];
        $not_id = $key[1];
        if ($user_data = $this->get_notifications_by_user($user_id)) {
            if (is_array($user_data)) {
                $user_data[$not_id]['checked'] = 1;
                $user_data = serialize($user_data);
                update_user_meta($user_id, 'ud_notification', $user_data);
            }
        }
        wp_die();
    }
    
    /**
     * 
     * Display notices in admin area
     *
     */
    function display_notices() {//notice-error, notice-warning, notice-success, or notice-info
        $user_id = get_current_user_id();
        if ($user_data = $this->get_notifications_by_user($user_id)) {
            foreach ($user_data as $key => $notification) {
                if (1 == $notification['checked'])
                    continue;
                $key = $user_id . '-' . $key;
                echo $this->create_notification_message($notification['text'], $notification['type'], $key);
            }
        }
    }

    function load_text_domain() {
        $plugin_dir = basename(dirname(__FILE__));
        $loaded = load_plugin_textdomain('user-dashboard-notifications', false, $plugin_dir . '\languages');
    }

    /**
     * 
     * Get the url for listing notifications
     *
     * @return string
     */
    function remove_extra_query_args() {
        $url = explode('?', esc_url_raw(add_query_arg(array())));
        return $url[0] . '?page=ud-notifications';
    }
    
    
    /**
     * 
     * Build HTML admin notifications
     *
     * @param string $message  the message text to be displyed
     * @param string $type  the notification type  {error, warning, success or info}
     * @param integer $key  the unique identifier for a notification {id_user-id_notification}
     * @return string
     */
    function create_notification_message($message, $type = 'success', $key = '') {
        if (empty($message))
            return '';
        if ($key)
            $key = 'data-key=' . $key;
        if (!in_array($type, ['success', 'error', 'warning', 'info']))
            $type = 'success';
        return '<div class="notice notice-' . $type . ' is-dismissible ud-notification" ' . $key . '>
            <p>' . $message . '</p>
        </div>';
    }
    
    /**
     * 
     * Remove admin notifications
     *
     * @param integer $key  the unique identifier for a notification {id_user-id_notification}
     * @return null
     */
    function remove_notifications($key) {
        $key_data = explode('-', $key);
        $user_id = $key_data[0];
        $not_id = $key_data[1];
        if ($user_data = $this->get_notifications_by_user($user_id)) {
            if (is_array($user_data)) {
                unset($user_data[$not_id]);
                $user_data = serialize($user_data);
                update_user_meta($user_id, 'ud_notification', $user_data);
            }
        }
    }
    
    /**
     * 
     * Create screen to list notifications
     *
     */
    function list_ud_notifications() {
        $remove = ( (isset($_POST['action']) && 'remove' == $_POST['action']) or ( isset($_POST['action2']) && 'remove' == $_POST['action2'])) ? true : false;
        if (isset($_GET['remove'])) {
            $this->remove_notifications($_GET['remove']);
        } else if (isset($_POST['notification_list_wpnonce_field']) && wp_verify_nonce($_POST['notification_list_wpnonce_field'], 'notification_list_wpnonce')) {
            if ($remove && !empty($_POST['remove-notifications']))
                foreach ($_POST['remove-notifications'] as $not) {
                    $this->remove_notifications($not);
                }
        }

        $args = array(
            'meta_key' => 'ud_notification',
            'meta_value' => '',
            'meta_compare' => '!=',
        );
//        if(isset($_GET['role'])){
//            $args['role']=$_GET['role'];
//        }
        $notifications = $user_fields = $roles = $notifications_count = [];
        if ($users = get_users($args)) {
            foreach ($users as $user) {

                if ($user_data = $this->get_notifications_by_user($user->ID)) {

                    if (!isset($user_fields[$user->ID]))
                        $user_fields[$user->ID] = ['user_name' => $user->user_login, 'user_email' => $user->user_email, 'role' => $user->roles[0]];
                    if (is_array($user_data)) {
                        foreach ($user_data as $key => $val) {
                            $new_key = $user->ID . '-' . $key;
                            $notifications[][$new_key]['data'] = $val;

                            if (empty($notifications_count[$user->ID])) {
                                $notifications_count[$user->ID] = 1;
                            } else {
                                $notifications_count[$user->ID] ++;
                            }

                            if (!isset($roles[$user->roles[0]])) {
                                $roles[$user->roles[0]] = 1;
                            } else {
                                $roles[$user->roles[0]] ++;
                            }
                        }
                    }
                }
            }
        }

        require_once(UD_NOTIFICATIONS_PLUGIN_DIR . '/templates/list_ud_notifications.php');
    }
    
    /**
     * 
     * Create screen to Add notifications
     *
     */
    function add_ud_notifications() {
        $message = $type = '';
        $users_list = [];

        if (isset($_POST['send-notification']) && isset($_POST['notification_wpnonce_field']) && wp_verify_nonce($_POST['notification_wpnonce_field'], 'notification_wpnonce')) {
            if ('role' == $_POST['notification_by'] && !empty($_POST['selected_roles']) && is_array($_POST['selected_roles'])) {
                if ($users = get_users(['role__in' => $_POST['selected_roles']])) {
                    foreach ($users as $user) {
                        $users_list[] = $user->ID;
                    }
                }
            } elseif ('user' == $_POST['notification_by'] && !empty($_POST['selected_users']) && is_array($_POST['selected_users'])) {
                $users_list = $_POST['selected_users'];
            }
            if (!empty($users_list)) {
                foreach ($users_list as $user_id) {
                    if ($user_data = $this->get_notifications_by_user($user_id)) {
                        $user_data[count($user_data)] = ['text' => $_POST['notification'], 'type' => $_POST['notification_type'], 'checked' => 0];
                    } else {
                        $user_data = [];
                        $user_data[0] = ['text' => $_POST['notification'], 'type' => $_POST['notification_type'], 'checked' => 0];
                    }

                    update_user_meta($user_id, 'ud_notification', serialize($user_data));
                }
                $message = __('Notification has been successfuly created', UD_NOTIFICATIONS_DOMAIN);
            } else {
                $message = __('No notificatons have been created, check', UD_NOTIFICATIONS_DOMAIN);
            }
        }
        require_once(UD_NOTIFICATIONS_PLUGIN_DIR . '/templates/add_ud_notifications.php');
    }
    
    
    /**
     * 
     * Get all notifications for a specific user
     *
     * @param string $user_id  the user ID to get the notifications for
     * @return array|boolean
     */
    function get_notifications_by_user($user_id) {
        if ($user_data = get_user_meta($user_id, 'ud_notification', true)) {
            return maybe_unserialize($user_data);
        }
        return false;
    }

}

if (is_admin())
    new User_Dashboard_Notifications ();


