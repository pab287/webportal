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
        <div id="collapsePersonal" class="collapse" :class="{show :activeSection == 'personalInfo'}" role="tabpanel" aria-labelledby="headingPersonal" data-parent="#accordionOtherAdditionalInfo">
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
                        <td data-label="First Name" v-text="main.firstname || '---'"> </td>
                        <td data-label="Middle Name" v-text="main.middlename || '---'"> </td>
                        <td data-label="Last Name" v-text="main.lastname || '---'"> </td>
                        <td data-label="Suffix" v-text="main.suffix || '---'"> </td>
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
                        <td data-label="Current Address" v-text="main.curr_addr || '---'"> </td>
                        <td data-label="Provincial Address" v-text="main.prov_addr || '---'"> </td>
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
                        <td data-label="Citizenship" v-text="main.citizenship || '---'"> </td>
                        <td data-label="Religion" v-text="main.religion || '---'"> </td>
                        <td data-label="Languages" v-text="main.languages || '---'"> </td>
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
                        <td data-label="Gender" v-text="main.gender || '---'"> </td>
                        <td data-label="Civil Status" v-text="main.civil_stat || '---'"> </td>
                        <td data-label="Date of Birth" v-text="formatDate(main.bday) || 'None'"> </td>
                        <td data-label="Place of Birth" v-text="main.birthplace || 'None'"> </td>
                        <td data-label="Blood Type" v-text="main.bloodtype || 'None'"> </td>
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
                        <td data-label="Height" v-text="main.height || '---'"> </td>
                        <td data-label="Weight" v-text="main.weight || '---'"> </td>
                        <td data-label="Hair Color" v-text="main.hair_color || '---'"> </td>
                        <td data-label="Complexion" v-text="main.complexion || '---'"> </td>
                        <td data-label="Tel. No." v-text="main.tel_no || '---'"> </td>
                        <td data-label="Mobile No." v-text="main.mobile_no || '---'"> </td>
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
        <div id="collapseAdditional" class="collapse" :class="{show :activeSection == 'additionalInfo'}" role="tabpanel" aria-labelledby="headingAdditional" data-parent="#accordionOtherAdditionalInfo">
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
                            <td data-label="Email" v-text="main.email || '---'" class="email_container"></td>
                            <td data-label="Tax Status" v-text="main.tax_status || '---'"></td>
                            <td data-label="Tin No." v-text="main.tin_no || '---'"></td>
                            <td data-label="Philhealth No." v-text="main.phealth_no || '---'"></td>
                            <td data-label="Pag-ibig No." v-text="main.pagibig_no || '---'"></td>
                            <td data-label="SSS No." v-text="main.sss_no || '---'"></td>
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
                                <td data-label="Name" v-text="dependent.dep_name"></td>
                                <td data-label="Age" v-text="calculateAge(dependent.dep_birthdate)"></td>
                                <td data-label="Date of Birth" v-text="formatDate(dependent.dep_birthdate)"></td>
                                <td data-label="Relationship" v-text="dependent.dep_relation"></td>
                            </tr>
                        </template>
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
        <div id="collapseQuestion" class="collapse" :class="{show :activeSection == 'employmentQuestion'}" role="tabpanel" aria-labelledby="headingQuestion" data-parent="#accordionOtherAdditionalInfo">
            <div class="card-body m-portlet__body--custom table-responsive">
                <table class="responsive mb-1" v-for="(question, key) in questions" :key="key">
                    <thead>
                        <tr>
                        <th scope="col">Questions</th>
                        </tr>
                    </thead>
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
        <div id="collapseEducation" class="collapse" :class="{show :activeSection == 'educBackground'}" role="tabpanel" aria-labelledby="headingEducation" data-parent="#accordionOtherAdditionalInfo">
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
        <div id="collapseLicense" class="collapse" :class="{show :activeSection == 'employmentQuestion'}" role="tabpanel" aria-labelledby="headingLicense" data-parent="#accordionOtherAdditionalInfo">
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
        <div id="collapseWork" :class="{show :activeSection == 'workExperience'}" class="collapse" role="tabpanel" aria-labelledby="headingWork" data-parent="#accordionOtherAdditionalInfo">
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
        <div id="collapseAward" class="collapse" role="tabpanel" :class="{show :activeSection == 'employeeAwards'}" aria-labelledby="headingAward" data-parent="#accordionOtherAdditionalInfo">
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
        <div id="collapseSkill" class="collapse" :class="{show :activeSection == 'empSkills'}" role="tabpanel" aria-labelledby="headingSkills" data-parent="#accordionOtherAdditionalInfo">
            <div class="card-body m-portlet__body--custom table-responsive">
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
        <div id="collapseOrg" class="collapse" :class="{show :activeSection == 'empOrg'}" role="tabpanel" aria-labelledby="headingOrg" data-parent="#accordionOtherAdditionalInfo">
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
        <div id="collapseTrain" class="collapse" :class="{show :activeSection == 'empTrainings'}" role="tabpanel" aria-labelledby="headingTrain" data-parent="#accordionOtherAdditionalInfo">
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
        <div id="collapseRef" class="collapse" :class="{show :activeSection == 'empPersonalReferences'}" role="tabpanel" aria-labelledby="headingRef" data-parent="#accordionOtherAdditionalInfo">
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
        <div id="collapseMed" class="collapse" :class="{show :activeSection == 'empMedicalHistory'}" role="tabpanel" aria-labelledby="headingMed" data-parent="#accordionOtherAdditionalInfo">
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
        <div id="collapseLegal" class="collapse" :class="{show :activeSection == 'empLegalHistory'}" role="tabpanel" aria-labelledby="headingLegal" data-parent="#accordionOtherAdditionalInfo">
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
        <div id="collapseAccountability" class="collapse" :class="{show :activeSection == 'empAccountability'}" role="tabpanel" aria-labelledby="headingAccountability" data-parent="#accordionOtherAdditionalInfo">
            <div class="card-body m-portlet__body--custom table-responsive">
                <table class="table table-bordered" id="accountability_table_mobile">
                    <thead>
                        <tr>
                            <th class="" scope="col" width="30%">STATUS</th>
                            <th class="" scope="col" width="70%">Information</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="headingEmployment" class="card-header m-portlet m-portlet--bordered m-portlet--unair bg-a9 m-portlet--head-solid-bg m-portlet--head-sm" role="tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <a class="m-portlet__nav-link collapsed" data-toggle="collapse" data-parent="#accordionOtherAdditionalInfo" href="#collapseEmployment" aria-expanded="false" aria-controls="collapseEmployment">
                        <h5 class="m-portlet__head-text">
                            <span>Employment Information</span>
                            <i class="la pull-right la-angle-down"></i>
                        </h5>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapseEmployment" class="collapse" :class="{show :activeSection == 'empEmploymentInfo'}" role="tabpanel" aria-labelledby="headingEmployment" data-parent="#accordionOtherAdditionalInfo">
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
                        <th class="" scope="col">VIEW</th>
                        <th class="" scope="col">ACTIONS TAKEN</th>
                    </tr>
                    </thead>
                    <tbody>
                        <template v-if="offenses == false">
                            <tr>
                                <td data-label="TYPE">NONE</td>
                                <td data-label="DATE">NONE</td>
                                <td data-label="NATURE">NONE</td>
                                <td data-label="VIEW">NONE</td>
                                <td data-label="ACTION TAKEN">NONE</td>
                            </tr>
                        </template>
                        <template v-else>
                            <tr v-for="offense in offenses" :key="offense.id">
                                <td data-label="TYPE" v-text="offense.offcom_type"></td>
                                <td data-label="DATE" v-text="offense.offcom_date"></td>
                                <td data-label="FILE" @click="openFileMobile(offense.filename)" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-decoration: underline;"> {{ offense.filename.length > 30 ? offense.filename.substring(0, 30) + '...' : offense.filename }}</td>
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
                                    <li v-for="(site, index) in stations" :key="index">
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
                <table class="responsive">
                    <thead class="customsalary">
                    <tr>
                        <th scope="col" colspan="4">WORK STATUS</th>
                    </tr>
                    </thead>
                    <thead>
                    <tr>
                        <th class="" scope="col" style="width: 13%">PROBEE END DATE</th>
                        <th class="" scope="col" style="width: 13%">DATE REGULARIZED</th>
                        <th class="" scope="col" style="width: 13%">DATE SEPARATED</th>
                        <th class="text-center" scope="col" style="width: 20%">REASON FOR SEPARATION</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td data-label="PROBEE END DATE" v-text="formatDate(main.date_end_prob)"></td>
                        <td data-label="DATE REGULARIZED" v-text="formatDate(main.date_regular)"></td>
                        <!-- <td data-label="DATE SEPARATED" v-text="formatDate(main.date_end)"></td> -->
                        <td data-label="DATE SEPARATED" v-text="(main.employee_status === 'Active' && (main.date_end !== '0000-00-00' || main.date_end === null)) ? 'N/A' : formatDate(main.date_end)"></td>
                        <!-- <td data-label="REASON FOR SEPARATION" v-text="main.resign_reason ? main.resign_reason : 'N/A'"></td> -->
                        <td data-label="REASON FOR SEPARATION" v-text="(main.employee_status === 'Active' && main.resign_reason) ? 'N/A' : (main.resign_reason ? main.resign_reason : 'N/A')"></td>
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
                        <td data-label="POSITION" v-text="main.position ? main.position : 'N/A'"></td>
                        <td data-label="TYPE" v-text="main.level ? main.level : 'N/A'"></td>
                        <td data-label="DEPARTMENT" v-text="main.department_description ? main.department_description : 'N/A'"></td>
                        <td data-label="COMPANY" v-text="main.company_id ? main.company_id : 'N/A'"></td>
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
        <div id="collapseJob" class="collapse" :class="{show :activeSection == 'jobDescription'}" role="tabpanel" aria-labelledby="headingJob" data-parent="#accordionOtherAdditionalInfo">
            <div class="card-body m-portlet__body--custom table-responsive">
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