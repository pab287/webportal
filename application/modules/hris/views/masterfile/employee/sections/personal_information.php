<style>
    .page-break {
        display: block;
        page-break-before: always;
    }
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
    .page-break {
        margin-top: 0;
        margin-bottom: 0;
        padding-top: 1px;
        padding-bottom: 0;
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
            <td data-label="First Name" v-text="printData.main.firstname || '---'">  </td>
            <td data-label="Middle Name" v-text="printData.main.middlename || '---'">  </td>
            <td data-label="Last Name" v-text="printData.main.lastname || '---'">  </td>
            <td data-label="Suffix" colspan="3" v-text="printData.main.suffix || '---'">  </td>
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
            <td data-label="Current Address" v-text="printData.main.curr_addr || '---'">  </td>
            <td data-label="Provincial Address" v-text="printData.main.prov_addr || '---'">  </td>
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
            <td data-label="Citizenship" v-text="printData.main.citizenship || '---'">  </td>
            <td data-label="Religion" v-text="printData.main.religion || '---'">  </td>
            <td data-label="Languages" v-text="printData.main.languages || '---'">  </td>
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
            <td data-label="Gender" v-text="printData.main.gender || '---'">  </td>
            <td data-label="Civil Status" v-text="printData.main.civil_stat || '---'">  </td>
            <td data-label="Date of Birth" v-text="formatDate(printData.main.bday) || 'None'"> </td>
            <td data-label="Place of Birth" v-text="printData.main.birthplace || 'None'"> </td>
            <td data-label="Blood Type" v-text="printData.main.bloodtype || 'None'"> </td>
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
            <td data-label="Height" v-text="printData.main.height || 'N/A'"></td>
            <td data-label="Weight" v-text="printData.main.weight || 'N/A'"></td>
            <td data-label="Hair Color" v-text="printData.main.hair_color || 'N/A'"></td>
            <td data-label="Complexion" v-text="printData.main.complexion || 'N/A'"></td>
            <td data-label="Tel. No." v-text="printData.main.tel_no || 'N/A'"></td>
            <td data-label="Mobile No." v-text="printData.main.mobile_no || 'N/A'"></td>
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
            <td data-label="Email" class="email_container" v-text="printData.main.email || 'NONE'"></td>
            <td data-label="Tax Status" v-text="printData.main.tax_status || 'NONE'"></td>
            <td data-label="Tin No." v-text="printData.main.tin_no || 'NONE'"></td>
            <td data-label="Philhealth No." v-text="printData.main.phealth_no || 'NONE'"></td>
            <td data-label="Pag-ibig No." v-text="printData.main.pagibig_no || 'NONE'"></td>
            <td data-label="SSS No." v-text="printData.main.sss_no || 'NONE'"></td>
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
                <span v-if="printData.main.fat_name" v-text="printData.main.fat_name"></span>
                <span v-else v-text="'None'"></span>
                <span v-if="printData.main.fat_deceased == 1" class="m-badge m-badge--primary m-badge--wide">Deceased</span>
            </td>
            <td>
                <span v-if="printData.main.mot_name" v-text="printData.main.mot_name"></span>
                <span v-else v-text="'None'"></span>
                <span v-if="printData.main.mot_deceased == 1" class="m-badge m-badge--primary m-badge--wide">Deceased</span>
            </td>
            <td>
                <span v-if="printData.main.partner_type == 1">
                    <span v-if="printData.main.spo_deceased == 1">
                        <span v-text="printData.main.spo_name"></span>
                        <span class="m-badge m-badge--primary m-badge--wide">Deceased</span>
                    </span>
                    <span v-else v-text="printData.main.spo_name"></span>
                </span>
                <span v-else-if="printData.main.partner_type == 2">
                    <span v-if="printData.main.partners_deceased == 1">
                        <span v-text="printData.main.partners_name"></span>
                        <span class="m-badge m-badge--primary m-badge--wide">Deceased</span>
                    </span>
                    <span v-else v-text="printData.main.partners_name"></span>
                </span>
                <span v-else v-text="'None'"></span>
            </td>
        </tr>
        <tr>
            <th class="row-header" scope="col">Address</th>
            <td v-text="printData.main.fat_addr ? printData.main.fat_addr : 'None'"></td>
            <td v-text="printData.main.mot_addr ? printData.main.mot_addr : 'None'"></td>
            <td>
                <span v-if="printData.main.partner_type == 1" v-text="printData.main.spo_addr"></span>
                <span v-else-if="printData.main.partner_type == 2" v-text="printData.main.partners_addr"></span>
                <span v-else>None</span>
            </td>
        </tr>
        <tr>
            <th class="row-header" scope="col">Company</th>
            <td v-text="printData.main.fat_company ? printData.main.fat_company : 'None'"></td>
            <td v-text="printData.main.mot_company ? printData.main.mot_company : 'None'"></td>
            <td>
                <span v-if="printData.main.partner_type == 1" v-text="printData.main.spo_company"></span>
                <span v-else-if="printData.main.partner_type == 2" v-text="printData.main.partners_company"></span>
                <span v-else>None</span>
            </td>
        </tr>
        <tr>
            <th class="row-header" scope="col">Occupation</th>
            <td v-text="printData.main.fat_occupation ? printData.main.fat_occupation : 'None'"></td>
            <td v-text="printData.main.mot_occupation ? printData.main.mot_occupation : 'None'"></td>
            <td>
                <span v-if="printData.main.partner_type == 1" v-text="printData.main.spo_occupation"></span>
                <span v-else-if="printData.main.partner_type == 2" v-text="printData.main.partners_occupation"></span>
                <span v-else>None</span>
            </td>
        </tr>
        <tr>
        <th class="row-header" scope="col">Contact No.</th>
            <td v-text="printData.main.fat_contact ? printData.main.fat_contact : 'None'"></td>
            <td v-text="printData.main.mot_contact ? printData.main.mot_contact : 'None'"></td>
            <td>
                <span v-if="printData.main.partner_type == 1" v-text="printData.main.spo_contact"></span>
                <span v-else-if="printData.main.partner_type == 2" v-text="printData.main.partners_contact"></span>
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
                    <span v-if="printData.main.fat_deceased == 1">{{ printData.main.fat_name }}<span class='m-badge m-badge--primary m-badge--wide'>Deceased</span></span>
                    <span v-else v-text="printData.main.fat_name ? printData.main.fat_name : 'None'"></span>
                </td>
                <td data-label="Address" v-text="printData.main.fat_addr ? printData.main.fat_addr : 'None'"></td>
                <td data-label="Company" v-text="printData.main.fat_company ? printData.main.fat_company : 'None'"></td>
                <td data-label="Occupation" v-text="printData.main.fat_occupation ? printData.main.fat_occupation : 'None'"></td>
                <td data-label="Contact No." v-text="printData.main.fat_contact ? printData.main.fat_contact : 'None'"></td>
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
                    <span v-if="printData.main.mot_deceased == 1">{{ printData.main.mot_name }}<span class='m-badge m-badge--primary m-badge--wide'>Deceased</span></span>
                    <span v-else v-text="printData.main.mot_name ? printData.main.mot_name : 'None'"></span>
                </td>
                <td data-label="Address" v-text="printData.main.mot_addr ? printData.main.mot_addr : 'None'"></td>
                <td data-label="Company" v-text="printData.main.mot_company ? printData.main.mot_company : 'None'"></td>
                <td data-label="Occupation" v-text="printData.main.mot_occupation ? printData.main.mot_occupation : 'None'"></td>
                <td data-label="Contact No." v-text="printData.main.mot_contact ? printData.main.mot_contact : 'None'"></td>
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
                    <span v-if="printData.main.partner_type == 1">
                        <span v-if="printData.main.spo_deceased == 1">
                            <span v-text="printData.main.spo_name"></span>
                            <span class="m-badge m-badge--primary m-badge--wide">Deceased</span>
                        </span>
                        <span v-else v-text="printData.main.spo_name"></span>
                    </span>
                    <span v-else-if="printData.main.partner_type == 2">
                        <span v-if="printData.main.partners_deceased == 1">
                            <span v-text="printData.main.partners_name"></span>
                            <span class="m-badge m-badge--primary m-badge--wide">Deceased</span>
                        </span>
                        <span v-else v-text="printData.main.partners_name"></span>
                    </span>
                    <span v-else v-text="'None'"></span>
                </td>
                <td data-label="Address" v-text="printData.main.partner_type == 1 ? printData.main.spo_addr : (printData.main.partner_type == 2 ? printData.main.partners_addr : 'None')"></td>
                <td data-label="Company" v-text="printData.main.partner_type == 1 ? printData.main.spo_company : (printData.main.partner_type == 2 ? printData.main.partners_company : 'None')"></td>
                <td data-label="Occupation" v-text="printData.main.partner_type == 1 ? printData.main.spo_occupation : (printData.main.partner_type == 2 ? printData.main.partners_occupation : 'None')"></td>
                <td data-label="Contact No." v-text="printData.main.partner_type == 1 ? printData.main.spo_contact : (printData.main.partner_type == 2 ? printData.main.partners_contact : 'None')"></td>
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
        <tr></tr>
        <template v-if="printData.dependents == false">
            <tr>
                <td data-label="Name">None</td>
                <td data-label="Age">None</td>
                <td data-label="Date of Birth">None</td>
                <td data-label="Relationship">None</td>
            </tr>
        </template>
        <template v-else>
            <tr v-for="dependent in printData.dependents" :key="dependent.dep_name">
                <td data-label="Name" v-text="dependent.dep_name"></td>
                <td data-label="Age" v-text="calculateAge(dependent.dep_birthdate)"></td>
                <td data-label="Date of Birth" v-text="formatDate(dependent.dep_birthdate)"></td>
                <td data-label="Relationship" v-text="dependent.dep_relation"></td>
            </tr>
        </template>
    </tbody>
</table>

    <table class="responsive page-break">
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
            <td data-label="Contact Person" v-text="printData.main.emer_name || 'None'"></td>
            <td data-label="Contact No." v-text="printData.main.emer_contact || 'None'"></td>
            <td data-label="Address" v-text="printData.main.emer_addr || 'None'"></td>
        </tr>
        </tbody>
    </table>

    <div style="page-break-before: always;" class="page-break"></div>