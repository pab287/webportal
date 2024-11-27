var getUrlParameter = function getUrlParameter(sParam){
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
    sURLVariables = sPageURL.split('&'),
    sParameterName,
    i;
    for (i = 0; i < sURLVariables.length; i++){
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam){
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
  };

param_id = getUrlParameter('id');

const contentEl = $('#content');
const categoryNavEl = $('#category-navigation');
const previewEl = $('#preview-container');
const mContentCls = $('.m-content');

$(previewEl).css({"width": ((contentEl.width() - categoryNavEl.width()) - 72.5) + "px"});

$("#add_modal").hide();
$("#add_topic").hide();
$("#edit_topic").hide();
$("#edit_modal").hide();
$("#add_file").hide();
$("#update_modal").hide();

$.validate({
    form : '#add_form',
    lang: 'en',
    onSuccess : function(form) {
            $.ajax({
                url: baseUrl("tos/training/add_category"),
                type: "POST",
                dataType: "json",
                data: $("#add_form").find("input,select,textarea").serialize(),
                beforeSend: function(){
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function(data){
                    if(data){
                        $("#add_modal").modal("hide");
                        $("#description").val("");     
                        toastr.success("Category added successfully", "Notification", 5000);
                        $("#treeview").jstree(true).settings.core.data.url = baseUrl("tos/training/treeview/");
                        $("#treeview").jstree(true).refresh();
                    }else{
                        toastr.error("Error processing request.", "Notification", 5000);
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
        return false;
    },
});

function delete_cat($id){
    if(confirm('Are you sure you want to delete this entire folder?')){
    $.ajax({
        url: baseUrl("tos/training/delete_category/") + $id,
        type: "GET",
        success: function() {
            $("#treeview").jstree(true).settings.core.data.url = baseUrl("tos/training/treeview/");
            $("#treeview").jstree(true).refresh();
        }     
    });
    }
}

function edit_cat($id){
    $.ajax({
        url: baseUrl("tos/training/edit_category/") + $id,
        type: "GET",
        success: function(data) {
            vmTab1.vm_tab1 = Object.assign({}, data);
            $.validate({
                form : '#edit_form',
                lang: 'en',
                onSuccess : function(form) {
                        $.ajax({
                            url: baseUrl("tos/training/update_category/") + $id,
                            type: "POST",
                            dataType: "json",
                            data: $("#edit_form").find("input,textarea").serialize(),
                            beforeSend: function(){
                                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                            },
                            success: function(data){
                                if(data){
                                    $("#edit_modal").modal("hide");
                                    $("#edit_description").val("");    
                                    $("#treeview").jstree(true).settings.core.data.url = baseUrl("tos/training/treeview/");
                                    $("#treeview").jstree(true).refresh();
                                    toastr.success("Data updated Successfully.", "Notification", 5000);
                                }else{
                                    toastr.error("Error processing request.", "Notification: Error", 5000);
                                }
                                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                            }
                        });
                    return false;
                },
            });
        }     
    });
}

var vmTab1 = new Vue({
    el: "#edit_form",
    data: { vm_tab1: {} },
  });

var treeview = $("#treeview").jstree(true)
$.ajax({
    url: baseUrl("tos/training/treeview/"),
    dataType: "json",
    success: function(data){
    $("#treeview").jstree({
        core:{
            data: data,
            check_callback : true,
            state: { opened : true },
        },
        types: {
            root: { icon: "fa fa-folder" },
            child: { icon: "fa fa-file" }
        },
        contextmenu: {
            items: function($node){
                if($node.parent=="#"){
                    return{
                        Create : {
                            label: "Add Topic",
                            action: function(obj){
                                fileUpload($node.id);
                                $("#add_topic").modal("show");
                            }
                        },
                        Rename : {
                            label: "Edit",
                            action: function(obj){
                                edit_cat($node.id);
                                $("#edit_modal").modal("show");
                            }
                        },
                        Remove : {
                            label: "Delete",
                            action: function(obj){
                                delete_cat($node.id);
                            }
                        }     
                    }
                }else if(!$node.data && $node.parent!="#"){
                    return{
                        Create : {
                            label: "Add File",
                            action: function(obj){
                                add_fileUpload($node.text);
                                $("#add_file").modal("show");
                            }
                        },
                        Rename : {
                            label: "Edit",
                            action: function(obj){
                                edit_topic($node.text);
                                $("#edit_topic").modal("show");
                            }
                        },
                        Remove : {
                            label: "Delete",
                            action: function(obj){
                                delete_topic($node.text);
                            }
                        }     
                    }
                }else if($node.data){
                    return{
                        Rename : {
                            label: "Update",
                            action: function(obj){
                                $("#update_modal").modal("show");
                                update_topic($node.parent);
                            }
                        },
                        Remove : {
                            label: "Delete",
                            action: function(obj){
                                delete_file($node.parent);
                            }
                        }     
                    }
                }
            }
        },
        plugins: ["dnd", "types", "state", "contextmenu", "changed", "search"]
        })
        .on("changed.jstree", function (e, result) {
           if(result.node){
                var filename = result.node.text;
                var path = result.node.data;
                if(path){
                view_file(filename, path, result.node.parent);        
            }else if(!path && result.node.parent!='#'){
                view_topic(result.node.parent, result.node.text);
            }else{
                $("#get_files").show();
                $("#file_dl").hide();
                $("#picture").remove();
               
                $.ajax({
                    url: baseUrl("tos/training/get_cat/") +result.node.id,
                    type: 'GET',
                    success: function(data){
                        cat_table(result.node.id);
                        $('#table-topic').DataTable().clear().destroy();
                        $("#table_preview").show();
                        $("#head_preview").remove();   
                        $("#head_button").remove();       
                        $("#path").append("<div id='head_preview'><h5 class='m-widget1__item m-widget1__info'><span class='m-widget1__title' style='text-transform:uppercase;'><i class='m-nav__link-icon la la-folder-open'></i> / "+data.description+"</span></h5></div>");
                        $("#path").append("<div id='head_button' class='col-3'><button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='edit_cat("+result.node.id+")' data-toggle='modal' data-target='#edit_modal'><i class='fa fa-pencil'></i></button> <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='delete_cat("+result.node.id+")'><i class='la la-trash'></i></button></div>");
                    }
                });    
            }
        }
        })
    }
});

function view_file($text, $data, $parent){
    $("#get_files").hide();
    $ext=$text.split('.').pop();
    if($ext=='png'||$ext=='jpg'||$ext=='jpeg'){
        $("#table_preview").show();
        $("#head_preview").remove();
        $("#head_button").remove(); 
        $("#picture").remove();
        $("#file_dl").hide();
        $("#container").append("<img src="+$data+" id='picture' style='max-width: 1000px; margin: 0 auto;'>");
    }else if($ext=='avi'||$ext=='mp4'){
        $("#table_preview").show();
        $("#head_preview").remove()
        $("#head_button").remove(); ;
        $("#picture").remove();
        $("#file_dl").hide();
        $("#container").append("<video autoplay='true' controls id='picture' width='1000' height='500'><source src="+$data+" type='video/mp4'></video>");
    }else if($ext=='txt'||$ext=='file'){
        $("#head_preview").remove();
        $("#head_button").remove(); 
        $("#file_dl").hide();
        $("#picture").remove();
        $("#content").load($data,$text);
    }else if($ext=='mp3'||$ext=='ogg'){
        $("#head_preview").remove();
        $("#head_button").remove(); 
        $("#picture").remove();
        $("#container").append("<audio autoplay='true' controls id='picture' width='1000' height='500'><source src="+$data+" type='audio/mp3'></audio>");
    }else{
        $("#table_preview").hide();
        $("#head_preview").remove();
        $("#head_button").remove(); 
        $("#file_dl").hide();
        $("#picture").remove();
        getDetails($parent);
    }
}

function view_topic($cat_id, $topic_name){
    $("#get_files").show();
    $("#file_dl").hide();
    $("#picture").remove();
   
    $.ajax({
        url: baseUrl("tos/training/get_cat/") +$cat_id,
        type: 'GET',
        success: function(data){
            topic_table($topic_name);
            $('#table-topic').DataTable().clear().destroy();
            $("#table_preview").show();
            $("#head_preview").remove();    
            $("#head_button").remove();       
            $("#path").append("<h5 id='head_preview' class='m-widget1__item m-widget1__info'><span class='m-widget1__title' style='text-transform:uppercase;'><i class='m-nav__link-icon la la-folder-open'></i> / "+data.description+" / "+ $topic_name+"</span></h5>");
        }
    });    
}

$("#file_dl").hide();
function getDetails($id){
    $.ajax({
        url: baseUrl("tos/training/get_details/") +$id,
        type: 'GET',
        success: function(data){
            vmTab4.vm_tab4 = Object.assign({}, data);
            $("#file_dl").show();
            var eventClick = 'view_topic('+data.category_id+',"'+data.name+'")';
            var cateventClick = 'goCat('+data.category_id+',"'+data.category_id+'")';
            $("#head_file").append("<h5 id='head_preview' class='m-widget4__item m-widget4__info'><span class='m-widget4__title' style='text-transform:uppercase;'><i class='m-nav__link-icon la la-folder-open'></i> / <a href='#'onclick='"+cateventClick+"'>"+data.description+ "</a> / <a href='#'onclick='"+eventClick+"'>"+ data.name +"</a> / "+data.filename+"</span></h5>")
            $("#download_file").on("click", function(){
                window.open(data.filepath);
            });
        }
    });
} 

function goCat($id){
    $.ajax({
        url: baseUrl("tos/training/get_cat/") +$id,
        type: 'GET',
        success: function(data){
            cat_table($id);
            $('#table-topic').DataTable().clear().destroy();
            $("#table_preview").show();
            $("#head_preview").remove();   
            $("#head_button").remove();       
            $("#path").append("<div id='head_preview'><h5 class='m-widget1__item m-widget1__info'><span class='m-widget1__title' style='text-transform:uppercase;'><i class='m-nav__link-icon la la-folder-open'></i> / "+data.description+"</span></h5></div>");
            $("#path").append("<div id='head_button' class='col-3'><button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='edit_cat("+$id+")' data-toggle='modal' data-target='#edit_modal'><i class='fa fa-pencil'></i></button> <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='delete_cat("+$id+")'><i class='la la-trash'></i></button></div>");

        }
    });
}

var vmTab4 = new Vue({
    el: "#file_preview",
    data: { vm_tab4: {} },
});

var fileUpload = function($nodeid) {
var url = baseUrl("tos/training/upload_file");
$("#fileupload")
    .fileupload({
    url: url,
    dataType: "json",
    formData: { csrf_token: _csrf_hash },
    done: function(e, data) {
        var result = data.result;
        if (result.response) {
            var filePath = result.added_file;
            var renderFile = result.render_file;
            var name = $("#topic").val();
            var size = result.filesize;
            var filename = result.filename;
            $("#file_append").text(renderFile);
            $("#upload").on("click", function(){
                if(filePath&&name&&size){
                    $.ajax({
                        url: baseUrl("tos/training/add_topic_detail/") +$nodeid,
                        formData: { csrf_token: _csrf_hash },
                        data: {path:filePath, name: name, size:size, filename:filename},
                        dataType: "json",
                        beforeSend: function(){
                            $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                        },
                        success: function data(data){
                            filePath ="";
                            name = "";
                            size="";
                            if(data){
                                $("#add_topic").modal("hide");
                                $("#topic").val("");    
                                $("#file_append").text(""); 
                                $("#treeview").jstree(true).settings.core.data.url = baseUrl("tos/training/treeview/");
                                $("#treeview").jstree(true).refresh();
                            }else{
                                toastr.error(data.toastr_msg, "Notification: Error", 5000);
                            }
                            $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                        },
                        done: function (){
                            toastr.success(data.toastr_msg, "Notification: Added Successfully", 5000);
                        }       
                    });
                }
            });
        } else {
            toastr.error(result.toastr_msg, "File error", 5000);
        }
    }
});
}

var add_fileUpload = function($nodeid) {
    var url = baseUrl("tos/training/upload_file");
    $("#add_fileupload")
         .fileupload({
        url: url,
        dataType: "json",
        formData: { csrf_token: _csrf_hash },
        done: function(e, data) {
            var result = data.result;
            if (result.response) {
                var filePath = result.added_file;
                var renderFile = result.render_file;
                var size = result.filesize;
                var filename = result.filename;
                
                $("#add_file_append").text(renderFile);
                $("#add_upload").on("click", function(){
                    if(filePath&&size){
                        $.ajax({
                            url: baseUrl("tos/training/add_file/") +$nodeid,
                            formData: { csrf_token: _csrf_hash },
                            data: {path:filePath, size:size, filename:filename},
                            dataType: "json",
                            beforeSend: function(){
                                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                            },
                            success: function data(data){
                                filePath ="";
                                name = "";
                                size="";
                                if(data){
                                    $("#add_file").modal("hide");
                                    $("#add_file_append").text("");  
                                    $("#treeview").jstree(true).settings.core.data.url = baseUrl("tos/training/treeview/");
                                    $("#treeview").jstree(true).refresh();
                                }else{
                                    toastr.error(data.toastr_msg, "Notification: Error", 5000);
                                }
                                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                            },
                            done: function (){
                                toastr.success(data.toastr_msg, "Notification: Added Successfully", 5000);
                            }       
                        });
                    }
                });
            } else {
                toastr.error(result.toastr_msg, "File error", 5000);
            }
        }
    });
}

function edit_topic($id){
    $.ajax({
        url: baseUrl("tos/training/edit_topic/") + $id,
        type: "GET",
        success: function(data) {
            vmTab2.vm_tab2 = Object.assign({}, data);
            $.validate({
                form : '#edit_topic_form',
                lang: 'en',
                onSuccess : function(form) {
                        $.ajax({
                            url: baseUrl("tos/training/update_topic/") + $id,
                            type: "POST",
                            dataType: "json",
                            data: $("#edit_topic_form").find("input,textarea").serialize(),
                            beforeSend: function(){
                                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                            },
                            success: function(data){
                                if(data){
                                    toastr.success(data.toastr_msg, "Notification: Updated Successfully", 5000);
                                    $("#edit_topic").modal("hide");
                                    $("#edit_topic_desc").val("");  
                                    $("#treeview").jstree(true).settings.core.data.url = baseUrl("tos/training/treeview/");
                                    $("#treeview").jstree(true).refresh();
                                }else{
                                    toastr.error(data.toastr_msg, "Notification: Error", 5000);
                                }
                                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                            }
                        });
                    return false;
                },
            });
        }     
    });
}

var vmTab2 = new Vue({
    el: "#edit_topic_form",
    data: { vm_tab2: {} },
});

function delete_topic($id){
    if(confirm('Are you sure you want to delete this entire folder?')){
    $.ajax({
        url: baseUrl("tos/training/delete_topic/") + $id,
        type: "GET",
        success: function() {
            $("#treeview").jstree(true).settings.core.data.url = baseUrl("tos/training/treeview/");
            $("#treeview").jstree(true).refresh();
        }     
    });
}
}

function update_topic($id){
    $.ajax({
        url: baseUrl("tos/training/edit_file/") + $id,
        type: "GET",
        success: function(data) {
            vmTab3.vm_tab3 = Object.assign({}, data);
            update_fileUpload($id);
        }
    });
}

var vmTab3 = new Vue({
    el: "#update_form",
    data: { vm_tab3: {} },
  });

var update_fileUpload = function($id) {
    var url = baseUrl("tos/training/upload_file");
    $("#update_fileupload")
      .fileupload({
        url: url,
        dataType: "json",
        formData: { csrf_token: _csrf_hash },
        done: function(e, data) {
            var result = data.result;
            if (result.response) {
                var filePath = result.added_file;
                var renderFile = result.render_file;
                var size = result.filesize;
                var filename = result.filename;
                $("#update_file_append").text(renderFile);
                $("#update_upload").on("click", function(){
                    if(filePath&&size){
                        $.ajax({
                            url: baseUrl("tos/training/update_file/") +$id,
                            formData: { csrf_token: _csrf_hash },
                            data: {path:filePath, size:size, filename:filename},
                            dataType: "json",
                            success: function data(data){
                                filePath ="";
                                size="";
                                if(data){
                                    $("#update_modal").modal("hide"); 
                                    $("#update_file_append").text(""); 
                                    $("#treeview").jstree(true).settings.core.data.url = baseUrl("tos/training/treeview/");
                                    $("#treeview").jstree(true).refresh();
                                    toastr.success(data.toastr_msg, "Notification: Updated Successfully", 5000);
                                }else{
                                    toastr.error(data.toastr_msg, "Notification: Error", 5000);
                                }
                            }          
                        });
                    }
                });
            } else {
              toastr.error(result.toastr_msg, "File error", 5000);
            }
        }
    });
}

function delete_file($id){
    if(confirm('Are you sure you want to delete this file?')){
    $.ajax({
        url: baseUrl("tos/training/delete_file/") + $id,
        type: "GET",
        success: function() {
            $("#treeview").jstree(true).settings.core.data.url = baseUrl("tos/training/treeview/");
            $("#treeview").jstree(true).refresh();
        }     
    });
}
}

function topic_table($id){

    var search_val = "";
    var tbl = $("#table-topic").DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        bPaginate: false,
        bInfo: false,
        order: false,
        ajax: {
            url: baseUrl("tos/training/topic_masterfile/") +$id,
            type: "post",
            dataType: "json",
            data: function(d){
                d.csrf_token = _csrf_hash,
                d.search['value'] = search_val
            }
        },
        searching: false,
        columns: [
            { data: "filename", width: "30%"},
            { data: "filesize", width: "20%", render: function (data) {return renderSize(data)}},
            { data: null, width: "15%", className: "text-center"},
        ],
        columnDefs: [
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function ( data, type, row, meta ) { return itemDatatableActions(row.id, row.filename, row.filepath); },
            },{
                targets: "_all",
                orderable: false
            }
        ]
    });

    function renderSize(data){
        return data+" kb";
    }

    $('#generalSearch').donetyping(function(callback) {
        search_val = $(this).val();
        tbl.ajax.reload();
    });

    //refresh datatable 
    $("#reload_dtTbl").on("click",function(){
        tbl.ajax.reload();
    });

    $("#table-topic thead").remove();

    
    function itemDatatableActions($id, $filename, $filepath){
        $name = '"'+$filename+'"';
        $path = '"'+$filepath+'"';
        if($id){
        var _actionButton ="";
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='view_file("+$name+', '+$path+', '+$id+")'><i class='la la-eye'></i></button>"; 	 
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='update_topic("+$id+")' data-toggle='modal' data-target='#update_modal'><i class='la la-rotate-right'></i></button>"; 
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='delete_file("+$id+")'><i class='la la-trash'></i></button>"; 	 	
            return _actionButton;
        }else{ 
            return false; 
        }
    }
}

function cat_table($id){ 
    var search_val = "";
    var tbl = $("#table-topic").DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        bPaginate: false,
        bInfo: false,
        order: false,
        ajax: {
            url: baseUrl("tos/training/category_masterfile/") +$id,
            type: "post",
            dataType: "json",
            data: function(d){
                d.csrf_token = _csrf_hash,
                d.search['value'] = search_val
            }
        },
        searching: false,
        columns: [
            { data: "name",width: "30%"},
            { data: "sum", width: "20%", render: function (data) {return renderSize(data)}},
            { data: null, width: "15%", className: "text-center"},
        ],
        columnDefs: [
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function ( data, type, row, meta ) { return itemDatatableActions(row.id, row.name, row.category_id); },
            },{
                targets: "_all",
                orderable: false
            }
        ]
    });

    function renderSize(data){
        return data+" kb";
    }

    //custom global search init
    $('#generalSearch').donetyping(function(callback) {
        search_val = $(this).val();
        tbl.ajax.reload();
    });

    //refresh datatable 
    $("#reload_dtTbl").on("click",function(){
        tbl.ajax.reload();
    });

    function itemDatatableActions($id, $name, $cat_id){
        $names = '"'+$name+'"';
        if($id){
        var _actionButton ="";
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='view_topic("+$cat_id+', '+$names+")'><i class='la la-folder-open'></i></button>";
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='edit_topic("+$names+")' data-toggle='modal' data-target='#edit_topic'><i class='la la-edit'></i></button>"; 
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='delete_topic("+$names+")'><i class='la la-trash'></i></button>"; 	 	
            return _actionButton;
        }else{ 
            return false; 
        }
    }
}
