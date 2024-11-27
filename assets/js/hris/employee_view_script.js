$(document).ready(function(){
    var id = $("#employee_id").val();
    getPerformanceRating(id);

    $('#column-options').on('click', function (e) {
        e.stopPropagation();
    });
});

function getPerformanceRating(id){
    $("#performance-rating").starRating({
        readOnly: true,
        totalStars: 5,
        starShape: 'rounded',
        starSize: 25,
        emptyColor: 'lightgray',
        hoverColor: '#FFC400',
        activeColor: '#FFAB00',
        ratedColor: '#FFAB00',
        useGradient: false,
        useFullStars: true,
      });

    $.ajax({
        url: baseUrl("core/profile/get_employee_rating_remarks/" + id),
        type: "post",
        dataType: "json",
        data: { csrf_token: _csrf_hash},
        success: function (response) {
            if(response){
                $("#performance-rating").starRating('setRating', response.rating);
                $("#performance-rating-description").html(response.description);
            }else{
                $("#performance-rating").starRating('setRating', 0);
                $("#performance-rating-description").html("");
            }
            
           
        }
    });
    
}

function printEmployeeDataSheet(element, avatar, info, user, timestamp) {
    const divToPrint = $(".data-sheet").html();
    const newWin = window.open('', 'Print-Employee Data Sheet');
    const style1 = baseUrl("assets/css/responsiveTable.css");
    const style2 = baseUrl("assets/vendors/base/vendors.bundle.css");
    const style3 = baseUrl("assets/demo/demo3/base/style.bundle.css");

    const bootstrapRowCol = `.row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px; }
        .col-1, .col-2, .col-3, .col-4, .col-5, .col-6, .col-7, .col-8, .col-9, .col-10, .col-11, .col-12, .col,
        .col-auto, .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm,
        .col-sm-auto, .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12, .col-md,
        .col-md-auto, .col-lg-1, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg,
        .col-lg-auto, .col-xl-1, .col-xl-2, .col-xl-3, .col-xl-4, .col-xl-5, .col-xl-6, .col-xl-7, .col-xl-8, .col-xl-9, .col-xl-10, .col-xl-11, .col-xl-12, .col-xl,
        .col-xl-auto {
        position: relative;
        width: 100%;
        min-height: 1px; }

        .col {
        flex-basis: 0;
        flex-grow: 1;
        max-width: 100%; }

        .col-auto {
        flex: 0 0 auto;
        width: auto;
        max-width: none; }

        .col-1 {
        flex: 0 0 8.33333%;
        max-width: 8.33333%; }

        .col-2 {
        flex: 0 0 16.66667%;
        max-width: 16.66667%; }

        .col-3 {
        flex: 0 0 25%;
        max-width: 25%; }

        .col-4 {
        flex: 0 0 33.33333%;
        max-width: 33.33333%; }

        .col-5 {
        flex: 0 0 41.66667%;
        max-width: 41.66667%; }

        .col-6 {
        flex: 0 0 50%;
        max-width: 50%; }

        .col-7 {
        flex: 0 0 58.33333%;
        max-width: 58.33333%; }

        .col-8 {
        flex: 0 0 66.66667%;
        max-width: 66.66667%; }

        .col-9 {
        flex: 0 0 75%;
        max-width: 75%; }

        .col-10 {
        flex: 0 0 83.33333%;
        max-width: 83.33333%; }

        .col-11 {
        flex: 0 0 91.66667%;
        max-width: 91.66667%; }

        .col-12 {
        flex: 0 0 100%;
        max-width: 100%; }
    `;
    const _document = '' +
        '<html>' +
        '   <head>' +
        '       <link rel="stylesheet" href="' + style1 + '" />' +
        '       <style>' +
        '           @media print {' +
        '               td {' +
        '                   text-transform: uppercase;' +
        '               }' +
        '               th {' +
        '                   text-align: left;' +
        '               }' +
        '               td[scope], th[scope] {' +
        '                   background-color: #f8f8f8;' +
        '                   -webkit-print-color-adjust: exact;' +
        '               }' +
        '               .avatar {' +
        '                   position: relative;' +
        '                   background-image: url("' + avatar + '");' +
        '                   width: 192px;' +
        '                   height: 192px;' +
        '                   background-size: cover;' +
        '                   background-position: center;' +
        '                   background-repeat: no-repeat;' +
        '                   overflow: hidden;' +
        '                   border-radius: 6px;' +
        '                   -webkit-print-color-adjust: exact;' +
        '               }' +
        '               table { page-break-inside: auto; }' +
        '               tr, td, th, { page-break-inside: avoid; page-break-after: auto; }' +
        '           }' +
        '' +
        '           td {' +
        '               text-transform: uppercase;' +
        '           }' +
        '           th {' +
        '               text-align: left;' +
        '           }' +
        '           body * {' +
        '               font-family: Arial, Helvetica, sans-serif;' +
        '               font-size: 12px;' +
        '           }' +
        '           img {' +
        '               width: 192px;' +
        '               border: 1px solid #dedede;' +
        '               border-radius: 6px;' +
        '           }' +
        '           .title {' +
        '               font-size: 18px;' +
        '               margin: 0;' +
        '           }' +
        '           th[scope], td[scope] {' +
        '               background: #f8f8f8;' +
        '           }' +
        '           .avatar {' +
        '               position: relative;' +
        '               background-image: url(' + avatar + ');' +
        '               width: 192px;' +
        '               height: 192px;' +
        '               background-size: cover;' +
        '               background-position: center;' +
        '               background-repeat: no-repeat;' +
        '               overflow: hidden;' +
        '               border-radius: 6px;' +
        '           }' +
        '           .m--hidden-desktop {' +
        '               display: none;' +
        '           }' +
        '           #hide-in-print {' +
        '               display: none;' +
        '           }' + bootstrapRowCol +
        '       </style>' +
        '   </head>' +
        '   <body onload="window.print()">' +
        '       <table border="0" collspan="0" cellpadding="0" cellspacing="0" width="100%"' +
        '              style="margin-bottom: 24px;">' +
        '           <tr valign="bottom">' +
        '               <td width="90%">' +
        '                   <h1 class="title">EMPLOYEE DATA SHEET</h1>' +
        '               </td>' +
        '               <td>' +
        '                   <div style="display: block;">' +
        '                       <div class="avatar"></div>' +
        '                       <p style="margin: 8px 0 0; font-size: 14px;">ID No.: ' + info.idno + '</p>' +
        '                       <p style="margin: 2px 0 0; font-size: 14px;">Biometric No.: ' + info.biometricno + '</p>' +
        '                   </div>' +
        '               </td>' +
        '           </tr>' +
        '       </table>' +
        '       ' + divToPrint +
        '       <table border="0" collspan="0" cellpadding="0" cellspacing="0" width="100%">' +
        '           <tr valign="bottom">' +
        '               <td style="line-height: 20px; text-align: justify;" colspan="2">' +
        '                   I AUTHORIZE THE COMPANY TO INVESTIGATE ALL INFORMATION CONTAINED IN THIS APPLICATION. I UNDERSTAND AND THAT ANY' +
        '                   MISREPRESENTATION OR OMISSION OF FACTS CALLED FOR IN THIS APPLICATION SHALL BE CONSIDERED CAUSE FOR ANY DISMISSAL.' +
        '               </td>' +
        '           </tr>' +
        '           <tr>' +
        '               <td width="50%" align="center">' +
        '                   <div style="width: 40%; border-top: 1px solid grey; margin-top: 48px;">DATE</div>' +
        '               </td>' +
        '               <td width="50%" align="center">' +
        '                   <div style="width: 70%; border-top: 1px solid grey; margin-top: 48px;">SIGNATURE OVER PRINTED NAME</div>' +
        '               </td>' +
        '           </tr>' +
        '           <tr>' +
        '               <td colspan="2">' +
        '                   <div style="margin-top: 48px;">' +
        '                       <i><b>Printed by: </b><span style="text-transform: capitalize;">' + user.toLowerCase() + ';</span> <b>Date & Time:</b> ' + timestamp + '</i>' +
        '                   </div>' +
        '               </td>' +
        '           </tr>' +
        '       </table>' +
        '   </body' +
        '</html>';
    newWin.document.open();
    newWin.document.write(_document);
    newWin.document.close();
    setTimeout(function () {
        newWin.close();
    }, 1500);
    
}

function showRemarks(remarks) {
    $('#remarksText').text(remarks);
    $('#remarksModal').modal('show');
}

