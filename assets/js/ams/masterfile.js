var _dtUserRole = $("#table-asset").DataTable({
	dom: '<"toolbar">frtlip',
	serverSide: true,
	processing: true,
	ajax: {
		url: baseUrl("masterfile/get_asset_collection"),
		type: "post",
		dataType: "json",
		data: {  _csrf_token : _csrf_hash }
	}, columns: [
		{ data: "image"},
		{ data: "assetacode"},
		{ data: "name"},
		{ data: "asset_name"},
		{ data: "area"},
		{ data: "sub_cat_code"},
		{ data: "station"},
		{ data: "created_by"},
        { data: null, width: "8%", className: "text-center" },
    ]
	// ], columnDefs: [{
	// 	data: null,
	// 	defaultContent: "",
	// 	targets: -1,
	// 	orderable: false,
	// 	render: function ( data, type, row, meta ) { return itemDatatableActions(row.id); },
	// }, { 
	// 	targets: "_all", 
	// 	defaultContent: "", 
	// }],
	// order: [[ 0, "desc" ]],
	// initComplete: function(settings, json){
	// 	if(typeof roleActionUpdate == "function"){ roleActionUpdate(); }
	// }
});