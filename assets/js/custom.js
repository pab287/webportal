var basePathUrl = "",
    sitePathUrl = "",
    _csrf_token = "",
    _csrf_hash = "";

var setBaseUrl = function (basePath) {
    basePathUrl = basePath;
};
var setSiteUrl = function (sitePath) {
    sitePathUrl = sitePath;
};

var baseUrl = function (pathToUrl) {
    return pathToUrl ? basePathUrl + pathToUrl : basePathUrl;
};
var siteUrl = function (pathToUrl) {
    return pathToUrl ? sitePathUrl + pathToUrl : sitePathUrl;
};

var setCrfSecurityToken = function (token = null, hash = null) {
    _csrf_token = token;
    _csrf_hash = hash;
};

String.prototype.ucWords = function () {
    return this.toLowerCase().replace(/\b[a-z]/g, function (letter) {
        return letter.toUpperCase();
    });
};
String.prototype.replaceAt = function (index, replacement) {
    return this.substr(0, index) + replacement + this.substr(index + replacement.length);
}

if (typeof $.fn.dataTable.defaults !== "undefined" && typeof $.fn.dataTable.defaults == "object") {
    $.extend($.fn.dataTable.defaults, {
        responsive: true,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search"
        },
        lengthMenu: [
            [10, 20, 50, 100, 200, 500, -1],
            [10, 20, 50, 100, 200, 500, "ALL"]
        ],
        drawCallback: function (settings) {
            $('[data-toggle="m-tooltip"]').tooltip({
                delay: { show: 300 }
            });
        }
    });
}

if (typeof $.blockUI.defaults !== "undefined" && typeof $.blockUI.defaults == "object") {
    $.extend($.blockUI.defaults, { baseZ: 1055 });
}

var mapBlockUI = function (callback) {
    mApp.blockPage({
        overlayColor: "blue",
        type: "loader",
        state: "primary",
        size: "lg",
        message: "PLEASE WAIT...",
        centerY: false,
        centerX: false,
        css: {
            position: 'fixed',
            margin: 'auto',
        }
    });
    $(".blockPage").css({
        width: "100%",
        left: 0,
        display: 'flex',
        "flex-direction": 'row',
        "justify-content": 'center',
        "align-items": "center",
        top: 0,
        bottom: 0,
        "background-color": "#6060660d",
    });
    $(".m-blockui span").css({
        color: "#1e3572cf",
        "font-weight": 'bold',
    })
    $(".blockUI.blockMsg .m-blockui")
        .removeAttr("style");
    if (typeof callback == "function") {
        return callback();
    } else if (typeof callback !== "function") {
        return callback;
    } else {
        return true;
    }
};

var mapBlockSessionExpireUI = function (callback) {
    mApp.blockPage({
        overlayColor: "blue",
        type: "loader",
        state: "primary",
        size: "lg",
        message: "SESSION HAS EXPIRED, RE-DIRECTING TO LOGIN PAGE",
        centerY: false,
        centerX: false,
        css: {
            position: 'fixed',
            margin: 'auto',
        }
    });
    $(".blockPage").css({
        width: "100%",
        left: 0,
        display: 'flex',
        "flex-direction": 'row',
        "justify-content": 'center',
        "align-items": "center",
        top: 0,
        bottom: 0,
        "background-color": "#6060660d",
    });
    $(".m-blockui span").css({
        color: "#1e3572cf",
        "font-weight": 'bold',
    })
    $(".blockUI.blockMsg .m-blockui")
        .removeAttr("style");
    if (typeof callback == "function") {
        return callback();
    } else if (typeof callback !== "function") {
        return callback;
    } else {
        return true;
    }
};

// $(".blockUI.blockMsg").center();

var mapUnblockUI = function (callback) {
    mApp.unblockPage();

    if (typeof callback == "function") {
        return callback();
    } else if (typeof callback !== "function") {
        return callback;
    } else {
        return true;
    }
};

var renderSelect2 = function () {
    var tempSelect2Rendered = $("select.select2").select2({
        width: "100%",
        placeholder: "Select an option"
    });

    tempSelect2Rendered.on("change", function (e) {
        var currentObject = $(e.target);
        currentObject.validate();
    });
};

/*** Override navTabs Content ***/

jQuery(document).on("click", "a.nav-link.m-tabs__link", function () {
    var _navTab = $(this);
    var _portletTabs = _navTab.closest(".m-portlet--tabs");
    if (typeof _portletTabs !== "undefined") {
        var _tabContent = _portletTabs.find(".tab-content");
        if (typeof _tabContent !== "undefined") {
            _tabContent.find(".tab-pane").removeClass("active");
            var _href = _navTab.attr("href");
            $(_href).addClass("active");
        }
    }
});

