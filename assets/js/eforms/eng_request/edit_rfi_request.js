let _employee = null;
let _projects = null;
let _req_types = null;
let rfiTable = null;
let rfaTable = null;
let is_archive = 0;
let informationEditor;
let person_in_charge = null;
let request_type_id = null;
let request_type_code = null;

let edit_rfi = new Vue ({
    el: '#edit_rfi_content',
    data: {
        activity_logs: {},
        content: {},
    },
    mounted: function () {

    },
})

