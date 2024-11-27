<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"
     id="modal-performance-rating-remarks">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Performancess Rating Remarks</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-left">
                <div class="d-flex flex-row justify-content-center">
                    <div id="performance-rating-in-remarks"></div>
                </div>
                <p style="min-height: 20px;" id="scale-description" class="mt-1 mb-0 text-center m--font-boldest"></p>
                
                <div class="d-flex flex-row justify-content-center">
                    <div id="performance-rating-in-rehire"></div>
                </div>
                <div class="form-group mt-3" id="employee-container">
                    <label for="employee">Employee</label>
                    <input type="text" id="employee" disabled class="form-control">
                </div>

                <!-- <div class="form-group mt-4">
                    <label for="purpose">Purpose</label>
                    <input type="text" id="purpose" disabled class="form-control">
                </div> -->

                <div class="form-group mt-4">
                    <label for="remarks">Remarks</label>
                    <textarea id="remarks" class="form-control" disabled></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Done</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document)
        .ready(function () {
            $("#performance-rating-in-remarks")
                .starRating({
                    totalStars: 5,
                    starShape: 'rounded',
                    starSize: 35,
                    emptyColor: 'lightgray',
                    hoverColor: '#FFAB00',
                    activeColor: '#FFAB00',
                    ratedColor: '#FFAB00',
                    useGradient: false,
                    initialRating: 0,
                    readOnly: true
                });
        });
</script>