$(function () {

$('tr').hover(function () {
    $(this).addClass('info');
},function () {
    $(this).removeClass('info');
});

$('#task_1').on('click',function () {

    $.ajax({
        url:"index2.php",
        type:"POST",
        data:{type:1},
        success:function (data) {

            doContent(data);
        }
    });
});

$('#task_2').on('click',function () {
    var order=$('#orderNumber').val();
    if(order.length<1){
        $('span').text('Введите № заказа');
    }
    else {

    $.ajax({
        url: "index2.php",
        type: "POST",
        data: 'type=2&order='+order,
        success: function (data) {
            doContent(data);
        }
    });}
});
$('#task_3').on('click',function () {
        $.ajax({
            url:"index2.php",
            type:"POST",
            data:{type:3},
            success:function (data) {
                doContent(data);
            }
        });
});
$('#task_4').on('click',function () {
        $.ajax({
            url:"index2.php",
            type:"POST",
            data:{type:4},
            success:function (data) {
                doContent(data);
            }
        });
});



});

/**
 * =====================================
 * Ф-я для получения ответа/адекватной обработки ошибки
 * ======================================
 * @param data
 */
function doContent(data) {
    if(data=='')
    {
        $('#result').html('<h1>Нет данных по вашему запросу</h1>');
    }
    else
    {
        $('#result').html(data);
    }}


