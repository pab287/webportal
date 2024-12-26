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


  @media screen and (max-width: 640px){
    #accountability_table_mobile_wrapper .col-sm-12.col-md-6{
        padding: 0;
    }
  }
</style>

<div id="accordionMain" class="accordion mb-5" role="tablist">
    <div class="card">
        <div id="personalInfo-head" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" >
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" href="#personalInfo-body" aria-expanded="false" aria-controls="personalInfo-body">
                        <h5 class="m-portlet__head-text">
                            <span>Personal Information</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="personalInfo-body" class="collapse" :class="{show :activeSection == 'personalInfo'}" aria-labelledby="personalInfo-head" data-parent="#accordionMain">
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
                            <td v-text="main.firstname || '---'"></td>
                            <td v-text="main.middlename || '---'"></td>
                            <td v-text="main.lastname || '---'"></td>
                            <td v-text="main.suffix || '---'"></td>
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
                        <td data-label="Current Address" v-text="main.curr_addr || 'None'"></td>
                        <td data-label="Provincial Address" v-text="main.prov_addr || 'None'"></td> </td>
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
                        <td data-label="Citizenship" v-text="main.citizenship || 'None'"></td>
                        <td data-label="Religion" v-text="main.religion || 'None'"></td>
                        <td data-label="Languages" v-text="main.languages || 'None'"></td>
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
                        <td data-label="Gender" v-text="main.gender || 'N/A'"></td>
                        <td data-label="Civil Status" v-text="main.civil_stat || 'N/A'"></td>
                        <td data-label="Date of Birth" v-text="formatDate(main.bday) || 'None'"></td>
                        <td data-label="Place of Birth" v-text="main.birthplace || 'None'"></td>
                        <td data-label="Blood Type" v-text="main.bloodtype || 'None'"></td>
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
                        <td data-label="Height" v-text="main.height || 'N/A'"></td>
                        <td data-label="Weight" v-text="main.weight || 'N/A'"></td>
                        <td data-label="Hair Color" v-text="main.hair_color || 'N/A'"></td>
                        <td data-label="Complexion" v-text="main.complexion || 'N/A'"></td>
                        <td data-label="Tel. No." v-text="main.tel_no || 'N/A'"></td>
                        <td data-label="Mobile No." v-text="main.mobile_no || 'N/A'"></td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="additionalInfo-head" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" href="#additionalInfo-body" aria-expanded="false" aria-controls="additionalInfo-body" role="tab">
                        <h5 class="m-portlet__head-text">
                            <span>Additional Information</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="additionalInfo-body" class="collapse" :class="{show :activeSection == 'additionalInfo'}" aria-labelledby="additionalInfo-head" data-parent="#accordionMain">
            <div class="card-body">
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
                            <td data-label="Email" class="email_container" v-text="main.email || 'NONE'"></td>
                            <td data-label="Tax Status" v-text="main.tax_status || 'NONE'"></td>
                            <td data-label="Tin No." v-text="main.tin_no || 'NONE'"></td>
                            <td data-label="Philhealth No." v-text="main.phealth_no || 'NONE'"></td>
                            <td data-label="Pag-ibig No." v-text="main.pagibig_no || 'NONE'"></td>
                            <td data-label="SSS No." v-text="main.sss_no || 'NONE'"></td>
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
                            <th class="row-header" scope="col">Name</th>
                            <td>
                                <span v-if="main.fat_name" v-text="main.fat_name"></span>
                                <span v-else v-text="'None'"></span>
                                <span v-if="main.fat_deceased == 1" class="m-badge m-badge--primary m-badge--wide">Deceased</span>
                            </td>
                            <td>
                                <span v-if="main.mot_name" v-text="main.mot_name"></span>
                                <span v-else v-text="'None'"></span>
                                <span v-if="main.mot_deceased == 1" class="m-badge m-badge--primary m-badge--wide">Deceased</span>
                            </td>
                            <td>
                                <span v-if="main.partner_type == 1">
                                    <span v-if="main.spo_deceased == 1">
                                        <span v-text="main.spo_name"></span>
                                        <span class="m-badge m-badge--primary m-badge--wide">Deceased</span>
                                    </span>
                                    <span v-else v-text="main.spo_name"></span>
                                </span>
                                <span v-else-if="main.partner_type == 2">
                                    <span v-if="main.partners_deceased == 1">
                                        <span v-text="main.partners_name"></span>
                                        <span class="m-badge m-badge--primary m-badge--wide">Deceased</span>
                                    </span>
                                    <span v-else v-text="main.partners_name"></span>
                                </span>
                                <span v-else v-text="'None'"></span>
                            </td>
                        </tr>
                        <tr>
                            <th class="row-header" scope="col">Address</th>
                            <td v-text="main.fat_addr ? main.fat_addr : 'None'"></td>
                            <td v-text="main.mot_addr ? main.mot_addr : 'None'"></td>
                            <td>
                                <span v-if="main.partner_type == 1" v-text="main.spo_addr"></span>
                                <span v-else-if="main.partner_type == 2" v-text="main.partners_addr"></span>
                                <span v-else>None</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="row-header" scope="col">Company</th>
                            <td v-text="main.fat_company ? main.fat_company : 'None'"></td>
                            <td v-text="main.mot_company ? main.mot_company : 'None'"></td>
                            <td>
                                <span v-if="main.partner_type == 1" v-text="main.spo_company"></span>
                                <span v-else-if="main.partner_type == 2" v-text="main.partners_company"></span>
                                <span v-else>None</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="row-header" scope="col">Occupation</th>
                            <td v-text="main.fat_occupation ? main.fat_occupation : 'None'"></td>
                            <td v-text="main.mot_occupation ? main.mot_occupation : 'None'"></td>
                            <td>
                                <span v-if="main.partner_type == 1" v-text="main.spo_occupation"></span>
                                <span v-else-if="main.partner_type == 2" v-text="main.partners_occupation"></span>
                                <span v-else>None</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="row-header" scope="col">Contact No.</th>
                            <td v-text="main.fat_contact ? main.fat_contact : 'None'"></td>
                            <td v-text="main.mot_contact ? main.mot_contact : 'None'"></td>
                            <td>
                                <span v-if="main.partner_type == 1" v-text="main.spo_contact"></span>
                                <span v-else-if="main.partner_type == 2" v-text="main.partners_contact"></span>
                                <span v-else>None</span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="m--hidden-desktop">
                    <table class="responsive">
                        <thead class="customsalary">
                            <tr>
                                <th colspan="5">Father</th>
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
                                    <span v-if="main.fat_deceased == 1">{{ main.fat_name }}<span class='m-badge m-badge--primary m-badge--wide'>Deceased</span></span>
                                    <span v-else v-text="main.fat_name ? main.fat_name : 'None'"></span>
                                </td>
                                <td data-label="Address" v-text="main.fat_addr ? main.fat_addr : 'None'"></td>
                                <td data-label="Company" v-text="main.fat_company ? main.fat_company : 'None'"></td>
                                <td data-label="Occupation" v-text="main.fat_occupation ? main.fat_occupation : 'None'"></td>
                                <td data-label="Contact No." v-text="main.fat_contact ? main.fat_contact : 'None'"></td>
                            </tr>
                        </tbody>
                    </table>

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
                                <td data-label="Name">
                                    <span v-if="main.mot_deceased == 1">{{ main.mot_name }}<span class='m-badge m-badge--primary m-badge--wide'>Deceased</span></span>
                                    <span v-else v-text="main.mot_name ? main.mot_name : 'None'"></span>
                                </td>
                                <td data-label="Address" v-text="main.mot_addr ? main.mot_addr : 'None'"></td>
                                <td data-label="Company" v-text="main.mot_company ? main.mot_company : 'None'"></td>
                                <td data-label="Occupation" v-text="main.mot_occupation ? main.mot_occupation : 'None'"></td>
                                <td data-label="Contact No." v-text="main.mot_contact ? main.mot_contact : 'None'"></td>
                            </tr>
                        </tbody>
                    </table>

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
                                    <span v-if="main.partner_type == 1">
                                        <span v-if="main.spo_deceased == 1">
                                            <span v-text="main.spo_name"></span>
                                            <span class="m-badge m-badge--primary m-badge--wide">Deceased</span>
                                        </span>
                                        <span v-else v-text="main.spo_name"></span>
                                    </span>
                                    <span v-else-if="main.partner_type == 2">
                                        <span v-if="main.partners_deceased == 1">
                                            <span v-text="main.partners_name"></span>
                                            <span class="m-badge m-badge--primary m-badge--wide">Deceased</span>
                                        </span>
                                        <span v-else v-text="main.partners_name"></span>
                                    </span>
                                    <span v-else v-text="'None'"></span>
                                </td>
                                <td data-label="Address" v-text="main.partner_type == 1 ? main.spo_addr : (main.partner_type == 2 ? main.partners_addr : 'None')"></td>
                                <td data-label="Company" v-text="main.partner_type == 1 ? main.spo_company : (main.partner_type == 2 ? main.partners_company : 'None')"></td>
                                <td data-label="Occupation" v-text="main.partner_type == 1 ? main.spo_occupation : (main.partner_type == 2 ? main.partners_occupation : 'None')"></td>
                                <td data-label="Contact No." v-text="main.partner_type == 1 ? main.spo_contact : (main.partner_type == 2 ? main.partners_contact : 'None')"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

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
                        <template v-if="dependents == false">
                            <tr>
                                <td data-label="Name">None</td>
                                <td data-label="Age">None</td>
                                <td data-label="Date of Birth">None</td>
                                <td data-label="Relationship">None</td>
                            </tr>
                        </template>
                        <template v-else>
                            <tr v-for="dependent in dependents" :key="dependent.dep_name">
                                <td data-label="Name">{{ dependent.dep_name }}</td>
                                <td data-label="Age">{{ calculateAge(dependent.dep_birthdate) }}</td>
                                <td data-label="Date of Birth">{{ formatDate(dependent.dep_birthdate) }}</td>
                                <td data-label="Relationship">{{ dependent.dep_relation }}</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
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
                            <td data-label="Contact Person" v-text="main.emer_name || 'None'"></td>
                            <td data-label="Contact No." v-text="main.emer_contact || 'None'"></td>
                            <td data-label="Address" v-text="main.emer_addr || 'None'"></td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <div class="card">
        <div id="employmentQuestion-head" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" href="#employmentQuestion-body" aria-expanded="false" aria-controls="employmentQuestion-body">
                        <h5 class="m-portlet__head-text">
                            <span>Employment Questions</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="employmentQuestion-body" class="collapse" :class="{show :activeSection == 'employmentQuestion'}" aria-labelledby="employmentQuestion-head" data-parent="#accordionMain">
            <div class="card-body">
                <table class="responsive mb-1" v-for="(question, key) in questions" :key="key">
                    <thead>
                        <tr>
                            <th class="" scope="col" v-text="question">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="questions">
                                <span v-text="hasAnswer(key)"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div id="educBackground-head" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" >
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" href="#educBackground-body" aria-expanded="false" aria-controls="educBackground-body">
                        <h5 class="m-portlet__head-text">
                            <span>Educational Background</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="educBackground-body" class="collapse" :class="{show :activeSection == 'educBackground'}" aria-labelledby="educBackground-head" data-parent="#accordionMain">
            <div class="card-body">
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
                        <template v-if="educations == false">
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
                            <tr v-for="education in educations" :key="education.id">
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
            </div>
        </div>
    </div>
    <div class="card">
        <div id="licenseAndCert-head" class="card-header  m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" >
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" href="#licenseAndCert-body" aria-expanded="false" aria-controls="licenseAndCert-body">
                        <h5 class="m-portlet__head-text">
                            <span>Licenses and Certificates</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="licenseAndCert-body" class="collapse" :class="{show :activeSection == 'licenseAndCert'}" aria-labelledby="licenseAndCert-head" data-parent="#accordionMain">
            <div class="card-body">
                <table class="responsive">
                    <thead>
                    <tr>
                        <th class="" scope="col">LICENSE/EXAM TYPE</th>
                        <th class="" scope="col">EXAM PLACE</th>
                        <th class="" scope="col" style="width: 8%">RATING</th>
                        <th class="" scope="col" style="width: 15%">RELEASE DATE</th>
                        <th class="" scope="col" style="width: 15%">EXAM DATE</th>
                        <th class="" scope="col">LICENSE NO.</th>
                    </tr>
                    </thead>
                    <tbody>
                        <template v-if="licensesAndCerts.licenses.length == 0">
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
                            <tr v-for="license in licensesAndCerts.licenses" :key="license.id">
                                <td data-label="LICENSE/EXAM TYPE" v-text="license.license_type"></td>
                                <td data-label="EXAM PLACE" v-text="license.exam_place"></td>
                                <td data-label="RATING" v-text="license.rating"></td>
                                <td data-label="RELEASE DATE" v-text="license.release_date"></td>
                                <td data-label="EXAM DATE" v-text="license.exam_date"></td>
                                <td data-label="LICENSE NO." v-text="license.license_no"></td>
                            </tr>
                        </template>
                    </tbody>

                    <template v-if="licensesAndCerts.if_driver > 0 && licensesAndCerts.driverlicenses && licensesAndCerts.driverlicenses.length > 0">
                        <thead>
                        <tr>
                            <th class="" scope="col" colspan="2">RESTRICTION</th>
                            <th class="" scope="col" colspan="2">LICENSE NO.</th>
                            <th class="" scope="col" colspan="2">EXPIRATION DATE</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr v-for="driverlicense in licensesAndCerts.driverlicenses" :key="driverlicense.license_no">
                                <td data-label="RESTRICTION" colspan="2" v-text="driverlicense.restriction"></td>
                                <td data-label="LICENSE NO." colspan="2" v-text="driverlicense.license_no"></td>
                                <td data-label="EXPIRATION DATE" colspan="2">
                                    <span :class="['m-badge m-badge--wide', getExpirationClass(driverlicense.expiration_date)]">
                                        <span v-text="driverlicense.expiration_date"></span>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </template>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="workExperience-head" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab" >
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" href="#workExperience-body" aria-expanded="false" aria-controls="workExperience-body">
                        <h5 class="m-portlet__head-text">
                            <span>Work Experience</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="workExperience-body" class="collapse" :class="{show :activeSection == 'workExperience'}" aria-labelledby="workExperience-head" data-parent="#accordionMain">
            <div class="card-body">
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
                        <template v-if="works == false">
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
                            <tr v-for="(experience, index) in works" :key="index">
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
            </div>
        </div>
    </div>
    <div class="card">
        <div id="employeeAwards-head" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionMain" href="#employeeAwards-body" aria-expanded="false" aria-controls="employeeAwards-body">
                        <h5 class="m-portlet__head-text">
                            <span>Awards and Achievements</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="employeeAwards-body" class="collapse" :class="{show :activeSection == 'employeeAwards'}" aria-labelledby="employeeAwards-head" data-parent="#accordionMain">
            <div class="card-body">
                <table class="responsive">
                    <thead>
                    <tr>
                        <th class="" scope="col">AWARD/ACHIEVEMENT</th>
                        <th class="" scope="col">INSTITUTION</th>
                        <th class="" scope="col" style="width: 15%">GIVEN DATE</th>
                    </tr>
                    </thead>
                    <tbody>
                        <template v-if="awards == false">
                            <tr>
                                <td data-label="AWARD/ACHIEVEMENT">None</td>
                                <td data-label="INSTITUTION">None</td>
                                <td data-label="GIVEN DATE">None</td>
                            </tr>
                        </template>
                        <template v-else>
                            <tr v-for="(award, index) in awards" :key="index">
                                <td data-label="AWARD/ACHIEVEMENT" v-text="award.award"></td>
                                <td data-label="INSTITUTION" v-text="award.award_institution"></td>
                                <td data-label="GIVEN DATE" v-text="award.award_date"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="empSkills-head" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionMain" href="#empSkills-body" aria-expanded="false" aria-controls="empSkills-body">
                        <h5 class="m-portlet__head-text">
                            <span>Skills</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="empSkills-body" class="collapse" :class="{show :activeSection == 'empSkills'}" aria-labelledby="empSkills-head" data-parent="#accordionMain">
            <div class="card-body">
                <table class="responsive">
                    <thead>
                        <tr>
                            <th class="" scope="col"><label>TECHNICAL/ MANAGEMENT/ BUSINESS/ SPECIAL SKILLS</label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-if="skillset == false">
                            <tr>
                                <td data-label="TECHNICAL/MANAGEMENT/BUSINESS/SPECIAL SKILLS">None</td>
                            </tr>
                        </template>
                        <template v-else>
                            <tr v-for="(skill, index) in skillset" :key="skill.id">
                                <td data-label="TECHNICAL/MANAGEMENT/BUSINESS/SPECIAL SKILLS" v-text="skill.skills"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
			</div>
		</div>
    </div>
    <div class="card">
        <div id="empOrg-head" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionMain" href="#empOrg-body" aria-expanded="false" aria-controls="empOrg-body">
                        <h5 class="m-portlet__head-text">
                            <span>Organizations</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="empOrg-body" class="collapse" :class="{show :activeSection == 'empOrg'}" aria-labelledby="empOrg-head" data-parent="#accordionMain">
            <div class="card-body">
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
                        <template v-if="organizations == false">
                            <tr >
                                <td data-label="INSTITUTION">None</td>
                                <td data-label="MEMBERSHIP TITLE">None</td>
                                <td data-label="FROM">None</td>
                                <td data-label="TO">None</td>
                            </tr>
                        </template>
                        <template v-else>
                            <tr v-for="organization in organizations" :key="organization.id">
                                <td data-label="INSTITUTION" v-text="organization.org_institution"></td>
                                <td data-label="MEMBERSHIP TITLE" v-text="organization.org_membership_title"></td>
                                <td data-label="FROM" v-text="organization.org_from"></td>
                                <td data-label="TO" v-text="organization.org_to"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
			</div>
		</div>
    </div>

    <div class="card">
        <div id="empTrainings-head" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionMain" href="#empTrainings-body" aria-expanded="false" aria-controls="empTrainings-body">
                        <h5 class="m-portlet__head-text">
                            <span>Training and Seminars</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="empTrainings-body" class="collapse" :class="{show :activeSection == 'empTrainings'}" aria-labelledby="empTrainings-head" data-parent="#accordionMain">
            <div class="card-body">
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
                        <template v-if="trainings == false">
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
                            <tr v-for="training in trainings" :key="training.id">
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
			</div>
		</div>
    </div>
  
    <div class="card">
        <div id="empPersonalReferences-head" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionMain" href="#empPersonalReferences-body" aria-expanded="false" aria-controls="empPersonalReferences-body">
                        <h5 class="m-portlet__head-text">
                            <span>Personal References</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="empPersonalReferences-body" class="collapse" :class="{show :activeSection == 'empPersonalReferences'}" aria-labelledby="empPersonalReferences-head" data-parent="#accordionMain">
            <div class="card-body">
                <table class="responsive">
                    <thead>
                        <tr>
                            <th class="" scope="col">NAME</th>
                            <th class="" scope="col" style="width: 15%;">CONTACT NO.</th>
                            <th class="" scope="col">ADDRESS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-if="references == false">
                            <tr>
                                <td data-label="NAME">None</td>
                                <td data-label="CONTACT NO.">None</td>
                                <td data-label="ADDRESS">None</td>
                            </tr>
                        </template>
                        <template v-else>
                            <tr v-for="reference in references" :key="reference.id">
                                <td data-label="NAME" v-text="reference.ref_name"></td>
                                <td data-label="CONTACT NO." v-text="reference.ref_contact_no"></td>
                                <td data-label="ADDRESS" v-text="reference.ref_address"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
			</div>
		</div>
    </div>

    <div class="card">
        <div id="empMedicalHistory-head" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionMain" href="#empMedicalHistory-body" aria-expanded="false" aria-controls="empMedicalHistory-body">
                        <h5 class="m-portlet__head-text">
                            <span>Medical History / Records</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="empMedicalHistory-body" class="collapse" :class="{show :activeSection == 'empMedicalHistory'}" aria-labelledby="empMedicalHistory-head" data-parent="#accordionMain">
            <div class="card-body">
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
                        <template v-if="medicals == false">
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
                            <tr v-for="medical in medicals" :key="medical.id">
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
			</div>
		</div>
    </div>

    <div class="card">
        <div id="empLegalHistory-head" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse"  data-parent="#accordionMain" href="#empLegalHistory-body" aria-expanded="false" aria-controls="empLegalHistory-body">
                        <h5 class="m-portlet__head-text">
                            <span>Legal History / Records</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="empLegalHistory-body" class="collapse" :class="{show :activeSection == 'empLegalHistory'}" aria-labelledby="empLegalHistory-head" data-parent="#accordionMain">
            <div class="card-body">
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
                        <template v-if="legals == false">
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
                            <tr v-for="legal in legals" :key="legal.id">
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
			</div>
		</div>
    </div>

    <div class="card">
        <div id="empAccountability-head" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionMain" href="#empAccountability-body" aria-expanded="false" aria-controls="empAccountability-body">
                        <h5 class="m-portlet__head-text">
                            <span>Accountability</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="empAccountability-body" class="collapse" :class="{show :activeSection == 'empAccountability'}" aria-labelledby="empAccountability-head" data-parent="#accordionMain">
            <div class="card-body">
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
                    <template v-if="accountability == false">
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
                    </template>
                    <template v-else>
                        <tr v-for="acct in accountability" :key="acct.id">
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
                            <td class="text-center" data-label="REMARKS">
                            <template v-if="hasRemarks(acct)">
                                <a href="javascript:void(0)" @click="showRemarks(acct.remarks_returned)">View Remarks</a>
                            </template>
                            <template v-else>NO REMARKS</template>
                            </td>
                            <td data-label="DATE" v-text="formatDate(acct.date_returned || 'N/A')"></td>
                        </tr>
                    </template>
                    </tbody>
                </table>
			</div>
		</div>
    </div>

    <div class="card">
        <div id="empEmploymentInfo-head" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionMain" href="#empEmploymentInfo-body" aria-expanded="false" aria-controls="empEmploymentInfo-body">
                        <h5 class="m-portlet__head-text">
                            <span>Employment Information</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="empEmploymentInfo-body" class="collapse" :class="{show :activeSection == 'empEmploymentInfo'}" aria-labelledby="empEmploymentInfo-head" data-parent="#accordionMain">
            <div class="card-body">
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
                        <template v-if="offenses == false">
                            <tr>
                                <td data-label="TYPE">NONE</td>
                                <td data-label="DATE">NONE</td>
                                <td data-label="NATURE">NONE</td>
                                <td data-label="ACTION TAKEN">NONE</td>
                            </tr>
                        </template>
                        <template v-else>
                            <tr v-for="offense in offenses" :key="offense.id">
                                <td data-label="TYPE" v-text="offense.offcom_type"></td>
                                <td data-label="DATE" v-text="offense.offcom_date"></td>
                                <td data-label="NATURE" v-text="offense.offcom_nature"></td>
                                <td data-label="ACTION TAKEN" v-text="offense.offcom_action"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <template v-if="salaries != 'not_allowed'">
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
                                <template v-if="salaries == false">
                                    <tr>
                                        <td data-label="DATE">NONE</td>
                                        <td data-label="RATE">NONE</td>
                                        <td data-label="POSITION">NONE</td>
                                        <td data-label="REMARKS">NONE</td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr v-for="(salary, index) in salaries" :key="index">
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
                        <td data-label="DEPARTMENT" v-text="main.department_description || 'None'"></td>
                        <td data-label="WORK MODE" v-text="main.work_mode || 'None'"></td>
                        <td data-label="PAYROLL TYPE" v-text="main.payroll_type || 'None'"></td>
                        <td data-label="LEVEL / RANKING" v-text="main.level || 'None'"></td>
                        <td data-label="COMPANY" v-text="main.company_id || 'None'"></td>
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
                        <td data-label="CURRENT STATION / LOCATION">
                            <span v-text="default_station.description || 'N/A'"></span>
                        </td>
                        <td data-label="STATIONS">
                            <ul class="row">
                                <template v-if="stations != false">
                                    <li class="col-4" v-for="(site, index) in stations" :key="index">
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
                        <td data-label="DATE REGULARIZED" v-text="formatDate(main.date_regular)"></td>
                        <td data-label="PROBEE END DATE" v-text="formatDate(main.date_end_prob)"></td>
                        <td data-label="DATE SEPARATED" v-text="formatDate(main.date_end)"></td>
                        <td data-label="REASON FOR SEPARATION" v-text="main.resign_reason ? main.resign_reason : 'N/A'"></td>
                    </tr>
                    </tbody>
                </table>

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
                        <td data-label="POSITION" v-text="main.position ? main.position : 'N/A'"></td>
                        <td data-label="TYPE" v-text="main.level ? main.level : 'N/A'"></td>
                        <td data-label="DEPARTMENT" v-text="main.department_description ? main.department_description : 'N/A'"></td>
                        <td data-label="COMPANY" v-text="main.company_id ? main.company_id : 'N/A'"></td>
                    </tr>
                    </tbody>
                </table>

			</div>
		</div>
    </div>

    <div class="card">
        <div id="jobDescription-head" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionMain" href="#jobDescription-body" aria-expanded="false" aria-controls="jobDescription-body">
                        <h5 class="m-portlet__head-text">
                            <span>Job Description</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="jobDescription-body" class="collapse" :class="{show :activeSection == 'jobDescription'}" aria-labelledby="jobDescription-head" data-parent="#accordionMain">
            <div class="card-body">
                <table class="responsive">
                    <tbody>
                    <tr>
                        <td id="job_desc" class="text-left">
                        <label v-if="job_desc && job_desc !== 'NONE'" v-html="formattedJobDesc()"></label>
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