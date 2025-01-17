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
        <template v-if="printData.educations == false">
            <tr>
                <td data-label="Level">None</td>
                <td data-label="School">None</td>
                <td data-label="Degree">None</td>
                <td data-label="Honors">None</td>
                <td data-label="From">None</td>
                <td data-label="To" >None</td>
            </tr>
        </template>
        <template v-else>
            <tr v-for="education in printData.educations" :key="education.id">
                <td data-label="Level" v-text="education.educ_level_type"></td>
                <td data-label="School" v-text="education.educ_school"></td>
                <td data-label="Degree" v-text="education.educ_degree"></td>
                <td data-label="Honors" v-text="education.educ_honors || 'N/A'"></td>
                <td data-label="From" v-text="education.educ_from"></td>
                <td data-label="To" v-text="education.educ_to"></td>
            </tr>
        </template>
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
        <template v-if="printData.licensesAndCerts.licenses.length == 0">
            <tr >
                <td data-label="LICENSE/EXAM TYPE">None</td>
                <td data-label="EXAM PLACE">None</td>
                <td data-label="RATING">None</td>
                <td data-label="RELEASE DATE">None</td>
                <td data-label="EXAM DATE">None</td>
                <td data-label="LICENSE NO.">None</td>
            </tr>
        </template>
        <template v-else>
            <tr v-for="license in printData.licensesAndCerts.licenses" :key="license.id">
                <td data-label="LICENSE/EXAM TYPE" v-text="license.license_type"></td>
                <td data-label="EXAM PLACE" v-text="license.exam_place"></td>
                <td data-label="RATING" v-text="license.rating"></td>
                <td data-label="RELEASE DATE" v-text="license.release_date"></td>
                <td data-label="EXAM DATE" v-text="license.exam_date"></td>
                <td data-label="LICENSE NO." v-text="license.license_no"></td>
            </tr>
        </template>
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
        <template v-if="printData.experiences == false">
            <tr>
                <td data-label="COMPANY">None</td>
                <td data-label="FROM">None</td>
                <td data-label="TO">None</td>
                <td data-label="POSITION">None</td>
                <td data-label="ID NO">None</td>
                <td data-label="STATUS">None</td>
                <td data-label="REASON FOR LEAVING">None</td>
            </tr>
        </template>
        <template v-else>
            <tr v-for="(experience, index) in printData.experiences" :key="index">
                <td data-label="COMPANY" v-text="experience.work_company"></td>
                <td data-label="FROM" v-text="experience.work_from"></td>
                <td data-label="TO" v-text="experience.work_to"></td>
                <td data-label="POSITION" v-text="experience.work_position"></td>
                <td data-label="ID NO" v-text="experience.old_idno || 'N/A'"></td>
                <td data-label="STATUS" v-text="experience.work_status || '<br>'"></td>
                <td data-label="REASON FOR LEAVING" v-text="experience.work_reason || 'N/A'"></td>
            </tr>
        </template>
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

<table class="responsive mb-1" v-for="(question, key) in questions" :key="key">
    <tbody>
        <tr>
            <th class="" scope="col" v-text="question">
            </th>
        </tr>
        <tr>
            <td class="questions">
                <span v-text="hasAnswer(key)"></span>
            </td>
        </tr>
    </tbody>
</table>

</table>
<!-- END QUESTION TABLES -->

