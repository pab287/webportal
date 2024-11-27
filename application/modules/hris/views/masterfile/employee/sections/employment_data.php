<style>
  @media print {
    table { page-break-inside:auto }
    div   { page-break-inside:avoid; } /* This is the key */
    thead { display:table-header-group }
    tfoot { display:table-footer-group }

    table {
      margin-top: -1px;
    }
  }
</style>
<!-- START EDUCATIONAL BACKGROUND TABLES -->
<!-- <table class="table-group-header">
    
</table> -->
<table class="responsive">
    <thead class="customsalary">
      <tr>
          <th scope="col" colspan="6">Educational Background</th>
      </tr>
    </thead>
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
            <td data-label="Honors"><?= $education->educ_honors ?></td>
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
<!-- END EDUCATIONAL BACKGROUND TABLES -->

<!-- START LICENCES & CERTIFICATIONS TABLES -->
<!-- <table class="table-group-header">
</table> -->
<table class="responsive">
    <thead class="customsalary">
      <tr>
          <th scope="col" colspan="6">LICENSES AND CERTIFICATES</th>
      </tr>
    </thead>
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
<!-- END LICENCES & CERTIFICATIONS TABLES -->
<!-- START LICENCES & CERTIFICATIONS TABLES -->
<!-- <table class="table-group-header">
    <thead>
    <tr>
        <th scope="col">WORK EXPERIENCES</th>
    </tr>
    </thead>
</table> -->
<table class="responsive">
    <thead thead class="customsalary">
      <tr>
          <th scope="col" colspan="7">WORK EXPERIENCES</th>
      </tr>
    </thead>
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
<!-- END LICENCES & CERTIFICATIONS TABLES -->

<!-- START QUESTION TABLES -->
<table class="table-group-header">
    <thead>
    <tr>
        <th scope="col">Questions</th>
    </tr>
    </thead>
</table>
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
<!-- END QUESTION TABLES -->

