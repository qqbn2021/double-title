let double_title_start_id = 0;
let double_title_has_more = true;
jQuery(document).ready(function ($) {
    // 同步文章
    $("#sync_post").click(function () {
        if (!double_title_has_more) {
            return;
        }
        $("#sync_post").attr("disabled", "disabled");
        $('#sync_post').text('同步文章中...');
        let interval_id = setInterval(function () {
            $('#sync_post').text('同步文章起始ID：' + double_title_start_id);
            if (double_title_has_more) {
                $.post(double_title_obj.ajax_url, {
                    _ajax_nonce: double_title_obj.nonce,
                    action: "sync_post",
                    start_id: double_title_start_id
                }, function (data) {
                    double_title_start_id = data.start_id || 0;
                    double_title_has_more = data.has_more || false;
                });
            } else {
                clearInterval(interval_id);
                $('#sync_post').text('同步文章成功');
                $('#sync_post').removeAttr('disabled');
            }
        }, 3000);
    });

    // 同步标题
    $("#sync_title").click(function () {
        if (!double_title_has_more) {
            return;
        }
        $("#sync_title").attr("disabled", "disabled");
        $('#sync_title').text('同步标题中...');
        let interval_id = setInterval(function () {
            if (double_title_has_more) {
                $.post(double_title_obj.ajax_url, {
                    _ajax_nonce: double_title_obj.nonce,
                    action: "sync_title",
                    start_id: double_title_start_id
                }, function (data) {
                    double_title_start_id = data.start_id || 0;
                    double_title_has_more = data.has_more || false;
                    let message = '同步ID：' + double_title_start_id;
                    if (data.subtitle) {
                        message += '，副标题：' + data.subtitle;
                    }
                    if (data.msg) {
                        message += '，' + data.msg;
                    }
                    $('#sync_title').text(message);
                    if (data.auth_fail) {
                        clearInterval(interval_id);
                    }
                });
            } else {
                clearInterval(interval_id);
                $('#sync_title').text('同步标题成功');
                $('#sync_title').removeAttr('disabled');
            }
        }, 3000);
    });

    /**
     * 修改副标题
     */
    $('#double_title_subtitle').change(function () {
        $.post(double_title_obj.ajax_url, {
            _ajax_nonce: double_title_obj.nonce,
            action: "update_title",
            subtitle: $('#double_title_subtitle').val(),
            id: $('#double_title_id').val()
        }, function (data) {
            if (data.result){
                location.reload();
            }
        });
    });
});