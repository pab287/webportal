let biometricNoExist = false;
let existInPersonnelList = false;
const alertModal = $('#alert-modal');
let image = null;

const activeStatusOptions = '' +
    '<option value=""></option>' +
    '<option value="REGULAR">REGULAR</option>' +
    '<option value="PROBATIONARY">PROBATIONARY</option>' +
    '<option value="NO CONTRACT">NO CONTRACT</option>' +
    '<option value="RETIRED">RETIREE</option>' +
    '<option value="CONSULTANT">CONSULTANT/RETAINER</option>' +
    '<option value="PROJECT BASED">PROJECT BASED</option>';

const inactiveStatusOptions = '' +
    '<option value=""></option>' +
    '<option value="RESIGN">RESIGNED</option>' +
    '<option value="TERMINATED">TERMINATED</option>' +
    '<option value="BLACKLISTED">BLACKLISTED</option>' +
    '<option value="END OF CONTRACT">END OF CONTRACT</option>' +
    '<option value="INDEFINITE LEAVE">INDEFINITE LEAVE</option>';

function urltoFile(url, filename, mimeType) {
    mimeType = mimeType || (url.match(/^data:([^;]+);/) || '')[1];
    return (fetch(url)
            .then(function (res) {
                return res.arrayBuffer();
            })
            .then(function (buf) {
                return new File([buf], filename, {type: mimeType});
            })
    );
}

const dataURItoBlob = (dataURI) => {
    const bytes = dataURI.split(',')[0].indexOf('base64') >= 0
        ? atob(dataURI.split(',')[1])
        : unescape(dataURI.split(',')[1]);
    const mime = dataURI.split(',')[0].split(':')[1].split(';')[0];
    const max = bytes.length;
    const ia = new Uint8Array(max);
    for (let i = 0; i < max; i += 1) ia[i] = bytes.charCodeAt(i);
    return new Blob([ia], {type: mime});
};

const resizeImage = ({file, maxSize}) => {
    const reader = new FileReader();
    const image = new Image();
    const canvas = document.createElement('canvas');

    const resize = () => {
        let {width, height} = image;

        if (width > height) {
            if (width > maxSize) {
                height *= maxSize / width;
                width = maxSize;
            }
        } else if (height > maxSize) {
            width *= maxSize / height;
            height = maxSize;
        }

        canvas.width = width;
        canvas.height = height;
        canvas.getContext('2d').drawImage(image, 0, 0, width, height);

        const dataUrl = canvas.toDataURL('image/jpeg');

        return dataURItoBlob(dataUrl);
    };

    return new Promise((ok, no) => {
        if (!file.type.match(/image.*/)) {
            no(new Error('Not an image'));
            return;
        }

        reader.onload = (readerEvent) => {
            image.onload = () => ok(resize());
            image.src = readerEvent.target.result;
        };

        reader.readAsDataURL(file);
    });
};

$.validate({
    form: $('#frm-new-employee'),
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const biometricno = $('#biometricno').val();
        let data = new FormData($(form)[0]);
        data.append('biometricNoExist', biometricNoExist);
        data.append('existInPersonnelList', existInPersonnelList);

        if (biometricNoExist) {
            $('.modal-title', alertModal).html(`<span class="m--font-bolder m--font-danger">BIOMETRIC NO. EXIST</span>`);
            $('.modal-body', alertModal).html(`<p class="m--font-bold m--regular-font-size-lg2">
                                                    Unable to proceed. Biometric No. 
                                                    <span class="m--font-boldest">${biometricno}</span> already exists.
                                               </p>`);
            alertModal.modal('show');
        } else {
            if (image) {
                urltoFile(image.base64data, image.name)
                    .then(function (file) {
                        data.append('image', file);
                        save();
                    });
            } else {
                save();
            }
        }

        function save() {
            $.ajax({
                url: baseUrl(`payroll/employee/save_employee`),
                type: 'POST',
                dataType: 'JSON',
                data,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response) {
                        const data = response.data;
                        const toast = response.success ? 'success' : 'warning';
                        toastr[toast](response.message, response.title, {timeOut: '10000'});

                        setTimeout(() => {
                            window.location.assign(baseUrl(`payroll/employee/edit_employee_masterfile/${data.id}`));
                        }, 1500);
                    }
                }
            });
        }

        return false;
    }
});

$('#company, #department, #position, #work-status')
    .select2({
        width: '100%',
        placeholder: 'LIST OF OPTIONS'
    });

$('#biometricno')
    .on('blur', function () {
        checkBiometricNo($(this).val());
    });

$('.close', $('.alert-biometric-exist'))
    .on('click', function () {
        $('.alert-biometric-exist').fadeOut();
    });

function checkBiometricNo(biometricno) {
    $.ajax({
        url: baseUrl(`payroll/employee/check_biometric_no/${biometricno}`),
        type: 'GET',
        dataType: 'JSON',
        global: false,
        success: function (response) {
            biometricNoExist = response.employee_list.length >= 1;
            existInPersonnelList = response.personnel_list.length >= 1;

            if (biometricNoExist) {
                $('.alert-biometric-exist').removeClass('alert-info').addClass('alert-danger');
                $('.alert-biometric-exist__message')
                    .html(`<span class="m--font-bolder">
                                    <strong>
                                        Oh Snap!
                                    </strong>
                                    Biometric No. 
                                    <span class="m--font-boldest2">${biometricno}</span> 
                                    already exists in HRIS's Employee List.
                               </span>`);
                $('.alert-biometric-exist').fadeIn();
            } else {
                $('.alert-biometric-exist').fadeOut();
            }
        }
    });
}

$('#m_datepicker_2')
    .datepicker({
        todayHighlight: true,
        orientation: 'bottom left',
        autoclose: true,
        templates: {
            leftArrow: '<i class="la la-angle-left"></i>',
            rightArrow: '<i class="la la-angle-right"></i>'
        },
        format: 'yyyy-mm-dd'
    });

$('#is-laborer')
    .on('input', function () {
        const checked = $(this)[0].checked;
        if (checked) {
            const option = $('#position').find('option:contains("LABORER")');
            const value = $(option)[0].value;
            $('#position').val(value).trigger('change');
        } else {
            $('#position').val(null).trigger('change');
        }
    });

$('#classification')
    .select2({
        width: '100%',
        placeholder: 'LIST OF OPTIONS'
    })
    .on('select2:select', function (e) {
        const data = e.params.data;
        const workStatus = $('#work-status');
        workStatus.find('option').remove();
        if (data.text === 'ACTIVE') {
            workStatus.append(activeStatusOptions);
        } else {
            workStatus.append(inactiveStatusOptions);
        }

        workStatus.removeAttr('disabled');
    });

$('#file-image')
    .on('change', function () {
        const file = this.files[0];
        const image_holder = $('#image--holder');

        if (file && file !== undefined) {
            const name = file.name;

            resizeImage({file, maxSize: 250})
                .then((resizedImage) => {
                    const reader = new FileReader();
                    reader.readAsDataURL(resizedImage);
                    reader.onloadend = function () {
                        const base64data = reader.result;
                        image_holder.attr('src', base64data);
                        image = {base64data, name};
                    }
                })
                .catch((err) => {
                });
        } else {
            image_holder.attr('src', baseUrl(`assets/images/profile/no_image.jpg`));
        }
    });