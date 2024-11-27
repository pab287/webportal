<script src="<?php echo base_url('assets/plugins/fileupload/js/vendor/jquery.ui.widget.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/fileupload/js/jquery.iframe-transport.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/daterangepicker.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/fileupload/js/jquery.fileupload.js'); ?>"></script>
<script src="https://www.google.com/recaptcha/api.js"></script>

<?php
setcookie ("public_user_id", rand(10,1000), time()+ (10 * 365 * 24 * 60 * 60));
if(!isset($_COOKIE['public_user_id'])){
?>
    <script>
        location.reload();
    </script>
<?php
}
?>
<div id="regestration">
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
    <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
    </symbol>
    <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
    </symbol>
    <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
    </symbol>
    </svg>
    <div class="container py-5 px-0">
        <div class="col-lg-12 pb-5">
            <h1 class="text-center font-weight-bold">GC&C CRS APPLICATION</h1>
        </div>
        <div class="d-flex justify-content-center">
            <div class="card col-6 p-0">
                <form id="resumeForm" method="POST" onsubmit="saveResumeInfo(this, event); return false;" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="public_user_id" value="<?php echo $_COOKIE['public_user_id']; ?>">
                    <div class="card-body row g-3">
                        <div class="col-md-12 py-2">
                            <label for="Firstname" class="form-label">Firstname *</label>
                            <input type="text" class="form-control" name="firstname" id="Firstname" data-validation="required" autocomplete="off">
                        </div>
                        <div class="col-md-12 py-2">
                            <label for="Lastname" class="form-label">Lastname *</label>
                            <input type="text" class="form-control" name="lastname" id="Lastname" data-validation="required" autocomplete="off">
                        </div>
                        <div class="col-md-12 py-2">
                            <label for="Middlename" class="form-label">Middlename *</label>
                            <input type="text" class="form-control" name="middlename" id="Middlename" data-validation="required" autocomplete="off">
                        </div>
                        <div class="col-md-12 py-2">
                            <label for="Suffix" class="form-label">Suffix *</label>
                            <input type="text" class="form-control" name="suffix" id="Suffix" data-validation="required" autocomplete="off">
                        </div>
                        <div class="col-md-12 py-2 form-group">
                            <label for="Recruitment" class="form-label">Recruitment Source *</label>
                            <select id="Recruitment" style="width: 100%;" name="recruitment" multiple="multiple"
                                        class="form-control" data-validation="required" autocomplete="off">
                                <option value="Mynimo">MYNIMO</option>
                                <option value="Jobstreet">JOBSTREET</option>
                                <option value="Facebook">FACEBOOK</option>
                                <option value="Linkedin">LINKEDIN</option>
                                <option value="Walk In">WALK IN</option>
                                <option value="REFERRAL">REFERRAL</option>
                            </select>
                        </div>
                        <div class="col-md-12 py-2 form-group">
                            <label for="School" class="form-label">School</label>
                            <select id="School" name="schools[]" multiple="multiple" class="form-control">
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="col-md-12 py-2 form-group">
                            <label for="Course" class="form-label">Course</label>
                            <select id="Course" name="courses[]" multiple="multiple" class="form-control">
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="col-md-12 py-2 form-group">
                            <label for="Position" class="form-label">Desired Position *</label>
                            <select id="Position" name="positions[]" multiple="multiple" class="form-control" data-validation="required" autocomplete="off">
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="col-md-12 py-2 form-group">
                            <label for="dateOfApplication" class="form-label">Date of Application *</label>
                            <input id="dateOfApplication" name="applied_dt" type="text" class="form-control" data-validation="required" autocomplete="off">
                        </div>
                        <div class="col-md-12 py-2 form-group">
                            <label for="Middlename" class="form-label">Contact No *</label>
                            <input type="text" class="form-control" name="contact_no" id="contact_no" data-validation="required" autocomplete="off">
                        </div>
                        <!-- <div class="col-md-12 py-2">
                            <label for="Remarks" class="form-label">Remarks</label>
                            <Textarea id="Remarks" name="remarks" type="text" class="form-control" rows="8"></Textarea>
                        </div> -->
                        <div class="col-md-12 py-2">
                            <div class="row p-0">
                                <div class="col-md-5">
                                    <input id="temp_fileupload" name="files" type="file" hidden multiple data-validation="required">
                                    <label class="file_label" for="temp_fileupload">Attach Resume</label>
                                </div>
                                <div class="col-md-7">
                                    <div id="captcha" class="g-recaptcha" data-sitekey="6LfYy9QlAAAAAHNzof6dl4R4O_GkFrFeyRc42zdU"></div>
                                </div>
                            </div>
                            <div id="progress" class="progress mt-2 d-none">
                                <div class="progress-bar progress-bar-success"></div>
                            </div>
                            <div id="temp_files" class="files"></div>
                            <div id="new_regestration_preview" class="m-widget2 row mt-3">
                                <template v-if="count">
                                    <div class="m-portlet m-portlet--rounded col-lg-12">
                                        <div class="m-portlet__head">
                                                <div class="m-portlet__head-caption">
                                                    <div class="m-portlet__head-title">
                                                        <h3 class="m-portlet__head-text">
                                                            Attachments
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                        <div class="m-portlet__body row">
                                            <template v-for="(item, index) in rows">
                                                <div v-bind:class="getClass(item.filename)">
                                                    <div class="m-widget4__item m-0 p-0">
                                                        <div class="m-widget4__img m-widget4__img--icon">
                                                            <img v-bind:src="getExtension(item.filename)" alt="" height="50" width="50">
                                                        </div>
                                                        <div class="m-widget4__info">
                                                            <span class="m-widget4__text" v-text="item.filename">
                                                            </span>
                                                        </div>
                                                        <div class="m-widget2__actions">
                                                            <div class="m-widget2__actions-nav">
                                                                <div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
                                                                    <a href="#" class="m-dropdown__toggle">
                                                                        <i class="la la-ellipsis-h"></i>
                                                                    </a>
                                                                    <div class="m-dropdown__wrapper">
                                                                        <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 14.7032px;"></span>
                                                                        <div class="m-dropdown__inner">
                                                                            <div class="m-dropdown__body">
                                                                                <div class="m-dropdown__content">
                                                                                    <ul class="m-nav">
                                                                                        <li class="m-nav__item">
                                                                                            <a href="#" class="m-nav__link" v-on:click="fileDelete(item.id, <?php echo $_COOKIE['public_user_id']; ?>)">
                                                                                                <i class="m-nav__link-icon flaticon-circle"></i>
                                                                                                <span class="m-nav__link-text">
                                                                                                    Remove
                                                                                                </span>
                                                                                            </a>
                                                                                        </li>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="px-3 pb-4">
                        <div class="col-md-12 d-flex justify-content-end px-0">
                            <button type="button" class="btn btn-danger mx-1" id="reset">Reset</button>
                            <button type="submit" name="save_resume" class="btn btn-primary ml-1">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
