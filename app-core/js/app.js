/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 * @version     : 2.2.0
 **/

function focus(id) {
    $('#'+id).focus();
}

function hide(id) {
    $('#'+id).hide();
}

function show(id) {
    $('#'+id).show();
}

function disable(id) {
    $('#' + id).addClass('disabled');
    $('#' + id).attr('disabled', true);
}

function enable(id) {
    $('#'+id).removeClass('disabled');
    $('#'+id).attr('disabled', false);
}

function temp_disable(id) {
    $('#' + id).addClass('disabled');
    setTimeout(function () { $('#' + id).removeClass('disabled'); }, 3000);
}

function remove(id) {
    $('#' + id).remove();
}

function goto_url(url) {
    if(url) { window.location = url; } else { window.location.reload(); }
}

function user_said(txt) {
    alert(txt);
}

function sess_error(txt) {
    swal.fire('', txt);
    setTimeout(function() { goto_url(); }, 500);
}

/* ------ Comm. ---- */

function data_serial(obj) {
    var str = [];
    for(var p in obj) {
        if(obj.hasOwnProperty(p)) {
            str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
        }
    }
    return str.join("&");
}

function get_data(info) {
    $.ajax({
        type: 'POST',
        url: 'get/data-get',
        data: data_serial(info),
        success: function(result) {
            $('#result').html(result);
            return true;
        },
        error: function(result) {
            alert('Error : Unable to process request, Please try again.');
        }
    });
    return false;
}

function dynamic_data(info, fieldName) {
    if(fieldName && fieldName == "modify") {  loader_start(); }
    $.ajax({
        type: 'POST',
        url: 'dynamic/data-dynamic',
        data: data_serial(info),
        success: function(result) {
            if(fieldName && fieldName == "modify") { loader_stop(); }
            $('#result2').html(result);
            return true;
        },
        error: function(result) {
            if(fieldName && fieldName == "modify") { loader_stop(); }
            alert('Error : Unable to process request, Please try again.');
        }
    });
    return false;
}

function dynamic_data2(info) {
    $.ajax({
        type: 'POST',
        url: 'dynamic/data-dynamic',
        data: data_serial(info),
        success: function (result) {
            $('#result2').html(result);
        }
    });
    return false;
}
function post_safe_data(formData, sbt = "sbt") {
    $.ajax({
        type: 'POST',
        url: 'post/data-post',
        data: data_serial(formData),
        success: function(result) {
            $('#result2').html(result);
            return true;
        },
        error: function(result) {
            if(sbt) { enable(sbt); }
            loader_stop();
            alert('Error : Unable to process request, Please try again.');
        }
    });
    return false;
}

function post_data(form, sbt) {
    $.ajax({
        type: 'POST',
        url: 'post/data-post',
        data: $('#'+form).serialize(),
        success: function(result) {
            $('#result2').html(result);
            return true;
        },
        error: function(result) {
            if(sbt) { enable(sbt); }
            loader_stop();
            alert('Error : Unable to process request, Please try again.');
        }
    });
    return false;
}

function deStr(data) {
    var str = document.createElement("textarea");
    str.innerHTML = atob(data);
    return str.value;
}

/** Loader */

function loader_start(id) {
    if(id) { $('#'+id).LoadingOverlay('show'); } else { $.LoadingOverlay('show'); }
}

function loader_stop(id) {
    if(id) { $('#'+id).LoadingOverlay('hide', true); } else { $.LoadingOverlay('hide', true); }
}

/** Modal */

function decode_ajax(html) {
    var txt = document.createElement("textarea");
    txt.innerHTML = atob(html);
    return txt.value;
}

function fetch_modal(fun, modal_size, mode, item = "", item2 = "", item3 = "") {
    if(!modal_size || modal_size == '') { modal_size = "lg" }
    if(!mode || mode == '') { mode = "V" }
    var data = {
        cmd: fun,
        data_mode: mode,
        req_token: window.req_id,
        id: item, id2: item2, id3: item3
    };
    get_modal_data(data, modal_size);
}

function send_form(id = "app-form", btn = "sbt") {
    disable(btn); loader_start(); post_data(id,btn);
}


function get_modal_data(info,m_size) {
    show_dataModal(m_size,'ModalWin','result'); // Open Pop-Up
    $.ajax({
        type: 'POST',
        url: 'get/data-get',
        data: data_serial(info),
        success: function (result) {
            $('#ModalWin-Response').html(result);
            return true;
        },
        error: function (result) {
            $('#ModalWin').modal('hide');
            alert('Error : Unable to process request, Please try again.');
        }
    });
    return false;
}

