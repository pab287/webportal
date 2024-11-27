<div class="">
    <div class="d-flex flex-row status-container">
        <!-- START IN WEEKLY -->
        <div class="pms-status">
            <div class="accordion">
                <div class="m-widget24 bolder-titles">
                    <div class="m-widget24__item pb-4">
                        <h4 class="m-widget24__title m--font-info">
                            WEEKLY
                        </h4>
                        <br>
                        <span class="m-widget24__desc">
                            Total tasks.
                        </span>
                        <span class="m-widget24__stats m--font-info" id="weekly-total">0</span>
                        <div class="m--space-10"></div>
                        <div class="progress m-progress--sm">
                            <div class="progress-bar m--bg-info"
                                 role="progressbar"
                                 aria-valuenow="0"
                                 aria-valuemin="0"
                                 aria-valuemax="100" id="pbWeekly"></div>
                        </div>
                    </div>
                </div>

                <div class="collapse show"
                     id="collapseWeekly">
                    <div class="m-widget4">
                    </div>
                </div>
            </div>
        </div>
        <!-- END IN WEEKLY -->

        <!-- START IN MONTHLY -->
        <div class="pms-status">
            <div class="accordion">
                <div class="m-widget24 bolder-titles">
                    <div class="m-widget24__item pb-4">
                        <h4 class="m-widget24__title m--font-info">
                            MONTHLY
                        </h4>
                        <br>
                        <span class="m-widget24__desc">
                            Total tasks.
                        </span>
                        <span class="m-widget24__stats m--font-info" id="monthly-total">0</span>
                        <div class="m--space-10"></div>
                        <div class="progress m-progress--sm">
                            <div class="progress-bar m--bg-info"
                                 role="progressbar"
                                 aria-valuenow="0"
                                 aria-valuemin="0"
                                 aria-valuemax="100" id="pbMonthly"></div>
                        </div>
                    </div>
                </div>

                <div class="collapse show"
                     id="collapseMonthly">
                    <div class="m-widget4">
                    </div>
                </div>
            </div>
        </div>
        <!-- END IN WEEKLY -->

        <!-- START IN BACKLOGS -->
        <div class="pms-status">
            <div class="accordion">
                <div class="m-widget24 bolder-titles">
                    <div class="m-widget24__item pb-4">
                        <h4 class="m-widget24__title m--font-danger">
                            Back Logs
                        </h4>
                        <br>
                        <span class="m-widget24__desc">
                                Past due tasks.
                            </span>
                        <span class="m-widget24__stats m--font-danger" id="backlogs-total">0</span>
                        <div class="m--space-10"></div>
                        <div class="progress m-progress--sm">
                            <div class="progress-bar m--bg-danger"
                                 role="progressbar"
                                 aria-valuenow="0"
                                 aria-valuemin="0"
                                 aria-valuemax="100" id="pbBacklogs"></div>
                        </div>
                    </div>
                </div>

                <div class="collapse show"
                     id="collapseBackLogs">
                    <div class="m-widget4">
                    </div>
                </div>
            </div>
        </div>
        <!-- END IN BACKLOGS -->

        <!-- START IN PROGRESS -->
        <div class="pms-status">
            <div class="accordion">
                <div class="m-widget24 bolder-titles">
                    <div class="m-widget24__item pb-4">
                        <h4 class="m-widget24__title m--font-primary">
                            In Progress
                        </h4>
                        <br>
                        <span class="m-widget24__desc">
                                Currently on going.
                            </span>
                        <span class="m-widget24__stats m--font-primary" id="in-progress-total">0</span>
                        <div class="m--space-10"></div>
                        <div class="progress m-progress--sm">
                            <div class="progress-bar m--bg-primary"
                                 role="progressbar"
                                 aria-valuenow="0"
                                 aria-valuemin="0"
                                 aria-valuemax="100" id="pbInProgress"></div>
                        </div>
                    </div>
                </div>

                <div class="collapse show"
                     id="collapseInProgress">
                    <div class="m-widget4">
                    </div>
                </div>
            </div>
        </div>
        <!-- END IN PROGRESS -->

        <!-- START IN AWAITING -->
        <div class="pms-status">
            <div class="accordion">
                <div class="m-widget24 bolder-titles">
                    <div class="m-widget24__item pb-4">
                        <h4 class="m-widget24__title">
                            Awaiting
                        </h4>
                        <br>
                        <span class="m-widget24__desc">
                                Task that has no contract yet.
                            </span>
                        <span class="m-widget24__stats m--font-dark" id="awaiting-total">0</span>
                        <div class="m--space-10"></div>
                        <div class="progress m-progress--sm">
                            <div class="progress-bar m-bg--custom_dark"
                                 role="progressbar"
                                 aria-valuenow="0"
                                 aria-valuemin="0"
                                 aria-valuemax="100" id="pbAwaiting"></div>
                        </div>
                    </div>
                </div>

                <div class="collapse show"
                     id="collapseAwaiting">
                    <div class="m-widget4">
                    </div>
                </div>
            </div>
        </div>
        <!-- END IN AWAITING -->

        <!-- START IN DEFERRED -->
        <div class="pms-status">
            <div class="accordion">
                <div class="m-widget24 bolder-titles">
                    <div class="m-widget24__item pb-4">
                        <h4 class="m-widget24__title m--font-success">
                            Deferred
                        </h4>
                        <br>
                        <span class="m-widget24__desc">
                            Awarded but not to be done yet.
                        </span>
                        <span class="m-widget24__stats m--font-success" id="deferred-total">0</span>
                        <div class="m--space-10"></div>
                        <div class="progress m-progress--sm">
                            <div class="progress-bar m--bg-success"
                                 role="progressbar"
                                 aria-valuenow="0"
                                 aria-valuemin="0"
                                 aria-valuemax="100" id="pbDeferred"></div>
                        </div>
                    </div>
                </div>

                <div class="collapse show"
                     id="collapseDeferred">
                    <div class="m-widget4">
                    </div>
                </div>
            </div>
        </div>
        <!-- END IN DEFERRED -->

        <div class="pms-status end-space">
        </div>
    </div>

    <div class="modal fade"
         data-keyboard="true" data-backdrop="static"
         modal-exempt-custom
         role="dialog" id="modal-container"></div>
</div>