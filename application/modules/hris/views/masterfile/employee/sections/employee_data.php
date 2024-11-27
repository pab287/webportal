<style>
  @media print {
    table { page-break-inside:auto }
    div   { page-break-inside:avoid; } /* This is the key */
    thead { display:table-header-group }
    tfoot { display:table-footer-group }

    .email_container {
      word-wrap: break-word;
    }

    table {
      margin-top: -1px;
    }
  }

    .bg-a9{
        background: #a9a8a8;
        border-color: #a9a8a8;
        border: 1px solid #a9a8a8 !important;
    }

    .bg-a9 .m-portlet__head {
        background-color: #a9a8a8;
        border-color: #a9a8a8;
    }

    .bg-a9 .m-portlet__head-text{
        color: #fff !important;
    }

  @media screen and (max-width: 640px){
    #accountability_table_mobile_wrapper .col-sm-12.col-md-6{
        padding: 0;
    }

    .m-portlet__head-text{
        font-size: 1.10rem;
    }

    td label{
        margin-bottom : 0 !important;
    }
  }

  @media screen and (max-width: 420px){
    .m-portlet__head-text {
        font-size: 12px;
    }
  }

  @media screen and (max-width: 390px){
    .m-portlet__head-text {
        font-size: 10px;
    }
    #break-390{
        display: block !important;
    }
  }
</style>

