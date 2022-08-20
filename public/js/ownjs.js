/**
 * For upload file should add have these property
 processData: false,
 contentType: false,
 * @param Object btn elemetn or jQuery Objec
 * @param array pilihan type, url, proccessData:false,contentType:false,dtType:'text',
 *              async:false,done,beforeSend,errMsg
 * @returns {gAjax}
 */
 var vAjax = function (i, pilihan) {
    var cls = '', option = {}, pst = '', heigth = 0, width = 0;
    if ((i != '') && (i != 'undefined') && (i != null)) {
        if (i.parent().hasClass('btn')) {
            i.parent().attr('disabled', 'disabled');
        }
        cls = i.attr('class');
    }
    if ($('.page-content').length > 0) {
        pst = $('body').offset(), heigth = $('body').css('height'), width = $('body').css('width');
        option = {
            position: 'absolute',
            top: pst.top,
            left: pst.left,
            height: heigth,
            width: width,
            "z-index": 9999,
            "text-align": 'center',
            "background-color": 'rgba(255,255,255,0.5)'
        };
    }
    let setting = $.extend({
        type: 'GET',
        url: '',
        dataType: 'text',
        async: false,
        data: {},
        done: function (ss) {
        },
        beforeSend: function () {
            if ((i != '') && (i != 'undefined') && (i != null)) {
                i.removeClass().addClass('fa fa-spin fa-spinner');
                if (i.parent().hasClass('btn')) {
                    i.parent().attr('disabled', 'disabled');
                }
            } else {
                $('#divOverlay').css(option).removeClass('hidden');
            }
        },
        error: function (e,status) {
            let er = e.responseJSON, msg = '', res = '';
            console.error('Ownjs {'+status+'} :: '+e.statusText);
            if(typeof er.errors !== 'undefined'){
                let firstkey = Object.keys(er.errors)[0];
                msg = er.errors[firstkey][0];;
                res = er.message + ' : <b>' + msg+'</b>'
            } else {
                res = 'Error ('+status+') <b>'+er.exception+'</b>';
                console.log(er.message)
            }
            msgAlert(res);
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    }, pilihan);
    setting.success = setting.done;
    delete setting.done;
    // {
    //     url: setting.url,
    //     dataType: setting.dataType,
    //     type: setting.type,
    //     beforeSend: setting.beforeSend,
    //     processData: setting.processData,
    //     contentType: setting.contentType,
    //     success: setting.done,
    //     data: setting.data,
    //     async: setting.async,
    //     error: setting.error,

    // }
    return $.ajax(setting).always(function () {
        if ((i != '') && (i != 'undefined') && (i != null)) {
            i.removeClass().addClass(cls);
            if (i.parent().hasClass('btn')) {
                i.parent().removeAttr('disabled');
            }
        } else {
            $('#divOverlay').addClass('hidden').removeAttr('style');
        }
    });
};

var refreshTableServerOn = function (a, url, setCol, i, orderCol = []) {
    var cls = '';
    if ((i != '') && (i != 'undefined') && (i != null)) {
        if (i.parent().hasClass('btn')) {
            i.parent().attr('disabled', 'disabled');
        }
        cls = i.attr('class');
    }
    $(a).DataTable().destroy();
    return $(a).DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url:url,
            beforeSend: function () {
                if ((i != '') && (i != 'undefined') && (i != null)) {
                    i.removeClass().addClass('fa fa-spin fa-spinner');
                    if (i.parent().hasClass('btn')) {
                        i.parent().attr('disabled', 'disabled');
                    }
                }
            }
        },
        drawCallback: function() {
            if ((i != '') && (i != 'undefined') && (i != null)) {
                i.removeClass().addClass(cls);
                if (i.parent().hasClass('btn')) {
                    i.parent().removeAttr('disabled');
                }
            }
         },
        columns: setCol,
        order: orderCol,
    });
};

var showModal = function(e){
    return $(e).modal({
        backdrop: 'static',
        keydrop: false
    }).on('hidden.bs.modal', function () {
        $(this).remove();
    });
}


$(document).on('click', '.modal-dismiss', function (e) {
    e.preventDefault();
    $.magnificPopup.close();
});

let msgAlert = function (message) {
    new PNotify({
        title: 'Alert Message',
        text: message,
        type: 'error',
        shadow: true,
        icon : 'fa fa-exclamation-triangle'
    });
}

let msgSuccess = function(message){
    new PNotify({
        title: 'Success Message',
        text: message,
        type: 'success',
        shadow: true,
        icon : 'fa fa-check'
    });
}

let msgInfo = function(message){
    new PNotify({
        title: 'Info Message',
        text: message,
        type: 'info',
        shadow: true,
    });
}

let toFormData = function(formObject){
    var foData = new FormData();
    let serializeData = $(formObject).serializeArray();
    $.each(serializeData,function(i,e){
        foData.append(e.name, e.value);
    });
    $(formObject).find('input[type="file"]').each(function(i, e){
        let val = typeof e.files[0] === 'undefined' ? '' : e.files[0];
        foData.append(e.name, val);
    });
    return foData;
}
