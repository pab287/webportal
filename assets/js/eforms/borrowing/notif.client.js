socket.on('connect', function(){
    socket.emit('get_all_notif');

    var notifBell = [];
    socket.on('borrowed', function(d){
        if(d.length != 0){

            notifBell.push("overdue");
            vmTopNotification.rows = Object.assign({}, d);
            vmTopNotification.count = d.length;
            $("#notifBadge").html(d.length);
            $("#notification .m-dropdown__header-title").html(d.length+" New");
            $("#m_topbar_notification_icon #notifBar").text(d.length);
        }else{
            $("#notification").css('display', 'none');
        }
    });
});

$(document).on("click","#topbar_notifications_overdue #notif_area .notif", function(){
    var div_id = $(this).attr("value");
    window.location.href = baseUrl("eforms/borrowing/overdue_borrowing/?data="+div_id);
});