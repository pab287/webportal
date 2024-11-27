<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>

        <link rel="stylesheet" href="<?=base_url('assets/plugins/pdf/pdf_viewer.min.css') ?>">

        <style>
            @media print{
                #view_pdf_container{
                    page-break-before: avoid;
                    page-break-after: avoid;
                }
            }
        </style>
    </head>
    <body>
        <div class="m-content">
            <div class="row">
                <div class="col-md-12">
                    <div id="view_pdf_container" style="text-align: center"></div>
                </div>
            </div>
        </div>
    </body>

    <script src="<?=base_url('assets/plugins/pdf/pdf.min.js') ?>"></script>

    <script>
        let data = [];
        var pdfDoc = null, pageNum = 1;
        var scale = 1.7; //Set Scale for zooming PDF.
        var resolution = 1; //Set Resolution to Adjust PDF clarity.

        data = <?=json_encode($data) ?>;

        var pdfjsLib = window['pdfjs-dist/build/pdf'];
        pdfjsLib.GlobalWorkerOptions.workerSrc = baseUrl('assets/plugins/pdf/pdf.worker.min.js');

        var url = baseUrl('uploads/files/qms/document/'+ data.id +'/'+data.filename);

        previewPDF(data.id, url);

        
        function previewPDF(id, url){
            pdfjsLib.getDocument(url).promise.then(function (pdfDoc_) {
                pdfDoc = pdfDoc_;
                
                //Reference the Container DIV.
                var pdf_container;
                pdf_container = document.getElementById("view_pdf_container");
                pdf_container.style.display = "block";
                
                //Loop and render all pages.
                for (var i = 1; i <= pdfDoc.numPages; i++) {
                    console.log(i);
                    RenderPage(pdf_container, i);
                }

            });
        }

        function RenderPage(pdf_container, num) {
            pdfDoc.getPage(num).then(function (page) {
                //Create Canvas element and append to the Container DIV.
                var canvas = document.createElement('canvas');
                canvas.id = 'pdf-' + num;
                ctx = canvas.getContext('2d');
                pdf_container.append(canvas);
                
                //Create and add empty DIV to add SPACE between pages.
                var spacer = document.createElement("div");
                spacer.style.height = "20px";
                pdf_container.appendChild(spacer);

                //Set the Canvas dimensions using ViewPort and Scale.
                var viewport = page.getViewport({ scale: scale });

                canvas.height = resolution * viewport.height;
                canvas.width = resolution * viewport.width;

                //Render the PDF page.
                var renderContext = {
                    canvasContext: ctx,
                    viewport: viewport,
                    transform: [resolution, 0, 0, resolution, 0, 0]
                };

                page.render(renderContext);
            });
        };


        function baseUrl(url){
            return  '<?=base_url() ?>' + url;
        }

        setTimeout(() => {
            window.print();
            window.focus();
            window.close();
        }, (500));
    </script>
</html>
