/* javascriptのコードを記載 */

function newline(str) {
    str = str.replace(/(?:\r\n|\r|\n)/g, '<br>');
    return str;
}

// var serverHost = 'http://formstylee.com/public/';
var serverHost = 'https://quanto3.com';
// var serverHost = 'http://localhost/quanto';

var currentTab = 0;
var formular = '';
var scope = {};
var calculate = null;
var total = 0;

var current = 1;
var delay_time = 1500;
var total_questions = -1;
var question_order = -1;
var profile_img_url = '';
var avart_name = '';
var progress_status = -1;
var initial_data = null;
var gradient_attrs = [
    'linear-gradient(0deg, rgba(255,255,255,1) 0%, rgba(255,255,255,1) 100%)',
    'linear-gradient(160deg, rgba(63,227,220,1) 0%, rgba(51,129,251,1) 50%, rgba(112,109,246,1) 100%)',
    'linear-gradient(90deg, rgba(250,116,149,1) 0%, rgba(253,223,65,1) 100%)',
    'linear-gradient(130deg, rgba(240,147,251,1) 0%, rgba(244,87,108,1) 100%)',
    'linear-gradient(160deg, rgba(32,211,252,1) 0%, rgba(182,32,254,1) 100%)',
    'linear-gradient(180deg, rgba(243,244,135,1) 0%, rgba(151,250,194,1) 100%)'
];

function addQ() {
    $('#content .row-a').each(function() {
        $(this).css('min-height', '0');
    });

    var bouncing_html = '<div class="loadingContainer"><div class="ball1"></div><div class="ball2"></div><div class="ball3"></div></div>';
    var q_html = '<div id="q-' + current + '" class="row row-q" style="display: none;">';
    // q_html += '<div class="avatar"><img src="./assets/img/avatar-default.jpg" alt="" /></div>';
    // q_html += '<div class="avatar-wrapper"><div class="avatar"><img src="' + profile_img_url + '" alt="" /></div><p class="avatar-name">' + avart_name + '</p></div>';
    q_html += '<div class="avatar-wrapper"><div class="avatar" style="background: url(' + profile_img_url+ ');"></div><p class="avatar-name">' + avart_name + '</p></div>';

    var min_height = 'style="min-height: calc(100vh - ' + $('.site-header').outerHeight() + 'px);"';
    q_html += '<div class="q-area"' + min_height + '>';
    q_html += '<div id="q-txt-row-main" class="q-txt-row"><div id="q-txt-main" class="q-txt">' + bouncing_html + '</div></div>';
    q_html += '<div id="q-txt-row-sub" class="q-txt-row"><div id="q-txt-sub" class="q-txt">' + bouncing_html + '</div></div>';
    q_html += '<div class="q-a-area" style="display: none;"></div></div></div>';
    q_html += '</div>';
    $('#chatview').append(q_html);
}

function addA(a_height = 0) {
    var bouncing_html = '<div class="loadingContainer"><div class="ball1"></div><div class="ball2"></div><div class="ball3"></div></div>';
    var q_html = '<div id="a-' + current + '" class="row row-a" style="min-height: ' + a_height + 'px;">';
    q_html += '<div class="a-area"><div class="a-txt">' + bouncing_html + '</div></div>';
    q_html += '<input id="a-input-' + current + '" type="hidden" name="answers[]" />';
    q_html += '</div>';
    $('#chatview').append(q_html);
}