function show_dataModal(m_size, modal_id = "ModalWin", result_id = "result") {
    if (m_size == "lg") { m_size = "modal-lg"; }
    else if (m_size == "xl") { m_size = "modal-xl"; }
    else if (m_size == "xxl") { m_size = "modal-xxl"; }
    else { m_size = ""; }
    var html_data = "<div class='modal' id='" + modal_id + "' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'><div class='modal-dialog modal-dialog-centered " + m_size + "' role='document'><div class='modal-content'><div class='modal-header'><h5 class='modal-title ml-3' id='" + modal_id + "-ModalLabel'>Loading...</h5><button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div><div id='" + modal_id + "-Response'><div class='modal-body'><div class='row'><div class='col-md-6 form-group'><div class='skeleton-wrapper-body'><div class='skeleton-label'></div><div class='skeleton-content'></div></div></div><div class='col-md-6 form-group'><div class='skeleton-wrapper-body'><div class='skeleton-label'></div><div class='skeleton-content'></div></div></div><div class='col-md-6 form-group'><div class='skeleton-wrapper-body'><div class='skeleton-label'></div><div class='skeleton-content'></div></div></div><div class='col-md-6 form-group'><div class='skeleton-wrapper-body'><div class='skeleton-label'></div><div class='skeleton-content'></div></div></div><div class='col-md-6 form-group d-none d-md-block'><div class='skeleton-wrapper-body'><div class='skeleton-label'></div><div class='skeleton-content'></div></div></div><div class='col-md-6 form-group d-none d-md-block'><div class='skeleton-wrapper-body'><div class='skeleton-label'></div><div class='skeleton-content'></div></div></div><div class='col-md-6 form-group d-none d-md-block'><div class='skeleton-wrapper-body'><div class='skeleton-label'></div><div class='skeleton-content'></div></div></div><div class='col-md-6 form-group d-none d-md-block'><div class='skeleton-wrapper-body'><div class='skeleton-label'></div><div class='skeleton-content'></div></div></div></div></div></div></div></div></div></div>";
    $('#'+modal_id).modal('hide');
    $('#'+modal_id).remove();
    $('#'+result_id).html(html_data);
    $('#'+modal_id).appendTo("body").modal({ show: true, backdrop: 'static', keyboard: true, show: true });
    return true;
    //#ModalWin-ModalLabel #ModalResponse
}

/** Search */
function fetch_help_modal(fun, m_size, destID = "", pageStart = "", filterId = "", filterVal = "") {
    if(!m_size) { m_size = "lg" }
    var info = {
        cmd: fun,
        req_token: window.req_id,
        mSize: m_size,
        dest_id: destID,
        start: pageStart,
        filter: filterId,
        filter_val: filterVal
    };
    show_dataModal(m_size, 'ModalWin-Help', 'result3'); // Open Pop-Up
    $.ajax({
        type: 'POST',
        url: 'search/data-search',
        data: data_serial(info),
        success: function (result) {
            $('#ModalWin-Help-Response').html(result);
            return true;
        },
        error: function (result) {
            $('#ModalWin-Help').modal('hide');
            alert('Error : Unable to process request, Please try again.');
        }
    });
    return false;
}

function on_change(pgm_code, fieldName, fieldVal, destID = "result2") {
    if(pgm_code) {
        if (fieldName && destID) {
            var data = {
                cmd: pgm_code, req_token: window.req_id,
                cmd2: 'onChange', field_name: fieldName, field_val: fieldVal, dest_id: destID
            };
            dynamic_data(data, fieldName);
        }
    } else {
        alert('onChange : Invalid Pgm.');
    }
}

/** ---- Custom ---- */

//On Click Loader
$(".start-loader").on("click", function () {
    loader_start();
});

//Multi Modal Fix
$(document).on('hidden.bs.modal', '.modal', function () {
    $('.modal:visible').length && $(document.body).addClass('modal-open');
});

//No Blank Space
$(document).on('keyup change', '.js-noSpace', function () {
    $(this).val($(this).val().replace(/ +?/g, ''));
});

//Uppercase
$(document).on('keyup change', '.js-toUpper', function () { 
    $(this).val($(this).val().toUpperCase());
});

//Lowercase
$(document).on('keyup change', '.js-toLower', function () { 
    $(this).val($(this).val().toLowerCase());
});

//Only Alphanumeric
$(document).on('keyup change', '.js-alphaNumeric', function () {
    $(this).val(this.value.replace(/[^a-zA-Z0-9]/g, ''));
});

//Only Numbers
$(document).on('keyup change', '.js-isNumeric', function () {
    $(this).val(this.value.replace(/[^0-9\.]/g, ''));
});

