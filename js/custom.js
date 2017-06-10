jQuery(document).ready(function ($) {

    $('.notification-by').click(function () {
        if ($(this).val() == 'role') {
            $('.by-role').show();
            $('.by-user').hide();

        } else {
            $('.by-role').hide();
            $('.by-user').show();

        }

    });



    $('#notification-form').submit(function () {
        var errors = [];

        if ($('.notification-by-role').is(':checked')) {
            if ($('.by-role select')[0].selectedIndex < 0)
                errors.push(udL10n.errorRole);

        } else {
            if (!$('.by-user select')[0].selectedIndex < 0)
                errors.push(udL10n.errorUser);

        }
        if (!$('#notification').val().length) {
            errors.push(udL10n.errorNotification);
        }
        if (errors.length) {
            var message = '';
            for (var e in errors) {
                message += '<p>' + errors[e] + '</p>';
            }
            $('.messages').html('<div class="notice notice-error is-dismissible"> ' + message + ' </div>');
            $("html, body").animate({ scrollTop: 0 }, "slow");
            return false;

        }
        return true;

    });

    $('.ud-notification .notice-dismiss').click(function () {
        $.post(ajaxurl, {action: 'notice_dismiss', key: $(this).parent().data('key')}, function () {

        });

    });

});


