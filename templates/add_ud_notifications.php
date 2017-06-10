<form method="post" action="" id="notification-form">
    <?php wp_nonce_field('notification_wpnonce', 'notification_wpnonce_field'); ?>


    <h2>Add Notification</h2>
    <div class="messages">
        <?php echo $this->create_notification_message($message, $type); ?>
    </div>


    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row"><?php echo _e('Create Notification by', UD_NOTIFICATIONS_DOMAIN); ?>:</th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text"><span><?php echo _e('Create Notification by', UD_NOTIFICATIONS_DOMAIN); ?>:</span></legend>
                        <label><input class="notification-by notification-by-role" type="radio" checked="checked" value="role" name="notification_by"> <span class=""><?php echo _e('Role', UD_NOTIFICATIONS_DOMAIN); ?></span></label><br>
                        <label><input class="notification-by notification-by-user" type="radio" value="user" name="notification_by"> <span class=""><?php echo _e('User', UD_NOTIFICATIONS_DOMAIN); ?></span></label><br>

                    </fieldset>
                </td>
            </tr>
            <tr class="by-role">
                <th><label for="role"><?php echo _e('Role', UD_NOTIFICATIONS_DOMAIN); ?></label></th>
                <td>

                    <select name="selected_roles[]" multiple="">
                        <?php
                        global $wp_roles;

                        $active_roles = count_users();

                        $selected = 'selected="selected"';
                        $all_roles = $wp_roles->roles;
                        $editable_roles = apply_filters('editable_roles', $all_roles);
                        foreach ($editable_roles as $key => $val) {
                            if (!empty($active_roles['avail_roles'][$key])) {
                                echo '<option value="' . $key . '" ' . $selected . '>' . $val['name'] . '</option>';
                                $selected = '';
                            }
                        }

                        //var_dump($editable_roles);
                        ?>

                    </select>
                    <p><?php echo _e('Select one or more roles of users to be notified', UD_NOTIFICATIONS_DOMAIN); ?></p>
                </td>
            </tr>
            <tr class="by-user">
                <th><label for="role"><?php echo _e('Users', UD_NOTIFICATIONS_DOMAIN); ?></label></th>
                <td>
                    <select name="selected_users[]" multiple="">
                        <?php
                        $users = get_users();
                        $selected = 'selected="selected"';
                        foreach ($users as $user) {
                            echo '<option value="' . $user->data->ID . '" ' . $selected . '>' . $user->data->user_login . '</option>';
                            $selected = '';
                        }
                        ?>
                    </select>
                    <p><?php echo _e('Select one or more users to be notified', UD_NOTIFICATIONS_DOMAIN); ?></p>
                </td>
            </tr>

            <tr class="">
                <th><label for="description"><?php echo _e('Notification', UD_NOTIFICATIONS_DOMAIN); ?></label></th>
                <td>
                    <textarea cols="30" rows="5"  name="notification" id="notification"></textarea>
                    <p class="notification"><?php echo _e('Add a notification text', UD_NOTIFICATIONS_DOMAIN); ?>.</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo _e('Notification type', UD_NOTIFICATIONS_DOMAIN); ?>:</th>
                <td>
                    <fieldset><legend class="screen-reader-text"><span><?php echo _e('Create Notification by', UD_NOTIFICATIONS_DOMAIN); ?>:</span></legend>
                        <label><input type="radio" checked="checked" value="error" name="notification_type"> <span class=""><?php echo _e('Notification Error', UD_NOTIFICATIONS_DOMAIN); ?></span></label><br>
                        <label><input type="radio" value="warning" name="notification_type"> <span class=""><?php echo _e('Notification Warning', UD_NOTIFICATIONS_DOMAIN); ?></span></label><br>

                        <label><input type="radio" value="success" name="notification_type"> <span class=""><?php echo _e('Notification Success', UD_NOTIFICATIONS_DOMAIN); ?></span></label><br>
                        <label><input type="radio" value="info" name="notification_type"> <span class=""><?php echo _e('Notification Info', UD_NOTIFICATIONS_DOMAIN); ?></span></label><br>

                    </fieldset>
                </td>
            </tr>




        </tbody>
    </table>




    <p class="submit"><input type="submit" value="<?php echo _e('Add notification', UD_NOTIFICATIONS_DOMAIN); ?>" class="button button-primary" id="submit" name="send-notification"></p>
</form> 

