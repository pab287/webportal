var vmAcctSearchFix = new Vue({
    el: "#tempSearchRefno",
    data: { rows: {}, acct_count: 0, acct_body: 0, accounted_id: [] },
    methods: {
        getItems: function () {
            var _this = this;
            var currentRow = _this.rows;
            var acctIds = _this.accounted_id;
            var tempItems = [];

            $.each(currentRow.asset_components, function (i, v) {
                if (jQuery.inArray(v.id, acctIds) == -1) {
                    tempItems.push(v);
                }
            });
            return tempItems;
        },
        setAcctBody: function (id, acct_id, type) {
            if (id) {
                $.ajax({
                    url: siteUrl("eforms/accountability/set_form_content_data"),
                    dataType: "json",
                    type: "post",
                    data: {
                        csrf_token: _csrf_hash,
                        id: id,
                        acct_id: acct_id,
                        acct_type: type,
                    }, success: function (json) {
                        if (json.response) {
                            vmAcctSearchFix.rows = Object.assign({}, json.data);
                            vmAcctSearchFix.accounted_id = json.accounted_id;
                            vmAcctSearchFix.acct_count = json.acct_count;
                            vmAcctSearchFix.acct_body = json.cc_count;
                        } else {
                            vmAcctSearchFix.rows = {};
                            vmAcctSearchFix.acct_count = 0;
                            vmAcctSearchFix.acct_body = 0;
                            vmAcctSearchFix.accounted_id = [];
                        }
                    }
                });
            }
        }
    }
});

var vmAcctFix = new Vue({
    el: "#m--accountability_body-fix",
    data: { rows: {}, count: 0 }
});

var generateAccountabilityBodyFix = function () {
    $.ajax({
        url: siteUrl("eforms/accountability/generate_form_content_fix"),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                vmAcctFix.rows = Object.assign({}, json.data);
                vmAcctFix.count = json.count;
            }
        }
    });
}

$(document).on("keypress", "#search_refno", function (e) {
    if (e.which == 13) {
        var value = e.target.value;
        $.ajax({
            url: siteUrl("eforms/accountability/get_form_content_fix/" + value + "/true"),
            dataType: "json",
            success: function (json) {
                if (json.response) {
                    vmAcctSearchFix.rows = Object.assign({}, json.data);
                    vmAcctSearchFix.accounted_id = json.accounted_id;
                    vmAcctSearchFix.acct_count = json.acct_count;
                    vmAcctSearchFix.acct_body = json.cc_count;
                } else {
                    vmAcctSearchFix.rows = {};
                    vmAcctSearchFix.acct_count = 0;
                    vmAcctSearchFix.acct_body = 0;
                    vmAcctSearchFix.accounted_id = [];
                }
            }
        });
    }

});