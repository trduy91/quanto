var currentQuestion = 0;
function allowDrop(ev) {
    ev.preventDefault();
}

function dragQuestion(ev, type, id) {
    console.log('drag', ev);
    var dragData = JSON.stringify({type, id});
    ev.dataTransfer.setData("question", dragData);
}

function dragAnswer(ev, type, answer) {
    var dragData = JSON.stringify({type, answer});
    ev.dataTransfer.setData("answer", dragData);
}

function dropQuestion(ev) {
    ev.preventDefault();
    if (ev.dataTransfer.getData("question")) {
        var data = JSON.parse(ev.dataTransfer.getData("question"));
        var id = data.id;
        var type = data.type;
        if (type === DRAG_TYPE.QUESTION) {
            $('#addQuestionDropArea').html(renderQuestion(id));
        }
    }


}

function dropAnswer(ev) {
    if (ev.dataTransfer.getData("answer")) {
        var data = JSON.parse(ev.dataTransfer.getData("answer"));
        var answer = data.answer;
        var type = data.type;
        if (type === DRAG_TYPE.ANSWER) {
            $(ev.target).html(renderAnswer(answer));

        }
    }

}

function onNewField( a_index, id, currentQ) {

    var answerArea = $(`#question_${currentQ} #_answer_${a_index}`);
    var referralOption = '<option value=""></option>';
    referral_info.forEach((info) => {

        referralOption += `<option value="${info.id}">${info.name}</option>`

    })
    var index = answerArea.find('.card-body > div').length+1;
    answerArea.find('.card-body').append(`
        <div class="col-6 p-1">
            <input type="hidden" value="${id}" name="questions[q_${currentQ}][answers][a_${index}][type]">
            <label>回答</label>
            <input placeholder="回答" class="form-control" name="questions[q_${currentQ}][answers][a_${index}][title]" required />
            <label>値</label>
            <input class="form-control" name="questions[q_${currentQ}][answers][a_${index}][value]" placeholder="価格" />
            <label>関連情報</label>
            <select class="form-control" name="questions[q_${currentQ}][answers][a_${index}][referral_info]" id="referralInfo">
                ${referralOption}
            </select>
            <label>回答写真</label>
            <input type="file" class="form-control mb-2" name="questions[q_${currentQ}][answers][a_${index}][file_url]">
        </div>
    `);
}

function onEdit(id) {
    currentQuestionId = $(`#${id}`).find('.questionID').val();
    $('#modalAddQuestion').modal('toggle');
    $('.dropArea').html($(`#${id}`).parent().html());
    $('.dropArea').find('button').css("display", "block");
    $('.dropArea').find('.buttonEdit').css("display", "none");
    $('.dropArea').find('input').prop("readonly", false);
    $('.dropArea').find('input[type="file"]').prop("disabled", false);
    $('.dropArea').find('textarea').prop("readonly", false);
    $('.dropArea').find('select').prop("disabled", false);
    $('.dropArea').find('select').prop("readonly", false);
    // $('.dropArea').find('select#referralInfo').prop('disabled', false);

}

function renderAnswer(answer) {
    var id = answer.id;
    var a_index = $(`#answers_${currentQuestion} .answerDropArea > div`).length + 1;
    var referralOption = '<option value=""></option>';
    referral_info.forEach((info) => {

        referralOption += `<option value="${info.id}">${info.name}</option>`

    })
    if (id === 4 || id === 5) {
        //checkbox answer
        //radio answer
        return `
        <div class="answer card mr-2 mb-2" id="_answer_${a_index}">
            <div class="card-header d-flex justify-content-between p-2">
                <span>回答 ${answer.name}</span>
                <button type="button" class="text-danger buttonDeleteAnswer" onclick="onDelete('_answer_${a_index}')"><i class="fa fa-times"></i></button>
            </div>
            <div class="card-body p-2 row">
                <input type="hidden" value="${id}" name="questions[q_${currentQuestion}][answers][a_${a_index}][type]">
                <div class="col p-1">
                    <label>回答</label>
                    <input placeholder="回答" class="form-control" name="questions[q_${currentQuestion}][answers][a_${a_index}][title]" required />
                    <label>値</label>
                    <input class="form-control" name="questions[q_${currentQuestion}][answers][a_${a_index}][value]" placeholder="価格" />
                    <label>関連情報</label>
                    <select class="form-control" name="questions[q_${currentQuestion}][answers][a_${a_index}][referral_info]" id="referralInfo">
                    ${referralOption}
                    </select>
                    <label>回答写真</label>
                    <input type="file" class="form-control mb-2" name="questions[q_${currentQuestion}][answers][a_${a_index}][file_url]">

                </div>


            </div>
            <button class="btn btn-primary" onclick="onNewField(${a_index}, ${id}, ${currentQuestion})"><i class="fa fa-plus"></i></button>
        </div>
        `;
    }

    //text input answer
    return `
        <div class="answer card mr-2 mb-2" id="_answer_${a_index}">
            <div class="card-header d-flex justify-content-between p-2">
                <span>回答 ${answer.name}</span>
                <button type="button" class="text-danger buttonDelete" onclick="onDelete('_answer_${a_index}')"><i class="fa fa-times"></i></button>
            </div>
            <div class="card-body p-2 row">
                <div class="col">
                    <input type="hidden" value="${id}" name="questions[q_${currentQuestion}][answers][a_${a_index}][type]">
                    <textarea placeholder="回答" class="form-control" name="questions[q_${currentQuestion}][answers][a_${a_index}][title]" required></textarea>
                    <div>
                        <select class="form-control" name="questions[q_${currentQuestion}][answers][a_${a_index}][referral_info]" id="referralInfo">
                        ${referralOption}
                        </select>
                    </div>
                </div>
            </div>
        </div>
        `;

}

