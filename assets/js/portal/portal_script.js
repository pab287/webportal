$(document).on("click", ".module_redirect", function () {
  var self = $(this);
  var dataId = self.data("id");
  $.ajax({
    url:  siteUrl("portal/redirect_to_module"),
    type: "post",
    dataType: "json",
    data: { id: dataId, csrf_token: _csrf_hash },
    success: function (json) {
      if (json.response) {
        window.location.href = json.redirect;
      } else {
        toastr.warning("Nothing to redirect!", "Portal Redirect", 5000);
      }
  }
});
});

if (typeof idleTimerTrigger !== "undefined" && typeof idleTimerTrigger == "function") {
    idleTimerTrigger();
}

if(typeof getAcctgcount !== "undefined" && typeof getAcctgcount == "function"){
    getAcctgcount();
}

if(window.location == siteUrl("portal/index")){

var vmTab1 = new Vue({
    el: "#portal_notifications",
    data: { vm_tab1: {show:false,}, vm_travel_order: {show:false,}, vm_acct: {show:false,}, vm_borrowing: {show:false,}, vm_overtime: {show:false,}, vm_transmittal: {show:false,}, vm_shipping : {show:false,}, vm_ca : {show:false,} },
    methods: {
        styles: {
            width: '50%',
        },
        getUnapprovedLoa(){
            $.ajax({
                url:  siteUrl("portal/get_unapproved_loa"),
                type: "get",
                dataType: "json",
                success: function (json) {
                    vmTab1.vm_tab1 = Object.assign({}, json);
                    vmTab1.vm_tab1.show = true;
                }
            });
        },
        getTravelOrder(){
            $.ajax({
                url:  siteUrl("portal/get_to_recommendation"),
                type: "get",
                dataType: "json",
                success: function (json) {
                    vmTab1.vm_travel_order = Object.assign({}, json);
                    vmTab1.vm_travel_order.show = true;
                }
            });
        },
        getAccountability(){
            $.ajax({
                url:  siteUrl("portal/get_accountability"),
                type: "post",
                dataType: "json",
                data: {
                  csrf_token : _csrf_hash,
                  company : $("#companySelect option:selected").val(),
                },
                success: function (json) {
                    vmTab1.vm_acct = Object.assign({}, json);
                    vmTab1.vm_acct.show = true;
                }
            });
        },
        getBorrowing(){
            $.ajax({
                url:  siteUrl("portal/get_borrowing"),
                type: "get",
                dataType: "json",
                success: function (json) {
                    vmTab1.vm_borrowing = Object.assign({}, json);
                    vmTab1.vm_borrowing.show = true;
                }
            });
        },
        getOvertime(){
            $.ajax({
                url:  siteUrl("portal/get_overtime"),
                type: "get",
                dataType: "json",
                success: function (json) {
                    vmTab1.vm_overtime = Object.assign({}, json);
                    vmTab1.vm_overtime.show = true;
                }
            });
        },
        getTransmittal(){
            $.ajax({
                url:  siteUrl("portal/get_transmittal"),
                type: "get",
                dataType: "json",
                success: function (json) {
                    vmTab1.vm_transmittal = Object.assign({}, json);
                    vmTab1.vm_transmittal.show=true;
                }
            });
        },getShipping(){
            $.ajax({
                url:  siteUrl("portal/get_shipping"),
                type: "get",
                dataType: "json",
                success: function (json) {
                    vmTab1.vm_shipping = Object.assign({}, json);
                    vmTab1.vm_shipping.show = true;
                }
            });
        },getCashadvance(){
            $.ajax({
                url:  siteUrl("portal/get_cashadvance"),
                type: "get",
                dataType: "json",
                success: function (json) {
                    vmTab1.vm_ca = Object.assign({}, json);
                    vmTab1.vm_ca.show = true;
                }
            });
        },
    },
    mounted: function(){

            $('#showLoa').on('click', () => {
              this.getUnapprovedLoa();
          });
            $('#showCA').on('click', () => {
              this.getCashadvance();
          });
            $('#showOT').on('click', () => {
              this.getOvertime();
          });
            $('#showBORR').on('click', () => {
              this.getBorrowing();
          });
            $('#showTO').on('click', () => {
              this.getTravelOrder();
          });
            $('#showACCT').on('click', () => {
              this.getAccountability();
          });
            $('#showTRANS').on('click', () => {
              this.getTransmittal();
          });
          $('#showSA').on('click', () => {
            this.getShipping();
          });
    
            $("#companySelect").on("select2:select", () => { // Use an arrow function to preserve `this`
              console.log("Selected");
              this.getAccountability(); // `this` now correctly refers to the Vue instance
            });
          },

    

  });
}

$(document).ready(function() {
  $("#companySelect").select2({
    placeholder: 'Select',
    width: '100%',
    allowClear: true,
    ajax: {
        url: baseUrl("portal/get_company_collection"),
        dataType: "json",
        async: true,
        contentType:"application/json; charset=utf-8",
        global: false,
        data: {csrf_token : _csrf_hash},
        processResults: function (data) {
            return data;
        }
    }
  });
});

