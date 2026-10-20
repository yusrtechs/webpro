<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .sync-container {
      padding: 20px;
      width: 100%;              /* ملئ الشاشة */
      background-color: #ffffff;
      border: 1px solid #dce3ea;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      overflow: hidden;

    }

    .sync-card {
      padding: 30px 20px;
      border: 1px solid #ddd;
      background-color: #f6f6f6;
    }

    .sync-card h3 {
      margin-bottom: 25px;
      color: #333;
      font-size: 16px;
      font-weight: bold;
    }

    .button-group {
      display: flex;
      gap: 15px;
      flex-wrap: nowrap;
      align-content: space-around;
      align-items: flex-end;
      justify-content: space-between;
    }

    .sync-btn {
      flex: 1;
      padding: 14px;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
      border: none;
      transition: 0.2s ease-in-out;
      position: relative;
      min-width: 150px;
      max-width: 250px; /* 🔹 الأزرار ما تكبر عن 250px */
    }


    .sync-btn.primary {
      background-color: #2a4d8f;
      color: #fff;
    }

    .sync-btn.primary:hover:not(.loading) {
      background-color: #1e3d72;
    }

    .sync-btn.secondary {
      background-color: #dee2e6;
      color: #333;
      border: 1px solid #1b52a0;
    }

    .sync-btn.secondary:hover:not(.loading) {
      background-color: #cdd3de;
    }

    /* Loader Animation */
    .sync-btn.loading {
      color: #1b52a0 !important;
      cursor: wait;
      pointer-events: none;
    }

    .sync-btn.loading::after {
      content: "";
      position: absolute;
      top: 50%;
      left: 50%;
      width: 18px;
      height: 18px;
      margin: -9px 0 0 -9px;
      border: 3px solid #fff;
      border-top: 3px solid #2a4d8f;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    </style>
    <style>
<?php if ($Settings->user_language == 'arabic') {
						?>

    /* Toast Container */
    .toast-container {
      position: fixed;
      bottom: 20px;
      left: 20px;
      display: flex;
      flex-direction: column;
      gap: 10px;
      z-index: 9999;
      direction: rtl;
    }
<?php }else {
		?>

/* Toast Container */
    .toast-container {
      position: fixed;
      bottom: 20px;
      right: 20px;
      display: flex;
      flex-direction: column;
      gap: 10px;
      z-index: 9999;
    }
<?php }
		?>

    .toast {
      background-color: #212529;
      color: #fff;
      padding: 12px 18px;
      border-radius: 8px;
      font-size: 14px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      opacity: 0;
      transform: translateX(100%);
      animation: slideIn 0.5s forwards, fadeOut 0.5s 3s forwards;
    }

    @keyframes slideIn {
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    @keyframes fadeOut {
      to {
        opacity: 0;
        transform: translateX(100%);
      }
    }
  </style>
<div class="box" style="display:none;">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-database"></i><?php echo lang('Synchronize_Data'); ?></h2>
    </div>

    <div class="sync-container">
    <div class="sync-card">

      <div class="button-group">
        <button class="sync-btn secondary" id="update_categories"><?php echo lang('Update_products'); ?></button>
        <a href="<?= admin_url('shop_settings'); ?>"  target="_blank"><button class="sync-btn primary" id="connect_store"><?php echo lang('visit_store'); ?></button></a>
      </div>
    </div>
  </div>


</div>

  <!-- Toast container -->
  <div class="toast-container" id="toastContainer"></div>

  <script>
    function startLoading(btn) {
      btn.classList.add("loading");

      // محاكاة عملية مزامنة (3 ثواني)
      setTimeout(() => {
        btn.classList.remove("loading");
        showToast("تمت المزامنة بنجاح ✅");
      }, 3000);
    }

    function showToast(message) {
      const container = document.getElementById("toastContainer");
      const toast = document.createElement("div");
      toast.className = "toast";
      toast.textContent = message;

      container.appendChild(toast);

      // إزالة التوست بعد انتهاء الأنيميشن
      setTimeout(() => {
        toast.remove();
      }, 4000);
    }
  </script>
<script type="text/javascript">

    $('document').ready(function () {


$(document)
    .ajaxStart(function() {
        $('#ajaxCall').hide();
    })
    .ajaxStop(function() {
        $('#ajaxCall').hide();
    });

    $('#connect_store_org').on('click', function (e) {
    e.preventDefault();

    let btn = this;
    btn.classList.add("loading");

    $.ajax({
        url: '<?= admin_url("shop_settings/get_store_link") ?>', // يرجع الرابط
        type: 'GET',
        dataType: 'json',
        success: function (res) {

            if (res.url) {

                // 1️⃣ فتح الرابط للمستخدم
                window.open(res.url, '_blank');

                // 2️⃣ تنفيذ الطلب في الخلفية (اختياري)
                let iframe = document.createElement("iframe");
                iframe.style.display = "none";
                iframe.src = res.url;
                document.body.appendChild(iframe);

                showToast("تم فتح المتجر والربط بنجاح ✅");
                $('#update_categories').trigger('click');
            }

            btn.classList.remove("loading");
        },
        error: function () {
            showToast("فشل الاتصال ❌");
            btn.classList.remove("loading");
        }
    });

});

$('#connect_store').on('click', function (e) {
    e.preventDefault();

    let btn = this;

    // إذا كان الزر يحتوي مسبقاً على الرابط جاهزاً، افتحه فوراً بشكل طبيعي ومسموح
    if ($(btn).data('url')) {
        window.open($(btn).data('url'), '_blank');
        return;
    }

    btn.classList.add("loading");

    // نفتح نافذة "تنبيهية/انتظار" فوراً بحدث الضغط الحقيقي
    let newWindow = window.open('', '_blank');
    if (newWindow) {
       // newWindow.document.write('<p style="font-family:sans-serif; text-align:center; margin-top:20%;">جاري تحضير رابط المتجر، يرجى الانتظار لحظة... ⏳</p>');
    }

    $.ajax({
        url: '<?= admin_url("shop_settings/get_store_link") ?>',
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            btn.classList.remove("loading");

            if (res && res.url) {
                // حفظ الرابط في الزر للضغطات القادمة
                $(btn).data('url', res.url);

                // توجيه النافذة المفتوحة مسبقاً للرابط الصحيح
                if (newWindow) {
                    newWindow.location.href = res.url;
                } else {
                    // إذا فشل فتح النافذة تماماً (بسبب إعدادات متشددة جداً في جهاز المستخدم)
                    // نقوم بتوجيه الصفحة الحالية أو إظهار زر واضح للاستكمال
                    window.location.href = res.url;
                }

                showToast("تم فتح المتجر والربط بنجاح ✅");
                $('#update_categories').trigger('click');
            } else {
                if (newWindow) newWindow.close();
                showToast("لم يتم العثور على رابط المتجر ❌");
            }
        },
        error: function (xhr) {
            btn.classList.remove("loading");
            if (newWindow) newWindow.close();
            console.error("تفاصيل الخطأ:", xhr.responseText);
            showToast("فشل الاتصال بالسيرفر ❌");
        }
    });
});
        $('#update_categories1').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();
            $.ajax({
                url: '<?=admin_url('shop_settings/update_categories')?>',
                type: 'GET',
                success: function (data) {
                    //console.log(data);
                    if(data.msg){
                        if(data.total!=0){
                            perf.html(data.msg+' '+Math.ceil(data.to * 100 / data.total)+' %');

                        }else{
                            perf.html(data.msg);

                        }


                     if(data.to!=data.total){
                        $('#update_categories').trigger('click');
                     }else{
                        btn.attr('disabled', false);
                     }

                    //btn.attr('disabled', false);
                }else{
                    perf.html(data);
                    btn.attr('disabled', false);
                }
                },
                error: function () {
                    perf.html('Request Failed!').show();
                    btn.attr('disabled', false);
                }
            });

        });

        $('#update_products1').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();
            $.ajax({
                url: '<?=admin_url('shop_settings/update_products')?>',
                type: 'GET',
                success: function (data) {
                    //console.log(data);
                    if(data.msg){
                        if(data.total!=0){
                            perf.html(data.msg+' '+Math.ceil(data.to * 100 / data.total)+' %');

                        }else{
                            perf.html(data.msg);

                        }


                     if(data.to!=data.total){
                        $('#update_products').trigger('click');
                     }else{
                        btn.attr('disabled', false);
                     }

                    //btn.attr('disabled', false);
                }else{
                    perf.html(data);
                    btn.attr('disabled', false);
                }
                },
                error: function () {
                    perf.html('Request Failed!').show();
                    btn.attr('disabled', false);
                }
            });
        });

        $('#update_categories').click(function (e) {
            var bttn=this;
            e.preventDefault();
            bttn.classList.add("loading");
            $.ajax({
                url: '<?=admin_url('shop_settings/update_categories')?>',
                type: 'GET',
                success: function (data) {
                    //console.log(data);
                    if(data.msg){
                        if(data.total!=0){
                            showToast(data.msg+' '+Math.ceil(data.to * 100 / data.total)+' %');

                        }else{
                            showToast(data.msg);


                        }


                     if(data.to!=data.total){
                        $('#update_categories').trigger('click');
                     }else{
                        bttn.classList.remove("loading");
                     }

                    //btn.attr('disabled', false);
                }else{
                    showToast(data);
                    bttn.classList.remove("loading");
                }
                },
                error: function () {
                    showToast('Request Failed!');
                    bttn.classList.remove("loading");
                }
            });

        });

        $('#update_products').click(function (e) {
            var bttn=this;
            e.preventDefault();
            bttn.classList.add("loading");
            $.ajax({
                url: '<?=admin_url('shop_settings/update_products')?>',
                type: 'GET',
                success: function (data) {
                    //console.log(data);
                    if(data.msg){
                        if(data.total!=0){
                            showToast(data.msg+' '+Math.ceil(data.to * 100 / data.total)+' %');

                        }else{
                            showToast(data.msg);


                        }


                     if(data.to!=data.total){
                        $('#update_products').trigger('click');
                     }else{
                        bttn.classList.remove("loading");
                     }

                    //btn.attr('disabled', false);
                }else{
                    showToast(data);
                    bttn.classList.remove("loading");
                }
                },
                error: function () {
                    showToast('Request Failed!');
                    bttn.classList.remove("loading");
                }
            });

        });

$('#connect_store').trigger('click');


    });



</script>