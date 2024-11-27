<style>
    .barcode-label {
        margin-bottom: 12px;
        font-size: 10px;
        font-weight: 500;
    }

    .barcode-code {
        height: 30px;
    }
</style>

<div class="modal fade" role="dialog" id="modal-print-barcode">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="m--font-boldest">BARCODE</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="barcodes">
                    <div class="row"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btnPrint" onclick="print('barcodes')">Print</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function print(elem) {
        var mywindow = window.open('', 'PRINT BARCODE');

        mywindow.document.write('<html><head><title>' + document.title + '</title>');
        mywindow.document.write(`<style type="text/css">
                                    @font-face {
                                        font-family: barcode;
                                        src: url("${baseUrl('assets/fonts/barcode/barcode.woff')}");
                                    }

                                    @media print {
                                          .col-1, .col-2, .col-3, .col-4, .col-5, .col-6,
                                          .col-7, .col-8, .col-9, .col-10, .col-11, .col-12 {
                                               float: left;
                                          }

                                          .col-12 {
                                               width: 100%;
                                          }

                                          .col-11 {
                                               width: 91.66666666666666%;
                                          }

                                          .col-10 {
                                               width: 83.33333333333334%;
                                          }

                                          .col-9 {
                                                width: 75%;
                                          }

                                          .col-8 {
                                                width: 66.66666666666666%;
                                          }

                                           .col-7 {
                                                width: 58.333333333333336%;
                                           }

                                           .col-6 {
                                                width: 50%;
                                           }

                                           .col-5 {
                                                width: 41.66666666666667%;
                                           }

                                           .col-4 {
                                                width: 33.33333333333333%;
                                           }

                                           .col-3 {
                                                width: 25%;
                                           }

                                           .col-2 {
                                                width: 16.666666666666664%;
                                           }

                                           .col-1 {
                                                width: 8.333333333333332%;
                                            }

                                           .text-center {
                                                text-align: center;
                                           }
                                    }

                                    .barcode-label {
                                        margin-bottom: 3px !important;
                                        font-size: 11px;
                                        font-family: Roboto, sans-serif;
                                    }

                                    .barcode-code {
                                        margin-top: 3px;
                                        height: 50px;
                                    }
                                 </style>`);
        mywindow.document.write('</head><body >');
        mywindow.document.write(document.getElementById(elem).innerHTML);
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        mywindow.print();
        mywindow.close();

        return true;
    }
</script>