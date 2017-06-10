<div class="wrap">

    <ul class="subsubsub">
        <li class="all"><a class="current" href="<?php echo $this->remove_extra_query_args(); ?>"><?php _e('All', UD_NOTIFICATIONS_DOMAIN); ?> <span class="count">(<?php echo count($notifications); ?>)</span></a> |</li>
        <?php
        if ($roles) {
            foreach ($roles as $role => $count) {
                $role_link = add_query_arg('role', $role);
                ?>
                <li class="<?php echo $role; ?>"><a href="<?php echo $role_link; ?>"><?php echo $role; ?> <span class="count">(<?php echo $count; ?>)</span></a> |</li>

                <?php
            }
        }
        ?>


    </ul>



    <form method="post" action="<?php echo $this->remove_extra_query_args(); ?>">

        <p class="search-box">
            <label for="user-search-input" class="screen-reader-text"><?php _e('Search Users', UD_NOTIFICATIONS_DOMAIN); ?>:</label>
            <input type="search" value="" name="s" id="user-search-input">
            <input type="submit" value="Search Users" class="button" id="search-submit"></p>

        <?php wp_nonce_field('notification_list_wpnonce', 'notification_list_wpnonce_field'); ?>	

        <div class="tablenav top">

            <div class="alignleft actions bulkactions">
                <label class="screen-reader-text" for="bulk-action-selector-top"><?php _e('Select bulk action', UD_NOTIFICATIONS_DOMAIN); ?></label><select id="bulk-action-selector-top" name="action">
                    <option value="-1"><?php _e('Bulk Actions', UD_NOTIFICATIONS_DOMAIN); ?></option>
                    <option value="remove"><?php _e('Remove', UD_NOTIFICATIONS_DOMAIN); ?></option>
                </select>
                <input type="submit" value="<?php _e('Apply', UD_NOTIFICATIONS_DOMAIN); ?>" class="button action" id="doaction">
            </div>




            <br class="clear">
        </div>

        <h3 class="screen-readere-text"><?php _e('Notifications list', UD_NOTIFICATIONS_DOMAIN); ?></h3>


        <table class="wp-list-table widefat fixed striped uesers">
            <thead>
                <tr>
                    <td class="check-column" ><label for="cb-select-all-1" class="screen-reader-text"><?php _e('Select All', UD_NOTIFICATIONS_DOMAIN); ?></label><input type="checkbox" ></td>
                    <th class="manage-column column-username column-primary sortable desc" ><span><?php _e('Username', UD_NOTIFICATIONS_DOMAIN); ?></span></th>
                    <th class="manage-column column-name sortable desc"  scope="col"><span><?php _e('Notification', UD_NOTIFICATIONS_DOMAIN); ?></span></th>
                    <th class="manage-column column-email sortable desc"  scope="col"><span><?php _e('Email', UD_NOTIFICATIONS_DOMAIN); ?></span></th>
                    <th class="manage-column column-role"  scope="col"><?php _e('Role', UD_NOTIFICATIONS_DOMAIN); ?></th>
                    <th class="manage-column column-posts num"  scope="col"><?php _e('Count', UD_NOTIFICATIONS_DOMAIN); ?></th>	
                    <th class="manage-column column-posts num"  scope="col"><?php _e('Checked', UD_NOTIFICATIONS_DOMAIN); ?></th>	
                </tr>
            </thead>

            <tbody id="the-list">
                <?php
                if ($notifications) {
                    foreach ($notifications as $notification) {
                        foreach ($notification as $key => $val) {

                            $key_data = explode('-', $key);
                            $user_id = $key_data[0];
                            $notifications_by_user = $notifications_count[$user_id];
                            if (isset($_GET['role']) && $user_fields[$user_id]['role'] != $_GET['role'])
                                continue;
                            if (!empty($_POST['s']) && false === stripos($user_fields[$user_id]['user_name'], $_POST['s']))
                                continue;;


                            $not_id = $key_data[1];
                            $remove_link = add_query_arg('remove', $key);
                            ?>

                            <tr id="user-<?php $user_id; ?>">
                                <th class="check-column" scope="row">
                                    <label for="user_<?php echo $user_id; ?>" class="screen-reader-text"></label>
                                    <input type="checkbox" value="<?php echo $key; ?>"  id="user_<?php echo $key; ?>" name="remove-notifications[]">
                                </th>
                                <td data-colname="Username" class="username column-username has-row-actions column-primary">
                                    <?php echo get_avatar($user_id, 32) ?>
                                    <strong><a href="<?php echo get_edit_user_link($user_id) ?> "><?php echo $user_fields[$user_id]['user_name']; ?></a></strong><br>
                                    <div class="row-actions">

                                        <span class="remove"><a href="<?php echo $remove_link; ?>" class="submitdelete"><?php _e('Remove', UD_NOTIFICATIONS_DOMAIN); ?></a></span>
                                    </div>

                                </td>

                                <td data-colname="Name" class="name column-name"><?php echo $val['data']['text']; ?></td>
                                <td data-colname="Email" class="email column-email"><a href="mailto:<?php echo $user_fields[$user_id]['user_email']; ?>"><?php echo $user_fields[$user_id]['user_email']; ?></a></td>
                                <td data-colname="Role" class="role column-role"><?php echo $user_fields[$user_id]['role']; ?></td>
                                <td data-colname="Posts" class="posts column-posts num"><?php echo $notifications_by_user; ?></td>
                                <td data-colname="checked" class="posts column-posts num"><?php echo !empty($val['data']['checked'])?'<span class="notification-checked">'._e('Yes', UD_NOTIFICATIONS_DOMAIN).'</span>':'<span class="notification-unchecked">No</span>'; ?></td>
                            </tr>

                        </tbody>
                        <?php
                    }
                }
            }
            ?>

            <tfoot>
                <tr>
                    <td class="check-column" ><label for="cb-select-all-2" class="screen-reader-text"><?php _e('Select All', UD_NOTIFICATIONS_DOMAIN); ?></label><input type="checkbox" id="cb-select-all-2"></td>
                    <th class="manage-column column-username column-primary sortable desc" ><span><?php _e('Username', UD_NOTIFICATIONS_DOMAIN); ?></span></th>
                    <th class="manage-column column-name sortable desc"  scope="col"><span><?php _e('Notification', UD_NOTIFICATIONS_DOMAIN); ?></span></th>
                    <th class="manage-column column-email sortable desc"  scope="col"><span><?php _e('Email', UD_NOTIFICATIONS_DOMAIN); ?></span></th>
                    <th class="manage-column column-role"  scope="col"><?php _e('Role', UD_NOTIFICATIONS_DOMAIN); ?></th>
                    <th class="manage-column column-posts num"  scope="col"><?php _e('Count', UD_NOTIFICATIONS_DOMAIN); ?></th>
                    <th class="manage-column column-posts num"  scope="col"><?php _e('Checked', UD_NOTIFICATIONS_DOMAIN); ?></th>
                </tr>
            </tfoot>

        </table>
        <div class="tablenav bottom">

            <div class="alignleft actions bulkactions">
                <label class="screen-reader-text" for="bulk-action-selector-bottom"><?php _e('Select bulk action', UD_NOTIFICATIONS_DOMAIN); ?></label><select id="bulk-action-selector-bottom" name="action2">
                    <option value="-1"><?php _e('Bulk Actions', UD_NOTIFICATIONS_DOMAIN); ?></option>
                    <option value="remove"><?php _e('Remove', UD_NOTIFICATIONS_DOMAIN); ?></option>
                </select>
                <input type="submit" value="<?php _e('Apply', UD_NOTIFICATIONS_DOMAIN); ?>" class="button action" id="doaction2">
            </div>

            <br class="clear">
        </div>
    </form>




</div>