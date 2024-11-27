<style>
    .ck-rounded-corners .ck.ck-editor__main>.ck-editor__editable, .ck.ck-editor__main>.ck-editor__editable.ck-rounded-corners{
        min-height: 200px !important;   
    }

    .dropzone .dz-preview .dz-error-message{
        top: 150px !important;
    }

    #tabbedCategories .nav-pills .nav-link {
        border: 1px solid transparent;
        background: #fff;
        padding: 17px 35px;
        color: #71748d;
        background-color: #dddde8;
        margin-bottom: 2px;
    }

    #tabbedCategories .nav-pills .nav-link:last-child{
        margin-bottom: 0px;
    }

    #tabbedCategories .nav-pills .nav-link.active {
        color: #000;
        font-weight: 700;
        background-color: #fff !important;
        border-color: transparent !important;
    }

    #tabbedCategories .nav-pills .nav-link {
        border: 1px solid transparent;
        border-radius: 0 !important;
    }

    #tabbedCategories .scrollable-tab{
        overflow-y: scroll;
    }

    .scrollable-tab::-webkit-scrollbar {
        width: 10px;
    }

    /* Track */
    .scrollable-tab::-webkit-scrollbar-track {
        background: #f1f1f1; 
    }
    
    /* Handle */
    .scrollable-tab::-webkit-scrollbar-thumb {
        background: #888; 
    }

    /* Handle on hover */
    .scrollable-tab::-webkit-scrollbar-thumb:hover {
        background: #555; 
    }

    #tabbedCategoriesl .tab-content {
        overflow: auto;
        -webkit-border-radius: 0px 4px 4px 4px;
        -moz-border-radius: 0px 4px 4px 4px;
        border-radius: 0px 4px 4px 4px;
        background: #fff;
        padding: 30px;
    }

    ul.timeline {
        list-style-type: none;
        position: relative;
        margin: 0;
    }
    ul.timeline:before {
        content: ' ';
        background: #d4d9df;
        display: inline-block;
        position: absolute;
        left: 29px;
        width: 2px;
        height: 100%;
        z-index: 400;
    }
    ul.timeline > li {
        margin: 1px 0 0;
        padding:0 20px;
        text-transform: uppercase;
    }
    ul.timeline > li:before {
        content: ' ';
        background: white;
        display: inline-block;
        position: absolute;
        border-radius: 50%;
        border: 3px solid #22c0e8;
        left: 22px;
        width: 15px;
        height: 15px;
        z-index: 400;
    }
    .col1{
        flex: 0 0 7%;
        max-width: 7%;
        text-align: center;
    }
    .col1 span {
        font-size: 16px;
        font-weight: 800;
        /* margin-bottom: 10px !important; */
    }
    .timeline li, .timeline li:last-child p{
        margin: 0
    }
    .scrollable{
        overflow-y: scroll;
        overflow-x: hidden;
    }
    .scrollable::-webkit-scrollbar {
        width: 5px;
    }
    .scrollable::-webkit-scrollbar-track {
        background : #e5e5e5;
        border-radius: 10px;
    }
    .scrollable::-webkit-scrollbar-thumb {
        background : rgba(255,255,255,0.5);
        border-radius: 11px;
        /* box-shadow:  0 0 6px rgba(0, 0, 0, 0.5); */
    } 
    #v-pills-tabContent ul:last-child li:last-child hr{
        display: none;
    }
</style>

<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="m-portlet m-portlet--mobile mb-0">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title"> 
                            <h3 class="m-portlet__head-text">
                                Document by Department Masterfile
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-12">
                                        <a href="javascript:void(0);"
                                            class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew" id="btnNewPolicy">
                                            <span>
                                                <i class="la la-plus"></i>
                                                <span>
                                                    New Document
                                                </span>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="tabbedCategories" class="container-fluid">
                        <template v-if="count > 0">
                            <div class="row border">
                                <div class="col-md-2 p-0">
                                    <div class="nav flex-column nav-pills m-0 d-flex flex-nowrap flex-column" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                        <template v-for="(item, index) in row">
                                            <a class="nav-link" :class="index == 0 ? 'active' : ''" :id="'v-pills-'+item.department+'-tab'" data-toggle="pill" :href="'#v-pills-'+item.department" role="tab" aria-selected="true" @click="getContent(item.department, item.department_id)">{{ item.department }}</a>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-10 p-4">
                                    <div class="tab-content" id="v-pills-tabContent">
                                        <template v-for="(item, index) in row">
                                            <div class="tab-pane" :class="index == 0 ? 'show active' : 'fade'" :id="'v-pills-'+item.department" role="tabpanel">
                                                
                                                <div class="row align-items-center">
                                                    <div class="col-xl-8 order-2 order-xl-1">
                                                        <div class="form-group m-form__group row align-items-center">
                                                            <div class="col-md-12">
                                                                <button class="btn btn-primary m-btn m-btn--icon m-btn--pill btnAdvance_search" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    <span><i class="fa fa-search"></i><span>Filter</span><span class="dropdown-toggle"></span></span>
                                                                </button>
                                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-query-builder">
                                                                        Query Builder
                                                                    </a>
                                                                    <a id="advance-search" class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-advance-search">
                                                                        Advance Search
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                                                        <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                                            <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." @input="getSearchEvent($event)" :id="item.department+'-generalSearch'">
                                                            <span class="m-input-icon__icon m-input-icon__icon--left">
                                                                <span> <i class="la la-search"></i> </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <table :id="'table-'+item.department" class="table table-striped table-bordered" width="100%"></table>

                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="row">
                                <div class="col-md-12">
                                    <h3 class="text-center">No Data found.</h3>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTempContent" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div id="modalTempContainer" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Temp Title</h5>
                    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">test</div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalRemovePolicy" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div id="modalTempContainer" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="la la-trash mr-2"></i>Archive Policy</h5>
                    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="policyId" value="0"/>
                    <p>Are you sure you want to archive this policy on the list?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger btnClose" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary btn-submit btnDelete btnRemoveCurrentPolicy">Yes
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalTempView" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div id="modalTempContainer" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="view-title">View Document</h5>
                    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="view-pdf" title="PDF" src="" style="width: 100%; height: 80vh;"></iframe>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-query-builder">
        <form id="frm-query-builder">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Query Builder</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="query-builder"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="clear_query_builder()" class="btn btn-danger btnAdvance_search mr-auto">Clear</button>
                        <button type="button" id="query-builder-btn" class="btn btn-primary btnAdvance_search">Generate</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="modal fade" id="modalAdvanceSearch" tabindex="-1" role="dialog" id="modal-advance-search">
        <div class="modal-dialog modal-md" role="document">
            <form method="POST">
                <div id="modalTempContainer" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="view-title">Advance Search</h5>
                        <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div id="modalSearchForm">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Title</label>
                                        <input type="text" v-model="search.title" class="form-control form-control-solid">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Document No.</label>
                                        <input type="text" v-model="search.document_no" class="form-control form-control-solid">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Author</label>
                                        <select id="author" class="form-control form-control-solid">
                                            <option value="-1">Select an Option</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Objective</label>
                                        <textarea class="form-control form-control-solid" v-model="search.objective" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Companies under Document</label>
                                        <select id="companies" class="form-control form-control-solid" multiple>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Document Type</label>
                                        <select id="category" class="form-control form-control-solid" multiple>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="clear_advance_search" class="btn btn-danger btnAdvance_search mr-auto">Clear</button>
                        <button type="button" id="modal-advance-search-submit" class="btn btn-primary btnAdvance_search">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>