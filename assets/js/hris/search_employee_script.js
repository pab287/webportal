var values = ["company"];
const characterLimit = 3;
jQuery(document)
    .ready(function () {
        $('.search-with-dropdown')
            .donetyping(function () {
                const searchKey = $(this).val();
                const filter = values;
                if (searchKey) {
                    if(filter){
                        searchEmployee(searchKey,filter);
                    }
                } else {
                    $('.search-with-dropdown-suggestion-list').addClass('invisible');
                }
            }, 1000, characterLimit);

        /** added to remove suggestion list when input is empty */
        $(".search-with-dropdown").on('keyup', function (e) {
            var val = $(this).val();

            if (val == 0){
                $('.search-with-dropdown-suggestion-list').addClass('invisible');
            }
        });

        $('.search-with-dropdown')
            .on('focus', function () {
                const searchKey = $(this).val();
                setTimeout(() => {
                    if (searchKey) {
                        $('.search-with-dropdown-suggestion-list').removeClass('invisible');
                    }
                }, 300);
            });

        $(document)
            .mouseup(function (e) {
                const container = $('.search-with-dropdown-container');
                const searchList = $('.search-with-dropdown-suggestion-list');

                // if the target of the click isn't the container nor a descendant of the container
                if (!container.is(e.target) && container.has(e.target).length === 0) {
                    searchList.addClass('invisible');
                }
            });

        function searchEmployee(searchKey,values) {
            var filter = [];
            filter = values;

            $.ajax({
                url: baseUrl('hris/dashboard/search_employee'),
                type: 'POST',
                dataType: 'JSON',
                data: {
                    csrf_token: _csrf_hash,
                    searchKey,
                    filter
                },
                success: function (response) {
                    const container = $('ul.employee-suggestion');
                    container.html('');

                    if (response.length <= 0) {
                        const li = '' +
                            '<li style="min-height: 50px;">' +
                            '    <div class="d-flex flex-column">' +
                            '        <p style="font-weight: normal; text-transform: none;" ' +
                            '           class="m-0 title">No matching record found for <strong>"' + searchKey.toUpperCase() + '"</strong>.</p>' +
                            '    </div>' +
                            '</li>';
                        container.append(li);
                    } else {
                        response.forEach((employee) => {
                            // const imageUrl = baseUrl('uploads/files/images/employee_files/empcode_' + employee.id + '/' + employee.image);
                            const imageUrl = employee.image;
                            const li = '' +
                                '<li onclick="openEmployeeDataSheet(' + employee.id + ')">' +
                                '   <div class="avatar" style="background-image: url(\'' + imageUrl + '\')"></div>' +
                                '   <div class="d-flex align-items-start flex-column pl-4" style="flex: 1;">' +
                                '       <p class="m-0 title">' + employee.employee_name + '</p>' +
                                '       <p class="m-0 text-muted">' + employee.company + '</p>' +
                                '       <p class="m-0 text-muted">' + employee.position + '</p>' +
                                '   </div>' +
                                '</li>';
                            container.append(li);
                        });
                    }

                    $('.search-with-dropdown-suggestion-list').removeClass('invisible');
                }
            });
        }

        $('.search-with-mobile-dropdown')
            .donetyping(function () {
                const searchKey = $(this).val();
                const filter = values;
                if (searchKey) {
                    if(filter){
                        searchMobileEmployee(searchKey,filter);
                    }
                } else {
                    $('.search-with-mobile-dropdown-suggestion-list').addClass('invisible');
                }
            });

        $('.search-with-mobile-dropdown')
            .on('focus', function () {
                const searchKey = $(this).val();
                setTimeout(() => {
                    if (searchKey) {
                        $('.search-with-mobile-dropdown-suggestion-list').removeClass('invisible');
                    }
                }, 300);
            });

        $(document)
            .mouseup(function (e) {
                const container = $('.search-with-mobile-dropdown-container');
                const searchList = $('.search-with-mobile-dropdown-suggestion-list');

                // if the target of the click isn't the container nor a descendant of the container
                if (!container.is(e.target) && container.has(e.target).length === 0) {
                    searchList.addClass('invisible');
                }
            });

        function searchMobileEmployee(searchKey,values) {
            var filter = [];
            filter = values;

            $.ajax({
                url: baseUrl('hris/dashboard/search_employee'),
                type: 'POST',
                dataType: 'JSON',
                data: {
                    csrf_token: _csrf_hash,
                    searchKey,
                    filter
                },
                success: function (response) {
                    const container = $('ul.employee-suggestion');
                    container.html('');

                    if (response.length <= 0) {
                        const li = '' +
                            '<li style="min-height: 50px;">' +
                            '    <div class="d-flex flex-column">' +
                            '        <p style="font-weight: normal; text-transform: none;" ' +
                            '           class="m-0 title">No matching record found for <strong>"' + searchKey.toUpperCase() + '"</strong>.</p>' +
                            '    </div>' +
                            '</li>';
                        container.append(li);
                    } else {
                        response.forEach((employee) => {
                            // const imageUrl = baseUrl('uploads/files/images/employee_files/empcode_' + employee.id + '/' + employee.image);
                            const imageUrl = employee.image;
                            const li = '' +
                                '<li onclick="openEmployeeDataSheet(' + employee.id + ')">' +
                                '   <div class="avatar" style="background-image: url(\'' + imageUrl + '\')"></div>' +
                                '   <div class="d-flex align-items-start flex-column pl-4" style="flex: 1;">' +
                                '       <p class="m-0 title">' + employee.employee_name + '</p>' +
                                '       <p class="m-0 text-muted">' + employee.company + '</p>' +
                                '       <p class="m-0 text-muted">' + employee.position + '</p>' +
                                '   </div>' +
                                '</li>';
                            container.append(li);
                        });
                    }

                    $('.search-with-mobile-dropdown-suggestion-list').removeClass('invisible');
                }
            });
        }
        
        $("#manual-limit").on("click", function () {
            const searchKey = $(".search-with-dropdown").val().trim();
            const filter = values;
            searchEmployee(searchKey,filter);
        });
    });

function openEmployeeDataSheet(emp_id) {
    const url = baseUrl('hris/masterfile/view_employee_masterfile/' + emp_id);
    window.location.assign(url);
}

function checkFilter(){
    var filters = [];
    $("input:checkbox[name=search_filter]:checked").each(function() {
        filters.push($(this).val());
    });

    values = filters;
}