</div>
<script>
$(document).ready(function(){
    temp_images();
});

$("#reset").on("click", function(){
    $.ajax({
        url: '<?php echo base_url("crs/online_registration/temp_file_delete_all"); ?>',
        type: "post",
        data: {
            csrf_token: '<?php echo $this->security->get_csrf_hash(); ?>',
            public_user_id: '<?php echo $_COOKIE['public_user_id']; ?>'
        },
        dataType: "json",
        success: function(resp){
            if(resp > 0){
                window.location.reload();
            }else{
                console.log("delete all is not working");
            }
        }
    });
    
});
function saveResumeInfo(formElement, e){
    e.preventDefault();
    grecaptcha.ready(() => {
        grecaptcha.render(document.getElementById('captcha'), {
           'sitekey' : '6LfYy9QlAAAAAHNzof6dl4R4O_GkFrFeyRc42zdU'
        });
    });
    const form = $(formElement);
    const formData = new FormData(formElement);

    var count = $(".m-widget4__text").text();
    if(form.isValid()){
        if(count != ""){
            $.ajax({
                url: '<?php echo base_url("crs/online_registration/insert_resume"); ?>',
                dataType: "JSON",
                type: "POST",
                processData: false,
                contentType: false,
                data: formData,
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message, "New Resume Saved.", 10000);
                        window.location.href = "<?php echo base_url("crs/online_registration/thank_you"); ?>"
                    }else{
                        toastr.error(response.message, "New Resume Not Saved.", 10000);
                    }
                }
            });
        }else{
            toastr.error("Upload your resume", "Resume Not Saved.", 10000);
        }
    }
}

$("#Recruitment").select2();

