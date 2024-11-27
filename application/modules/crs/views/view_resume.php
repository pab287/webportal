<style>
  tbody > tr > td[data-label] {
    word-break: break-word;
  }
  p{
    margin: 0
  }
</style>

<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
              <span class="m-portlet__head-icon">
                  <a type="button" href="../resume" title="Go to Masterfile" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnBack">
                      <i class="la la-arrow-left"></i>
                  </a>
              </span>
							<h3 class="m-portlet__head-text">
								View Application
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools"></div>
				</div>
				<div class="m-portlet__body">
          <div id="viewResume">
            <div class="m-portlet__section">
              <table class="responsive">
                <thead class="customsalary pihid">
                  <tr>
                      <th scope="col" colspan="6">Application Information</th>
                  </tr>
                </thead>
                <thead>
                  <tr>
                    <th class="" scope="col">Recruitment Source</th>
                    <th class="" scope="col">School</th>
                    <th class="" scope="col">Courses</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><span v-text="row.recruitment"></span></td>
                    <td>
                      <ul v-for="(item, index) in listItems(row.school)">
                        <li v-text="item"></li>
                      </ul>
                    </td>
                    <td>
                      <ul v-for="(item, index) in listItems(row.course)">
                        <li v-text="item"></li>
                      </ul>
                    </td>
                  </tr>
                </tbody>
              </table>
  
              <table class="responsive">
                <thead class="customsalary">
                  <tr>
                    <th class="" scope="col">Tags</th>
                    <th class="" scope="col">Position</th>
                    <th class="" scope="col">Date of Application</th>
                    <th class="" scope="col">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <template v-if="row.tags">
                        <ul v-for="(item, index) in listItems(row.tags)">
                          <li v-text="item"></li>
                        </ul>
                      </template>
                      <template v-else>
                        N/A
                      </template>
                    </td>
                    <td>
                      <ul v-for="(item, index) in listItems(row.position)">
                        <li v-text="item"></li>
                      </ul>
                    </td>
                    <td><p v-text="row.date">&nbsp;</p></td>
                    <td>
                      <template v-if="row.status !== ''">
                        <span class="m-badge text-white m-badge--wide font-weight-bold" :class="class_name" role="alert" v-text="row.status"></span>
                      </template>
                    </td>
                  </tr>
                </tbody>
              </table>
  
              <table class="responsive">
                <thead class="customsalary">
                  <tr>
                    <th class="" scope="col">Remarks</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <template v-if="row.description">
                        <p class="m--margin-top-5" v-text="row.description">&nbsp;</p></td>
                      </template>
                      <template v-else>
                        <p class="m--margin-top-5">None</p>
                      </template>
                  </tr>
                </tbody>
              </table>
            </div>
  
            <div class="m-portlet__section">
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
                        <th class="" scope="col" style="width: 15%;">Suffix</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="First Name"><p v-text="row.firstname"></p></td>
                        <td data-label="Middle Name"><p v-text="row.middlename"></p></td>
                        <td data-label="Last Name"><p v-text="row.lastname"></p></td>
                        <td data-label="Suffix"><p v-text="row.suffix">&nbsp;</p></td>
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
                        <td data-label="Current Address"><p v-text="row.current_address"></p></td>
                        <td data-label="Provincial Address"> <p v-text="row.permanent_address"></p></td>
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
                        <td data-label="Citizenship"><p v-text="row.citizenship"></p></td>
                        <td data-label="Religion"> <p v-text="row.religion"></p> </td>
                        <td data-label="Languages"> <p v-text="row.languages"></p> </td>
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
                        <td data-label="Gender"> <p v-text="row.gender"></p> </td>
                        <td data-label="Civil Status"> <p v-text="row.civil_status"></p> </td>
                        <td data-label="Date of Birth"><p v-text="row.date_of_birth"></p></td>
                        <td data-label="Place of Birth"> <p v-text="row.birthplace"></p> </td>
                        <td data-label="Blood Type"> <p v-text="row.bloodtype"></p> </td>
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
                        <td data-label="Height"><p v-text="row.height"></p></td>
                        <td data-label="Weight"><p v-text="row.weight"></p></td>
                        <td data-label="Hair Color"><p v-text="row.hair_color"></p></td>
                        <td data-label="Complexion"><p v-text="row.complexion"></p></td>
                        <td data-label="Tel. No."><p v-text="row.tel_no ? row.tel_no : 'N/A'"></p></td>
                        <td data-label="Mobile No."><p v-text="row.contact_no ? row.contact_no : 'N/A'"></p></td>
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
                        <td data-label="Email" class="email_container"><p v-text="row.email ? row.email : 'N/A'"></p></td>
                        <td data-label="Tax Status"><p v-text="row.tax_status ? row.tax_status : 'N/A'"></p></td>
                        <td data-label="Tin No."><p v-text="row.tin_no ? row.tin_no : 'N/A'"></p></td>
                        <td data-label="Philhealth No."><p v-text="row.phealth_no ? row.phealth_no : 'N/A'"></p></td>
                        <td data-label="Pag-ibig No."><p v-text="row.pagibig_no ? row.pagibig_no : 'N/A'"></p></td>
                        <td data-label="SSS No."><p v-text="row.sss_no ? row.sss_no : 'N/A'"></p></td>
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
                          <template v-if="row.fat_name">
                            <p> {{ row.fat_name }} <span :class="row.fat_deceased">Deceased</span></p>
                          </template>
                          <template v-else>
                            None
                          </template>
                        </td>
                        <td>
                          <template v-if="row.mot_name">
                            <p> {{ row.mot_name }} <span :class="row.mot_deceased">Deceased</span></p>
                          </template>
                          <template v-else>
                            None
                          </template>
                        </td>
                        <td>
                          <template v-if="row.partners_name">
                            <p> {{ row.partners_name }} <span :class="row.partners_deceased">Deceased</span></p>
                          </template>
                          <template v-else>
                            None
                          </template>
                        </td>
                    </tr>
                    <tr>
                        <th class="row-header" scope="col">Address</th>
                        <td><p v-text="row.fat_addr ? row.fat_addr : 'None'"></p></td>
                        <td><p v-text="row.mot_addr ? row.mot_addr : 'None'"></p></td>
                        <td><p v-text="row.partners_addr ? row.partners_addr : 'None'"></p></td>
                    </tr>
                    <tr>
                        <th class="row-header" scope="col">Company</th>
                        <td><p v-text="row.fat_company ? row.fat_company : 'None'"></p></td>
                        <td><p v-text="row.mot_company ? row.mot_company : 'None'"></p></td>
                        <td><p v-text="row.partners_company ? row.partners_company : 'None'"></p></td>
                    </tr>
                    <tr>
                        <th class="row-header" scope="col">Occupation</th>
                        <td><p v-text="row.fat_occupation ? row.fat_occupation : 'None'"></p></td>
                        <td><p v-text="row.mot_occupation ? row.mot_occupation : 'None'"></p></td>
                        <td><p v-text="row.partners_occupation ? row.mot_occupation : 'None'"></p></td>
                    </tr>
                    <tr>
                        <th class="row-header" scope="col">Contact No.</th>
                        <td><p v-text="row.fat_contact ? row.fat_contact : 'None'"></p></td>
                        <td><p v-text="row.mot_contact ? row.mot_contact : 'None'"></p></td>
                        <td><p v-text="row.partners_contact ? row.mot_contact : 'None'"></p></td>
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
                              <template v-if="row.fat_name">
                                <p> {{ row.fat_name }} <span :class="row.fat_deceased">Deceased</span></p>
                              </template>
                              <template v-else>
                                None
                              </template>
                            </td>
                            <td data-label="Address"><p v-text="row.fat_addr ? row.fat_addr : 'None'"></p></td>
                            <td data-label="Company"><p v-text="row.fat_company ? row.fat_company : 'None'"></p></td>
                            <td data-label="Occupation"><p v-text="row.fat_occupation ? row.fat_occupation : 'None'"></p></td>
                            <td data-label="Contact No."><p v-text="row.fat_contact ? row.fat_contact : 'None'"></p></td>
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
                              <template v-if="row.mot_name">
                                <p> {{ row.mot_name }} <span :class="row.mot_deceased">Deceased</span></p>
                              </template>
                              <template v-else>
                                None
                              </template>
                            </td>
                            <td data-label="Address"><p v-text="row.mot_addr ? row.mot_addr : 'None'"></p></td>
                            <td data-label="Company"><p v-text="row.mot_company ? row.mot_company : 'None'"></p></td>
                            <td data-label="Occupation"><p v-text="row.mot_occupation ? row.mot_occupation : 'None'"></p></td>
                            <td data-label="Contact No."><p v-text="row.mot_contact ? row.mot_contact : 'None'"></p></td>
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
                              <template v-if="row.partners_name">
                                <p> {{ row.partners_name }} <span :class="row.partners_deceased">Deceased</span></p>
                              </template>
                              <template v-else>
                                None
                              </template>
                            </td>
                            <td data-label="Address">
                              <p v-text="row.partners_addr ? row.partners_addr : 'None'"></p>
                            </td>
                            <td data-label="Company">
                              <p v-text="row.partners_company ? row.partners_company : 'None'"></p>
                            </td>
                            <td data-label="Occupation">
                              <p v-text="row.partners_occupation ? row.mot_occupation : 'None'"></p>
                            </td>
                            <td data-label="Contact No.">
                              <p v-text="row.partners_contact ? row.mot_contact : 'None'"></p>
                            </td>
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
                  <template v-if="!isEmpty(row.dependents)">
                    <tr v-for="(item, index) in row.dependents">
                      <td data-label="Name"><p v-text="item.dep_name ? item.dep_name : 'None'"></p></td>
                      <td data-label="Age"><p v-text="item.age ? item.age : 'None'"></p></td>
                      <td data-label="Date of Birth"><p v-text="item.birthdate ? item.birthdate : 'None'"></p></td>
                      <td data-label="Relationship"><p v-text="item.dep_relation ? item.dep_relation : 'None'"></p></td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr>
                      <td colspan="4" class="text-center">None</td>
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
                    <td data-label="Contact Person"><p v-text="row.emer_name"></p></td>
                    <td data-label="Contact No."><p v-text="row.emer_contact"></p></td>
                    <td data-label="Address"><p v-text="row.emer_addr"></p></td>
                </tr>
                </tbody>
              </table>
            </div>
  
            <div class="m-portlet__section">
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
                  <template v-if="!isEmpty(row.education)">
                    <tr v-for="(item, index) in row.education">
                      <td data-label="Level"><p v-text="item.educ_level_type ? item.educ_level_type : 'None'"></p></td>
                      <td data-label="School"><p v-text="item.educ_school ? item.educ_school : 'None'"></p></td>
                      <td data-label="Degree"><p v-text="item.educ_degree ? item.educ_degree : 'None'"></p></td>
                      <td data-label="Honors"><p v-text="item.educ_honors ? item.educ_honors : 'None'"></p></td>
                      <td data-label="From"><p v-text="item.educ_from ? item.educ_from : 'None'"></p></td>
                      <td data-label="To"><p v-text="item.educ_to ? item.educ_to : 'None'"></p></td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr>
                      <td colspan="6" class="text-center">None</td>
                    </tr>
                  </template>
                </tbody>
              </table>
  
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
                  <template v-if="!isEmpty(row.license)">
                    <tr v-for="(item, index) in row.license">
                        <td data-label="LICENSE/EXAM TYPE"><p v-text="item.license_type ? item.license_type : 'None'"></p></td>
                        <td data-label="EXAM PLACE"><p v-text="item.exam_place ? item.exam_place : 'None'"></p></td>
                        <td data-label="RATING"><p v-text="item.rating ? item.rating : 'None'"></p></td>
                        <td data-label="RELEASE DATE"><p v-text="item.release_date ? item.release_date : 'None'"></p></td>
                        <td data-label="EXAM DATE"><p v-text="item.exam_date ? item.exam_date : 'None'"></p></td>
                        <td data-label="LICENSE NO."><p v-text="item.license_no ? item.license_no : 'None'"></p></td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr>
                      <td scope="col" colspan="6" class="text-center">None</td>
                    </tr>
                  </template>
                </tbody>
                  <!-- START DRIVER'S LICENSE ROW -->
                  <template v-if="!isEmpty(row.drivers)">
                    <thead>
                      <tr>
                          <th class="" scope="col" colspan="2">RESTRICTION</th>
                          <th class="" scope="col" colspan="2">LICENSE NO.</th>
                          <th class="" scope="col" colspan="2">EXPIRATION DATE</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(item, index) in row.drivers">
                          <td data-label="RESTRICTION" colspan="2"><p v-text="item.restriction ? item.restriction : 'None'"></p></td>
                          <td data-label="LICENSE NO." colspan="2"><p v-text="item.license_no ? item.license_no : 'None'"></p></td>
                          <td data-label="EXPIRATION DATE" colspan="2"><p v-text="item.expiration_date ? item.expiration_date : 'None'"></p></td>
                      </tr>
                    </tbody>
                  </template>
                  <!-- END DRIVER'S LICENSE ROW -->
              </table>
  
              <table class="responsive">
                <thead thead class="customsalary">
                  <tr>
                      <th scope="col" colspan="6">WORK EXPERIENCES</th>
                  </tr>
                </thead>
                <thead>
                <tr>
                    <th class="" scope="col" style="width: 25%">COMPANY</th>
                    <th class="" scope="col" style="width: 5%">FROM</th>
                    <th class="" scope="col" style="width: 5%">TO</th>
                    <th class="" scope="col" style="width: 25%">POSITION</th>
                    <th class="" scope="col" style="width: 11%">STATUS</th>
                    <th class="" scope="col" style="width: 13%">REASON FOR LEAVING</th>
                </tr>
                </thead>
                <tbody>
                  <template v-if="!isEmpty(row.work)">
                    <tr v-for="(item, index) in row.work">
                        <td data-label="COMPANY"><p v-text="item.work_company ? item.work_company : 'None'"></p></td>
                        <td data-label="FROM"><p v-text="item.work_from ? item.work_from : 'None'"></p></td>
                        <td data-label="TO"><p v-text="item.work_to ? item.work_to : 'None'"></p></td>
                        <td data-label="POSITION"><p v-text="item.work_position ? item.work_position : 'None'"></p></td>
                        <td data-label="STATUS"><p v-text="item.work_status ? item.work_status : 'None'"></p></td>
                        <td data-label="REASON FOR LEAVING"><p v-text="item.work_reason ? item.work_reason : 'None'"></p></td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr>
                      <td colspan="6" class="text-center">None</td>
                    </tr>
                  </template>
                </tbody>
              </table>
  
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
                  <template v-if="!isEmpty(row.award)">
                    <tr v-for="(item, index) in row.award">
                        <td data-label="AWARD/ACHIEVEMENT"><p v-text="item.award ? item.award : 'None'"></p></td>
                        <td data-label="INSTITUTION"><p v-text="item.award_institution ? item.award_institution : 'None'"></p></td>
                        <td data-label="GIVEN DATE"><p v-text="item.award_date ? item.award_date : 'None'"></p></td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr>
                      <td colspan="3" class="text-center">None</td>
                    </tr>
                  </template>
                </tbody>
              </table>
  
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
                  <template v-if="!isEmpty(row.skill)">
                    <tr v-for="(item, index) in row.skill">
                      <td data-label="TECHNICAL/MANAGEMENT/BUSINESS/SPECIAL SKILLS"><p v-text="item.skills ? item.skills : 'None'"></p></td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr>
                      <td colspan="1" class="text-center">None</td>
                    </tr>
                  </template>
                </tbody>
              </table>
  
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
                  <template v-if="!isEmpty(row.org)">
                    <tr v-for="(item, index) in row.org">
                        <td data-label="INSTITUTION"><p v-text="item.org_institution ? item.org_institution : 'None'"></p></td>
                        <td data-label="MEMBERSHIP TITLE"><p v-text="item.org_membership_title ? item.org_membership_title : 'None'"></p></td>
                        <td data-label="FROM"><p v-text="item.org_from ? item.org_from : 'None'"></p></td>
                        <td data-label="TO"><p v-text="item.org_to ? item.org_to : 'None'"></p></td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr>
                      <td colspan="4" class="text-center">None</td>
                    </tr>
                  </template>
                </tbody>
             </table>
  
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
                  <template v-if="!isEmpty(row.training)">
                    <tr v-for="(item, index) in row.training">
                        <td data-label="TRAINING"><p v-text="item.training ? item.training : 'None'"></p></td>
                        <td data-label="FROM"><p v-text="item.train_from ? item.train_from : 'None'"></p></td>
                        <td data-label="TO"><p v-text="item.train_to ? item.train_to : 'None'"></p></td>
                        <td data-label="INSTITUTION"><p v-text="item.train_institution ? item.train_institution : 'None'"></p></td>
                        <td data-label="CONDUCTOR"><p v-text="item.train_conductor ? item.train_conductor : 'None'"></p></td>
                        <td data-label="VENUE"><p v-text="item.train_venue ? item.train_venue : 'None'"></p></td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr>
                      <td colspan="6" class="text-center">None</td>
                    </tr>
                  </template>
                </tbody>
              </table>
  
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
                  <template v-if="!isEmpty(row.reference)">
                    <tr v-for="(item, index) in row.reference">
                        <td data-label="NAME"><p v-text="item.ref_name ? item.ref_name : 'None'"></p></td>
                        <td data-label="CONTACT NO."><p v-text="item.ref_contact_no ? item.ref_contact_no : 'None'"></p></td>
                        <td data-label="ADDRESS"><p v-text="item.ref_address ? item.ref_address : 'None'"></p></td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr>
                      <td colspan="3" class="text-center">None</td>
                    </tr>
                  </template>
                </tbody>
              </table>
  
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
                  <template v-if="!isEmpty(row.med)">
                    <tr v-for="(item, index) in row.med">
                        <td data-label="DETAILS"><p v-text="item.med_details ? item.med_details : 'None'"></p></td>
                        <td data-label="MED.NO."><p v-text="item.med_no ? item.med_no : 'None'"></p></td>
                        <td data-label="DATE"><p v-text="item.med_date ? item.med_date : 'None'"></p></td>
                        <td data-label="VENUE"><p v-text="item.med_venue ? item.med_venue : 'None'"></p></td>
                        <td data-label="PHYSICIAN"><p v-text="item.med_physician ? item.med_physician : 'None'"></p></td>
                        <td data-label="FINDINGS"><p v-text="item.med_findings ? item.med_findings : 'None'"></p></td>
                        <td data-label="REMARKS"><p v-text="item.remarks ? item.remarks : 'None'"></p></td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr>
                      <td colspan="7" class="text-center">None</td>
                    </tr>
                  </template>
                </tbody>
              </table>
  
              <table class="responsive">
                <thead class="customsalary">
                  <tr>
                      <th scope="col" colspan="7">Attachment</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="" colspan="7" scope="col">
                      <p>
                        {{ row.filename }} 
                        <span class="flaticon-file-1 ml-2" data-toggle="m-tooltip" title="View Attachment" @click="view_resume(row.body_id)"></span>
                        <span class="flaticon-download ml-3" data-toggle="m-tooltip" title="Download Attachment" @click="downloadFile(row.filename)"></span>
                      </p>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
				</div>
			</div>
    </div>
  </div>