$(document).ready(function() {
    $('#progress-row').hide();
    if (survey_id == -1) return;

    url = serverHost + '/api/v1/survey/get/' + survey_id;
    var request = $.get(url, function(data) {

        $('#brand').css('background-image', 'url('+ serverHost +'/' + data.brand_logo_path + ')');
        $('#title span').text(data.title);
        $('#brand-name').text(data.brand_name);

        if (data.brand_description != null && data.brand_description != "") {
            $('#brand-desc').show();
            $('#brand-desc').html(newline(data.brand_description));
        }

        if (data.description != null && data.description != "") {
            $('#description').show();
            $('#description').html(newline(data.description));
        }

        if (data.callout_color == null || data.callout_color == "") {
            data.callout_color = "#ffffff";
        }

        profile_img_url = serverHost + '/' + data.user_profile_url;
        avart_name = data.user_profile_name;
        progress_status = data.progress_status;

        // add_first_question(data);
        initial_data = data;

        total_questions = data.question_count;
        gradient_idx = data.gradient_color;
        var prog_html = '';
        for (i = 0; i < total_questions; i++) {
            prog_html += '<div class="prog-bar-wrap"><div id="prog-bar-' + (i + 1) + '" class="prog-bar"><span></span></div></div>';
        }
        $('#progress-inner').append(prog_html);

        var prog_bar_w = (100 / total_questions) + "%";

        style_string = '<style>';
        style_string += 'body { background-color: ' + data.background_color + ';}';
        // style_string += '.row.row-q .avatar-wrapper .avatar-name {color: ' + data.char_color + ';}';
        style_string += '.row.row-q .q-area .q-txt-row .q-txt {color: ' + data.char_color + '; background: ' + data.callout_color + ';}';
        style_string += '.row.row-q .q-area .q-txt-row .q-txt:before { border-color: transparent ' + data.callout_color + ' transparent transparent;} ';
        // style_string += '.row.row-q .q-area .q-txt-row .q-txt:after { border-color: transparent ' + data.callout_color + ' transparent transparent;} ';
        // style_string += '.row.row-q .q-area .q-a-area .q-a-list { background: rgba(' + hexToRgb(data.border_color).r + ',' + hexToRgb(data.border_color).g + ',' + hexToRgb(data.border_color).b + ', 0.3);}';
        style_string += '.row.row-q .q-area .q-a-area .q-a-list { background: ' + gradient_attrs[gradient_idx] + ';}';
        style_string += '.row.row-q .q-area .q-a-area .q-a-list .q-a-item { color: ' + data.char_color + '; background: ' + data.callout_color + ';}';
        style_string += '.row.row-q .q-area .q-a-area .q-a-list .q-a-item.selected { border-color: ' + data.border_color + ';}';
        // style_string += '.row.row-q .q-area .q-a-area .q-a-form-fields { background: rgba(' + hexToRgb(data.border_color).r + ',' + hexToRgb(data.border_color).g + ',' + hexToRgb(data.border_color).b + ', 0.3);}';
        style_string += '.row.row-q .q-area .q-a-area .q-a-form-fields { background: ' + gradient_attrs[gradient_idx] + ';}';
        style_string += '.row.row-q .q-area .q-a-area .q-a-form-fields p {color: ' + data.char_color + ';}';
        style_string += '.row.row-q .q-area .q-a-area input[type=submit] { border-color: ' + data.border_color + ';} ';
        style_string += '.row.row-a .a-area .a-txt {color: ' + data.char_color + '; background: ' + data.callout_color + ';}';
        style_string += '.row.row-a .a-area .a-txt:before { border-color: transparent transparent transparent ' + data.callout_color + ';} ';
        // style_string += '.row.row-a .a-area .a-txt:after { border-color: transparent transparent transparent ' + data.callout_color + ';} ';

        style_string += 'header .site-header { background-color: ' + data.background_color + ';} ';
        style_string += 'header .btn-start { color: ' + data.char_color + '; background: ' + data.callout_color + ';} ';
        style_string += 'header .title-desc .title span { color: ' + data.char_color + '; background: ' + data.callout_color + ';} ';
        // style_string += 'header .title-desc .description { color: ' + data.char_color + ';} ';
        style_string += 'header .brand-name { color: ' + data.char_color + ';} ';
        style_string += 'header .brand-desc { color: ' + data.char_color +  '; background: ' + data.callout_color + ';} ';

        style_string += 'header .progress-row .point { background-color: ' + data.border_color + ';} ';
        style_string += 'header .progress-row .progress-inner .prog-bar-wrap { width: ' + prog_bar_w + ';} ';
        /* style_string += 'header .progress-row .progress-inner .prog-bar-wrap { border-color: ' + data.border_color + ';} ';
        style_string += 'header .progress-row .progress-inner .prog-bar-wrap:nth-child(1) { border-color: ' + data.border_color + ';} '; */
        style_string += 'header .progress-row .progress-inner .prog-bar-wrap .prog-bar { background: linear-gradient(to left, #d9d9d9 50%, ' + data.border_color + ' 50%) right;} ';
        style_string += 'header .progress-row .progress-inner .prog-bar-wrap .prog-bar.confirmed span { background: ' + data.border_color + ';}';
        style_string += '.answer-select {border-color: '+ data.border_color +';}';
        style_string += '.answer-select.selected {background-color: '+ data.callout_color +';}';
        style_string += '</style>';
        $('head').append(style_string);

        setTimeout(function() {
            $('#loading-area').hide();
        }, 100);
    })
    .done(function(data) {
    })
    .fail(function() {
    })
    .always(function() {

    });

});