<!-- START RETURN TO WORK TABLES -->
<template>
  <div v-if="printData.return_to_work && printData.return_to_work.length > 0">
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
        <template v-if="!printData.return_to_work">
          <tr>
            <td data-label="REFERENCE #">None</td>
            <td data-label="EFFECTIVE DATE">None</td>
            <td data-label="TYPE">None</td>
            <td data-label="PURPOSE">None</td>
            <td data-label="APPROVED BY">None</td>
          </tr>
        </template>
        <template v-else>
          <tr v-for="(rtw, index) in printData.return_to_work" :key="index">
          <td data-label="REFERENCE #">
              <a href="javascript:void(0);" v-text="rtw.reference_no || 'N/A'">
              </a>
            </td>
            <td data-label="EFFECTIVE DATE" v-text="rtw.from_date || 'N/A'"></td>
            <td data-label="TYPE" v-text="rtw.return_type == 1 ? 'RECALLED' : rtw.return_type == 2 ? 'REQUEST TO EXTEND' : 'ABSENT'"></td>
            <td data-label="PURPOSE" v-text="rtw.reason || 'N/A'"></td>
            <td data-label="APPROVED BY" v-text="rtw.firstname || 'N/A'"></td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</template>
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
        <template v-if="printData.awards == false">
            <tr>
                <td data-label="AWARD/ACHIEVEMENT">None</td>
                <td data-label="INSTITUTION">None</td>
                <td data-label="GIVEN DATE">None</td>
            </tr>
        </template>
        <template v-else>
            <tr v-for="(award, index) in printData.awards" :key="index">
                <td data-label="AWARD/ACHIEVEMENT" v-text="award.award"></td>
                <td data-label="INSTITUTION" v-text="award.award_institution"></td>
                <td data-label="GIVEN DATE" v-text="award.award_date"></td>
            </tr>
        </template>
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
        <template v-if="printData.skillset == false">
            <tr>
                <td data-label="TECHNICAL/MANAGEMENT/BUSINESS/SPECIAL SKILLS">None</td>
            </tr>
        </template>
        <template v-else>
            <tr v-for="(skill, index) in printData.skillset" :key="skill.id">
                <td data-label="TECHNICAL/MANAGEMENT/BUSINESS/SPECIAL SKILLS" v-text="skill.skills"></td>
            </tr>
        </template>
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
        <template v-if="printData.organizations == false">
            <tr >
                <td data-label="INSTITUTION">None</td>
                <td data-label="MEMBERSHIP TITLE">None</td>
                <td data-label="FROM">None</td>
                <td data-label="TO">None</td>
            </tr>
        </template>
        <template v-else>
            <tr v-for="organization in printData.organizations" :key="organization.id">
                <td data-label="INSTITUTION" v-text="organization.org_institution"></td>
                <td data-label="MEMBERSHIP TITLE" v-text="organization.org_membership_title"></td>
                <td data-label="FROM" v-text="organization.org_from"></td>
                <td data-label="TO" v-text="organization.org_to"></td>
            </tr>
        </template>
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
        <template v-if="printData.trainings == false">
            <tr>
                <td data-label="TRAINING">None</td>
                <td data-label="FROM">None</td>
                <td data-label="TO">None</td>
                <td data-label="INSTITUTION">None</td>
                <td data-label="CONDUCTOR">None</td>
                <td data-label="VENUE">None</td>
            </tr>
        </template>
        <template v-else>
            <tr v-for="training in printData.trainings" :key="training.id">
                <td data-label="TRAINING" v-text="training.training"></td>
                <td data-label="FROM" v-text="training.train_from"></td>
                <td data-label="TO" v-text="training.train_to"></td>
                <td data-label="INSTITUTION" v-text="training.train_institution"></td>
                <td data-label="CONDUCTOR" v-text="training.train_conductor"></td>
                <td data-label="VENUE" v-text="training.train_venue"></td>
            </tr>
        </template>
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
        <template v-if="printData.references == false">
            <tr>
                <td data-label="NAME">None</td>
                <td data-label="CONTACT NO.">None</td>
                <td data-label="ADDRESS">None</td>
            </tr>
        </template>
        <template v-else>
            <tr v-for="reference in printData.references" :key="reference.id">
                <td data-label="NAME" v-text="reference.ref_name"></td>
                <td data-label="CONTACT NO." v-text="reference.ref_contact_no"></td>
                <td data-label="ADDRESS" v-text="reference.ref_address"></td>
            </tr>
        </template>
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
        <template v-if="printData.medicals == false">
            <tr>
                <td data-label="DETAILS">NONE</td>
                <td data-label="MED.NO.">NONE</td>
                <td data-label="DATE">NONE</td>
                <td data-label="VENUE">NONE</td>
                <td data-label="PHYSICIAN">NONE</td>
                <td data-label="FINDINGS">NONE</td>
                <td data-label="REMARKS">NONE</td>
            </tr>
        </template>
        <template v-else>
            <tr v-for="medical in printData.medicals" :key="medical.id">
                <td data-label="DETAILS" v-text="medical.med_details"></td>
                <td data-label="MED.NO." v-text="medical.med_no"></td>
                <td data-label="DATE" v-text="medical.med_date"></td>
                <td data-label="VENUE" v-text="medical.med_venue"></td>
                <td data-label="PHYSICIAN" v-text="medical.med_physician"></td>
                <td data-label="FINDINGS" v-text="medical.med_findings"></td>
                <td data-label="REMARKS" v-text="medical.remarks"></td>
            </tr>
        </template>
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
        <template v-if="printData.legals == false">
            <tr>
                <td data-label="CASE NO.">NONE</td>
                <td data-label="DETAILS">NONE</td>
                <td data-label="DATE">NONE</td>
                <td data-label="COURT FIELD">NONE</td>
                <td data-label="PROSECUTOR">NONE</td>
                <td data-label="STATUS">NONE</td>
            </tr>
        </template>
        <template v-else>
            <tr v-for="legal in printData.legals" :key="legal.id">
                <td data-label="CASE NO." v-text="legal.leg_case_no || 'N/A'"></td>
                <td data-label="DETAILS" v-text="legal.leg_details || 'N/A'"></td>
                <td data-label="DATE" v-text="legal.leg_case_date || 'N/A'"></td>
                <td data-label="COURT FIELD" v-text="legal.leg_court_field || 'N/A'"></td>
                <td data-label="PROSECUTOR" v-text="legal.leg_prosecutor || 'N/A'"></td>
                <td data-label="STATUS" v-text="legal.leg_status || 'N/A'"></td>
            </tr>
        </template>
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
        <template v-if="printData.offenses == false">
            <tr>
                <td data-label="TYPE">NONE</td>
                <td data-label="DATE">NONE</td>
                <td data-label="NATURE">NONE</td>
                <td data-label="ACTION TAKEN">NONE</td>
            </tr>
        </template>
        <template v-else>
            <tr v-for="offense in printData.offenses" :key="offense.id">
                <td data-label="TYPE" v-text="offense.offcom_type"></td>
                <td data-label="DATE" v-text="offense.offcom_date"></td>
                <td data-label="NATURE" v-text="offense.offcom_nature"></td>
                <td data-label="ACTION TAKEN" v-text="offense.offcom_action"></td>
            </tr>
        </template>
    </tbody>