<div id="accordionOtherAdditionalInfo" class="accordion mb-5" role="tablist" aria-multiselectable="true">
    <div class="card">
        <div id="headingPersonal" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfo" href="#collapsePersonal" aria-expanded="false" aria-controls="collapsePersonal">
                        <h5 class="m-portlet__head-text">
                            <span>Personal Information</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapsePersonal" class="collapse show" role="tabpanel" aria-labelledby="headingPersonal" data-parent="#accordionOtherAdditionalInfo">
            <div class="card-body m-portlet__body--custom table-responsive">
            <table class="responsive">
                <thead>
                    <tr>
                        <th class="" scope="col">First Name</th>
                        <th class="" scope="col">Middle Name</th>
                        <th class="" scope="col">Last Name</th>
                        <th class="" scope="col" style="width: 15%;">Suffix</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="First Name"> <label><?= $data->main->firstname ?></label> </td>
                        <td data-label="Middle Name"> <label><?= $data->main->middlename ? $data->main->middlename : " --- " ?></label> </td>
                        <td data-label="Last Name"> <label><?= $data->main->lastname ?></label> </td>
                        <td data-label="Suffix"> <label><?= $data->main->suffix ? $data->main->suffix : "&nbsp;" ?></label> </td>
                    </tr>
                </tbody>
            </table>

            <table class="responsive">
                <thead>
                    <tr>
                        <th class="" scope="col">Current Address</th>
                        <th class="" scope="col">Permanent Address</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Current Address"> <label><?= $data->main->curr_addr ? $data->main->curr_addr : "None"  ?></label> </td>
                        <td data-label="Provincial Address"> <label><?= $data->main->prov_addr ? $data->main->prov_addr : "None"  ?></label> </td>
                    </tr>
                </tbody>
            </table>

            <table class="responsive">
                <thead>
                    <tr>
                        <th class="" scope="col">Citizenship</th>
                        <th class="" scope="col">Religion</th>
                        <th class="" scope="col">Languages</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Citizenship"> <label><?= $data->main->citizenship ? $data->main->citizenship : "None" ?></label> </td>
                        <td data-label="Religion"> <label><?= $data->main->religion ? $data->main->religion : "None" ?></label> </td>
                        <td data-label="Languages"> <label><?= $data->main->languages ? $data->main->languages : "None" ?></label> </td>
                    </tr>
                </tbody>
            </table>

            <table class="responsive">
                <thead>
                    <tr>
                        <th class="" scope="col">Gender</th>
                        <th class="" scope="col">Civil Status</th>
                        <th class="" scope="col" style="width: 13%;">Date of Birth</th>
                        <th class="" scope="col">Place of Birth</th>
                        <th class="" scope="col">Blood Type</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Gender"> <label><?= $data->main->gender ? $data->main->gender : "N/A"  ?></label> </td>
                        <td data-label="Civil Status"> <label><?= $data->main->civil_stat ? $data->main->civil_stat : "N/A" ?></label> </td>
                        <td data-label="Date of Birth">
                            <label>
                                <?php
                                    $bday = new DateTime($data->main->bday);
                                    echo $bday->format("M d, Y");
                                ?>
                            </label>
                        </td>
                        <td data-label="Place of Birth"> <br id="break-390" style="display: none"><label><?= $data->main->birthplace ? $data->main->birthplace : "None"  ?></label> </td>
                        <td data-label="Blood Type"> <label><?= $data->main->bloodtype ? $data->main->bloodtype : "None" ?></label> </td>
                    </tr>
                </tbody>
            </table>

            <table class="responsive">
                <thead>
                    <tr>
                        <th class="" scope="col">Height</th>
                        <th class="" scope="col">Weight</th>
                        <th class="" scope="col">Hair Color</th>
                        <th class="" scope="col">Complexion</th>
                        <th class="" scope="col">Tel. No.</th>
                        <th class="" scope="col">Mobile No.</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Height"><label><?= $data->main->height ? $data->main->height : "N/A" ?></label></td>
                        <td data-label="Weight"><label><?= $data->main->weight ? $data->main->weight : "N/A" ?></label></td>
                        <td data-label="Hair Color"><label><?= $data->main->hair_color ? $data->main->hair_color : "N/A" ?></label></td>
                        <td data-label="Complexion"><label><?= $data->main->complexion ? $data->main->complexion : "N/A" ?></label></td>
                        <td data-label="Tel. No."><label><?= $data->main->tel_no ? $data->main->tel_no : "N/A" ?></label></td>
                        <td data-label="Mobile No."><label><?= $data->main->mobile_no ? $data->main->mobile_no : "N/A" ?></label></td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="headingAdditional" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfo" href="#collapseAdditional" aria-expanded="false" aria-controls="collapseAdditional">
                        <h5 class="m-portlet__head-text">
                            <span>Additional Information</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseAdditional" class="collapse" role="tabpanel" aria-labelledby="headingAdditional" data-parent="#accordionOtherAdditionalInfo">
            <div class="card-body m-portlet__body--custom table-responsive">
                <table class="responsive">
                    <thead>
                        <tr>
                            <th class="" scope="col">Email</th>
                            <th class="" scope="col" style="width: 120px;">Tax Status</th>
                            <th class="" scope="col">Tin No.</th>
                            <th class="" scope="col">Philhealth No.</th>
                            <th class="" scope="col">Pag-ibig No.</th>
                            <th class="" scope="col">SSS No.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="Email" class="email_container"><label><?= $data->main->email ? $data->main->email : "NONE" ?></label></td>
                            <td data-label="Tax Status"><label><?= $data->main->tax_status ? $data->main->tax_status : "NONE" ?></label></td>
                            <td data-label="Tin No."><label><?= $data->main->tin_no ? $data->main->tin_no : "NONE" ?></label></td>
                            <td data-label="Philhealth No."><label><?= $data->main->phealth_no ? $data->main->phealth_no : "NONE" ?></label></td>
                            <td data-label="Pag-ibig No."><label><?= $data->main->pagibig_no ? $data->main->pagibig_no : "NONE" ?></label></td>
                            <td data-label="SSS No."><label><?= $data->main->sss_no ? $data->main->sss_no : "NONE" ?></label></td>
                        </tr>
                    </tbody>
                </table>

                <table class="responsive m--hidden-mobile">
                    <thead>
                        <tr>
                            <th class="" scope="col" style="width: 110px;"></th>
                            <th class="" scope="col">Father</th>
                            <th class="" scope="col">Mother</th>
                            <th class="" scope="col">Spouse/Partner</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                                if($data->main->mot_deceased){
                                    $deceased_mot = "<span class='m-badge m-badge--primary m-badge--wide'>Deceased</span>";
                                }else{
                                    $deceased_mot = "";
                                }

                                if($data->main->fat_deceased){
                                    $deceased_fat = "<span class='m-badge m-badge--primary m-badge--wide'>Deceased</span>";
                                }else{
                                    $deceased_fat = "";
                                }
                            ?>
                            <th class="row-header" scope="col">Name</th>
                            <td><label><?= $data->main->fat_name ? $data->main->fat_name." ".$deceased_fat : "None" ?></label></td>
                            <td><label><?= $data->main->mot_name ? $data->main->mot_name." ".$deceased_mot : "None" ?></label></td>
                            <td>
                                <label>
                                    <?php 
                                        if($data->main->partner_type == 1){
                                            if($data->main->spo_deceased){
                                                echo $data->main->spo_name." "."<span class='m-badge m-badge--primary m-badge--wide'>Deceased</span>";
                                            }else{
                                                echo $data->main->spo_name;
                                            }
                                        }else if($data->main->partner_type == 2){
                                            if($data->main->partners_deceased){
                                                echo $data->main->partners_name." "."<span class='m-badge m-badge--primary m-badge--wide'>Deceased</span>";
                                            }else{
                                                echo $data->main->partners_name;
                                            }
                                        }else{
                                            echo "None";
                                        } 
                                    ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th class="row-header" scope="col">Address</th>
                            <td><label><?= $data->main->fat_addr ? $data->main->fat_addr : "None"  ?></label></td>
                            <td><label><?= $data->main->mot_addr ? $data->main->mot_addr : "None"  ?></label></td>
                            <td>
                                <label>
                                    <?php 
                                        if($data->main->partner_type == 1){
                                            echo $data->main->spo_addr;
                                        }else if($data->main->partner_type == 2){
                                            echo $data->main->partners_addr;
                                        }else{
                                            echo "None";
                                        } 
                                    ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th class="row-header" scope="col">Company</th>
                            <td><label><?= $data->main->fat_company ? $data->main->fat_company : "None" ?></label></td>
                            <td><label><?= $data->main->mot_company ? $data->main->mot_company : "None" ?></label></td>
                            <td>
                                <label>
                                    <?php 
                                        if($data->main->partner_type == 1){
                                            echo $data->main->spo_company;
                                        }else if($data->main->partner_type == 2){
                                            echo $data->main->partners_company;
                                        }else{
                                            echo "None";
                                        } 
                                    ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th class="row-header" scope="col">Occupation</th>
                            <td><label><?= $data->main->fat_occupation ? $data->main->fat_occupation : "None" ?></label></td>
                            <td><label><?= $data->main->mot_occupation ? $data->main->mot_occupation : "None" ?></label></td>
                            <td>
                                <label>
                                    <?php 
                                        if($data->main->partner_type == 1){
                                            echo $data->main->spo_occupation;
                                        }else if($data->main->partner_type == 2){
                                            echo $data->main->partners_occupation;
                                        }else{
                                            echo "None";
                                        } 
                                    ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th class="row-header" scope="col">Contact No.</th>
                            <td><label><?= $data->main->fat_contact ? $data->main->fat_contact : "None"  ?></label></td>
                            <td><label><?= $data->main->mot_contact ? $data->main->mot_contact : "None"  ?></label></td>
                            <td>
                                <label>
                                    <?php 
                                        if($data->main->partner_type == 1){
                                            echo $data->main->spo_contact;
                                        }else if($data->main->partner_type == 2){
                                            echo $data->main->partners_contact;
                                        }else{
                                            echo "None";
                                        } 
                                    ?>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="m--hidden-desktop">
                    <table class="table-group-header">
                        
                    </table>
                    <table class="responsive">
                        <thead class="customsalary">
                            <tr>
                                <th colspan="5">FATHER</th>
                            </tr>
                        </thead>
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Address</th>
                                <th scope="col">Company</th>
                                <th scope="col">Occupation</th>
                                <th scope="col">Contact No.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <?php
                                    if($data->main->fat_deceased){
                                        $deceased_fat = "<span class='m-badge m-badge--primary m-badge--wide'>Deceased</span>";
                                    }else{
                                        $deceased_fat = "";
                                    }
                                ?>

                                <td data-label="Name"><label><?= $data->main->fat_name ? $data->main->fat_name.$deceased_fat : "None" ?></label></td>
                                <td data-label="Address"><label><?= $data->main->fat_addr ? $data->main->fat_addr : "None" ?></label></td>
                                <td data-label="Company"><label><?= $data->main->fat_company ? $data->main->fat_company : "None" ?></label></td>
                                <td data-label="Occupation"><label><?= $data->main->fat_occupation ? $data->main->fat_occupation : "None" ?></label></td>
                                <td data-label="Contact No."><label><?= $data->main->fat_contact ? $data->main->fat_contact : "None" ?></label></td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- <table class="table-group-header">
                        
                    </table> -->
                    <table class="responsive">
                        <thead class="customsalary">
                            <tr>
                                <th colspan="5">MOTHER</th>
                            </tr>
                        </thead>
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Address</th>
                                <th scope="col">Company</th>
                                <th scope="col">Occupation</th>
                                <th scope="col">Contact No.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <?php
                                    if($data->main->mot_deceased){
                                        $deceased_mot = "<span class='m-badge m-badge--primary m-badge--wide'>Deceased</span>";
                                    }else{
                                        $deceased_mot = "";
                                    }
                                ?>
                                <td data-label="Name"><label><?= $data->main->mot_name ? $data->main->mot_name." ".$deceased_mot : "None" ?></label></td>
                                <td data-label="Address"><label><?= $data->main->mot_addr ? $data->main->mot_addr : "None" ?></label></td>
                                <td data-label="Company"><label><?= $data->main->mot_company ? $data->main->mot_company : "None" ?></label></td>
                                <td data-label="Occupation"><label><?= $data->main->mot_occupation ? $data->main->mot_occupation : "None" ?></label></td>
                                <td data-label="Contact No."><label><?= $data->main->mot_contact ? $data->main->mot_contact : "None" ?></label></td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- <table class="table-group-header">
                        
                    </table> -->
                    <table class="responsive">
                        <thead class="customsalary">
                            <tr>
                                <th colspan="5">SPOUSE/PARTNER</th>
                            </tr>
                        </thead>
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Address</th>
                                <th scope="col">Company</th>
                                <th scope="col">Occupation</th>
                                <th scope="col">Contact No.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td data-label="Name">
                                    <label>
                                        <?php 
                                            if($data->main->partner_type == 1){
                                                if($data->main->spo_deceased){
                                                    echo $data->main->spo_name." "."<span class='m-badge m-badge--primary m-badge--wide'>Deceased</span>";
                                                }else{
                                                    echo $data->main->spo_name;
                                                }
                                            }else if($data->main->partner_type == 2){
                                                if($data->main->partners_deceased){
                                                    echo $data->main->partners_name." "."<span class='m-badge m-badge--primary m-badge--wide'>Deceased</span>";
                                                }else{
                                                    echo $data->main->partners_name;
                                                }
                                            }else{
                                                echo "None";
                                            } 
                                        ?>
                                    </label>
                                </td>
                                <td data-label="Address">
                                    <label>
                                        <?php 
                                            if($data->main->partner_type == 1){
                                                echo $data->main->spo_addr;
                                            }else if($data->main->partner_type == 2){
                                                echo $data->main->partners_addr;
                                            }else{
                                                echo "None";
                                            } 
                                        ?>
                                    </label>
                                </td>
                                <td data-label="Company">
                                    <label>
                                        <?php 
                                            if($data->main->partner_type == 1){
                                                echo $data->main->spo_company;
                                            }else if($data->main->partner_type == 2){
                                                echo $data->main->partners_company;
                                            }else{
                                                echo "None";
                                            } 
                                        ?>
                                    </label>
                                </td>
                                <td data-label="Occupation">
                                    <label>
                                        <?php 
                                            if($data->main->partner_type == 1){
                                                echo $data->main->spo_occupation;
                                            }else if($data->main->partner_type == 2){
                                                echo $data->main->partners_occupation;
                                            }else{
                                                echo "None";
                                            } 
                                        ?>
                                    </label>
                                </td>
                                <td data-label="Contact No.">
                                    <label>
                                        <?php 
                                            if($data->main->partner_type == 1){
                                                echo $data->main->spo_contact;
                                            }else if($data->main->partner_type == 2){
                                                echo $data->main->partners_contact;
                                            }else{
                                                echo "None";
                                            } 
                                        ?>
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- START DEPENDENT TABLES -->
                <!-- <table class="table-group-header">
                
                </table> -->
                <table class="responsive">
                    <thead class="customsalary">
                    <tr>
                        <th scope="col" colspan="4">Dependents</th>
                    </tr>
                    </thead>
                    <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col" style="width: 8%;">Age</th>
                        <th scope="col" style="width: 13%">Date of Birth</th>
                        <th scope="col">Relationship</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php 
                    foreach ($data->dependents as $dependent) {
                        $currentDate = date_create(date("Y-m-d"));
                        $nextDate = date_create(date("Y-m-d", strtotime($dependent->dep_birthdate)));
                        $intervalDate = date_diff($currentDate, $nextDate);
                        
                        $tempAge = 0;
                        if($dependent->dep_birthdate == "0000-00-00"){
                            $tempAge = "---";
                        }else{
                            if ($intervalDate->y == 0 && $intervalDate->m == 0) {
                                $tempAge = $intervalDate->d;
                                $tempAge = $tempAge > 1 ? $tempAge . " Days Old" : " Day Old";
                            } else if ($intervalDate->y == 0) {
                                $tempAge = $intervalDate->m;
                                $tempAge = $tempAge > 1 ? $tempAge . " Months Old" : " Month Old";
                            } else {
                                $tempAge = $intervalDate->y;
                                $tempAge = $tempAge > 1 ? $tempAge . " Years Old" : "1 Year Old";
                            }
                        }
                        $dep_birthdate = $dependent->dep_birthdate;
                        if (!empty($dep_birthdate)) {
                            if($dep_birthdate == "0000-00-00"){
                                $dep_birthdate = "---";
                            }else{
                                $dep_birthdate = new DateTime($dep_birthdate);
                                $dep_birthdate = $dep_birthdate->format("M d, Y");
                            }
                        } else {
                            echo "None";
                        }

                        ?>
                        <tr>
                            <td data-label="Name"><label><?= $dependent->dep_name ?></label></td>
                            <td data-label="Age"><label><?= $tempAge ?></label></td>
                            <td data-label="Date of Birth">
                                <label><?php echo $dep_birthdate;?></label>
                            </td>
                            <td data-label="Relationship"><label><?= $dependent->dep_relation ?></label></td>
                        </tr>
                    <?php } ?>

                    <?php if (count($data->dependents) <= 0) { ?>
                        <tr>
                            <td data-label="Name">None</td>
                            <td data-label="Age">None</td>
                            <td data-label="Date of Birth">None</td>
                            <td data-label="Relationship">None</td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <!-- END DEPENDENT TABLES -->
                <!-- <table class="table-group-header">
                    
                </table> -->
                <table class="responsive">
                    <thead>
                    <tr>
                        <th scope="col" colspan="3">In Case Of Emergency Please Notify</th>
                    </tr>
                    </thead>
                    <thead>
                    <tr>
                        <th scope="col">Contact Person</th>
                        <th scope="col" style="width: 14%">Contact No.</th>
                        <th scope="col">Address</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td data-label="Contact Person"><label><?= $data->main->emer_name ? $data->main->emer_name : "None" ?></label></td>
                        <td data-label="Contact No."><label><?= $data->main->emer_contact ? $data->main->emer_contact : "None" ?></label></td>
                        <td data-label="Address"><label><?= $data->main->emer_addr ? $data->main->emer_addr : "None" ?></label></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="headingQuestion" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfo" href="#collapseQuestion" aria-expanded="false" aria-controls="collapseQuestion">
                        <h5 class="m-portlet__head-text">
                            <span>Employment Questions</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseQuestion" class="collapse" role="tabpanel" aria-labelledby="headingQuestion" data-parent="#accordionOtherAdditionalInfo">
            <div class="card-body m-portlet__body--custom table-responsive">
                <?php foreach ($data->questions as $key => $question) {
                    $answer = "ques" . ($question->a); ?>
                    <table class="responsive <?= ($key+1) < count($data->questions) ? 'mb-1' : '' ?>">
                        <thead>
                        <tr>
                            <th class="" scope="col">
                                <?= $question->q ?>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td data-label="<?= $question->q ?>" class="questions">
                                <label><?= $data->main->$answer ? $data->main->$answer : "N/A" ?></label>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="headingEducation" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfo" href="#collapseEducation" aria-expanded="false" aria-controls="collapseEducation">
                        <h5 class="m-portlet__head-text">
                            <span>Educational Background</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseEducation" class="collapse" role="tabpanel" aria-labelledby="headingEducation" data-parent="#accordionOtherAdditionalInfo">
            <div class="card-body m-portlet__body--custom table-responsive">
                <table class="responsive">
                    <thead>
                    <tr>
                        <th class="" scope="col" style="width: 12%">Level</th>
                        <th class="" scope="col" style="width: 35%">School</th>
                        <th class="" scope="col">Degree</th>
                        <th class="" scope="col" style="width: 15%">Honors</th>
                        <th class="" scope="col" style="width: 5%">From</th>
                        <th class="" scope="col" style="width: 5%">To</th>
                    </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($data->educations as $education) { ?>
                            <tr>
                                <td data-label="Level"><label><?= $education->educ_level_type ?></label></td>
                                <td data-label="School"><label><?= $education->educ_school ?></label></td>
                                <td data-label="Degree"><label><?= $education->educ_degree ?></label></td>
                                <td data-label="Honors"><label><?= $education->educ_honors ? $education->educ_honors: "N/A" ?></label></td>
                                <td data-label="From"><label><?= $education->educ_from ?></label></td>
                                <td data-label="To"><label><?= $education->educ_to ?></label></td>
                            </tr>
                        <?php } ?>

                        <?php if (count($data->educations) <= 0) { ?>
                            <tr>
                                <td data-label="Level">None</td>
                                <td data-label="School">None</td>
                                <td data-label="Degree">None</td>
                                <td data-label="Honors">None</td>
                                <td data-label="From">None</td>
                                <td data-label="To">None</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="headingLicense" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfo" href="#collapseLicense" aria-expanded="false" aria-controls="collapseLicense">
                        <h5 class="m-portlet__head-text">
                            <span>Licenses and Certificates</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseLicense" class="collapse" role="tabpanel" aria-labelledby="headingLicense" data-parent="#accordionOtherAdditionalInfo">
            <div class="card-body m-portlet__body--custom table-responsive">
                <table class="responsive">
                    <thead>
                    <tr>
                        <th class="" scope="col">LICENSE/EXAM TYPE</th>
                        <th class="" scope="col">EXAM PLACE</th>
                        <th class="" scope="col">RATING</th>
                        <th class="" scope="col" style="width: 13%">RELEASE DATE</th>
                        <th class="" scope="col" style="width: 13%">EXAM DATE</th>
                        <th class="" scope="col">LICENSE NO.</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php foreach ($data->licenses as $license) { ?>
                        <tr>
                            <td data-label="LICENSE/EXAM TYPE"><label><?= $license->license_type ?></label></td>
                            <td data-label="EXAM PLACE"><label><?= $license->exam_place ?></label></td>
                            <td data-label="RATING"><label><?= $license->rating ?></label></td>
                            <td data-label="RELEASE DATE"><label><?= $license->release_date ?></label></td>
                            <td data-label="EXAM DATE"><label><?= $license->exam_date ?></label></td>
                            <td data-label="LICENSE NO."><label><?= $license->license_no ?></label></td>
                        </tr>
                    <?php } ?>

                    <?php if (count($data->licenses) <= 0) { ?>
                        <tr>
                            <td data-label="LICENSE/EXAM TYPE">None</td>
                            <td data-label="EXAM PLACE">None</td>
                            <td data-label="RATING">None</td>
                            <td data-label="RELEASE DATE">None</td>
                            <td data-label="EXAM DATE">None</td>
                            <td data-label="LICENSE NO.">None</td>
                        </tr>
                    <?php } ?>
                    <!-- START DRIVER'S LICENSE ROW -->
                    <?php if($data->if_driver > 0 AND count($data->driverlicenses) > 0){ ?>
                    <thead>
                    <tr>
                        <th class="" scope="col" colspan="2">RESTRICTION</th>
                        <th class="" scope="col" colspan="2">LICENSE NO.</th>
                        <th class="" scope="col" colspan="2">EXPIRATION DATE</th>
                    </tr>
                    </thead>
                    <?php foreach ($data->driverlicenses as $driverlicense) {
                    $date_now = date("Y-m-d");
                        if($date_now > $driverlicense->expiration_date){
                            $expiration_date = "<span class='m-badge m-badge--danger m-badge--wide'>$driverlicense->expiration_date</span>";
                        }else{
                            $expiration_date = "<span class='m-badge m-badge--success m-badge--wide'>$driverlicense->expiration_date</span>";
                        }
                        
                    ?>
                        <tr>
                            <td data-label="RESTRICTION" colspan="2"><label><?= $driverlicense->restriction ?></label></td>
                            <td data-label="LICENSE NO." colspan="2"><label><?= $driverlicense->license_no ?></label></td>
                            <td data-label="EXPIRATION DATE" colspan="2"><label><?= $expiration_date ?></label></td>
                        </tr>
                    <?php } } ?>
                    <!-- END DRIVER'S LICENSE ROW -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="headingWork" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfo" href="#collapseWork" aria-expanded="false" aria-controls="collapseWork">
                        <h5 class="m-portlet__head-text">
                            <span>Work Experiences</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseWork" class="collapse" role="tabpanel" aria-labelledby="headingWork" data-parent="#accordionOtherAdditionalInfo">
            <div class="card-body m-portlet__body--custom table-responsive">
                <table class="responsive">
                    <thead>
                    <tr>
                        <th class="" scope="col" style="width: 25%">COMPANY</th>
                        <th class="" scope="col" style="width: 5%">FROM</th>
                        <th class="" scope="col" style="width: 5%">TO</th>
                        <th class="" scope="col" style="width: 25%">POSITION</th>
                        <th class="" scope="col" style="width: 8%">ID NO</th>
                        <th class="" scope="col" style="width: 11%">STATUS</th>
                        <th class="" scope="col" style="width: 13%">REASON FOR LEAVING</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data->experiences as $experience) { ?>
                            <tr>
                                <td data-label="COMPANY"><label><?= $experience->work_company ?></label></td>
                                <td data-label="FROM"><label><?= $experience->work_from ?></label></td>
                                <td data-label="TO"><label><?= $experience->work_to ?></label></td>
                                <td data-label="POSITION"><label><?= $experience->work_position ?></label></td>
                                <td data-label="ID NO"><label><?= empty($experience->old_idno) ? "N/A" : $experience->old_idno ?></label></td>
                                <td data-label="STATUS"><label><?= $experience->work_status ? $experience->work_status : "<br>" ?></label></td>
                                <td data-label="REASON FOR LEAVING"><label><?=  empty($experience->work_reason) ? "N/A" : $experience->work_reason ?></label></td>
                            </tr>
                        <?php } ?>

                        <?php if (count($data->experiences) <= 0) { ?>
                            <tr>
                                <td data-label="COMPANY">None</td>
                                <td data-label="FROM">None</td>
                                <td data-label="TO">None</td>
                                <td data-label="POSITION">None</td>
                                <td data-label="ID NO">None</td>
                                <td data-label="STATUS">None</td>
                                <td data-label="REASON FOR LEAVING">None</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="headingAward" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfo" href="#collapseAward" aria-expanded="false" aria-controls="collapseAward">
                        <h5 class="m-portlet__head-text">
                            <span>Awards and Achievements</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseAward" class="collapse" role="tabpanel" aria-labelledby="headingAward" data-parent="#accordionOtherAdditionalInfo">
            <div class="card-body m-portlet__body--custom table-responsive">
                <table class="responsive">
                    <thead>
                    <tr>
                        <th class="" scope="col">AWARD/ACHIEVEMENT</th>
                        <th class="" scope="col">INSTITUTION</th>
                        <th class="" scope="col" style="width: 15%">GIVEN DATE</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data->awards as $award) { ?>
                            <tr>
                                <td data-label="AWARD/ACHIEVEMENT"><label><?= $award->award ?></label></td>
                                <td data-label="INSTITUTION"><label><?= $award->award_institution ?></label></td>
                                <td data-label="GIVEN DATE"><label><?= $award->award_date ?></label></td>
                            </tr>
                        <?php } ?>

                        <?php if (count($data->awards) <= 0) { ?>
                            <tr>
                                <td data-label="AWARD/ACHIEVEMENT">None</td>
                                <td data-label="INSTITUTION">None</td>
                                <td data-label="GIVEN DATE">None</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="headingSkills" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfo" href="#collapseSkill" aria-expanded="false" aria-controls="collapseSkill">
                        <h5 class="m-portlet__head-text">
                            <span>Skills</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseSkill" class="collapse" role="tabpanel" aria-labelledby="headingSkills" data-parent="#accordionOtherAdditionalInfo">
            <div class="card-body m-portlet__body--custom table-responsive">
                <table class="responsive">
                    <thead>
                        <tr>
                            <th class="" scope="col"><label>TECHNICAL/ MANAGEMENT/ BUSINESS/ SPECIAL SKILLS</label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data->skills as $skill) { ?>
                            <tr>
                                <td data-label="TECHNICAL/MANAGEMENT/BUSINESS/SPECIAL SKILLS">
                                    <label><?= $skill->skills ?></label>
                                </td>
                            </tr>
                        <?php } ?>

                        <?php if (count($data->skills) <= 0) { ?>
                            <tr>
                                <td data-label="TECHNICAL/MANAGEMENT/BUSINESS/SPECIAL SKILLS">None</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="headingOrg" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfo" href="#collapseOrg" aria-expanded="false" aria-controls="collapseOrg">
                        <h5 class="m-portlet__head-text">
                            <span>Organizations</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseOrg" class="collapse" role="tabpanel" aria-labelledby="headingOrg" data-parent="#accordionOtherAdditionalInfo">
            <div class="card-body m-portlet__body--custom table-responsive">
                <table class="responsive">
                    <thead>
                        <tr>
                            <th class="" scope="col">INSTITUTION</th>
                            <th class="" scope="col">MEMBERSHIP TITLE</th>
                            <th class="" scope="col" style="width: 10%">FROM</th>
                            <th class="" scope="col" style="width: 10%">TO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data->organizations as $organization) { ?>
                            <tr>
                                <td data-label="INSTITUTION"><label><?= $organization->org_institution ?></label></td>
                                <td data-label="MEMBERSHIP TITLE"><label><?= $organization->org_membership_title ?></label></td>
                                <td data-label="FROM"><label><?= $organization->org_from ?></label></td>
                                <td data-label="TO"><label><?= $organization->org_to ?></label></td>
                            </tr>
                        <?php } ?>

                        <?php if (count($data->organizations) <= 0) { ?>
                            <tr>
                                <td data-label="INSTITUTION">None</td>
                                <td data-label="MEMBERSHIP TITLE">None</td>
                                <td data-label="FROM">None</td>
                                <td data-label="TO">None</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="headingTrain" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfo" href="#collapseTrain" aria-expanded="false" aria-controls="collapseTrain">
                        <h5 class="m-portlet__head-text">
                            <span>Training and Seminars</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseTrain" class="collapse" role="tabpanel" aria-labelledby="headingTrain" data-parent="#accordionOtherAdditionalInfo">
            <div class="card-body m-portlet__body--custom table-responsive">
                <table class="responsive">
                    <thead>
                        <tr>
                            <th class="" scope="col">TRAINING</th>
                            <th class="" scope="col" style="width: 10%">FROM</th>
                            <th class="" scope="col" style="width: 10%">TO</th>
                            <th class="" scope="col">INSTITUTION</th>
                            <th class="" scope="col">CONDUCTOR</th>
                            <th class="" scope="col">VENUE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data->trainings as $training) { ?>
                            <tr>
                                <td data-label="TRAINING"><label><?= $training->training ?></label></td>
                                <td data-label="FROM"><label><?= $training->train_from ?></label></td>
                                <td data-label="TO"><label><?= $training->train_to ?></label></td>
                                <td data-label="INSTITUTION"><label><?= $training->train_institution ?></label></td>
                                <td data-label="CONDUCTOR"><label><?= $training->train_conductor ?></label></td>
                                <td data-label="VENUE"><label><?= $training->train_venue ?></label></td>
                            </tr>
                        <?php } ?>

                        <?php if (count($data->trainings) <= 0) { ?>
                            <tr>
                                <td data-label="TRAINING">None</td>
                                <td data-label="FROM">None</td>
                                <td data-label="TO">None</td>
                                <td data-label="INSTITUTION">None</td>
                                <td data-label="CONDUCTOR">None</td>
                                <td data-label="VENUE">None</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="headingRef" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfo" href="#collapseRef" aria-expanded="false" aria-controls="collapseRef">
                        <h5 class="m-portlet__head-text">
                            <span>Personal References</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseRef" class="collapse" role="tabpanel" aria-labelledby="headingRef" data-parent="#accordionOtherAdditionalInfo">
            <div class="card-body m-portlet__body--custom table-responsive">
                <table class="responsive">
                    </thead>
                    <thead>
                        <tr>
                            <th class="" scope="col">NAME</th>
                            <th class="" scope="col" style="width: 15%;">CONTACT NO.</th>
                            <th class="" scope="col">ADDRESS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data->references as $reference) { ?>
                            <tr>
                                <td data-label="NAME"><label><?= $reference->ref_name ?></label></td>
                                <td data-label="CONTACT NO."><label><?= $reference->ref_contact_no ?></label></td>
                                <td data-label="ADDRESS"><label><?= $reference->ref_address ?></label></td>
                            </tr>
                        <?php } ?>

                        <?php if (count($data->references) <= 0) { ?>
                            <tr>
                                <td data-label="NAME">None</td>
                                <td data-label="CONTACT NO.">None</td>
                                <td data-label="ADDRESS">None</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="headingMed" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfo" href="#collapseMed" aria-expanded="false" aria-controls="collapseMed">
                        <h5 class="m-portlet__head-text">
                            <span>Medical History / Records</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseMed" class="collapse" role="tabpanel" aria-labelledby="headingMed" data-parent="#accordionOtherAdditionalInfo">
            <div class="card-body m-portlet__body--custom table-responsive">
                <table class="responsive">
                    <thead>
                        <tr>
                            <th class="" scope="col">DETAILS</th>
                            <th class="" scope="col">MED.NO.</th>
                            <th class="" scope="col" style="width: 13%">DATE</th>
                            <th class="" scope="col">VENUE</th>
                            <th class="" scope="col">PHYSICIAN</th>
                            <th class="" scope="col">FINDINGS</th>
                            <th class="" scope="col">REMARKS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data->medicals as $medical) { ?>
                            <tr>
                                <td data-label="DETAILS"><label><?= $medical->med_details ?></label></td>
                                <td data-label="MED.NO."><label><?= $medical->med_no ?></label></td>
                                <td data-label="DATE"><label><?= $medical->med_date ?></label></td>
                                <td data-label="VENUE"><label><?= $medical->med_venue ?></label></td>
                                <td data-label="PHYSICIAN"><label><?= $medical->med_physician ?></label></td>
                                <td data-label="FINDINGS"><label><?= $medical->med_findings ?></label></td>
                                <td data-label="REMARKS"><label><?= $medical->remarks ?></label></td>
                            </tr>
                        <?php } ?>

                        <?php if (count($data->medicals) <= 0) { ?>
                            <tr>
                                <td data-label="DETAILS">NONE</td>
                                <td data-label="MED.NO.">NONE</td>
                                <td data-label="DATE">NONE</td>
                                <td data-label="VENUE">NONE</td>
                                <td data-label="PHYSICIAN">NONE</td>
                                <td data-label="FINDINGS">NONE</td>
                                <td data-label="REMARKS">NONE</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="headingLegal" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfo" href="#collapseLegal" aria-expanded="false" aria-controls="collapseLegal">
                        <h5 class="m-portlet__head-text">
                            <span>Legal History / Records</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseLegal" class="collapse" role="tabpanel" aria-labelledby="headingLegal" data-parent="#accordionOtherAdditionalInfo">
            <div class="card-body m-portlet__body--custom table-responsive">
                <table class="responsive">
                    <thead>
                        <tr>
                            <th class="" scope="col">CASE NO.</th>
                            <th class="" scope="col">DETAILS</th>
                            <th class="" scope="col" style="width: 13%">DATE</th>
                            <th class="" scope="col">COURT FIELD</th>
                            <th class="" scope="col">PROSECUTOR</th>
                            <th class="" scope="col">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data->legals as $legal) { ?>
                            <tr>
                                <td data-label="CASE NO."><label><?= $legal->leg_case_no ? $legal->leg_case_no : "N/A" ?></label></td>
                                <td data-label="DETAILS"><label><?= $legal->leg_details ? $legal->leg_details : "N/A" ?></label></td>
                                <td data-label="DATE"><label><?= $legal->leg_case_date ? $legal->leg_case_date : "N/A" ?></label></td>
                                <td data-label="COURT FIELD"><label><?= $legal->leg_court_field ? $legal->leg_court_field : "N/A" ?></label></td>
                                <td data-label="PROSECUTOR"><label><?= $legal->leg_prosecutor ? $legal->leg_prosecutor : "N/A" ?></label></td>
                                <td data-label="STATUS"><label><?= $legal->leg_status ? $legal->leg_status : "N/A" ?></label></td>
                            </tr>
                        <?php } ?>

                        <?php if (count($data->legals) <= 0) { ?>
                            <tr>
                                <td data-label="CASE NO.">NONE</td>
                                <td data-label="DETAILS">NONE</td>
                                <td data-label="DATE">NONE</td>
                                <td data-label="COURT FIELD">NONE</td>
                                <td data-label="PROSECUTOR">NONE</td>
                                <td data-label="STATUS">NONE</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="headingAccountability" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfo" href="#collapseAccountability" aria-expanded="false" aria-controls="collapseAccountability">
                        <h5 class="m-portlet__head-text">
                            <span>Accountability</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseAccountability" class="collapse" role="tabpanel" aria-labelledby="headingAccountability" data-parent="#accordionOtherAdditionalInfo">
            <div class="card-body m-portlet__body--custom table-responsive">
                <table class="table table-bordered" id="accountability_table_mobile">
                    <thead>
                        <tr>
                            <th class="" scope="col" width="30%">STATUS</th>
                            <th class="" scope="col" width="70%">Information</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data->accountability as $acct): ?>
                            <tr>
                                <td style="font-size: .8em"><?= $acct->status ?></td>
                                <td style="font-size: .8em">
                                    <p style="margin-bottom: 0.3rem"><b>Ref. No:</b> <span><?= $acct->reference_no ?></span></p>
                                    <p style="margin-bottom: 0.3rem"><b>Asset Code:</b> <span><?= $acct->asset_code ?></span></p>
                                    <p style="margin-bottom: 0.3rem"><b>Asset Name:</b> <span><?= $acct->aname ?></span></p>
                                    <p style="margin-bottom: 0.3rem"><b>Amount:</b> <span><?= number_format($acct->amount, 2, ".", ",") ?></span></p>
                                    <p style="margin-bottom: 0.3rem">
                                        <b>Returned:</b>
                                        <?php
                                            if (intval($acct->is_returned) === 1) {
                                                echo "<span class='m-badge m-badge--success px-2 m--font-bolder'>Yes</span>";
                                            } else {
                                                echo "<span class='m-badge m-badge--danger px-2 m--font-bolder'>No</span>";
                                            }
                                        ?>
                                    </p>
                                    <p style="margin-bottom: 0.3rem"><b>Date:</b> <span><?= $acct->date_returned !== "0000-00-00" ? date("M j, Y", strtotime($acct->date_returned)) : "N/A" ?></span></p>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="headingEmployment" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfo" href="#collpaseEmployment" aria-expanded="false" aria-controls="collpaseEmployment">
                        <h5 class="m-portlet__head-text">
                            <span>Employment Information</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collpaseEmployment" class="collapse" role="tabpanel" aria-labelledby="headingEmployment" data-parent="#accordionOtherAdditionalInfo">
            <div class="card-body m-portlet__body--custom table-responsive">
                <table class="responsive">
                    <thead class="customsalary">
                    <tr>
                        <th scope="col" colspan="4">OFFENSE AND COMMENDATIONS</th>
                    </tr>
                    </thead>
                    <thead>
                    <tr>
                        <th class="" scope="col">TYPE</th>
                        <th class="" scope="col" style="width: 13%">DATE</th>
                        <th class="" scope="col">NATURE</th>
                        <th class="" scope="col">ACTION TAKEN</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data->offenses as $offense) { ?>
                        <tr>
                            <td data-label="TYPE"><label><?= $offense->offcom_type ?></label></td>
                            <td data-label="DATE"><label><?= $offense->offcom_date ?></label></td>
                            <td data-label="NATURE"><label><?= $offense->offcom_nature ?></label></td>
                            <td data-label="ACTION TAKEN"><label><?= $offense->offcom_action ?></label></td>
                        </tr>
                    <?php } ?>

                    <?php if (count($data->offenses) <= 0) { ?>
                        <tr>
                            <td data-label="TYPE">NONE</td>
                            <td data-label="DATE">NONE</td>
                            <td data-label="NATURE">NONE</td>
                            <td data-label="ACTION TAKEN">NONE</td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <!-- OFFENSES AND COMMENDATIONS -->

                <?php 
                    $actions = $this->core_layout->getCurrentActions();
                    $session_id = $this->core_layout->getCurrentEmployeeId();
                    if(in_array("view_own_request", $this->core_layout->getCurrentActions()) AND $data->main->id != $session_id){ 
                ?>
                <!-- START SALARY HISTORY -->

                <?php }elseif(in_array("view_own_request", $this->core_layout->getCurrentActions()) AND $data->main->id == $session_id){ ?>
                    <table class="responsive">
                        <thead class="customsalary">
                        <tr>
                            <th scope="col" colspan="4">SALARY HISTORY</th>
                        </tr>
                        </thead>
                        <thead>
                        <tr>
                            <th class="" scope="col" style="width: 13%">DATE</th>
                            <th class="" scope="col" style="width: 15%">RATE</th>
                            <th class="" scope="col">POSITION</th>
                            <th class="" scope="col">REMARKS</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php 
                        foreach ($data->salaries as $salaryIndex => $salary) { 
                            if($salary->sal_rate != ""){
                                $salary_rate = number_format(str_replace(',', '', $salary->sal_rate), 2, '.', ',');    
                            }else{
                                $salary_rate = $salary->sal_rate;
                            }
                            $grandTotal = floatval($main->basic_rate) + floatval($allowance);
                        ?>
                            <tr>
                                <td data-label="DATE"><?= $salary->sal_date ?></td>
                                <td data-label="RATE">
                                    <?= $salary_rate?>
                                    <?php 
                                        if($salary->sal_rate == $grandTotal && $salaryIndex == 0){
                                                echo "<p class='m-0'><small><span class='m-badge m-badge--success m-badge--wide'>Current</span></small></p>";
                                        }
                                    ?>
                                </td>
                                <td data-label="POSITION"><?= $salary->sal_position ? $salary->sal_position : "<br>" ?></td>
                                <td data-label="REMARKS"><?= $salary->sal_remarks ?></td>
                            </tr>
                        <?php } ?>

                        <?php if (count($data->salaries) <= 0) { ?>
                            <tr>
                                <td data-label="DATE">NONE</td>
                                <td data-label="RATE">NONE</td>
                                <td data-label="POSITION">NONE</td>
                                <td data-label="REMARKS">NONE</td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                <?php }else{ ?>
                    <table class="responsive">
                        <thead class="customsalary">
                        <tr>
                            <th scope="col" colspan="4">SALARY HISTORY</th>
                        </tr>
                        </thead>
                        <thead>
                        <tr>
                            <th class="" scope="col" style="width: 13%">DATE</th>
                            <th class="" scope="col" style="width: 15%">RATE</th>
                            <th class="" scope="col">POSITION</th>
                            <th class="" scope="col">REMARKS</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php 
                        foreach ($data->salaries as $salaryIndex => $salary) { 
                            if($salary->sal_rate != ""){
                                $salary_rate = number_format(str_replace(',', '', $salary->sal_rate), 2, '.', ',');    
                            }else{
                                $salary_rate = $salary->sal_rate;
                            }
                            $grandTotal = floatval($main->basic_rate) + floatval($allowance);
                        ?>
                            <tr>
                                <td data-label="DATE"><?= $salary->sal_date ?></td>
                                <td data-label="RATE">
                                    <?= $salary_rate?>
                                    <?php 
                                        if($salary->sal_rate == $grandTotal && $salaryIndex == 0){
                                                echo "<p class='m-0'><small><span class='m-badge m-badge--success m-badge--wide'>Current</span></small></p>";
                                        }
                                    ?>
                                </td>
                                <td data-label="POSITION"><?= $salary->sal_position ? $salary->sal_position : "<br>" ?></td>
                                <td data-label="REMARKS"><?= $salary->sal_remarks ?></td>
                            </tr>
                        <?php } ?>

                        <?php if (count($data->salaries) <= 0) { ?>
                            <tr>
                                <td data-label="DATE">NONE</td>
                                <td data-label="RATE">NONE</td>
                                <td data-label="POSITION">NONE</td>
                                <td data-label="REMARKS">NONE</td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>

                <!-- SALARY HISTORY -->

                <!-- START SALARY HISTORY -->
                
                <!-- SALARY HISTORY -->

                <!-- START EMPLOYEE INFORMATION -->
                <table class="responsive">
                    <thead class="customsalary">
                    <tr>
                        <th scope="col" colspan="6">EMPLOYEE INFORMATION</th>
                    </tr>
                    </thead>
                    <thead>
                    <tr>
                        <th class="" scope="col">DEPARTMENT</th>
                        <th class="" scope="col" style="width: 15%">WORK MODE</th>
                        <th class="" scope="col">PAYROLL TYPE</th>
                        <th class="" scope="col">LEVEL / RANKING</th>
                        <th class="" scope="col">COMPANY</th>
                        <th class="" scope="col">STATIONS</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td data-label="DEPARTMENT"><label><?= $data->main->department_description ? $data->main->department_description : "None" ?></label></td>
                        <td data-label="WORK MODE"><label><?= $data->main->work_mode ? $data->main->work_mode : "None" ?></label></td>
                        <td data-label="PAYROLL TYPE"><label><?= $data->main->payroll_type ? $data->main->payroll_type : "None" ?></label></td>
                        <td data-label="LEVEL / RANKING"><label><?= $data->main->level ? $data->main->level : "None" ?></label></td>
                        <td data-label="COMPANY"><label><?= $data->main->company_id ? $data->main->company_id : "None"?></label></td>
                        <td data-label="STATIONS"><ul>
                        <?php if(count($data->station) != 0){
                            foreach($data->station as $sites){ ?>
                                <li><?php echo (isset($sites->location_name) && $sites->location_name) ? $sites->location_name : "N/A"; ?></li>
                        <?php
                            }
                        }
                        ?>
                        </ul></td>
                    </tr>
                    </tbody>
                </table>
                <!-- EMPLOYEE INFORMATION -->

                <!-- START WORK STATUS -->
                <table class="responsive">
                    <thead class="customsalary">
                    <tr>
                        <th scope="col" colspan="4">WORK STATUS</th>
                    </tr>
                    </thead>
                    <thead>
                    <tr>
                        <th class="" scope="col" style="width: 13%">DATE REGULARIZED</th>
                        <th class="" scope="col" style="width: 13%">PROBEE END DATE</th>
                        <th class="" scope="col" style="width: 13%">DATE SEPARATED</th>
                        <th class="text-center" scope="col" style="width: 20%">REASON FOR SEPARATION</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td data-label="DATE REGULARIZED"><label><?= ($data->main->date_regular == "0000-00-00" OR $data->main->date_regular == NULL) ? "N/A" : $data->main->date_regular ?></label></td>
                        <td data-label="PROBEE END DATE">
                            <label>
                                <?php
                                    echo $data->main->date_end_prob;

                                    /** commented out for showing of auto generate probe end date */
                                    // if($data->main->work_status == "PROBATIONARY"){
                                    //     echo "N/A";
                                    // }else{
                                    //     echo $data->main->date_end_prob;
                                    // }  
                                    /** commented out for showing of auto generate probe end date */
                                //($data->main->date_end_prob == "0000-00-00" OR $data->main->date_end_prob == NULL) ? "N/A" : $data->main->date_end_prob?>
                            </label>
                        </td>
                        <td data-label="DATE SEPARATED"><label><?= ($data->main->date_end == "0000-00-00" OR $data->main->date_end == NULL) ? "N/A" : $data->main->date_end ?></label></td>
                        <td data-label="REASON FOR SEPARATION"><label><?= $data->main->resign_reason ? $data->main->resign_reason : "N/A" ?></label></td>
                    </tr>
                    </tbody>
                </table>
                <!-- WORK STATUS -->

                <table class="responsive">
                    <thead class="customsalary">
                    <tr>
                        <th scope="col" colspan="4">JOB DETAIL</th>
                    </tr>
                    </thead>
                    <thead>
                    <tr>
                        <th class="" scope="col">POSITION</th>
                        <th class="" scope="col">TYPE</th>
                        <th class="" scope="col">DEPARTMENT</th>
                        <th class="" scope="col">COMPANY</th>
                    </tr>
                    </thead>
                    <tbody>

                    <tr>
                        <td data-label="POSITION"><label><?= $data->main->position ? $data->main->position : "N/A" ?></label></td>
                        <td data-label="TYPE"><label><?= $data->main->level ? $data->main->level : "N/A" ?></label></td>
                        <td data-label="DEPARTMENT"><label><?= $data->main->department_description ? $data->main->department_description : "N/A" ?></label></td>
                        <td data-label="COMPANY"><label><?= $data->main->company_id ? $data->main->company_id : "N/A" ?></label></td>
                    </tr>
                    </tbody>
                </table>
                <!-- JOB DETAIL -->
            </div>
        </div>
    </div>
    <div class="card">
        <div id="headingJob" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfo" href="#collapseJob" aria-expanded="false" aria-controls="collapseJob">
                        <h5 class="m-portlet__head-text">
                            <span>Job Description</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseJob" class="collapse" role="tabpanel" aria-labelledby="headingJob" data-parent="#accordionOtherAdditionalInfo">
            <div class="card-body m-portlet__body--custom table-responsive">
                <table class="responsive">
                    <tbody>
                        <tr>
                        <?php
                            $dom = new DOMDocument;
                            @$dom->loadHTML($data->main->job_desc);
                            $allElements = $dom->getElementsByTagName("li");

                            $count = $allElements->length;
                            if($count > 0){
                                $display = $data->main->job_desc;
                            }else{
                                $display = nl2br($data->main->job_desc);
                            }
                        ?>
                            <td id="job_desc" class="text-left">
                            <?php if($display != NULL OR $display != "NONE"){?>
                                <label><?= $display ?></label>
                            <?php } ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $("#accountability_table_mobile").dataTable({
        pageLength : 5,
        bLengthChange : false
    });

    if(screen.width <= 450){
        $("#accountability_table #returned").removeClass("text-center");
      
    }
    $(window).resize(function(){
        if(screen.width <= 450){
            $("#accountability_table #returned").removeClass("text-center");
         
            
        }
    });
    

});

</script>