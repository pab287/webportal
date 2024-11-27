var accomplishmentContent = $("#contract_accomplishment-content");
var weekDuration = accomplishmentContent.find("#week_duration");
var vmContractData = new Vue({
    el: "#contract_accomplishment-content",
    data: { row: {}, items: {}, item_count: 0, grand_total: 0, adjustments: [], item_counter: [], sub_total: [], sub_ret_total: [] },
    methods: {
        renderOddEvenClass: function (index) {
            var tempValue = index % 2;
            return (tempValue == 0) ? "odd" : "even";
        }, doEventHere: function (e) {
            console.log(e);
        }
    },
    mounted: function () {
        var _this = this;
        var weekDuration = $(_this.$el).find("#week_duration");
        setTimeout(function () {
            if (typeof weekDuration !== "undefined") {
                weekDuration.daterangepicker({
                    showDropdowns: true,
                    cancelClass: "btn-danger"
                });
            }
        }, 1500);
    }
});

var timeout;
var getCurrentItems = function (id) {
    $.ajax({
        url: baseUrl("pms/contract/get_current_contract_accomplishment/" + id),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                var tempData = Object.assign({}, json.row);
                vmContractData.row = tempData;
                vmContractData.items = Object.assign({}, json.items);
                vmContractData.adjustments = json.adjustment_items;
                vmContractData.item_count = json.item_count;
                vmContractData.item_counter = json.item_counter;
                vmContractData.sub_total = json.sub_total;
                vmContractData.sub_ret_total = json.sub_ret_total;
                vmContractData.grand_total = json.grand_total;

                setTimeout(function () {
                    $('.form-control-table_field.maskQty').maskMoney({
                        allowZero: true,
                        affixesStay: true,
                        allowNegative: false,
                    }).maskMoney('mask');
                    $('.form-control-table_field.maskQty').keyup(function (e) {
                        var _self = this;
                        var _remaining = $(_self).data("remaining");
                        clearTimeout(timeout);
                        timeout = setTimeout(function () {
                            console.log(e.target.value);
                            console.log(_remaining);
                        }, 500);
                    });
                }, 500);
            }
        }
    });
}

jQuery(document).ready(function () {
    getCurrentItems(_tempContentData.id);
});