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
  }
</style>

<div id="accordionOtherAdditionalInfoWeb" class="accordion mb-5" role="tablist" aria-multiselectable="true">
    <div class="card">
        <div id="headingPersonalWeb" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfoWeb" href="#collapsePersonalWeb" aria-expanded="false" aria-controls="collapsePersonalWeb">
                        <h5 class="m-portlet__head-text">
                            <span>Personal Information</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapsePersonalWeb" class="collapse show" role="tabpanel" aria-labelledby="headingPersonalWeb" data-parent="#accordionOtherAdditionalInfoWeb">
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
                        <td data-label="First Name"> <?= $data->main->firstname ?> </td>
                        <td data-label="Middle Name"> <?= $data->main->middlename ? $data->main->middlename : " --- " ?> </td>
                        <td data-label="Last Name"> <?= $data->main->lastname ?> </td>
                        <td data-label="Suffix"> <?= $data->main->suffix ? $data->main->suffix : "&nbsp;" ?> </td>
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
                        <td data-label="Current Address"> <?= $data->main->curr_addr ? $data->main->curr_addr : "None"  ?> </td>
                        <td data-label="Provincial Address"> <?= $data->main->prov_addr ? $data->main->prov_addr : "None"  ?> </td>
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
                        <td data-label="Citizenship"> <?= $data->main->citizenship ? $data->main->citizenship : "None" ?> </td>
                        <td data-label="Religion"> <?= $data->main->religion ? $data->main->religion : "None" ?> </td>
                        <td data-label="Languages"> <?= $data->main->languages ? $data->main->languages : "None" ?> </td>
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
                        <td data-label="Gender"> <?= $data->main->gender ? $data->main->gender : "N/A"  ?> </td>
                        <td data-label="Civil Status"> <?= $data->main->civil_stat ? $data->main->civil_stat : "N/A" ?> </td>
                        <td data-label="Date of Birth">
                            <?php
                                $bday = new DateTime($data->main->bday);
                                echo $bday->format("M d, Y");
                            ?>
                        </td>
                        <td data-label="Place of Birth"> <?= $data->main->birthplace ? $data->main->birthplace : "None"  ?> </td>
                        <td data-label="Blood Type"> <?= $data->main->bloodtype ? $data->main->bloodtype : "None" ?> </td>
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
                        <td data-label="Height"><?= $data->main->height ? $data->main->height : "N/A" ?></td>
                        <td data-label="Weight"><?= $data->main->weight ? $data->main->weight : "N/A" ?></td>
                        <td data-label="Hair Color"><?= $data->main->hair_color ? $data->main->hair_color : "N/A" ?></td>
                        <td data-label="Complexion"><?= $data->main->complexion ? $data->main->complexion : "N/A" ?></td>
                        <td data-label="Tel. No."><?= $data->main->tel_no ? $data->main->tel_no : "N/A" ?></td>
                        <td data-label="Mobile No."><?= $data->main->mobile_no ? $data->main->mobile_no : "N/A" ?></td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="headingAdditionalWeb" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfoWeb" href="#collapseAdditionalWeb" aria-expanded="false" aria-controls="collapseAdditionalWeb">
                        <h5 class="m-portlet__head-text">
                            <span>Additional Information</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseAdditionalWeb" class="collapse" role="tabpanel" aria-labelledby="headingAdditionalWeb" data-parent="#accordionOtherAdditionalInfoWeb">
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
                            <td data-label="Email" class="email_container"><?= $data->main->email ? $data->main->email : "NONE" ?></td>
                            <td data-label="Tax Status"><?= $data->main->tax_status ? $data->main->tax_status : "NONE" ?></td>
                            <td data-label="Tin No."><?= $data->main->tin_no ? $data->main->tin_no : "NONE" ?></td>
                            <td data-label="Philhealth No."><?= $data->main->phealth_no ? $data->main->phealth_no : "NONE" ?></td>
                            <td data-label="Pag-ibig No."><?= $data->main->pagibig_no ? $data->main->pagibig_no : "NONE" ?></td>
                            <td data-label="SSS No."><?= $data->main->sss_no ? $data->main->sss_no : "NONE" ?></td>
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
                            <td><?= $data->main->fat_name ? $data->main->fat_name." ".$deceased_fat : "None" ?></td>
                            <td><?= $data->main->mot_name ? $data->main->mot_name." ".$deceased_mot : "None" ?></td>
                            <td>
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
                            </td>
                        </tr>
                        <tr>
                            <th class="row-header" scope="col">Address</th>
                            <td><?= $data->main->fat_addr ? $data->main->fat_addr : "None"  ?></td>
                            <td><?= $data->main->mot_addr ? $data->main->mot_addr : "None"  ?></td>
                            <td>
                                <?php if($data->main->partner_type == 1){
                                    echo $data->main->spo_addr;
                                }else if($data->main->partner_type == 2){
                                    echo $data->main->partners_addr;
                                }else{
                                    echo "None";
                                } ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="row-header" scope="col">Company</th>
                            <td><?= $data->main->fat_company ? $data->main->fat_company : "None" ?></td>
                            <td><?= $data->main->mot_company ? $data->main->mot_company : "None" ?></td>
                            <td>
                                <?php if($data->main->partner_type == 1){
                                    echo $data->main->spo_company;
                                }else if($data->main->partner_type == 2){
                                    echo $data->main->partners_company;
                                }else{
                                    echo "None";
                                } ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="row-header" scope="col">Occupation</th>
                            <td><?= $data->main->fat_occupation ? $data->main->fat_occupation : "None" ?></td>
                            <td><?= $data->main->mot_occupation ? $data->main->mot_occupation : "None" ?></td>
                            <td>
                                <?php if($data->main->partner_type == 1){
                                    echo $data->main->spo_occupation;
                                }else if($data->main->partner_type == 2){
                                    echo $data->main->partners_occupation;
                                }else{
                                    echo "None";
                                } ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="row-header" scope="col">Contact No.</th>
                            <td><?= $data->main->fat_contact ? $data->main->fat_contact : "None"  ?></td>
                            <td><?= $data->main->mot_contact ? $data->main->mot_contact : "None"  ?></td>
                            <td>
                                <?php if($data->main->partner_type == 1){
                                    echo $data->main->spo_contact;
                                }else if($data->main->partner_type == 2){
                                    echo $data->main->partners_contact;
                                }else{
                                    echo "None";
                                } ?>
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

                                <td data-label="Name"><?= $data->main->fat_name ? $data->main->fat_name.$deceased_fat : "None" ?></td>
                                <td data-label="Address"><?= $data->main->fat_addr ? $data->main->fat_addr : "None" ?></td>
                                <td data-label="Company"><?= $data->main->fat_company ? $data->main->fat_company : "None" ?></td>
                                <td data-label="Occupation"><?= $data->main->fat_occupation ? $data->main->fat_occupation : "None" ?></td>
                                <td data-label="Contact No."><?= $data->main->fat_contact ? $data->main->fat_contact : "None" ?></td>
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
                                <td data-label="Name"><?= $data->main->mot_name ? $data->main->mot_name." ".$deceased_mot : "None" ?></td>
                                <td data-label="Address"><?= $data->main->mot_addr ? $data->main->mot_addr : "None" ?></td>
                                <td data-label="Company"><?= $data->main->mot_company ? $data->main->mot_company : "None" ?></td>
                                <td data-label="Occupation"><?= $data->main->mot_occupation ? $data->main->mot_occupation : "None" ?></td>
                                <td data-label="Contact No."><?= $data->main->mot_contact ? $data->main->mot_contact : "None" ?></td>
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
                                </td>
                                <td data-label="Address">
                                    <?php if($data->main->partner_type == 1){
                                        echo $data->main->spo_addr;
                                    }else if($data->main->partner_type == 2){
                                        echo $data->main->partners_addr;
                                    }else{
                                        echo "None";
                                    } ?>
                                </td>
                                <td data-label="Company">
                                    <?php if($data->main->partner_type == 1){
                                        echo $data->main->spo_company;
                                    }else if($data->main->partner_type == 2){
                                        echo $data->main->partners_company;
                                    }else{
                                        echo "None";
                                    } ?>
                                </td>
                                <td data-label="Occupation">
                                    <?php if($data->main->partner_type == 1){
                                        echo $data->main->spo_occupation;
                                    }else if($data->main->partner_type == 2){
                                        echo $data->main->partners_occupation;
                                    }else{
                                        echo "None";
                                    } ?>
                                </td>
                                <td data-label="Contact No.">
                                    <?php if($data->main->partner_type == 1){
                                        echo $data->main->spo_contact;
                                    }else if($data->main->partner_type == 2){
                                        echo $data->main->partners_contact;
                                    }else{
                                        echo "None";
                                    } ?>
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
                            <td data-label="Name"><?= $dependent->dep_name ?></td>
                            <td data-label="Age"><?= $tempAge ?></td>
                            <td data-label="Date of Birth">
                                <?php echo $dep_birthdate;?>
                            </td>
                            <td data-label="Relationship"><?= $dependent->dep_relation ?></td>
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
                        <td data-label="Contact Person"><?= $data->main->emer_name ? $data->main->emer_name : "None" ?></td>
                        <td data-label="Contact No."><?= $data->main->emer_contact ? $data->main->emer_contact : "None" ?></td>
                        <td data-label="Address"><?= $data->main->emer_addr ? $data->main->emer_addr : "None" ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="headingQuestionWeb" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfoWeb" href="#collapseQuestionWeb" aria-expanded="false" aria-controls="collapseQuestionWeb">
                        <h5 class="m-portlet__head-text">
                            <span>Employment Questions</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseQuestionWeb" class="collapse" role="tabpanel" aria-labelledby="headingQuestionWeb" data-parent="#accordionOtherAdditionalInfoWeb">
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
        <div id="headingEducationWeb" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfoWeb" href="#collapseEducationWeb" aria-expanded="false" aria-controls="collapseEducationWeb">
                        <h5 class="m-portlet__head-text">
                            <span>Educational Background</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseEducationWeb" class="collapse" role="tabpanel" aria-labelledby="headingEducationWeb" data-parent="#accordionOtherAdditionalInfoWeb">
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
                                <td data-label="Level"><?= $education->educ_level_type ?></td>
                                <td data-label="School"><?= $education->educ_school ?></td>
                                <td data-label="Degree"><?= $education->educ_degree ?></td>
                                <td data-label="Honors"><?= $education->educ_honors ? $education->educ_honors: "N/A" ?></td>
                                <td data-label="From"><?= $education->educ_from ?></td>
                                <td data-label="To"><?= $education->educ_to ?></td>
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
        <div id="headingLicenseWeb" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfoWeb" href="#collapseLicenseWeb" aria-expanded="false" aria-controls="collapseLicenseWeb">
                        <h5 class="m-portlet__head-text">
                            <span>Licenses and Certificates</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseLicenseWeb" class="collapse" role="tabpanel" aria-labelledby="headingLicenseWeb" data-parent="#accordionOtherAdditionalInfoWeb">
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
                            <td data-label="LICENSE/EXAM TYPE"><?= $license->license_type ?></td>
                            <td data-label="EXAM PLACE"><?= $license->exam_place ?></td>
                            <td data-label="RATING"><?= $license->rating ?></td>
                            <td data-label="RELEASE DATE"><?= $license->release_date ?></td>
                            <td data-label="EXAM DATE"><?= $license->exam_date ?></td>
                            <td data-label="LICENSE NO."><?= $license->license_no ?></td>
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
                            <td data-label="RESTRICTION" colspan="2"><?= $driverlicense->restriction ?></td>
                            <td data-label="LICENSE NO." colspan="2"><?= $driverlicense->license_no ?></td>
                            <td data-label="EXPIRATION DATE" colspan="2"><?= $expiration_date ?></td>
                        </tr>
                    <?php } } ?>
                    <!-- END DRIVER'S LICENSE ROW -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="headingWorkWeb" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfoWeb" href="#collapseWorkWeb" aria-expanded="false" aria-controls="collapseWorkWeb">
                        <h5 class="m-portlet__head-text">
                            <span>Work Experiences</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseWorkWeb" class="collapse" role="tabpanel" aria-labelledby="headingWorkWeb" data-parent="#accordionOtherAdditionalInfoWeb">
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
                                <td data-label="COMPANY"><?= $experience->work_company ?></td>
                                <td data-label="FROM"><?= $experience->work_from ?></td>
                                <td data-label="TO"><?= $experience->work_to ?></td>
                                <td data-label="POSITION"><?= $experience->work_position ?></td>
                                <td data-label="ID NO"><?= empty($experience->old_idno) ? "N/A" : $experience->old_idno ?></td>
                                <td data-label="STATUS"><?= $experience->work_status ? $experience->work_status : "<br>" ?></td>
                                <td data-label="REASON FOR LEAVING"><?=  empty($experience->work_reason) ? "N/A" : $experience->work_reason ?></td>
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
        <div id="headingAwardWeb" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfoWeb" href="#collapseAwardWeb" aria-expanded="false" aria-controls="collapseAwardWeb">
                        <h5 class="m-portlet__head-text">
                            <span>Awards and Achievements</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseAwardWeb" class="collapse" role="tabpanel" aria-labelledby="headingAwardWeb" data-parent="#accordionOtherAdditionalInfoWeb">
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
                                <td data-label="AWARD/ACHIEVEMENT"><?= $award->award ?></td>
                                <td data-label="INSTITUTION"><?= $award->award_institution ?></td>
                                <td data-label="GIVEN DATE"><?= $award->award_date ?></td>
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
        <div id="headingSkillsWeb" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfoWeb" href="#collapseSkillWeb" aria-expanded="false" aria-controls="collapseSkillWeb">
                        <h5 class="m-portlet__head-text">
                            <span>Skills</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseSkillWeb" class="collapse" role="tabpanel" aria-labelledby="headingSkillsWeb" data-parent="#accordionOtherAdditionalInfoWeb">
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
        <div id="headingOrgWeb" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfoWeb" href="#collapseOrgWeb" aria-expanded="false" aria-controls="collapseOrgWeb">
                        <h5 class="m-portlet__head-text">
                            <span>Organizations</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseOrgWeb" class="collapse" role="tabpanel" aria-labelledby="headingOrgWeb" data-parent="#accordionOtherAdditionalInfoWeb">
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
                                <td data-label="INSTITUTION"><?= $organization->org_institution ?></td>
                                <td data-label="MEMBERSHIP TITLE"><?= $organization->org_membership_title ?></td>
                                <td data-label="FROM"><?= $organization->org_from ?></td>
                                <td data-label="TO"><?= $organization->org_to ?></td>
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
        <div id="headingTrainWeb" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfoWeb" href="#collapseTrainWeb" aria-expanded="false" aria-controls="collapseTrainWeb">
                        <h5 class="m-portlet__head-text">
                            <span>Training and Seminars</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseTrainWeb" class="collapse" role="tabpanel" aria-labelledby="headingTrainWeb" data-parent="#accordionOtherAdditionalInfoWeb">
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
                                <td data-label="TRAINING"><?= $training->training ?></td>
                                <td data-label="FROM"><?= $training->train_from ?></td>
                                <td data-label="TO"><?= $training->train_to ?></td>
                                <td data-label="INSTITUTION"><?= $training->train_institution ?></td>
                                <td data-label="CONDUCTOR"><?= $training->train_conductor ?></td>
                                <td data-label="VENUE"><?= $training->train_venue ?></td>
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
        <div id="headingRefWeb" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfoWeb" href="#collapseRefWeb" aria-expanded="false" aria-controls="collapseRefWeb">
                        <h5 class="m-portlet__head-text">
                            <span>Personal References</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseRefWeb" class="collapse" role="tabpanel" aria-labelledby="headingRefWeb" data-parent="#accordionOtherAdditionalInfoWeb">
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
                                <td data-label="NAME"><?= $reference->ref_name ?></td>
                                <td data-label="CONTACT NO."><?= $reference->ref_contact_no ?></td>
                                <td data-label="ADDRESS"><?= $reference->ref_address ?></td>
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
        <div id="headingMedWeb" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfoWeb" href="#collapseMedWeb" aria-expanded="false" aria-controls="collapseMedWeb">
                        <h5 class="m-portlet__head-text">
                            <span>Medical History / Records</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseMedWeb" class="collapse" role="tabpanel" aria-labelledby="headingMedWeb" data-parent="#accordionOtherAdditionalInfoWeb">
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
                                <td data-label="DETAILS"><?= $medical->med_details ?></td>
                                <td data-label="MED.NO."><?= $medical->med_no ?></td>
                                <td data-label="DATE"><?= $medical->med_date ?></td>
                                <td data-label="VENUE"><?= $medical->med_venue ?></td>
                                <td data-label="PHYSICIAN"><?= $medical->med_physician ?></td>
                                <td data-label="FINDINGS"><?= $medical->med_findings ?></td>
                                <td data-label="REMARKS"><?= $medical->remarks ?></td>
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
        <div id="headingLegalWeb" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfoWeb" href="#collapseLegalWeb" aria-expanded="false" aria-controls="collapseLegalWeb">
                        <h5 class="m-portlet__head-text">
                            <span>Legal History / Records</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseLegalWeb" class="collapse" role="tabpanel" aria-labelledby="headingLegalWeb" data-parent="#accordionOtherAdditionalInfoWeb">
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
                                <td data-label="CASE NO."><?= $legal->leg_case_no ? $legal->leg_case_no : "N/A" ?></td>
                                <td data-label="DETAILS"><?= $legal->leg_details ? $legal->leg_details : "N/A" ?></td>
                                <td data-label="DATE"><?= $legal->leg_case_date ? $legal->leg_case_date : "N/A" ?></td>
                                <td data-label="COURT FIELD"><?= $legal->leg_court_field ? $legal->leg_court_field : "N/A" ?></td>
                                <td data-label="PROSECUTOR"><?= $legal->leg_prosecutor ? $legal->leg_prosecutor : "N/A" ?></td>
                                <td data-label="STATUS"><?= $legal->leg_status ? $legal->leg_status : "N/A" ?></td>
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
        <div id="headingAccountabilityWeb" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfoWeb" href="#collapseAccountabilityWeb" aria-expanded="false" aria-controls="collapseAccountabilityWeb">
                        <h5 class="m-portlet__head-text">
                            <span>Accountability</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseAccountabilityWeb" class="collapse" role="tabpanel" aria-labelledby="headingAccountabilityWeb" data-parent="#accordionOtherAdditionalInfoWeb">
            <div class="card-body m-portlet__body--custom table-responsive">
                <table class="responsive" id="accountability_table">
                    <thead class="customsalary">
                    <tr>
                        <th scope="col" colspan="8">ACCOUNTABILITY</th>
                    </tr>
                    </thead>
                    <thead>
                    <tr>
                        <th class="" scope="col" width="12%">STATUS</th>
                        <th class="" scope="col" width="12%">REF. NO</th>
                        <th class="" scope="col" width="12%">ASSET CODE</th>
                        <th class="" scope="col">ASSET NAME</th>
                        <th class="text-right" scope="col" width="10%">AMOUNT</th>
                        <th class="text-center" scope="col" width="10%">RETURNED</th>
                        <th class="text-center" scope="col" width="13%">REMARKS</th>
                        <th class="" scope="col" width="13%">DATE</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data->accountability as $acct) { ?>
                        <tr>
                            <td data-label="STATUS"><?= $acct->status ?></td>
                            <td data-label="REF. NO"><?= $acct->reference_no ?></td>
                            <td data-label="ASSET CODE"><?= $acct->asset_code ?></td>
                            <td data-label="ASSET NAME"><?= $acct->aname ?></td>
                            <td class="text-right" data-label="AMOUNT"><?= number_format($acct->amount, 2, ".", ",") ?></td>
                            <td data-label="RETURNED" class="text-center" id="returned">
                                <?php
                                    if (intval($acct->is_returned) === 1) {
                                        echo "<span class='m-badge m-badge--success px-2 m--font-bolder'>Yes</span>";
                                    } else {
                                        echo "<span class='m-badge m-badge--danger px-2 m--font-bolder'>No</span>";
                                    }
                                ?>
                            </td>
                            <td  class="text-center" data-label="REMARKS">
                                <?php if (!empty($acct->remarks_returned)):?>
                                    <a href="javascript:void(0)" onclick=" showRemarks('<?= htmlspecialchars($acct->remarks_returned)?>')">View Remarks</a>
                                <?php else:?>
                                    NO REMARKS
                                <?php endif;?>
                            </td>
                            <td data-label="DATE"><?= $acct->date_returned !== "0000-00-00" ? date("M j, Y", strtotime($acct->date_returned)) : "N/A" ?></td>
                        </tr>
                    <?php } ?>

                    <?php if (count($data->accountability) <= 0) { ?>
                        <tr>
                            <td data-label="STATUS">NONE</td>
                            <td data-label="REF. NO">NONE</td>
                            <td data-label="ASSET CODE">NONE</td>
                            <td data-label="ASSET NAME">NONE</td>
                            <td data-label="AMOUNT">NONE</td>
                            <td data-label="RETURNED">NONE</td>
                            <td data-label="REMARKS">NONE</td>
                            <td data-label="DATE">NONE</td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="headingEmploymentWeb" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfoWeb" href="#collpaseEmploymentWeb" aria-expanded="false" aria-controls="collpaseEmploymentWeb">
                        <h5 class="m-portlet__head-text">
                            <span>Employment Information</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collpaseEmploymentWeb" class="collapse" role="tabpanel" aria-labelledby="headingEmploymentWeb" data-parent="#accordionOtherAdditionalInfoWeb">
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
                            <td data-label="TYPE"><?= $offense->offcom_type ?></td>
                            <td data-label="DATE"><?= $offense->offcom_date ?></td>
                            <td data-label="NATURE"><?= $offense->offcom_nature ?></td>
                            <td data-label="ACTION TAKEN"><?= $offense->offcom_action ?></td>
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
                        <th scope="col" colspan="5">EMPLOYEE INFORMATION</th>
                    </tr>
                    </thead>
                    <thead>
                    <tr>
                        <th class="" scope="col">DEPARTMENT</th>
                        <th class="" scope="col" style="width: 15%">WORK MODE</th>
                        <th class="" scope="col">PAYROLL TYPE</th>
                        <th class="" scope="col">LEVEL / RANKING</th>
                        <th class="" scope="col">COMPANY</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td data-label="DEPARTMENT"><?= $data->main->department_description ? $data->main->department_description : "None" ?></td>
                        <td data-label="WORK MODE"><?= $data->main->work_mode ? $data->main->work_mode : "None" ?></td>
                        <td data-label="PAYROLL TYPE"><?= $data->main->payroll_type ? $data->main->payroll_type : "None" ?></td>
                        <td data-label="LEVEL / RANKING"><?= $data->main->level ? $data->main->level : "None" ?></td>
                        <td data-label="COMPANY"><?= $data->main->company_id ? $data->main->company_id : "None"?></td>
                    </tr>
                    </tbody>
                </table>

                <table class="responsive">
                    <col width="30%">
                    <col width="*">
                    <thead class="customsalary">
                        <tr>
                            <th class="" scope="col">CURRENT STATION / LOCATION</th>
                            <th class="" scope="col">STATIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="CURRENT STATION / LOCATION" style="vertical-align: top">
                                <?php echo isset($data->default_station->description) && $data->default_station->description ? $data->default_station->description: ""; ?>
                            </td>
                            <td data-label="STATIONS">
                                <ul class="row"><?php if(count($data->station) > 0){
                                    foreach($data->station as $sites){ ?>
                                    <li class="col-4"><?php echo (isset($sites->location_name) && $sites->location_name) ? $sites->location_name : "N/A"; ?></li>
                                <?php } } ?>
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
                        <td data-label="DATE REGULARIZED"><?= ($data->main->date_regular == "0000-00-00" OR $data->main->date_regular == NULL) ? "N/A" : $data->main->date_regular ?></td>
                        <td data-label="PROBEE END DATE">
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
                        </td>
                        <td data-label="DATE SEPARATED"><?= ($data->main->date_end == "0000-00-00" OR $data->main->date_end == NULL) ? "N/A" : $data->main->date_end ?></td>
                        <td data-label="REASON FOR SEPARATION"><?= $data->main->resign_reason ? $data->main->resign_reason : "N/A" ?></td>
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
                        <td data-label="POSITION"><?= $data->main->position ? $data->main->position : "N/A" ?></td>
                        <td data-label="TYPE"><?= $data->main->level ? $data->main->level : "N/A" ?></td>
                        <td data-label="DEPARTMENT"><?= $data->main->department_description ? $data->main->department_description : "N/A" ?></td>
                        <td data-label="COMPANY"><?= $data->main->company_id ? $data->main->company_id : "N/A" ?></td>
                    </tr>
                    </tbody>
                </table>
                <!-- JOB DETAIL -->
            </div>
        </div>
    </div>
    <div class="card">
        <div id="headingJobWeb" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfoWeb" href="#collapseJobWeb" aria-expanded="false" aria-controls="collapseJobWeb">
                        <h5 class="m-portlet__head-text">
                            <span>Job Description</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseJobWeb" class="collapse" role="tabpanel" aria-labelledby="headingJobWeb" data-parent="#accordionOtherAdditionalInfoWeb">
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

<div class="modal fade" id="remarksModal" tabindex="-1" role="dialog" aria-labelledby="remarksModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="remarksModalLabel">Remarks</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="remarksText" class="text-center"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>