function showTab(n) {
    var x = document.getElementsByClassName("tab");
    x[n].style.display = "block";
    var selected = x[n].getElementsByClassName('selected');
    if (selected.length > 0){
        document.getElementById("nextBtn").disabled = false;
    } else {
        document.getElementById("nextBtn").disabled = true;
    }

    if (n == 0) {
        document.getElementById("prevBtn").style.display = "none";
    } else {
        document.getElementById("prevBtn").style.display = "inline";
    }
    if (n == (x.length - 1)) {
        document.getElementById("nextBtn").style.display = "none";
        document.getElementById("submitBtn").style.display = "inline";
    } else {
        document.getElementById("nextBtn").innerHTML = "次へ";
        document.getElementById("nextBtn").style.display = "inline";
        document.getElementById("submitBtn").style.display = "none";
    }
}

function nextPrev(n) {
    $('[data-toggle="popover"]').popover("hide");
    var nextQuestion = $('#nextBtn').data('nextQuestion');
    var x = document.getElementsByClassName("tab");
    if (nextQuestion == null || nextQuestion == 0){
        // Hide the current tab:
        x[currentTab].style.display = "none";
        // Increase or decrease the current tab by 1:
        currentTab = currentTab + n;

        showTab(currentTab);
    } else {
        var y = [].slice.call(x);
        currentTab = y.indexOf(x[`q_${nextQuestion}`]);
        showTab(currentTab);
    }


}


$('#btn-start').click(function() {
    $('#btn-start').hide();
    $('#brand-desc').slideUp(100);
    $('#title-desc').slideUp(100);


    setTimeout(function() {
        displaySurvey(initial_data)

        showTab(currentTab)
    }, 100);

});