//Only Numbers no dots
$(document).on('keyup change', '.js-isnodecNumeric', function () {
    $(this).val(this.value.replace(/[^0-9]/g, ''));
});

// Only Alphanumeric and space
$(document).on('keydown keyup change paste', '.js-alphaNumericspace', function () {
    $(this).val(this.value.replace(/[^a-zA-Z0-9 ]/g, ''));
});

// Auto resize textarea
$(document).on('change keyup keydown paste cut', '.js-autoResize', function () {
    $(this).css("height", "initial");
    $(this).height($(this)[0].scrollHeight - 16);
});

//Remove line breaks in textarea
$(document).on('change keyup keydown paste cut', '.js-noEnter', function () {
    $(this).val($(this).val().replace(/\r?\n/gi, ' '));
});

$(document).on('change keyup keydown paste cut', '.js-noEnter', function (e) {
    if (e.keyCode == 13 && !e.shiftKey) {
        e.preventDefault();
        return false;
    }
});

//Auto Tab
$(document).on('keyup change', '.js-autoTab', function () {
    var $this = $(this);
    setTimeout(function () {
        if($this.val().length >= parseInt($this.attr("maxlength"), 10))
        $this.next("input").focus();
    }, 100);
});

/* Input only decimal and number */
function isOnlyDecimalNumber(event) {
    // Call this fucntion on oninput event only
    var input = event.target;
    var value = input.value;

    var dotCount = (value.match(/\./g) || []).length;

    if (dotCount < 2) {
        input.value = value.replace(/[a-zA-Z$&+,:;=?"/\\[\]@#|{}_'<>^*()%!-]/g, '')
    } else {
        input.value = input.value.slice(0, value.length - 1)
    }
}

// Check max characters
function max_characters(eid) {
    var max_chars = $(eid).attr('maxLength');
    if ($.isNumeric(max_chars)) {
        var t_length = $(eid).val().length;
        $(eid).val($(eid).val().substring(0, max_chars));
        var t_length = $(eid).val().length;
        var remain = max_chars - parseInt(t_length);
        $(eid).next('p').remove();
        $(eid).after('<p class="text-muted small my-1"> Max. ' + remain + ' characters remaining</p>');
    }
}

$(document).on("change keyup keydown paste cut", ".js-maxCheck", function () {
    max_characters($(this));
});

$(".js-maxCheck").each(function () {
    max_characters($(this));
});

$(document).ajaxComplete(function () {
    $(".js-maxCheck").each(function () {
        max_characters($(this));
    });
});

$(document).on('show.bs.modal','#ModalWin', function () {
    OverlayScrollbars(document.querySelector('#ModalWin'), {});
});

//comma
$(document).on('keyup change', '.js-commaSp', function () {
    //$(this).val(this.value.replace(/[^0-9\.]/g, ''));

    var input = this.value.replace(/,/g, '');
    if (input.length < 1)
        $(this).val('');
    else {
        var val = parseFloat(input);
        var formatted = inrFormat(input);
        if (formatted.indexOf('.') > 0) {
            var split = formatted.split('.');
            formatted = split[0] + '.' + split[1].substring(0, 2);
        }
        $(this).val(formatted);
    }
});

// Convert to INR format
function inrFormat(val) {
    var x = val;
    //var x = parseFloat(val).toFixed(2);
    x = x.toString();
    var afterPoint = '';
    if (x.indexOf('.') > 0)
        afterPoint = x.substring(x.indexOf('.'), x.length);
    x = Math.floor(x);
    x = x.toString();
    var lastThree = x.substring(x.length - 3);
    var otherNumbers = x.substring(0, x.length - 3);
    if (otherNumbers != '')
        lastThree = ',' + lastThree;
    var res = otherNumbers.replace(/\B(?=(\d{3})+(?!\d))/g, ",") + lastThree + afterPoint;
    return res;
}

// Reformat Date
function reformatDateString(evt) {
    var date = evt.split(/\D/);
    return date.reverse().join('-');
}

/** Input only Decimal (2 Decimal Places) */
function isDecimalNumber(el, evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    var number = el.value.split('.');
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    //just one dot
    if (number.length > 1 && charCode == 46) {
        return false;
    }
    //get the carat position
    var caratPos = getSelectionStart(el);
    var dotPos = el.value.indexOf(".");
    if (caratPos > dotPos && dotPos > -1 && (number[1].length > 1)) {
        return false;
    }
    return true;
}

function getSelectionStart(o) {
    if (o.createTextRange) {
        var r = document.selection.createRange().duplicate()
        r.moveEnd('character', o.value.length)
        if (r.text == '') return o.value.length
        return o.value.lastIndexOf(r.text)
    } else return o.selectionStart
}