var collapsibleAccordionIconSwitch = function () {
    $(".accordion .collapse.show").each(function () {
        $(this)
            .prev(".card-header")
            .find(".la")
            .addClass("la-angle-up")
            .removeClass("la-angle-down");
    });

    $(".accordion .collapse")
        .on("show.bs.collapse", function () {
            $(this)
                .prev(".card-header.m-portlet")
                .find(".la")
                .removeClass("la-angle-down")
                .addClass("la-angle-up");
        })
        .on("hide.bs.collapse", function () {
            $(this)
                .prev(".card-header.m-portlet")
                .find(".la")
                .removeClass("la-angle-up")
                .addClass("la-angle-down");
        });
};

var firstLoad = function () {
    setTimeout(mapBlockUI, 100);
};

window.onload = firstLoad();

jQuery(document)
    .ready(function () {
        renderSelect2();
        uniqueCodeTampering();
        setTimeout(mapUnblockUI, 200);

        $('.modal:not([modal-exempt-custom])').modal({
            backdrop: "static",
            keyboard: false,
            show: false
        });

    })
    .ajaxStart(function () {
        mapBlockUI();
    })
    .ajaxStop(function () {
        setTimeout(mapUnblockUI, 200);
    });

(function ($) {
    $.fn.donetyping = function (callback, delaySeconds = 1000, limit = 0) {
        var _this = $(this);
        var x_timer;
        // _this.keyup(function () {
        //     var trim = _this.val().trim();
        //     var length = trim.length;

        //     if (limit <= 0) {
        //         clearTimeout(x_timer);
        //         x_timer = setTimeout(clear_timer, delaySeconds);
        //     }
            
        //     if (length >= limit) {
        //         clearTimeout(x_timer);
        //         x_timer = setTimeout(clear_timer, delaySeconds);
        //     }
        // });

        /**
         * added delay function to keyup event as it still trigger the request event if the length of the value is less than the limit
         */
        _this.keyup(delay( function(e) {
            var trim = _this.val().trim();
            var length = trim.length;

            if (limit <= 0) {
                clearTimeout(x_timer);
                x_timer = setTimeout(clear_timer, delaySeconds);
            }

            if (length >= limit) {
                clearTimeout(x_timer);
                x_timer = setTimeout(clear_timer, delaySeconds);
            }
        }, 200));

        function clear_timer() {
            clearTimeout(x_timer);
            callback.call(_this);
        }
    };
    /*** global disable ajax caching ***/
    $.ajaxSetup({ cache: false });
    /*** global disable ajax caching ***/
})(jQuery);

$(document)
    .on("click", ".btn-reveal-password",
        function (e) {
            const el = e.currentTarget;
            $(el).hasClass("show_password") ? hidePassword($(el)) : showPassword($(el));
        });

function hidePassword(e) {
    e.removeClass("show_password la-eye-slash").addClass("hide_password la-eye");
    $("input.password").attr("type", "password");
}

function showPassword(e) {
    e.removeClass("hide_password la-eye").addClass("show_password la-eye-slash");
    $("input.password").attr("type", "text");
}

function openChangePassword() {
    $(".change-password-modal").find("form").resetForm();
    $(".change-password-modal").modal("show");
}

function openChangePin() {
    $(".change-pin-modal").find("form").resetForm();
    $(".change-pin-modal").modal("show");
}

function forgetPin() {
    $(".forget-pin-modal").find("form").resetForm();
    $(".forget-pin-modal").modal("show");
}

function openAttendanceLog() {
    $("#attendance-log").modal("show");
}


function changePasswordLater(id){
    $.ajax({
        url: baseUrl("users/change_password_later"),
        type: "POST",
        dataType: "JSON",
        data: {
            id:id,
            csrf_token : _csrf_hash
        },
        global: false,
        success: function (response) {
            if(response){
                $(".password-change-reminder").modal("hide");
                toastr.success("","Password cahnge waived successfully", 20000);
            }else{
                toastr.error("","Error", 20000);
            }
        }
    });
}
   
function changePasswordNow(){
    $(".password-change-reminder").modal("hide");
    $("#force_change_modal").modal("show", {
        backdrop: 'static',
        keyboard: false
    });
}