function displaySurvey(data) {
    var questionData = data.questions;
    var settings = data.settings;
    var prefix = '';
    if (settings != null && settings != ''){
        var settingArr = JSON.parse(settings);
        formular = settingArr['formular'] ? decodeURIComponent(settingArr['formular']) : null;
        prefix = settingArr['prefix'];


    }
    var q_html = '';
    if (questionData.length > 0) {
        questionData.forEach(q => {
            var q_settings = q.settings;
            if (q_settings && q_settings != '') {
                var q_settings_array = JSON.parse(q_settings);
                var q_code = q_settings_array['question_code'] ?  q_settings_array['question_code']  : 'q_'+q.id ;
                scope = {...scope, [q_code]: 0};
            }

            var q_referral = data.referral.filter(re => re.id == q.referral_info);
            q_html += `
            <div class="tab" id="q_${q.id}">質問
              <div class="question">
                <div class="title">${q.title}
                ${q.file_url ? (
                  `
                    <div class="question-image">
                      <img src="${serverHost}/${q.file_url}">
                    </div>
                  `
                )  : ''}
                </div>
                <input type="hidden" id="answer_q_${q.id}" name="answers['${q.id}']">
                ${q_referral.length > 0 ? (
                    `
                    <span class="referralInfo" data-toggle="popover" data-html="true" data-title="${q_referral[0].name}" data-content="${q_referral[0].info}">
                        <i class="fas fa-info-circle"></i>
                    </span>
                    `
                ) : ''}
              </div>
              <div class="answerList">
                ${renderAnswer(data.answers, q.id, q.answer_align)}
              </div>
            </div>
            `;
        })
        if (formular) {
            calculate = math.compile(formular);
            total = calculate.eval(scope);
        }


        q_html += `
            <div class="tab">
                <div class="q-a-form-fields-wrapper">
                    <div class="q-a-form-fields">

                        <div class="input-group mb-3">
                          <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-default">メールアドレス</span>
                          </div>
                          <input type="email" required placeholder="test@mail.com" name="email" class="form-control" aria-label="メールアドレス" aria-describedby="inputGroup-sizing-default">
                        </div>
                        <div class="input-group mb-3">
                          <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-default">お名前</span>
                          </div>
                          <input type="text" required placeholder="山田 太郎" name="name" class="form-control" aria-label="メールアドレス" aria-describedby="inputGroup-sizing-default">
                        </div>
                        <div class="input-group mb-3">
                          <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-default">郵便番号</span>
                          </div>
                          <input type="text" placeholder="111-1111" name="zip_code" class="form-control" aria-label="メールアドレス" aria-describedby="inputGroup-sizing-default" onKeyUp="AjaxZip3.zip2addr(this,\'\',\'address\',\'address\');">
                        </div>
                        <div class="input-group mb-3">
                          <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-default">住所</span>
                          </div>
                          <input type="text" placeholder="" name="address" class="form-control" aria-label="メールアドレス" aria-describedby="inputGroup-sizing-default">
                        </div>
                        <div class="input-group mb-3">
                          <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-default">電話番号</span>
                          </div>
                          <input type="tel" placeholder="03-1234-5678" name="phone_number" class="form-control" aria-label="メールアドレス" aria-describedby="inputGroup-sizing-default">
                        </div>
                    </div>
                </div>
            </div>
        `
        q_html += `
              <div class="controlButton" style="overflow:auto;">
                <div style="float:right;">
                  <button type="button" id="prevBtn" class="btn btn-secondary" disabled onclick="nextPrev(-1)">戻る</button>
                  <button type="button" id="nextBtn" class="btn btn-primary" disabled data-nextQuestion="" onclick="nextPrev(1)">次へ</button>
                  <button type="submit" id="submitBtn" class="btn btn-primary" style="display: none">送信</button>
                </div>
              </div>`
        console.log(formular);
        if (formular!= undefined) {
            q_html += `
                <div class="total">
                    <div class="total_title">Total:</div>
                    <div class="prefix">${prefix}</div>
                    <div class="total_result" id="total_result">${total}</div>
                    <input type="hidden" id="total_result_hidden" name="total" value="${total}">
                </div>
            `
        }

    }
    $('#survey').html(q_html);
    $('[data-toggle="popover"]').popover();


}

