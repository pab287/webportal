let pay_count = 0 , acctg_count = 0, approval_count = 0;

$(".spinner").css('display', 'flex');
socket.on('connect', () => {
	var comp = _for_ca_actions.indexOf("ca_acctg_fo_notif") !== -1 ? company_id : "", total;
	socket.emit('getCount', {"comp": comp, "currentAction" : _for_ca_actions});
	socket.on('dataCount', function(d){
		$(".spinner").css('display', 'none');
		pay_count = _for_ca_actions.indexOf('ca_payroll_notif') !== -1 ? d.payroll : 0;
		acctg_count = _for_ca_actions.indexOf("ca_acctg_notif") !== -1 || _for_ca_actions.indexOf("ca_acctg_fo_notif") !== -1 ? d.acctg : 0;
		approval_count = _for_ca_actions.indexOf('ca_approval_notif') !== -1 ? d.approval : 0;
		total = pay_count + acctg_count + approval_count;
		vmCaNotification.total = total;

		if(total == 0){
			$("#for_notif").css('display', 'none');
		}else{
			$("#for_notif").css('display', 'inline-block');
		}
	});
});

function getCA(type, comp = null){
	var data = socket.emit('getData', {"type" : type, 'comp' : comp});

	if(data){
		socket.on('data', function(d){
			var args = JSON.parse(d.data);
			vmCaNotification.rows = Object.assign({}, args);

			var pay = _for_ca_actions.indexOf('ca_payroll_notif') !== -1 ? d.tempResponse.payroll : 0;
			var acctg = _for_ca_actions.indexOf("ca_acctg_notif") !== -1 || _for_ca_actions.indexOf("ca_acctg_fo_notif") !== -1 ? d.tempResponse.acctg : 0;
			var approval = _for_ca_actions.indexOf('ca_approval_notif') !== -1 ? d.tempResponse.approval : 0;

			vmCaNotification.payroll = pay;
			vmCaNotification.acctg = acctg;
			vmCaNotification.approval = approval;
			vmCaNotification.total = pay + acctg + approval;
		});
	}
}

var vmCaNotification = new Vue({
    el: "#for_notif",
    data: { rows: {}, total: null, payroll: null, acctg: null, approval: null },
	methods : {
		redirectViewUrl(id, type){
			window.open(siteUrl('eforms/cash_advance/view_cash_advance?id='+id+"&notif=true&type="+type));
		},
		getToDate: function (date){
			if(date){
				return moment(date).format('MMM. DD, YYYY');
			}
		}
	}
});