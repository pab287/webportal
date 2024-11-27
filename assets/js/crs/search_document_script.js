jQuery(document)
    .ready(function () {
        $('.search-with-dropdown')
            .donetyping(function () {
                const searchKey = $(this).val();
                if (searchKey) {
                    searchItem(searchKey);
                } else {
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

        function searchItem(searchKey) {
            $.ajax({
                url: baseUrl('crs/search_employee_document'),
                type: 'POST',
                dataType: 'JSON',
                data: {
                    csrf_token: _csrf_hash,
                    searchKey
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
                        response.forEach((item) => {
                            const li = '' +
                                '<li onclick="openData(\'' + item.filename + '\',' + item.file_exist + ')">' +
                                '   <div class="d-flex flex-column" style="flex: 1;">' +
                                '       <p class="m-0 title">' + item.applicant + '</p>' +
                                (item.file_exist ? '<p class="m-0 m--regular-font-size-sm1 mt-1">' + item.filename + '</p>' : '<p class="m-0 m--regular-font-size-sm1 mt-1">RESUME NOT FOUND.</p>') +
                                '   </div>' +
                                '</li>';
                            container.append(li);
                        });
                    }

                    $('.search-with-dropdown-suggestion-list').removeClass('invisible');
                }
            });
        }
    });

function openData(filename, file_exist) {
    const url = baseUrl('uploads/files/hrd/' + filename);
    if (file_exist) {
        window.open(url, "_blank");
        $('.search-with-dropdown-suggestion-list').addClass('invisible');
    }
}