function renderAnswer(answerList, questionID, answer_align) {
    if (answerList.length > 0){
        var answers = answerList.filter((answer) => answer.question_id === questionID)
        var resultHtml = '';
        var answerAlign = 'left';
        if(answer_align == 1)  answerAlign = 'center';
        if(answer_align == 2)  answerAlign = 'right';
        answers.forEach((ans) => {
            var ans_referral = initial_data.referral.filter(re => re.id == ans.referral_info);
            if (ans.type == 1) {
                resultHtml += `
                    <div class="answer-input">
                        <div class="title" style="text-align: ${answerAlign}">${ans.title}</div>
                        ${ans.file_url ? (
                            `
                            <div class="answer-image" onclick="handleSelectAnswer(this, ${questionID}, ${ans.id})">
                              <img src="${serverHost}/${ans.file_url}">
                            </div>
                          `
                        )  : ''}
                        </div>
                        ${ans_referral.length > 0 ? (
                            `
                            <span class="referralInfo"
                                data-toggle="popover"
                                data-html="true"
                                data-title="${ans_referral[0].name}"
                                data-content="${unescape(ans_referral[0].info)}">
                                <i class="fas fa-info-circle"></i>
                            </span>
                            `
                        ) : ''}
                        <input type="text" class="answer_${ans.id} answerText" oninput="handleChangeText(this, ${questionID}, ${ans.id})" />
                    </div>
                `
            }else {
                resultHtml += `
            <div class="answer-select">
                <div class="title" style="text-align: ${answerAlign}" onclick="handleSelectAnswer(this, ${questionID}, ${ans.id})">${ans.title}</div>
                ${ans.file_url ? (
                    `
                    <div class="answer-image" onclick="handleSelectAnswer(this, ${questionID}, ${ans.id})">
                      <img src="${serverHost}/${ans.file_url}">
                    </div>
                  `
                )  : ''}
                ${ans_referral.length > 0 ? (
                    `
                    <span class="referralInfo"
                        data-toggle="popover"
                        data-html="true"
                        data-title="${ans_referral[0].name}"
                        data-content="${unescape(ans_referral[0].info)}">
                        <i class="fas fa-info-circle"></i>
                    </span>
                    `
                ) : ''}

            </div>
        `
            }
        }
        )
        return `
            <div class="answer">
            ${resultHtml}
            </div>
        `;
    }

}
function handleChangeText(element, questionID, answerID) {
    var answerList = initial_data.answers;
    var currentAnswer = answerList.find((a) => a.id === answerID);
    if (currentAnswer){
        $(`#answer_q_${questionID}`).val( element.value);
        var nextQuestion = currentAnswer.next_question_id;
        if (nextQuestion != null && nextQuestion != 0) {
            $('#nextBtn').data('nextQuestion', nextQuestion);
        }

        $('#nextBtn').attr('disabled', true);
        if (element.value != '') {
            $('#nextBtn').attr('disabled', false);
            $('#prevBtn').attr('disabled', false);
        }

    }
}

function handleSelectAnswer(element, questionID, answerID) {
    var answerList = initial_data.answers;
    var questionList = initial_data.questions;
    var currentAnswer = answerList.find((a) => a.id === answerID);
    var currentQuestion = questionList.find((q) => q.id === questionID);
    var q_settings = currentQuestion.settings;
    var q_code = '';
    if (q_settings && q_settings != '') {
        var q_settings_array = JSON.parse(q_settings);
        q_code = q_settings_array['question_code'] ?  q_settings_array['question_code']  : 'q_'+q.id ;

    }
    if (currentAnswer){
        var parents = element.parentElement.parentElement.children;
        var newValue = null;
        if (currentAnswer.type === 4) {
            element.parentElement.classList.toggle('selected');
            var listAns = $(`#answer_q_${questionID}`).val();

            if(listAns != null && listAns != "") {
                var listAnsArr = [];
                listAnsArr = JSON.parse(listAns);
                var index = listAnsArr.indexOf(answerID);
                if (listAnsArr.length > 0 && index > -1) {
                    listAnsArr.splice(index, 1);
                    if (scope[q_code] > Number(currentAnswer.value)) {
                        newValue = Number(scope[q_code]) - Number(currentAnswer.value)

                    } else {
                        newValue = 0;
                    }

                } else {
                    listAnsArr.push(answerID);
                    newValue = Number(scope[q_code]) + Number(currentAnswer.value)
                }
                $(`#answer_q_${questionID}`).val(JSON.stringify(listAnsArr));
            } else {
                $(`#answer_q_${questionID}`).val(JSON.stringify([answerID]));
                newValue = Number(scope[q_code]) + Number(currentAnswer.value)
            }
        }else {
            for (var i = 0; i < parents.length; i++) {
                parents[i].classList.remove('selected');
            }
            element.parentElement.classList.add('selected');
            $(`#answer_q_${questionID}`).val(JSON.stringify([answerID]));
            var nextQuestion = currentAnswer.next_question_id;
            if (nextQuestion != null && nextQuestion != 0) {
                $('#nextBtn').data('nextQuestion', nextQuestion);
            }
            newValue = Number(currentAnswer.value)
        }
        scope = {...scope, [q_code]: newValue };
        if (calculate) {
            total = calculate.eval(scope);
            $('#total_result').html(total);
            $('#total_result_hidden').val(total);
        }

        $('#nextBtn').attr('disabled', true);
        for (var i = 0; i < parents.length; i++) {
            if (parents[i].classList.contains('selected')) {
                $('#nextBtn').attr('disabled', false);
                $('#prevBtn').attr('disabled', false);
                break;
            }
        }
    }


}


