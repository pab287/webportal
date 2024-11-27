if(module_id == 1){
    let nearingbell = [];
    socket.on('employee_list', function(d){
        nearingbell.push("nearingonemonth");
        vmHRISNearingOneMonthNotification.rows = Object.assign({}, d);
        vmHRISNearingOneMonthNotification.count = d.length;

        vmHRISNearingOneMonthPageNotification.rows = Object.assign({}, d);
        vmHRISNearingOneMonthPageNotification.count = d.length;
        $("#nearingBadge").html(d.length);
    });
}