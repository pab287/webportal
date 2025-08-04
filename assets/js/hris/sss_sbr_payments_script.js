let _companies = [];
let _years = [];
const months = [
    { id: 1, text: "January" },
    { id: 2, text: "February" },
    { id: 3, text: "March" },
    { id: 4, text: "April" },
    { id: 5, text: "May" },
    { id: 6, text: "June" },
    { id: 7, text: "July" },
    { id: 8, text: "August" },
    { id: 9, text: "September" },
    { id: 10, text: "October" },
    { id: 11, text: "November" },
    { id: 12, text: "December" }
];

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.years !== "undefined" && _tempContentData.years.length > 0){
        _years = _tempContentData.years;
    }
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){
        _companies = _tempContentData.company;
    }
}

$("#modal-sbr_payment #company").select2({ 
    data: _companies, 
    width: '100%',
    allowClear: true,
    placeholder: 'Select Company',
    dropdownParent: $("#modal-sbr_payment")
});
$("#modal-sbr_payment #payment_date").datepicker({ format: 'yyyy-mm-dd', autoclose: true });
$("#modal-sbr_payment #month")
    .select2({
        width: '100%',
        data: months,
        placeholder: "SELECT MONTH",
        allowClear: true,
        dropdownParent: $("#modal-sbr_payment")
    });

$("#modal-sbr_payment #year")
    .select2({
        width: '100%',
        data: _years,
        placeholder: "SELECT YEAR",
        allowClear: true,
        dropdownParent: $("#modal-sbr_payment")
    });

$("#table-sbr_payments").DataTable({
    "dom": 'frtlip',
    "searching": false,
    "ordering": false,
    "lengthMenu": [ 10, 25, 50, 100 ],
    "pageLength": 10
});