</div>

<br>

<!-- <div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
              <span class="m-portlet__head-icon">
                  <a type="button" href="../resume" title="Go to Masterfile" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnBack">
                      <i class="la la-arrow-left"></i>
                  </a>
              </span>
							<h3 class="m-portlet__head-text">
								View Resume
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools"></div>
				</div>
				<div class="m-portlet__body">
					<div class="row" id="viewResume">
            <div class="col-md-6">
              <div class="form-group m-form__group row">
                <label class="col-3">
                  Name:
                </label>
                <div class="col-9"><b><p id="ref_no" v-text="row.name">&nbsp; </p></b></div>
              </div>
              <div class="form-group m-form__group row">
                <label class="col-3">
                  Contact no:
                </label>
                <div class="col-9"><b><p id="ref_no" v-text="row.contact_no">&nbsp; </p></b></div>
              </div>
              <div class="form-group m-form__group row">
                <label class="col-3">
                  Recruitment Source
                </label>
                <div class="col-9" id="source">
                  <b><span v-text="row.recruitment"></span></b>
                </div>
              </div>
              <div class="form-group m-form__group row">
                <label class="col-3">
                  Tags
                </label>
                <div class="col-9" id="from">
                    <ul v-for="(item, index) in listItems(row.tags)">
                      <b><li v-text="item"></li></b>
                    </ul>
                </div>
              </div>
              <div class="form-group m-form__group row">
                <label class="col-3">
                  Status
                </label>
                <div class="col-9">
                  <b><span class="m-badge text-white m-badge--wide font-weight-bold" :class="class_name" role="alert" v-if="row.status !== ''" v-text="row.status"></span></b>
                </div>
              </div>
              <div class="form-group m-form__group row" id="other_remark">
                <label class="col-3">
                  School
                </label>
                <div class="col-9" >
                    <ul v-for="(item, index) in listItems(row.school)">
                      <b><li v-text="item"></li></b>
                    </ul>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group m-form__group row" >
                <label class="col-3 ">
                  Courses
                </label>
                <div class="col-9" >
                  <ul v-for="(item, index) in listItems(row.course)">
                    <b><li v-text="item"></li></b>
                  </ul>
                </div> 
              </div>
              <div class="form-group m-form__group row" >
                <label class="col-3">
                  position
                </label>
                <div class="col-9" id="edited">
                  <ul v-for="(item, index) in listItems(row.position)">
                    <b><li v-text="item"></li></b>
                  </ul>
                </div>
              </div>
              <div class="form-group m-form__group row" id="approve">
                <label class="col-3">
                  Date of Application
                </label>
                <div class="col-9">
                  <b><p v-text="row.date">&nbsp;</p></b>
                </div>
              </div>
              <div class="form-group m-form__group row" id="release_remark">
                <label class="col-3 col-form-label">
                  Remarks
                </label>
                <div class="col-9" id="remark">
                  <b><p  class="m--margin-top-5" v-text="row.description">&nbsp;</p></b>
                </div>
              </div> 
            </div>         
          </div>
          <div class="m-separator m-separator--dashed d-xl-12"></div> 
          <br>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group m-form__group row">
                <div class="m_datatable  m-datatable--default  m-datatable--scroll col-12">
                  <table class="table table-striped table-bordered" id="view_file_table" width="100%">
                    <thead>
                      <tr>
                        <th>File Name</th>
                        <th>File Size</th>
                        <th>Uploaded By</th>
                        <th>Date Uploaded</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div> 
              </div>
            </div>
          </div>
				</div>
      </div>
    </div>
  </div>
</div> -->

<?php $this->load->view("modals/view_file_modal") ?>
