<style>
    @media (min-width: 1400px) and (max-width: 1920px){
        .asset-percentage-summary-container {
            min-width: 0px;
        }
        .asset-per-location-container {
            min-width: 0px;
        }
    }

    @media (max-width: 800px){
        .asset-percentage-summary-container {
            min-width: 800px;
        }
    }

    @media (min-width: 300px) and (max-width: 1400px){
        .asset-per-location-container {
            min-width: 1000px;
        }
    }

    @media (min-width: 1400px) and (max-width: 1920px){
        .employee-status-chart-container {
            min-width: 0px;
        }
    }

    @media (min-width: 450px) and (max-width: 540px){
        .employee-status-chart-container {
            min-width: 800px;
        }
    }

    @media (min-width: 450px) and (max-width: 540px){
        .employee-status-chart-container {
            min-width: 800px;
        }
    }

</style>
<div class="m-content m--full-height">
    <div class="row d-flex flex-row align-items-end position-relative"
         style="z-index: 1; padding-top: 25px;">
        <div class="col-12">
            <h4>
                DASHBOARD
                <small class="text-muted">ASSET MANAGEMENT SYSTEM</small>
            </h4>
        </div>
    </div>

    <div class="m-portlet mt-5">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon">
                        <i class="flaticon-graph"></i>
                    </span>
                    <h3 class="m-portlet__head-text">
                        ASSET PERCENTAGE SUMMARY
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
            </div>
        </div>
        <div class="m-portlet__body" style="overflow-x: auto;">
            <div class="asset-percentage-summary-container" style="height: 500px;"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-6">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="flaticon-graph"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                RECENTLY ADDED ASSETS
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <button class="btn btn-primary btn-sm m-btn m-btn--icon m-btn--pill btnAdvance_search"
                                type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            <span><i class="fa fa-search"></i><span>Filter</span><span
                                        class="dropdown-toggle"></span></span>
                        </button>
                        <div class="dropdown-menu" id="asset_type" aria-labelledby="dropdownMenuButton"
                                x-placement="bottom-start"
                                style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                            <a class="dropdown-item" href="#" id="asset" onclick="get_recently_added_assets('week')">
                                <i class="flaticon-calendar"></i>&nbsp;Last 7 Days
                            </a>
                            <a class="dropdown-item" href="#" id="asset component" onclick="get_recently_added_assets('month')">
                                <i class="flaticon-calendar"></i>&nbsp;This Month
                            </a>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="recently-added-assets-container" style="height: 500px;"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="flaticon-graph"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                ACCOUNTED/UNACCOUNTED ASSETS
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <button class="btn btn-primary btn-sm m-btn m-btn--icon m-btn--pill btnAdvance_search"
                                type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            <span><i class="fa fa-search"></i><span>Filter</span><span
                                        class="dropdown-toggle"></span></span>
                        </button>
                        <div class="dropdown-menu" id="asset_type" aria-labelledby="dropdownMenuButton"
                                x-placement="bottom-start"
                                style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                            <a class="dropdown-item" href="#" id="asset" onclick="get_accounted_unaccounted_assets('accounted_asset')">
                                <i class="flaticon-calendar"></i>&nbsp;Asset
                            </a>
                            <a class="dropdown-item" href="#" id="vehicles" onclick="get_accounted_unaccounted_assets('accounted_vehicles')">
                                <i class="flaticon-calendar"></i>&nbsp;Vehicles
                            </a>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="accounted-unaccounted-assets-container" style="height: 500px;"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-lg-12 col-md-12">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="flaticon-graph"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                <span id="per_loc">ASSETS</span> PER LOCATION
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <button class="btn btn-primary btn-sm m-btn m-btn--icon m-btn--pill btnAdvance_search"
                                type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            <span><i class="fa fa-search"></i><span>Filter</span><span
                                        class="dropdown-toggle"></span></span>
                        </button>
                        <div class="dropdown-menu" id="asset_type" aria-labelledby="dropdownMenuButton"
                                x-placement="bottom-start"
                                style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                            <a class="dropdown-item" href="#" id="assets" onclick="get_asset_per_location('asset')">
                                <i class="flaticon-open-box"></i>&nbsp;Asset
                            </a>
                            <a class="dropdown-item" href="#" id="asset components" onclick="get_asset_per_location('asset_component')">
                                <i class="flaticon-open-box"></i>&nbsp;Asset Components
                            </a>
                            <a class="dropdown-item" href="#" id="vehicles" onclick="get_asset_per_location('vehicle')">
                                <i class="flaticon-truck"></i>&nbsp;Vehicle
                            </a>
                            <a class="dropdown-item" href="#" id="vehicle equipment" onclick="get_asset_per_location('vehicle_equipment')">
                                <i class="flaticon-truck"></i>&nbsp;Vehicle Equipment
                            </a>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body" style="overflow-x: auto;">
                    <div class="asset-per-location-container" style="height: 500px;"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-7 col-lg-7 col-md-7">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="flaticon-graph"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                            <span id="per_status">ASSETS</span> PER STATUS
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <button class="btn btn-primary btn-sm m-btn m-btn--icon m-btn--pill btnAdvance_search"
                                type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            <span><i class="fa fa-search"></i><span>Filter</span><span
                                        class="dropdown-toggle"></span></span>
                        </button>
                        <div class="dropdown-menu" id="asset_status" aria-labelledby="dropdownMenuButton"
                                x-placement="bottom-start"
                                style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                            <a class="dropdown-item" href="#" id="assets" onclick="get_asset_per_status('asset')">
                                <i class="flaticon-open-box"></i>&nbsp;Asset
                            </a>
                            <a class="dropdown-item" href="#" id="asset components" onclick="get_asset_per_status('asset_component')">
                                <i class="flaticon-open-box"></i>&nbsp;Asset Components
                            </a>
                            <a class="dropdown-item" href="#" id="vehicles" onclick="get_asset_per_status('vehicle')">
                                <i class="flaticon-truck"></i>&nbsp;Vehicle
                            </a>
                            <a class="dropdown-item" href="#" id="vehicle equipment" onclick="get_asset_per_status('vehicle_equipment')">
                                <i class="flaticon-truck"></i>&nbsp;Vehicle Equipment
                            </a>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body" style="overflow-x: auto;">
                    <div class="asset-per-status-container" style="height: 500px; min-width: 800px;"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-5 col-lg-5 col-md-5">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="flaticon-graph"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                ASSET WITH INCOMPLETE DETAILS
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                    </div>
                </div>
                <div class="m-portlet__body" style="overflow-x: auto;">
                    <div class="asset-incomplete-details-container" style="height: 500px; min-width: 600px;"></div>
                </div>
            </div>
        </div>

    </div>
</div>