function sendPin(form) {
    const formEl = $(form);
    const formData = new FormData(form);
    formData.append("csrf_token", _csrf_hash);
    if (formEl.isValid()) {
        $.ajax({
            url: baseUrl("users/forget_pin"),
            type: "POST",
            dataType: "JSON",
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
                if (response.result) {
                    $(".forget-pin-modal").modal('hide');
                    formEl.resetForm();
                    toastr.success('PIN SUCCESSFULLY SENT TO YOUR EMAIL!', "SUCCESS SENT!", 20000);
                } else {
                    toastr.error('ERROR', "ERROR", 20000);
                }
            }
        });
    }

}

function processChangePassword(form) {
    const formEl = $(form);
    const formData = new FormData(form);
    formData.append("csrf_token", _csrf_hash);

    if (formEl.isValid()) {
        $.ajax({
            url: baseUrl("users/process_change_password"),
            type: "POST",
            dataType: "JSON",
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
                if (response.success) {
                    formEl.resetForm();
                    $(".change-password-modal").modal("hide");
                    toastr.success(response.message, "Password successfully changed.", 20000);
                } else {
                    toastr.error(response.message, "Error", 20000);
                }
            }
        });
    }
}

let typingTimer = 0
const doneTypingInterval = 1000;
$(document)
    .on("keyup", ".change-password-modal input[name='reset_pin']", function () {
        const _this = $(this);
        const id = $(".change-password-modal input[name='id']").val();
        const reset_pin = _this.val();

        if (reset_pin) {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function () {
                $.ajax({
                    url: baseUrl("users/verify_pin"),
                    type: "POST",
                    dataType: "JSON",
                    global: false,
                    data: {
                        csrf_token: _csrf_hash,
                        reset_pin,
                        id
                    },
                    success: function (response) {
                        const disabled = !response.success;
                        $(".change-password-modal button[type='submit']").attr("disabled", disabled);
                        if (response.success) {
                            toastr.success(response.message, "Pin Verification Successful.", 20000);
                        } else {
                            toastr.error(response.message, "Pin Verification Failed.", 20000);
                        }
                    }
                });
            }, doneTypingInterval);
        }
    })
    .on("input", ".change-password-modal input[name='reset_pin']", function () {
        clearTimeout(typingTimer);
    });

$(document)
    .on("keyup", ".change-pin-modal input[name='old_pin']", function () {
        const _this = $(this);
        const id = $(".change-pin-modal input[name='id']").val();
        const reset_pin = _this.val();

        if (reset_pin) {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function () {
                $.ajax({
                    url: baseUrl("users/verify_pin"),
                    type: "POST",
                    dataType: "JSON",
                    global: false,
                    data: {
                        csrf_token: _csrf_hash,
                        reset_pin,
                        id
                    },
                    success: function (response) {
                        const disabled = !response.success;
                        $(".change-pin-modal button[type='submit']").attr("disabled", disabled);
                        if (response.success) {
                            toastr.success(response.message, "Pin Verification Successful.", 20000);
                        } else {
                            toastr.error(response.message, "Pin Verification Failed.", 20000);
                        }
                    }
                });
            }, doneTypingInterval);
        }
    })
    .on("input", ".change-pin-modal input[name='new_pin']", function () {
        clearTimeout(typingTimer);
    });

function processChangePin(form) {
    const formEl = $(form);
    const formData = new FormData(form);
    formData.append("csrf_token", _csrf_hash);

    if (formEl.isValid()) {
        $.ajax({
            url: baseUrl("users/process_change_pin"),
            type: "POST",
            dataType: "JSON",
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
                if (response.success) {
                    formEl.resetForm();
                    $(".change-pin-modal").modal("hide");
                    toastr.success(response.message, "Password successfully changed.", 20000);
                } else {
                    toastr.error(response.message, "Error", 20000);
                }
            }
        });
    }
}

/* EDWIN'S FUNCTION */
function serializeArrayToObject(form) {
    return form.serializeArray().reduce((o, item) => ({ ...o, [item.name]: item.value }), {});
}

/*  END EDWIN'S FUNCTION */

var uniqueCodeTampering = function () {
    $('input.m--uniqueCode').on('keypress', function (event) {
        var regex = new RegExp("^[a-zA-Z0-9 \b]+$");
        var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
        if (!regex.test(key)) {
            event.preventDefault();
            return false;
        }
    }).on('keyup', function (event) {
        var code = event.keyCode || event.which;
        if (code == 32) {
            $(this).val($(this).val().replace(/ /g, "_"));
        }
    });
}

