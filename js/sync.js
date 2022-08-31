let double_title_has_more = true;
let double_end_id = 0;
let timerInterval;

/**
 * 点击同步按钮
 */
jQuery(document).ready(function ($) {
    /**
     * 同步双标题
     * @param total 文章总数
     */
    function sync_post(total) {
        Swal.fire({
            title: '文章双标题生成中',
            html: '<b>准备中</b>',
            timer: (total + 1) * 3000,
            timerProgressBar: true,
            allowOutsideClick: false,
            showCloseButton: true,
            didOpen: () => {
                Swal.showLoading();
                const b = Swal.getHtmlContainer().querySelector('b')
                timerInterval = setInterval(() => {
                    if (double_title_has_more) {
                        $.post(double_title_obj.ajax_url, {
                            action: 'sync_post',
                            _ajax_nonce: double_title_obj.nonce,
                            double_end_id: double_end_id,
                            double_type: '2'
                        }, function (data) {
                            double_title_has_more = data.has_more || false;
                            double_end_id = data.end_id || 0;
                            b.textContent = data.post_id + ' ' + data.post_title;
                        });
                    } else {
                        clearInterval(timerInterval);
                        Swal.close();
                        Swal.fire({
                            title: '温馨提示',
                            text: "文章双标题生成成功",
                            icon: 'success',
                            showConfirmButton: true,
                            confirmButtonText: '确定'
                        });
                    }
                }, 3000);
            },
            willClose: () => {
                clearInterval(timerInterval);
            }
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.timer) {
                Swal.fire({
                    title: '温馨提示',
                    text: "文章双标题生成成功",
                    icon: 'success',
                    showConfirmButton: true,
                    confirmButtonText: '确定'
                });
            }
        });
    }

    // 同步文章双标题
    $("#sync_post").click(function () {
        Swal.fire({
            title: '确定要预生成文章双标题吗?',
            showCancelButton: true,
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            text: '每隔3秒钟生成1篇文章双标题',
            icon: 'warning'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(double_title_obj.ajax_url, {
                    action: 'sync_post',
                    _ajax_nonce: double_title_obj.nonce,
                }, function (data) {
                    double_title_has_more = data.has_more || false;
                    double_end_id = data.end_id || 0;
                    let total = data.total || 0;
                    if (!double_title_has_more) {
                        Swal.fire({
                            title: '温馨提示',
                            text: "没有文章需要预生成双标题",
                            icon: 'success',
                            showConfirmButton: true,
                            confirmButtonText: '确定'
                        });
                        return;
                    }
                    sync_post(total);
                });
            }
        });
    });

    // 删除文章双标题
    $('#delete_post').click(function (){
        Swal.fire({
            title: '确定要删除文章双标题吗?',
            showCancelButton: true,
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            text: '删除后，文章需要重新生成双标题',
            icon: 'warning'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(double_title_obj.ajax_url, {
                    action: 'delete_post',
                    _ajax_nonce: double_title_obj.nonce,
                }, function (data) {
                    Swal.fire({
                        title: '温馨提示',
                        text: "删除文章双标题成功",
                        icon: 'success',
                        showConfirmButton: true,
                        confirmButtonText: '确定'
                    });
                });
            }
        });
    });
});