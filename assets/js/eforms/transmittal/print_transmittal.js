var getUrlParameter = function getUrlParameter(sParam) {
	var sPageURL = decodeURIComponent(window.location.search.substring(1)),
	sURLVariables = sPageURL.split('&'),
	sParameterName,
	i;
	
	for (i = 0; i < sURLVariables.length; i++) 
	{
		sParameterName = sURLVariables[i].split('=');
		if (sParameterName[0] === sParam) 
		{
			return sParameterName[1] === undefined ? true : sParameterName[1];
		}
	}
};
	
param_id = getUrlParameter('id');
$.ajax({
	url : baseUrl("eforms/transmittal/populate_head/") + param_id,
	type : "GET",
	dataType : "JSON",
	success : function (data) {
		$('#company_from').append(data.data.company_from);
		$('#refernce_no').append(data.data.reference_no);
		$('#approved_by').append(data.data.approved_by);
		$('.approvername').append(data.data.approved_by);
		$('#ship_to').append(': <b>'+data.data.ship_to + '<br>' + '&nbsp;&nbsp;' + data.data.ship_to_address+'</b>');
		$('#ship_date').append(': <b>'+moment(data.data.ship_date).format('MMM DD, YYYY hh:mm A')+'</b>');
			if (data.data.is_service == 1) {
				$('#vehicle_label').append('Plate Number');
				$('#vehicle').append(': <b>'+data.plateno+'</b>');
				$('#driver_label').append('Driver');
				$('#driver').append(': <b>'+data.data.driver+'</b>');
			} else if (data.data.is_others == 1) {
				$('#vehicle_label').append('Remarks');
				$('#vehicle').append(': <b>'+data.data.others_remarks+'</b>');
			}
		$('#transporter').append(': <b>'+data.data.transporter+'</b>');
		populate_body(id);
	},
	error : function (jqXHR, textStatus, errorThrown) {
		alert(errorThrown);
	}
});

function populate_body(id) {
	$.ajax({
		url : baseUrl("eforms/transmittal/populate_body/") + id,
		type : "GET",
		dataType : "JSON",
		success : function (data) {
			$.each(data, function(key, val) {
				$('#tbody').append('<tr>'+'<td width="100%"><b>' + val.description + '</b></td>'+'</tr>');	
			});
		},
		error : function (jqXHR, textStatus, errorThrown) {
		alert(errorThrown);
		}
	});
}
	
function print_trans(){
	window.print();
	window.close();
}