function add_first_question(data) {
    addQ();

    $('#q-' + current).show();
    EPPZScrollTo.scrollVerticalToElementById('end-anchor', 20);

    $(document).trigger('get-question', [data.first_question.id]);
}

$(document).bind('get-question', function(event, id) {
    url = serverHost + '/api/v1/questions/get/' + id;
    var request = $.get(url, function(data) {
        question_order = data.ord;
        setTimeout(function() {
            $('#q-' + current + ' .q-txt-row#q-txt-row-main .q-txt').html(newline(data.title));

            if (data.sub_title == null || data.sub_title == '') {
                $(document).trigger('show-answer-list', [data]);
            } else {
                $(document).trigger('show-add-question', [data]);
            }
        }, delay_time);
    })
    .done(function(data) {
    })
    .fail(function() {
    })
    .always(function() {
    });
});

$(document).bind('show-add-question', function(event, data) {
    $('#q-' + current + ' .q-txt-row#q-txt-row-sub').show();
    setTimeout(function() {
        $('#q-' + current + ' .q-txt-row#q-txt-row-sub .q-txt').html(newline(data.sub_title));
        $(document).trigger('show-answer-list', [data]);
    }, delay_time);
});

function getYoutubeID(url) {
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
    const match = url.match(regExp);

    return (match && match[2].length === 11)
      ? match[2]
      : null;
}