$("#School").select2({
    width: '100%',
    ajax: {
        url: '<?php echo base_url("crs/online_registration/get_school"); ?>',
        global: false,
        processResults: function (data) {
            return data;
        },
        delay: 500
    },
});

$("#Course").select2({
    width: '100%',
    ajax: {
        url: '<?php echo base_url("crs/online_registration/get_course"); ?>',
        global: false,
        processResults: function (data) {
            return data;
        },
        delay: 500
    },
});

$("#Position").select2({
    width: '100%',
    ajax: {
        url: '<?php echo base_url("crs/online_registration/get_position"); ?>',
        global: false,
        processResults: function (data) {
            return data;
        },
        delay: 500
    },
});

$("#dateOfApplication").datepicker({
    todayHighlight: true,
    autoclose: true,
    pickerPosition: 'bottom left',
    todayBtn: 'linked',
    format: 'mm/dd/yyyy',
    forceParse: false
});

var array_file_ext = ["doc", "pdf", "jpg"];
var temp_images = function () {
    var url = '<?php echo base_url("crs/online_registration/add_temp_file"); ?>';
    $("#temp_fileupload").fileupload({
        url: url,
        dataType: "json",
        formData: {
            csrf_token: '<?php echo $this->security->get_csrf_hash(); ?>',
            public_user_id: '<?php echo $_COOKIE['public_user_id']; ?>'
        },
        done: function (e, data) {
            $("#progress").removeClass("d-none");
            $("#progress").show();
            var result = data.result;
            console.log(result);
            if(result == 0){
                alert("File is not supported! File is not uploaded!");
            }
            previewFile.count = result.length;
            previewFile.rows = Object.assign({}, previewFile.rows, result);
        },
        progressall: function (e, data) {
            var progress = parseInt((data.loaded / data.total) * 100, 10);
            var progressTotal = 0;

            var steps = setInterval(function () {
                progressTotal += 10;
                $("#progress .progress-bar").css("width", progressTotal + "%");
                if (progressTotal == 100) {
                    clearInterval(steps);
                    progressTotal = 0;
                    setTimeout(function () {
                        $("#progress .progress-bar").css("width", progressTotal + "%");
                    }, 1500);
                }
            }, 10);

            if (progress == 100) {
                setTimeout(function () {
                    $("#progress").hide();
                }, 1000);
            }
        }
    })
    .prop("disabled", !$.support.fileInput)
    .parent()
    .addClass($.support.fileInput ? undefined : "disabled");
};
var previewFile = new Vue({
    el: "#new_regestration_preview",
    data: { 
        rows: [],
        className: "",
        count: 0
    },
    methods: {
        getExtension: function(file){
            var type = file.split('.').pop();
            if(array_file_ext.includes(type)){
                switch(type) {
                case "doc":
                    return '<?php echo base_url("assets/images/file_icons/doc.svg"); ?>';
                      
                case "pdf":
                    return '<?php echo base_url("assets/images/file_icons/pdf.svg"); ?>';
                    
                case "jpg":
                    return '<?php echo base_url("assets/images/file_icons/jpg.svg"); ?>';
                    
                default:
                    return '<?php echo base_url("assets/images/file_icons/default.svg"); ?>';
                }
            }
        },
        getClass: function(classes){
            var type = classes.split('.').pop();
            if(array_file_ext.includes(type)){
                switch(type) {
                case "doc":
                    return "m-widget4 m-widget2__item m-widget2__item--primary col-lg-12";
                        
                case "pdf":
                    return "m-widget4 m-widget2__item m-widget2__item--danger col-lg-12";
                    
                case "jpg":
                    return "m-widget4 m-widget2__item m-widget2__item--success col-lg-12";
                    
                default:
                    return "m-widget4 m-widget2__item m-widget2__item--default col-lg-12";
                }
            }
        },
        fileDelete: function(id, public_user_id){
            $.ajax({
                url: '<?php echo base_url("crs/online_registration/temp_file_delete"); ?>',
                type: "post",
                data: {
                    csrf_token: '<?php echo $this->security->get_csrf_hash(); ?>',
                    id: id,
                    public_user_id: public_user_id
                },
                dataType: "json",
                success: function(resp){
                    previewFile.rows = Object.assign({});
                    previewFile.rows = Object.assign({}, resp);
                }
            });
        }
    }
});
</script>