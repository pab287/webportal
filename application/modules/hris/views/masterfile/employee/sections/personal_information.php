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
</style>
<table class="responsive">
    <thead class="customsalary pihid">
      <tr>
          <th scope="col" colspan="6">Personal Information</th>
      </tr>
    </thead>
    <!--table table-bordered m-table m-table--border-brand m-table--head-bg-brand-->
    <thead>
        <tr>
            <th class="" scope="col">First Name</th>
            <th class="" scope="col">Middle Name</th>
            <th class="" scope="col">Last Name</th>
            <th class="" scope="col" style="width: 15%;" colspan="3">Suffix</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td data-label="First Name"> <?= $data->main->firstname ?> </td>
            <td data-label="Middle Name"> <?= $data->main->middlename ?> </td>
            <td data-label="Last Name"> <?= $data->main->lastname ?> </td>
            <td data-label="Suffix" colspan="3"> <?= $data->main->suffix ? $data->main->suffix : "&nbsp;" ?> </td>
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
