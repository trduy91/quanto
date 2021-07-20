var q_index = 0;
var a_index = 0;
var DRAG_TYPE = {
    QUESTION: 1,
    ANSWER: 2,
};
var currentQuestionId = null;
(function ($) {



    $('#btnAddQuestion').click(function () {
        $('#modalAddQuestion').modal('toggle');
        if ($(`#question_${currentQuestionId}_wrapper`).length > 0){
            //edit saved question
            $(`#question_${currentQuestionId}_wrapper`).html($('.dropArea .question'));
        } else {
            //add new question
            $('#questions-container').append(`
                <div id="question_${q_index}_wrapper"></div>
            `)
            $(`#question_${q_index}_wrapper`).append($('.dropArea .question'));
        }
        // $('#questions-container').append($('.dropArea .question'));
        $('#questions-container').find('button').css("display", "none");
        $('#questions-container').find('.buttonEdit').css("display", "block");
        $('#questions-container').find('.buttonDelete').css("display", "block");
        $('#questions-container').find('input').prop('readonly', true);
        $('#questions-container').find('input[type="file"]').prop('disabled', true);
        $('#questions-container').find('select#referralInfo').prop('disabled', true);
        $('#questions-container').find('select#questionReferralInfo').prop('disabled', true);
        $('#questions-container textarea').prop('readonly', true)

///////////////////////////////

        // if (selectedType == 3) {
        //     $('#questions-container').append(`
        //         <div class="question" id="question_${q_index}">
        //         <input type="hidden" value="${selectedType}" name="questions[q_${q_index}][type]">
        //         <div class="row form-group">
        //
        //         <label  class="ml-2 pl-1 col-form-label d-flex align-items-center">質問${q_ord}:</label>
        //         <div class="col-md-10">
        //         <input type="text" placeholder="質問" class="form-control" value="${textVal}" name="questions[q_${q_index}][title]" required>
        //         </div>
        //         <div class="col-md-1 d-flex align-items-center">
        //         <button type="button" class="btn btn-danger" onclick="onDelete('question_${q_index}')"><i class="fa fa-times"></i></button>
        //         </div>
        //         </div>
        //         <div class="row form-group">
        //         <div class="col-md-10">
        //             <div class="form-group">
        //                 <input type="file" class="form-control-file d-none" name="questions[q_${q_index}][movie_file]" id="questions[q_${q_index}][movie_file]${q_index}">
        //                 <input type="hidden" name="questions[q_${q_index}][movie_file_tmp]" data-name="questions[q_${q_index}][movie_file]${q_index}" data-index="questions[q_${q_index}][movie_file_tmp]" id="questions[q_${q_index}][movie_file_tmp]${q_index}" value="-">
        //                 <label for="questions[q_${q_index}][movie_file]${q_index}" class="form-control-label btn btn-primary">動画を選択してください。</label>
		// 				<p class="text-danger">（* テキスト）</p>
		// 				<a href="https://cloudconvert.com/mov-to-mp4">https://cloudconvert.com/mov-to-mp4</a>
        //             </div>
        //         </div>
        //         <div class="col-md-1">
        //             <div class="form-group">
        //                 <button type="button" class="btn btn-danger" onclick="onDeleteMovie('questions[q_${q_index}][movie_file]', '${q_index}')">
        //                     <i class="fa fa-times"></i>
        //                 </button>
        //             </div>
        //         </div>
        //         <div class="col-md-12 mt-2">
        //         <textarea class="form-control" name="questions[q_${q_index}][movie_source]"  placeholder="ソースコード(iframe)でアップ（1024以下の文字を入力してください。）"></textarea>
        //         </div>
        //         <div class="col-md-12 mt-2">
        //         <input type="text" class="form-control" name="questions[q_${q_index}][movie_url]"  placeholder="URLでアップ(YoutubeなどのURL)">
        //         </div>
        //         </div>
        //         <div class="d-flex mb-2">
        //             <div id="answers_${q_index}" class="d-flex flex-wrap">
        //
        //             </div>
        //             <div class="d-block mb-5">
        //                 <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalAddAnswer" data-answerid="answers_${q_index}">+</button>
        //                 <button type="button" class="btn btn-outline-warning ml-auto" data-toggle="modal" data-target="#modalAddSubQuestion" data-subQueid="answers_${q_index}">+</button>
        //             </div>
        //
        //         </div>
        //         </div>
        //     `);
        //
        //     $("input[type=file]").on('change', function () {
        //         var file = $(this).prop('files')[0];
        //         var id = $(this).attr('id');
        //         $('label[for="' + id + '"]').text(file.name);
        //         $('input[data-name="' + id + '"]').val(file.name);
        //     });
        // }

    });

    $('input[name="background_color"]').change(function () {
        $('#preview').css('background', $(this).val());
    })

    // $('input[name="char_color"]').change(function () {
    //     $('#preview-text').css('background', $(this).val());
    // })

    $('input[name="border_color"]').change(function () {
        $('#preview-img').css('border', `4px solid ` + $(this).val());
    })

    $('#questions-container').on('click', '[data-toggle="modal"]', function (e) {
        let $target = $(e.target);
        let value = $target.data('answerid');
        $('#container-id').val(value);
        $('#sub-container-id').val(value);console.log(value);
    });

    $('#btnAddReferralInfo').on('click', function () {
        $('#modalReferralInfo').modal('toggle');
    });

    $(".modal").on("hidden.bs.modal", function(){
        $("#addQuestionDropArea").html("");
    });

    $("form").on("submit", function() {
        $('#questions-container').find('select').prop('disabled', false);
        $('#questions-container').find('input[type="file"]').prop('disabled', false);
    })

})(jQuery);

function onDelete(id) {
    result = window.confirm('本当に削除しますか？')
    if(result) {
        if ($('#modalAddQuestion').hasClass('show')) {
            $(`.modal-content #${id}`).remove();
        } else {
            $(`#questions-container #${id}`).remove();
        }

    }
}

function onSubDelete(id) {
    result = window.confirm('本当に削除しますか？')
    if(result) {
        $('#_sub_que_' + id).remove();
    }

    $('button[data-answerid=subQues-div' + id + ']').removeAttr('disabled');
}

$(function () {
    $("input[type=file]").on('change', function () {
        var file = $(this).prop('files')[0];
        var id = $(this).attr('id');
        if(file.size > 10485760) {
            alert('10MB以下のファイルを選択してください。');
            return;
        }

        $('label[for="' + id + '"]').text(file.name);
        $('input[data-name="' + id + '"]').val(file.name);
    });
});

function onDeleteMovie(name, id) {
    $('[name="' + name + '"]').val('');
    $('[data-index="' + name + '"]').val('-');
    $('label[for="' + name + id + '"]').text('動画を選択してください。');

}

function editReferralInfo(info) {
    $('#txtReferralEditID').val(info.id);
    $('#txtReferralEditName').val(info.name);
    $('#txtReferralEditInfo').html(info.info).text();
    $('#modalEditReferralInfo').modal('toggle');
}

function formularSetting() {
    $("#survey").append(`
        <input type="hidden" name="surveyRedirect" value="admin.formularSetting">
    `);
    var title = document.getElementsByName('title');
    if (title[0].value !== ''){
        $("#survey").submit();
    }else {
        alert('フォームを入力してください');
    }

}
