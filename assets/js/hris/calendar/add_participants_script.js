let eventsDetails = null;
let participants = null;
if (_tempContentData !== undefined && _tempContentData !== null && _tempContentData !== '') {
    console.log(_tempContentData);
    eventsDetails = {..._tempContentData.event_details};
    participants = {..._tempContentData.participants};
}

let eventVue = new Vue({
    el: "#events-content",
    data: {
        eventsData:{

        },
    },
    mounted: function () {
        this.eventsData = JSON.parse(JSON.stringify(eventsDetails));
    },
    computed: {
        eventStatus() {
            return this.eventsStatus(this.eventsData.event_from, this.eventsData.event_to);
        }
    },
    methods:{
        eventsStatus(date_from, date_to) {
            const now = new Date();
            const start = new Date(date_from);
            const end = new Date(date_to);
            if (now < start) {
                return { label: "Upcoming Event", class: "bg-warning text-dark" };
            }
            if (now >= start && now <= end) {
                return { label: "Ongoing Event", class: "bg-success" };
            }
            return { label: "Event Done", class: "bg-secondary" };
        },
        formatDate(date_from, date_to) {
            const start = new Date(date_from), end = new Date(date_to)
            const fmt = (d, opts) => d.toLocaleDateString("en-US", opts)
            const optMD = { month: "short", day: "numeric" }, optY = { year: "numeric" }
    
            if (start.getFullYear() === end.getFullYear()) {
                if (start.getMonth() === end.getMonth())
                    return `${fmt(start, optMD)} - ${end.getDate()}, ${start.getFullYear()}`
                return `${fmt(start, optMD)} - ${fmt(end, optMD)}, ${start.getFullYear()}`
            }
            return `${fmt(start, { ...optMD, ...optY })} - ${fmt(end, { ...optMD, ...optY })}`
        }
    },
});

let participantsArray = Object.values(participants);
console.log(participantsArray);
const participantsTable = $('#participantsTable').DataTable({
    dom: 'frtlip',
    data: participantsArray,
    scrollX: true,
    responsive: true,
    autoWidth: false,
    searching: false,
    rowId: 'id',
    columns: [
        { data: 'id', visible: false },
        { data: null, title: 'Participant',
            render: function (data, type, row, meta) {
                return `
                    <div class="font-weight-bold text-uppercase">${row.fullname}</div>
                    <div class="text-muted small">${row.position ?? ''}</div>
                `;
            }
        },
        { data: null, title: 'Company', className: "text-center",
            render: function (data, type, row, meta) {
                return `
                    <div>${row.company ?? ''}</div>
                    <div class="text-muted small">${row.department ?? ''}</div>
                `;
            }
        },
        { data: null, title: 'Contact', className: "text-center",
            render: function (data, type, row, meta) {
                return `
                    <div><i class="la la-envelope"></i> ${row.email ?? ''}</div>
                    <div><i class="la la-phone"></i> ${row.mobile_no ?? ''}</div>
                `;
            }
        },
        { data: 'status', title: 'Status', className: "text-center",
            render: function (data, type, row, meta) {
                const statusMap = {
                    pending: 'badge-warning',
                    invited: 'badge-info',
                    confirmed: 'badge-success',
                    declined: 'badge-danger'
                };
                return `<span class="badge ${statusMap[data] || 'badge-secondary'}">${data}</span>`;
            }
        },
        { data: null, title: 'Action', className: "text-center", orderable: false,
            render: function (data, type, row, meta) {
                return itemDatatableActions(row.id, row.status);
                if (row.id) {
                    var _actionButton = "";
                    _actionButton += "<div class='dropdown'>";
                    _actionButton += "<a href='#' class='btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' data-toggle='dropdown'>";
                    _actionButton += "<i class='fa fa-ellipsis-v'></i>";
                    _actionButton += "</a>";
                    _actionButton += "<div class='dropdown-menu dropdown-menu-right'>";
            
                    _actionButton += "<a style='width: auto; cursor: pointer;' class='dropdown-item btnEdit' onclick='edit_resume(" + row.id + ")'><i class='la la-pencil-square'></i>Edit</a>";
                    _actionButton += "<a style='width: auto; cursor: pointer;' class='dropdown-item btnEdit' onclick='archive_resume(" + row.id + ")'><i class='la la-folder'></i>Archive</a>";
                    _actionButton += " </div>";
                    _actionButton += "</div>";
                    return _actionButton;
                } else {
                    return "HH";
                }
            }
        }
    ]
});

function itemDatatableActions(id, status) {
    let _actionButton = "";
    _actionButton += " <a style='text-decoration: none;' " +
        "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit' " +
        "   onclick='onEditEvent(" + id + ")' " +  
        "   data-toggle='m-tooltip' data-placement='bottom' title='View Ticket' " +
        "   data-skin='dark' " +
        "   title='View Event'>" +
        "   <i class='la la-eye'></i>" +
        "</a>";

    _actionButton += " <button " +
        "   type='button' " +
        "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnArchive' " +
        "   onclick='deleteArchive(" + id + ")' " +   
        "   data-toggle='m-tooltip' data-placement='bottom' title='Archive Ticket' " +
        "   data-skin='dark'>" +
        "   <i class='la la-file-archive-o'></i>" +
        "</button>";

        _actionButton += " <a " +
        "   href='" + baseUrl('hris/calendar/add_participants/') + id + "' " +
        "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' " +
        "   data-toggle='m-tooltip' data-placement='bottom' title='Add Participants' " +
        "   data-skin='dark'>" +
        "   <i class='la la-user-plus'></i>" +
        "</a>";

    return _actionButton;
}