</table>
<!-- OFFENSES AND COMMENDATIONS -->

<template v-if="printData.salaries != 'not_allowed'">
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
                <template v-if="printData.salaries == false">
                    <tr>
                        <td data-label="DATE">NONE</td>
                        <td data-label="RATE">NONE</td>
                        <td data-label="POSITION">NONE</td>
                        <td data-label="REMARKS">NONE</td>
                    </tr>
                </template>
                <template v-else>
                    <tr v-for="(salary, index) in printData.salaries" :key="index">
                        <td data-label="DATE" v-text="salary.sal_date"></td>
                        <td data-label="RATE">
                        <span v-text="formatSalaryRate(salary.sal_rate)">{{index}}</span>
                        <template v-if="index == 0">
                            <p class="m-0">
                            <small>
                                <span class="m-badge m-badge--success m-badge--wide">Current</span>
                            </small>
                            </p>
                        </template>
                        </td>
                        <td data-label="POSITION" v-text="salary.sal_position || '<br>'"></td>
                        <td data-label="REMARKS" v-text="salary.sal_remarks"></td>
                    </tr>
                </template>
            </tbody>
        </table>
</template>

<!-- SALARY HISTORY -->

<!-- START SALARY HISTORY -->
<!-- <table class="table-group-header">
    