$(document).bind('show-answer-list', function(event, data) {
    type = data.type;
    answers = data.answers;
    next_id = data.next_question_id;
    a_area_html = '<div class="q-a-area-wrapper">';
    if (type == 2) { // 画像
        a_area_html += '<div class="q-a-img"><div class="q-a-img-wrapper"><img src="'+ serverHost +'/' + data.file_url + '"></div></div>';
    } else if (type == 3) { // 動画
        a_area_html += '<div class="q-a-video"><div class="q-a-video-wrapper">';
        if (data.movie_file != null && data.movie_file != '') {
            a_area_html += '<video controls autoplay loop muted>';
            a_area_html += '<source src="' + serverHost + data.movie_file + '" type="video/mp4">';
            a_area_html += '</video>';
        } else if (data.movie_source != null && data.movie_source != '') {
            a_area_html += newline(data.movie_source);
        } else if (data.movie_url != null && data.movie_url != '') {
            var youtube_id = getYoutubeID(data.movie_url);
            if (youtube_id != null) {
                var youtube_iframe = '<iframe width="100%" height="315" src="//www.youtube.com/embed/' + youtube_id + '?rel=0&autoplay=1&mute=1&loop=1" frameborder="0" allowfullscreen></iframe>';
                a_area_html += youtube_iframe;
            } else {
                a_area_html += '<video controls autoplay loop muted>';
                a_area_html += '<source src="' + data.movie_url + '" type="video/mp4">';
                a_area_html += '</video>';
            }
        }

        a_area_html += '</div></div>';
    }

    a_area_html += '<div class="q-a-list">';
    for (i = 0; i < answers.length; i++) {
        if (answers[i].type != 2) {
            a_area_html += '<div class="q-a-item">' + newline(answers[i].title) + '</div>';
        } else {
            // a_area_html += '<div class="q-a-item"><div class="q-a-item-img"><img src="http://formstylee.com/public/' + answers[i].file_url + '"></div>' + newline(answers[i].title) + '</div>';
            a_area_html += '<div class="q-a-item"><div class="q-a-item-img" style="background: url('+ serverHost + '/' + answers[i].file_url + '"></div>' + newline(answers[i].title) + '</div>';
        }
    }
    a_area_html += '</div></div>';

    setTimeout(function() {
        $('#q-' + current + ' .q-a-area').show();
        $('#q-' + current + ' .q-a-area').html(a_area_html);

        $('#q-' + current + ' .q-a-area .q-a-img .q-a-img-wrapper img').one("load", function() {
            // EPPZScrollTo.scrollVerticalToElementById('end-anchor', 20);
            img_loaded = true;
        });
        $('#q-' + current + ' .q-a-area .q-a-list .q-a-item .q-a-item-img img').one("load", function() {
            // EPPZScrollTo.scrollVerticalToElementById('end-anchor', 20);
            img_loaded = true;
        });
        EPPZScrollTo.scrollVerticalToElementById('end-anchor', 20);

        $('#q-' + current + ' .q-a-list .q-a-item').click(function() {
            if ($(this).parent().hasClass('processed')) {
                return;
            }

            $(this).addClass('selected');

            sel_a_idx = $('#q-' + current + ' .q-a-list .q-a-item').index(this);
            $(this).parent().addClass('processed');

            $(this).parent().parent().parent().parent().css('min-height', 'auto');
            // EPPZScrollTo.scrollVerticalToElementById('end-anchor', 20);

            var a_height = $(window).height() - ($(this).parent().parent().parent().parent().outerHeight() + $('.site-header').outerHeight() + 16);

            addA(a_height);

            setTimeout(function() {
                $('#a-' + current + ' .a-txt').html('「' + answers[sel_a_idx].title + '」です。');
                $('#a-input-' + current).val(answers[sel_a_idx].id);

                for (i = 0; i < (question_order + 1); i++) {
                    var prog_bar_id = "#prog-bar-" + (i + 1);
                    if (!$(prog_bar_id).hasClass('confirmed')) {
                        $(prog_bar_id).addClass('confirmed');
                    }
                }

                if (answers[sel_a_idx].next_question_id != 0) {
                    next_id = answers[sel_a_idx].next_question_id;
                }

                current++;
                addQ();
                $('#q-' + current).show();
                EPPZScrollTo.scrollVerticalToElementById('end-anchor', 20);
                if (next_id != null && next_id != 0) {
                    $(document).trigger('get-question', [next_id]);
                } else {
                    $(document).trigger('show-last-message', [data]);
                }
            }, delay_time);
        });
    }, delay_time);
});

$(document).bind('show-last-message', function(event, data) {
    setTimeout(function() {
        $('#q-' + current + ' .q-txt').html('アンケート内容を送信してください。');
        var form_html = '<div class="q-a-form-fields-wrapper"><div class="q-a-form-fields">';
        form_html += '<div class="q-a-form-field-row"><p>メールアドレス</p><input type="email" placeholder="test@mail.com" name="email" /></div>';
        form_html += '<div class="q-a-form-field-row"><p>お名前</p><input type="text" placeholder="山田 太郎" name="full_name" /></div>';
        form_html += '<div class="q-a-form-field-row"><p>郵便番号11</p><input type="text" placeholder="111-1111" name="zip_code" onKeyUp="AjaxZip3.zip2addr(this,\'\',\'address\',\'address\');"/></div>';
        form_html += '<div class="q-a-form-field-row"><p>住所</p><input type="text" placeholder="" name="address"/></div>';
        form_html += '<div class="q-a-form-field-row"><p>電話番号</p><input type="tel" placeholder="03-1234-5678" name="phone_number" /></div>';
        form_html += '</div>';
        form_html += '<input type="submit" value="送信する"></div>';
        $('#q-' + current + ' .q-a-area').append(form_html);
        $('#q-' + current + ' .q-a-area').show();
        EPPZScrollTo.scrollVerticalToElementById('end-anchor', 20);
    }, delay_time);
});