function renderQuestion(id) {
    q_index = $('#questions-container').children('.question').length + 1;
    while ($(`#question_${q_index}`).length > 0) {
        q_index++;
    }
    currentQuestion = q_index;
    var referralOption = '<option value=""></option>';
    referral_info.forEach((info) => {

        referralOption += `<option value="${info.id}">${info.name}</option>`

    })
    return `
            <div class="question" id="question_${q_index}">
                <input type="hidden" value="${id}" name="questions[q_${q_index}][type]">

                <div class="row form-group ">
                    <label  class="ml-2 pl-1 col-form-label d-flex al{q_index}:ign-items-center">質問</label>
                    <div class="col-md-8">
                        <input type="text" placeholder="質問" class="form-control" name="questions[q_${q_index}][title]" required>
                    </div>
                    <div class="col-md-1 d-flex align-items-center">
                        <button type="button" class="btn btn-danger buttonDelete" onclick="onDelete('question_${q_index}')"><i class="fa fa-times"></i></button>
                        <button type="button" class="btn btn-primary buttonEdit" style="display: none" onclick="onEdit('question_${q_index}')"><i class="fa fa-pen"></i></button>
                    </div>
                </div>
                <div class="row form-group ">
                    <label  class="ml-2 pl-1 col-form-label d-flex al{q_index}:ign-items-center">質問コード</label>
                    <div class="col-md-8">
                        <input type="text" value="q_${q_index}" name="questions[q_${q_index}][question_code]" />
                    </div>
                </div>
                <div class="row form-group ">
                    <label  class="ml-2 pl-1 col-form-label d-flex al{q_index}:ign-items-center">関連情報</label>
                    <div class="col-md-8">

                        <select class="form-control" name="questions[q_${q_index}][referral_info]" id="questionReferralInfo">
                        ${referralOption}
                        </select>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md">
                        <input type="file" class="form-control" value="" name="questions[q_${q_index}][file_url]">
                    </div>
                </div>
                <div class="d-flex mb-2">
                    <div id="answers_${q_index}" class="d-flex answerDropArea flex-wrap" ondrop="dropAnswer(event)">

                    </div>
                </div>
            </div>
        `;
}





function dropFormular(event) {

    if (event.dataTransfer.getData("formular")) {
        var data = JSON.parse(event.dataTransfer.getData("formular"));

        var value = data.value;
        var type = data.type;
        console.log(type);
        console.log(value);
        var textarea = $('#formularSetting textarea');
        textarea.val(textarea.val() + renderFormular(type, value));
    }
}

function dragFormular(ev, type, value) {
    var dragData = JSON.stringify({type, value});
    ev.dataTransfer.setData("formular", dragData);

}

function formClear(){
    $('#formularSetting textarea').val('');
}

function renderFormular(type, value) {
    switch (type) {
        case 'question':
            return `${value}`;
        case 'math':
            if (value === 'plus')
                return ' + ';
            if (value === 'minus')
                return ' - ';
            if (value === 'multiple')
                return ' * ';
            if (value === 'divide')
                return ' / ';
            if (value === 'open')
                return ' ( ';
            if (value === 'close')
                return ' ) ';
        default:
            return '';
    }
}

function submitFormular(){
    var formular = $('#formularSetting textarea').val();
    $('#formularSetting #formularValue').val(encodeURIComponent(formular));
    $('#formularSetting').submit();
}