<!-- START RETURN TO WORK TABLES -->
<?php if (count($data->return_to_work) > 0) { ?>
<!-- <table class="table-group-header">
    
</table> -->
<table class="responsive">
    <thead class="customsalary">
      <tr>
          <th scope="col" colspan="5">RETURN TO WORK</th>
      </tr>
    </thead>
    <thead>
    <tr>
        <th class="" scope="col">REFERENCE #</th>
        <th class="" scope="col">EFFECTIVE DATE</th>
        <th class="" scope="col">TYPE</th>
        <th class="" scope="col">PURPOSE</th>
        <th class="" scope="col">APPROVED BY</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($data->return_to_work as $rtw) { ?>
        <tr>
            <td data-label="REFERENCE #"><a href="<?= base_url('eforms/return_to_work/view_return_to_work/') ?><?=$rtw->id?>"><?= $rtw->reference_no ?></a></td>
            <td data-label="EFFECTIVE DATE"><?= $rtw->from_date ?></td>
            <td data-label="TYPE">
            <?php
            if($rtw->return_type == 1){
                $type = "RECALLED";
            }else if($rtw->return_type == 2){
                $type = "REQUEST TO EXTEND";
            }else{
                $type = "ABSENT";
            }
            echo $type;
            ?></td>
            <td data-label="PURPOSE"><?= $rtw->reason ?></td>
            <td data-label="APPROVED BY"><?= $rtw->firstname ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<?php } ?>
<!-- END RETURN TO WORK TABLES -->

<!-- START AWARD AND ACHIEVEMENTS -->
<!-- <table class="table-group-header">
   
</table> -->
<table class="responsive">
    <thead class="customsalary">
      <tr>
          <th scope="col" colspan="3">AWARDS AND ACHIEVEMENTS</th>
      </tr>
    </thead>
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
<!-- END AWARD AND ACHIEVEMENTS -->

<!-- START SKILLS -->
<!-- <table class="table-group-header">
    
</table> -->
<table class="responsive">
    <thead class="customsalary">
      <tr>
          <th scope="col" colspan="">SKILLS</th>
      </tr>
    </thead>
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
<!-- END SKILLS -->

<!-- START ORGANIZATIONS -->
<!-- <table class="table-group-header">
    
</table> -->
<table class="responsive">
    <thead class="customsalary">
      <tr>
          <th scope="col" colspan="4">ORGANIZATIONS</th>
      </tr>
    </thead>
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
<!-- END ORGANIZATIONS -->

<!-- START TRAINING AND SEMINARS -->
<!-- <table class="table-group-header">
    
</table> -->
<table class="responsive">
    <thead class="customsalary">
      <tr>
          <th scope="col" colspan="6">TRAININGS AND SEMINARS</th>
      </tr>
    </thead>
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
<!-- TRAINING AND SEMINARS -->

<!-- START PERSONAL REFERENCES -->
<!-- <table class="table-group-header">
   
</table> -->
<table class="responsive">
    <thead class="customsalary">
      <tr>
          <th scope="col" colspan="3">PERSONAL REFERENCES</th>
      </tr>
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
<!-- PERSONAL REFERENCES -->

<!-- START MEDICAL HISTORY/RECORDS -->
<!-- <table class="table-group-header">
    
</table> -->
<table class="responsive">
    <thead class="customsalary">
      <tr>
          <th scope="col" colspan="7">MEDICAL HISTORY/RECORDS</th>
      </tr>
    </thead>
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
<!-- MEDICAL HISTORY/RECORDS -->

<!-- START LEGAL HISTORY/RECORDS -->
<!-- <table class="table-group-header">
    
</table> -->
<table class="responsive">
    <thead class="customsalary">
      <tr>
          <th scope="col" colspan="6">LEGAL HISTORY/RECORDS</th>
      </tr>
    </thead>
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
<!-- LEGAL HISTORY/RECORDS -->

<!-- START OFFENSES AND COMMENDATIONS -->
<!-- <table class="table-group-header">
    
</table> -->
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
// isset($data->main->id) ? $data->main->id : "";
if(in_array("view_own_request", $this->core_layout->getCurrentActions()) AND $data->main->id != $session_id){ 
?>
<!-- START SALARY HISTORY -->

<?php }elseif(in_array("view_own_request", $this->core_layout->getCurrentActions()) AND $data->main->id == $session_id){ ?>
    <!-- <table class="table-group-header">
    
</table> -->
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
<!-- <table class="table-group-header">
    <thead>
    <tr>
        <th scope="col">SALARY HISTORY</th>
    </tr>
    </thead>
</table> -->
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
<!-- <table class="table-group-header">
    
</table> -->
<table class="responsive" id="accountability_table">
    <thead class="customsalary">
    <tr>
        <th scope="col" colspan="7">ACCOUNTABILITY</th>
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
            <td data-label="DATE">NONE</td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<!-- SALARY HISTORY -->

<!-- START EMPLOYEE INFORMATION -->
<!-- <table class="table-group-header">
    
</table> -->
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
            <td data-label="CURRENT STATION / LOCATION"  style="vertical-align: top">
                <?php echo isset($data->default_station->description) && $data->default_station->description ? $data->default_station->description: ""; ?>
            </td>
            <td data-label="STATIONS">
                <ul class="row"><?php if(count($data->station) != 0){ 
                    foreach($data->station as $sites){ ?>
                    <li class="col-4"><?php echo (isset($sites->location_name) && $sites->location_name) ? $sites->location_name : "N/A"; ?></li>
                    <?php } } ?>
                </ul>
            </td>
        </tr>
    </tbody>
</table>
<!-- EMPLOYEE INFORMATION -->

<!-- START WORK STATUS -->
<!-- <table class="table-group-header">
    
</table> -->
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
                if($data->main->work_status == "PROBATIONARY"){
                    echo "N/A";
                }else{
                    echo $data->main->date_end_prob;
                }  
            //($data->main->date_end_prob == "0000-00-00" OR $data->main->date_end_prob == NULL) ? "N/A" : $data->main->date_end_prob?>
        </td>
        <td data-label="DATE SEPARATED"><?= ($data->main->date_end == "0000-00-00" OR $data->main->date_end == NULL) ? "N/A" : $data->main->date_end ?></td>
        <td data-label="REASON FOR SEPARATION"><?= $data->main->resign_reason ? $data->main->resign_reason : "N/A" ?></td>
    </tr>
    </tbody>
</table>
<!-- WORK STATUS -->

<!--START CONTRACT STATUS -->
<!-- <?php// if($data->main->work_status == "PROJECT BASED"){?>
<table class="table-group-header">
    <thead>
    <tr>
        <th scope="col">CONTRACT STATUS - PROJECT BASED</th>
    </tr>
    </thead>
</table>
<table class="responsive">
    <thead>
    <tr>
        <th class="" scope="col" style="width: 13%">DATE START</th>
        <th class="" scope="col" style="width: 13%">DATE END</th>
        <th class="" scope="col" style="width: 13%">DAYS LEFT</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <?php
        //$start_date = date_create($data->main->date_start);
        //$end_date = date_create($data->main->date_end_prob);
        //$days =  date_diff($start_date, $end_date);
        //$days_left = $days->format('%a days');
        ?>
        <td data-label="DATE START"><?//= $data->main->date_start; ?></td>
        <td data-label="DATE END"><?//= $data->main->date_end_prob; ?></td>
        <td data-label="DAYS LEFT"><?//= $days_left; ?></td>
    </tr>
    </tbody>
</table>
<?php //} ?> -->

<!-- CONTRACT STATUS -->
<!-- START JOB DETAIL -->
<!-- <table class="table-group-header">
    
</table> -->
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

<!-- START JOB DESCRIPTION -->
<table class="responsive">
    <thead class="customsalary">
    <tr>
        <th class="custom-head_bg--primary" scope="col" colspan="1">
            <span>JOB DESCRIPTION</span>
        </th>
    </tr>
    </thead>
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
        <td id="job_desc" data-label="JOB DESCRIPTION" class="text-left">
        <?php if($display != NULL OR $display != "NONE"){?>
            <label><?= $display ?></label>
        <?php } ?>
        </td>
    </tr>
    <!-- $data->main->job_desc -->
    </tbody>
</table>
<!-- JOB DESCRIPTION -->
<script type="text/javascript">
$(document).ready(function(){
    
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