/*** Number Format ***/
var numberFormat = function (numbers) {
    if (numbers) {
        var _num = numbers.toString().replace(/,/g, "");
        _num = parseFloat(_num).toFixed(2);

        var components = _num.toString().split(".");
        components[0] = components[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        return components.join(".");
    } else {
        return "0.00";
    }
}

var toNumber = function (numbers) {
    if (numbers) {
        var _num = numbers.toString().replace(/,/g, "");
        _num = parseFloat(_num).toFixed(2);
        return _num;
    } else {
        return "0.00";
    }
}
/*** idle timer session checker ***/
var idleTimerTrigger = function ($isActive=false) {
    $isActive = typeof idleTimerState !== "undefined" ? idleTimerState: $isActive;
    
    if($isActive){
        var docTimeout = 60000;
    $(document).on("idle.idleTimer", function (event, elem, obj) {
        console.log("current page is in idled state.");
    }).on("active.idleTimer", function (event, elem, obj, e) {
        let isActive = true;
        $.ajax({
            url: siteUrl("users/get_session_status"),
            global: false,
            success: function (data, textStatus, xhr) {

                const tempData = $.trim(data);
                if (typeof tempData !== "undefined" && tempData.toString() !== "") {
                    const result = JSON.parse(data);
                    if (typeof result !== "undefined" && typeof result == "object" && Object.keys(result).length > 0) { isActive = result.response; }
                } else { isActive = false; }

                if (isActive == false) {
                    /*** toastr.warning("Session has expired, You will be logged out in a few seconds.", "Session Expired"); ***/
                    mapBlockSessionExpireUI();
                    setTimeout(function () {
                        window.location.replace(siteUrl("login/index"));
                    }, 3000);
                }
            }
        });

        console.log("checking session timeout state, is active `" + isActive + "`");
    }).idleTimer(docTimeout);
    }else{
        return false;
    }
}
/*** idle timer session checker ***/

/*** Number Format ***/

// function to force logout temporarily disabled
// since this code will override remember me function
/*
var idleTime = 0;
$(document).ready(function () {
    //Increment the idle time counter every minute.
    var idleInterval = setInterval(timerIncrement, 60000); // 1 minute

    //Zero the idle timer on mouse movement.
    $(this).mousemove(function (e) {
        idleTime = 0;
    });
    $(this).keypress(function (e) {
        idleTime = 0;
    });
});

// check and force logout
function timerIncrement() {
    idleTime = idleTime + 1;
    if (idleTime > 60) { // 1 hour
        $.ajax({
            url: baseUrl(`/login/login/logout`),
            success: function () {
                window.location.assign(baseUrl())
            }
        });
    }
}*/

/*** stop all ajax request ***/
$.xhrPool = []; 
$.xhrPool.abortAll = function () { 
    $(this).each(function (idx, jqXHR) { jqXHR.abort(); });
    $.xhrPool.length = 0;
};

$.ajaxSetup({
    beforeSend: function (jqXHR) { $.xhrPool.push(jqXHR); },
    complete: function (jqXHR) {
        var index = $.xhrPool.indexOf(jqXHR);
        if (index > -1) { $.xhrPool.splice(index, 1); }
    }
});

/*** let globalUrlRedirect = null;
$(document).on("click", ".m-menu__link", function (e) {
    const tempLink = $(e.target).attr("href");
    globalUrlRedirect = tempLink;
}); ***/

$(window).on('beforeunload', function (e) {
    if ($.xhrPool.length > 0) {
        $.xhrPool.abortAll();
        /*** window.stop();
        console.log("before unload function"); ***/
        /*** if (globalUrlRedirect) {
             console.log("testing links");
             console.log(globalUrlRedirect);
             window.location.replace(globalUrlRedirect);
         } */
    }
});

/*** stop all ajax request ***/

function delay(callback, ms) {
    var timer = 0;
    return function() {
      var context = this, args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
        callback.apply(context, args);
      }, ms || 0);
    };
  }

  function activate2FA(status) {
    if (status == 1) {
        $(".two-factor-modal .modal-title").text("Deactivate Two-Factor Authentication?");
    } else {
        $(".two-factor-modal .modal-title").text("Activate Two-Factor Authentication?");
    }
    $(".two-factor-modal").modal("show");
    $("#confirmTwoFactor").off("click").on("click", function () {
        $.ajax({
            url: siteUrl("users/activate_2FA"), 
            type: "POST",
            data: {
                csrf_token: _csrf_hash, 
                status: status 
            },
            dataType: "JSON",
            success: function (response) {
                if (response.success) {
                    window.location.replace(response.redirect);
                } else {
                    $(".two-factor-modal").modal("hide");
                    toastr.error(response.message,"Please contact IT Department");
                }
            },
        });
    });
}
