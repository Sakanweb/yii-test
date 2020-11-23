var fileNames = [];
var fileData = [];
var responseCount = 0;
const STATUS_NEW = `<span class="text-danger">NEW</span>`;
const STATUS_PROCESSING = `<span class="text-primary">PROCESSING</span>`;
const STATUS_DONE = `<span class="text-success">DONE</span>`;

$(document).ready(function () {
    $('.alert').alert()

    let max_fields = maximum_number_imports;
    let wrapper = $("#input_fields_wrap");
    let add_button = $("#add_field_button");
    let x = 1;
    $(add_button).on("click", function (e) {
        e.preventDefault();
        if (x < max_fields) {
            x++;
            $(wrapper).append(`
                <div class="form-group">
                    <input type="file" class="form-control" name="importFile[]" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" >
                    <a href="#" class="remove_field">Remove</a>
                </div>`);
        }
    });

    $(wrapper).on("click", ".remove_field", function (e) {
        e.preventDefault();
        let currentFileVal = $(this).parent().find('input[type=file]').val().split('\\').pop();
        $(this).parent('div').remove();
        x--;
        if (currentFileVal !== "") {
            let index = getKeyByValue(fileNames, currentFileVal);
            fileNames.splice(index, 1);
            fileData.splice(index, 1);
            updateImportList()
        }
    });
});

$(document).on("change", 'input[type=file]', function () {
    let val = $(this).val().split('\\').pop();
    console.log(val);
    if(!check_file(val)) {
        alert(`File type is not correct!`);
        $(this).val("")
        return;
    }
    updateImportsFileNames();
});

$('#upload').on('click', () => {
    if (fileNames.length > maximum_number_imports) {
        alert(`You can only upload a maximum of ${maximum_number_imports} files`);
        return;
    } else if (fileNames.length === 0) {
        alert("Import File is required!");
        return;
    }
    let storeName = $("input[name=storeName]").val();

    if (storeName.trim() === "") {
        alert(`Store Name is required!`);
        return;
    }

    let _csrf = $("input[name=_csrf]").val();

    let actionUrl = $('#fileform').attr('action');
    let form_data = "";
    $("#upload").prop("disabled", true);

    for (let index = 0; index < fileData.length; index++) {
        let formData = "";
        formData = new FormData();
        formData.append("Import[importFile]", fileData[index][0]);
        formData.append("_csrf", _csrf);
        formData.append("storeName", storeName);

        importRequest(actionUrl, formData, index);
    }
});


function importRequest(actionUrl, formData, fileIndex) {
    $.ajax({
        url: actionUrl,
        cache: false,
        contentType: false,
        processData: false,
        async: true,
        data: formData,
        type: 'POST',
        dataType: 'json',
        xhr: function () {
            let xhr = $.ajaxSettings.xhr();
            if (xhr.upload) {
                xhr.upload.addEventListener('progress', function (event) {
                    changeTextStatus(fileIndex, STATUS_PROCESSING)
                }, false);
            }
            return xhr;
        },
        success: function (res, status) {
            if (status == 'success') {
                $('#fileList_' + fileIndex).find("td:eq(1)").html(STATUS_DONE);
                responseCount++;
                //doRedirect();
            }
        },
        error: function (res) {
            console.log(res)
        }
    })
}

function updateImportList() {
    let str = "";
    $.each(fileNames, function (i) {
        str += `<tr id="fileList_${i}"><td>${fileNames[i]}</td><td>${STATUS_NEW}</td></tr>`;
    });
    $("#importList").html(str)
}

function updateImportsFileNames() {
    fileNames = [];
    fileData = [];
    $('input[type=file]').each(
        function (index) {
            let val = $(this).val().split('\\').pop();
            let file = $(this).prop("files")
            if (val !== "") {
                fileNames.push(val);
                fileData.push(file);
            }
        }
    );
    updateImportList();
}

function getKeyByValue(object, value) {
    return Object.keys(object).find(key => object[key] === value);
}

/**
 *
 * @param file
 * @returns {boolean}
 */
function check_file(file) {
    let ext = file.split(".");
    ext = ext[ext.length - 1].toLowerCase();
    if (file_types.lastIndexOf(ext) == -1) {
        return false;
    } else {
        return true;
    }
}

function changeTextStatus(fileIndex, status) {
    $('#fileList_' + fileIndex).find("td:eq(1)").html(status)
}

function doRedirect() {
    setTimeout(() => {
        window.location.pathname = redirect_url;
        }, 3000
    );
}