</table> -->
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
            <th class="" scope="col" width="13%">DATE</th>
        </tr>
    </thead>
    <tbody>
    <template v-if="printData.accountability == false">
        <tr>
            <td data-label="STATUS">NONE</td>
            <td data-label="REF. NO">NONE</td>
            <td data-label="ASSET CODE">NONE</td>
            <td data-label="ASSET NAME">NONE</td>
            <td data-label="AMOUNT">NONE</td>
            <td data-label="RETURNED">NONE</td>
            <td data-label="DATE">NONE</td>
        </tr>
    </template>
    <template v-else>
        <tr v-for="acct in printData.accountability" :key="acct.id">
            <td data-label="STATUS" v-text="acct.status"></td>
            <td data-label="REF. NO" v-text="acct.reference_no"></td>
            <td data-label="ASSET CODE" v-text="acct.asset_code"></td>
            <td data-label="ASSET NAME" v-text="acct.aname"></td>
            <td class="text-right" data-label="AMOUNT">{{ formatAmount(acct.amount) }}</td>
            <td data-label="RETURNED" class="text-center" id="returned">
            <span class="m-badge m-badge--success px-2 m--font-bolder" v-if="isReturned(acct)">
                Yes
            </span>
            <span class="m-badge m-badge--danger px-2 m--font-bolder" v-else>
                No
            </span>
            </td>
            <td data-label="DATE" v-text="formatDate(acct.date_returned || 'N/A')"></td>
        </tr>
    </template>
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
            <td data-label="DEPARTMENT" v-text="printData.main.department_description || 'None'"></td>
            <td data-label="WORK MODE" v-text="printData.main.work_mode || 'None'"></td>
            <td data-label="PAYROLL TYPE" v-text="printData.main.payroll_type || 'None'"></td>
            <td data-label="LEVEL / RANKING" v-text="printData.main.level || 'None'"></td>
            <td data-label="COMPANY" v-text="printData.main.company_id || 'None'"></td>
        </tr>
    </tbody>
</table>

<table class="responsive">
    <template v-if="main.position === 'owner'"></template>
    <template v-else>
        <template v-if="['SUPERVISORY', 'MANAGERIAL', 'EXECUTIVE'].includes(main.level)">
            <thead class="customsalary">
                <th>SUPERVISOR</th>
            </thead>
            <tbody>
                <tr>
                    <td data-label="SUPERVISOR">Charles Anthony M. Dumancas</td>
                </tr>
            </tbody>
        </template>
        <template v-else>
            <thead class="customsalary">
                <th>SUPERVISOR</th>
                <th>DEPARTMENT MANAGER</th>
            </thead>
            <tbody>
                <tr>
                    <td data-label="SUPERVISOR" v-text="supervisor || 'N/A'"></td>
                    <td data-label="MANAGER" v-text="manager || 'N/A'"></td>
                </tr>
            </tbody>
        </template>
    </template>
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
        <td data-label="CURRENT STATION / LOCATION">
            <template v-if="printData.default_station != null">
                <span v-text="printData.default_station.description"></span>
            </template>
            <template v-else>
                <span v-text="'N/A'"></span>
            </template>

        </td>
        <td data-label="STATIONS">
            <ul class="row">
                <template v-if="printData.stations != false">
                    <li class="col-4" v-for="(site, index) in printData.stations" :key="index">
                        <span v-text="site.location_name || 'N/A'"></span>
                    </li>
                </template>
                <template v-else>
                    <span v-text="'N/A'"></span>
                </template>
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
        <td data-label="DATE SEPERATED">
            <?=($data->main->employee_status == 'Active' && ($data->main->date_end !== '0000-00-00' || $data->main->date_end !== NULL)) ? "N/A" : ($data->main->date_end == '0000-00-00' || $data->main->date_end == NULL ? "N/A" : $data->main->date_end) ?>
        </td>
        <!-- <td data-label="DATE SEPARATED"><?//= ($data->main->date_end == "0000-00-00" OR $data->main->date_end == NULL) ? "N/A" : $data->main->date_end ?></td> -->
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
            <td data-label="POSITION" v-text="printData.main.position ? main.position : 'N/A'"></td>
            <td data-label="TYPE" v-text="printData.main.level ? main.level : 'N/A'"></td>
            <td data-label="DEPARTMENT" v-text="printData.main.department_description ? main.department_description : 'N/A'"></td>
            <td data-label="COMPANY" v-text="printData.main.company_id ? main.company_id : 'N/A'"></td>
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
                        <td id="job_desc" class="text-left">
                        <label v-if="job_desc && job_desc !== 'NONE'" v-html="formattedJobDesc()"></label>
                        </td>
                    </tr>
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

