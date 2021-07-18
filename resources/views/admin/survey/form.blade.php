@extends('layouts.admin', ['title' => 'アンケート'])

@section('js')
    <script src="{{ asset('js/survey.js') }}"></script>
{{--    <script src="{{ asset('js/drag.js') }}"></script>--}}
@endsection
<?php
echo '<script>';
echo 'var GradientList = ' . json_encode(GRADIENT_COLOR) . ';';
echo 'var referral_info = ' . json_encode($referral_info) . ';';
echo '</script>';
?>
@section('main-content')
    <div class="row">
        <div class="col-8">

            <form class="" id="survey" method="post" action="{{ route('admin.survey.save') }}" enctype="multipart/form-data">
                @csrf
                <input type="submit" class="btn btn-primary" value="保存">
                @if (isset($survey['id']))
                    <div class="row">
                        <lable class="col-md-2 col-form-label">ID</lable>
                        <input type="hidden" name="id" value="{{$survey['id']}}">
                        <div class="col-md-6 d-flex align-items-center">{{$survey['id']}}</div>
                    </div>
                @endif

                <div class="form-group row">

                    <lable class="col-md-1 col-form-label">色設定</lable>
                    <div class="col-md-7 align-items-center">
                        <div class="row">
                            <div class="col-md-12 d-flex">
                                <lable class="col-form-label mr-3">全体背景:</lable>
                                <input class="" type="color" name="background_color"
                                       value="{{ isset($survey['background_color']) ? $survey['background_color'] : '#eeebff' }}">
                                <lable class="col-form-label mx-3">文字:</lable>
                                <input class="" type="color" name="char_color"
                                       value="{{ isset($survey['char_color']) ? $survey['char_color'] : '#785cff' }}">
                                <lable class="col-form-label mx-3">枠カラー:</lable>
                                <input class="" type="color" name="border_color".
                                       value="{{ isset($survey['border_color']) ? $survey['border_color'] : '#785cff' }}">
                                <lable class="col-form-label mx-3">背景:</lable>
                                <input class="" type="color" name="callout_color"
                                       value="{{ isset($survey['callout_color']) ? $survey['callout_color'] : '#785cff' }}">
                            </div>
                        </div>
                        <div class="row d-flex align-items-center">
                            <lable class="col-md-4 col-form-label mr-0">グラデーション:</lable>
                            <div class="col-md-2">
                                <span class="gradient_background"></span>
                            </div>
                            <div class="col-md-5">
                                <select name="gradient_color" class="form-control">
                                    @foreach(GRADIENT_COLOR as $key => $item)
                                        <option value="{{ $key }}" {{ (isset($survey['gradient_color']) && $survey['gradient_color'] == $key) ? 'selected' : '' }}>{{ $item[1] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-center form-inline">
                        <div class="row">
                            <div class="col-md-12">
                                <lable class="col-form-label mr-3">ステータス:</lable>
                                <select class="form-control" name="status">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}" {{ $status->id == $survey['status'] ? 'selected' : ''}}>{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex">
                                <lable class="col-form-label mr-3">進捗状況ステータスバー:</lable>
                                <div class="switch_box box_1 d-flex align-items-center">
                                    <input type="checkbox" class="switch_1" name="progress_status" {{ (isset($survey['progress_status']) && $survey['progress_status'] == 1) ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                @if (isset($survey['profile_path']))
                    <div>
                        <img src="{{asset($survey['profile_path'])}}" class="fs-profile-image">
                    </div>
                @endif
                <div class="form-group row">
                    <lable class="col-md-3 col-form-label">ブランドロゴ</lable>
                    <div class="col-md-6 d-flex align-items-center">
                        <input type="file" name="profile_path">
                    </div>
                </div>
                <div class="form-group row">
                    <lable class="col-md-3 col-form-label">　　ブランド名 <br>(会社またはユーザ名)
                    </lable>
                    <div class="col-md-6 d-flex align-items-center">
                        <input type="text" name="brand_name" class="form-control" value="{{ isset($survey['brand_name']) ? $survey['brand_name'] : '' }}">
                    </div>
                </div>
                <div class="form-group row">
                    <lable class="col-md-3 col-form-label">紹介文: </lable>
                    <div class="col-md-9 d-flex align-items-center">
                        <textarea class="form-control" placeholder="紹介文" name="brand_description">{{ isset($survey['brand_description']) ? $survey['brand_description'] : '' }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="">タイトル: </label>
                    <div class="col-md-10 d-flex align-items-center">
                        <input class="form-control" type="text" placeholder="タイトル" name="title"
                               value="{{ isset($survey['title']) ? $survey['title'] : '' }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="">説明: </label>
                    <div class="col-md-10">
                        <textarea classmodalAddQuestion="form-control" placeholder="説明" name="description">{{ isset($survey['description']) ? $survey['description'] : '' }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="">小計のプレフィックス: </label>
                    <div class="col-md-10">
                        <?php
                        $prefix = '';
                        if (isset($survey['settings'])) {
                            $surveySettings = json_decode($survey['settings']);
                            if (isset($surveySettings->prefix)) {
                                $prefix = $surveySettings->prefix;
                            }

                        }

                        ?>
                        <input classmodalAddQuestion="form-control" type="text" placeholder="円" name="totalPrefix" value="<?php echo $prefix; ?>" />
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="">ユーザにメールを送ります: </label>
                    <div class="col-md-10">
                        <?php
                        $autoSendMail = '';
                        if (isset($survey['settings'])) {
                            $surveySettings = json_decode($survey['settings']);
                            if (isset($surveySettings->autoSendMail)) {
                                $autoSendMail = $surveySettings->autoSendMail;
                            }
                        }

                        ?>
                        <input classmodalAddQuestion="form-control" type="checkbox" <?php echo $autoSendMail == 1 ? 'checked' : ''; ?> placeholder="円" name="autoSendMail" value="1" />
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="">フォーミュラ: </label>
                    <div class="col-md-10 d-flex align-items-center">
                        <?php
                        $formular = '';
                        if (isset($survey['settings'])) {
                            $surveySettings = json_decode($survey['settings']);
                            $formular = urldecode($surveySettings->formular);
                        }


                        ?>
                        <input class="form-control" type="text" readonly
                               value="{{$formular}}" >
                    </div>
                </div>
                <div>
                    <input type="button" class="btn btn-secondary" onclick="formularSetting()" value="式の設定">
                </div>
                <div id="questions-container">
                    @php
                        $q_index = 0;
                        $a_index = 0;
                    @endphp
                    @if (isset($questions))
                        @foreach( $questions as $question)
                            @php
                                $next_questions = [['id'=>0, 'title'=>'']];
                                foreach ($questions as $q_item){
                                    if ($question->ord < $q_item->ord){
                                        $next_questions[] = ['id' => $q_item->id, 'title'=>$q_item->title];
                                    }
                                }
                                $q_index = $question->id;
                            @endphp
                            <div id="question_{{$question->id}}_wrapper">

                                <div class="question" id="question_{{$question->id}}">
                                    <input type="hidden" class="questionID" value="{{$question->id}}" name="questions[q_{{$q_index}}][id]">
                                    <input type="hidden" value="{{$question->type}}" name="questions[q_{{$q_index}}][type]">
                                    <div class="row form-group ">
                                        <label class="col-md-2 pl-1 col-form-label d-flex align-items-center">質問{{$q_index + 1}}:</label>
                                        <div class="col-md-8">
                                            <textarea placeholder="質問" class="form-control" name="questions[q_{{$q_index}}][title]" required>{{$question->title}}</textarea>
                                        </div>
                                        <div class="col-md-1 d-flex align-items-center">
                                            <button type="button" class="btn btn-danger" onclick="onDelete('question_{{$q_index}}')">
                                                <i class="fa fa-times"></i>
                                            </button>
                                            <button type="button" class="btn btn-primary buttonEdit" style="display: block" onclick="onEdit('question_{{$q_index}}')"><i class="fa fa-pen"></i></button>
                                        </div>
                                    </div>
                                    <div class="row form-group ">
                                        <label  class="ml-2 pl-1 col-form-label d-flex al{{$q_index}}:ign-items-center">質問コード</label>
                                        <div class="col-md-8">
                                            <input type="text" value="{{json_decode($question->settings, true)['question_code']}}" name="questions[q_{{$q_index}}][question_code]" />
                                        </div>
                                    </div>
                                    <div class="row form-group ">
                                        <label  class="ml-2 pl-1 col-form-label d-flex al{{$q_index}}:ign-items-center">関連情報</label>
                                        <div class="col-md-8">
                                            <select class="form-control" disabled name="questions[q_{{$q_index}}][referral_info]" id="questionReferralInfo">
                                                <option value=""></option>
                                                @foreach($referral_info as $ref)
                                                    <option value="{{$ref->id}}" {{$ref->id === $question->referral_info ? 'selected' : ''}}>{{$ref->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div id="subQues-div{{ $q_index }}">
                                        @if(isset($question->sub_title) && $question->sub_title != "")
                                            <div class="row form-group " id="_sub_que_{{ $q_index }}">
                                                <label class="col-md-2 pl-1 col-form-label d-flex align-items-center">サーブ質問{{$q_index + 1}}:</label>
                                                <div class="col-md-9">
                                                    <textarea placeholder="サーブ質問" class="form-control" name="questions[q_{{$q_index}}][sub_title]" required>{{$question->sub_title}}</textarea>
                                                </div>
                                                <div class="col-md-1 d-flex align-items-center">
                                                    <button type="button" class="btn btn-danger" onclick="onDelete('_sub_que_{{$q_index}}')">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @if($question->type == 3)
                                        <?php
                                        if(isset($question->movie_file) && $question->movie_file != "") {
                                            $file_name = explode('/', $question->movie_file);
                                            $file_name = $file_name[count($file_name) - 1];
                                        } else {
                                            $file_name = '動画を選択してください。';
                                        }
                                        ?>
                                        <div class="row">
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <input type="file" class="form-control-file d-none" style="opacity: 0;" name="questions[q_{{$q_index}}][movie_file]"  id="questions[q_{{$q_index}}][movie_file]{{$q_index}}">
                                                    <input type="hidden" name="questions[q_{{$q_index}}][movie_file_tmp]" data-name="questions[q_{{$q_index}}][movie_file]{{$q_index}}" data-index="questions[q_{{$q_index}}][movie_file]" id="questions[q_{{$q_index}}][movie_file_tmp]{{$q_index}}" value="{{ !empty($question->movie_file) ? $question->movie_file : '-'  }}">
                                                    <label for="questions[q_{{$q_index}}][movie_file]{{$q_index}}" class="form-control-label btn btn-primary">{{ $file_name }}</label>
                                                    <p class="text-danger">（* テキスト）</p>
                                                    <a href="https://cloudconvert.com/mov-to-mp4">https://cloudconvert.com/mov-to-mp4</a>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-danger" onclick="onDeleteMovie('questions[q_{{$q_index}}][movie_file]', '{{$q_index}}')">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <textarea class="form-control" name="questions[q_{{$q_index}}][movie_source]" placeholder="ソースコード(iframe)でアップ（1024以下の文字を入力してください。）">{{ isset($question->movie_source) ? $question->movie_source : '' }}</textarea>
                                        </div>
                                        <div class="col-md-12 mt-2 mb-2">
                                            <input type="text" class="form-control" name="questions[q_{{$q_index}}][movie_url]" value="{{ isset($question->movie_url) ? $question->movie_url : '' }}" placeholder="URLでアップ(YoutubeなどのURL)">
                                        </div>
                                    @endif

                                    @if ($question->type == 2)
                                        @if ($question->file_url != null)
                                            <div class="row">
                                                <img src="{{ asset($question->file_url) }}" class="col fs-question-image mb-2">
                                            </div>
                                        @endif
                                        <div class="row form-group">
                                            <div class="col-md">
                                                <input type="file" class="form-control"
                                                       name="questions[q_{{$q_index}}][file_url]">
                                            </div>
                                        </div>
                                    @endif
                                    <div class="d-flex mb-1">
                                        <div id="answers_{{$q_index}}" class="d-flex answerDropArea flex-wrap">
                                            @php
                                                $parents = [['id' => 0, 'title' => '']];
                                                foreach($answers as $item){
                                                    if($item->question_id == $question->id && $item->type == 3){
                                                        $parents[] = ['id' => $item->id, 'title' => $item->title];
                                                    }
                                                }
                                                $checkbox_radio_item = [];
                                                foreach($answers as $item){
                                                    if($item->question_id == $question->id && ($item->type == 4 || $item->type == 5 )){
                                                        $checkbox_radio_item[] = $item->id;
                                                    }
                                                }
                                            @endphp
                                            @php
                                            $hasRadio = false;
                                            @endphp
                                            @foreach($answers as $answer)

                                                @if ($answer->type != 4 && $answer->type != 5)
                                                    <?php
                                                    if ($hasRadio) {
                                                        $hasRadio = false;
                                                    ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    }
                                                    ?>
                                                <div class="answer card mr-2 mb-2" id="_answer_{{$a_index}}">
                                                    <div class="card-header d-flex justify-content-between p-2">
                                                        <span>回答</span>
                                                        <button class="text-danger buttonDelete" type="button" onclick="onDelete('_answer_{{$a_index}}')"><i class="fa fa-times"></i></button>

                                                    </div>
                                                    <div class="card-body p-2">
                                                        <input type="hidden" value="{{$answer->id}}" name="questions[q_{{$q_index}}][answers][a_{{$a_index}}][id]">
                                                        <input type="hidden" value="{{$answer->type}}" name="questions[q_{{$q_index}}][answers][a_{{$a_index}}][type]">

                                                        <textarea placeholder="回答" class="form-control" name="questions[q_{{$q_index}}][answers][a_{{$a_index}}][title]" required>{{$answer->title}}</textarea>
                                                        @if (count($parents) > 1 && $answer->type != 3)
                                                            集団
                                                            <select class="form-control mt-1" name="questions[q_{{$q_index}}][answers][a_{{$a_index}}][parent_id]">
                                                                @foreach($parents as $item)
                                                                    <option value="{{ $item['id'] }}" {{$item['id'] == $answer->parent_id ? 'selected' : ''}}>{{ $item['title'] }}</option>
                                                                @endforeach
                                                            </select>
                                                        @endif
                                                        @if (count($next_questions) > 1)
                                                            次の問題
                                                            <select class="form-control mt-1" name="questions[q_{{$q_index}}][answers][a_{{$a_index}}][next_question_id]">
                                                                @foreach($next_questions as $n_item)
                                                                    <option value="{{ $n_item['id'] }}" {{$n_item['id'] == $answer->next_question_id ? 'selected' : ''}}>{{ $n_item['title'] }}</option>
                                                                @endforeach
                                                            </select>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endif
                                                @if (($answer->type == 4 || $answer->type == 5) && $answer->question_id == $question->id)
                                                @if (!$hasRadio)
                                                <div class="answer card mr-2 mb-2" id="_answer_{{$a_index}}">
                                                    <div class="card-header d-flex justify-content-between p-2">
                                                        <span>回答</span>
                                                        <button class="text-danger buttonDelete" type="button" onclick="onDelete('_answer_{{$a_index}}', this)"><i class="fa fa-times"></i></button>
                                                    </div>
                                                    <div class="card-body p-2 row">
                                                @endif
                                                        <?php
                                                        $hasRadio = true;
                                                        ?>
                                                        <div class="col-6 p-1">
                                                            <input type="hidden" value="{{$answer->id}}" name="questions[q_{{$q_index}}][answers][a_{{$a_index}}][id]">
                                                            <input type="hidden" value="{{$answer->type}}" name="questions[q_{{$q_index}}][answers][a_{{$a_index}}][type]">
                                                            <label>回答</label>
                                                            <input placeholder="回答" class="form-control" name="questions[q_{{$q_index}}][answers][a_{{$a_index}}][title]" required value="{{$answer->title}}" />
                                                            <label>値</label>
                                                            <input placeholder="値" class="form-control" name="questions[q_{{$q_index}}][answers][a_{{$a_index}}][value]" required value="{{$answer->value}}" />
                                                            <label>関連情報</label>
                                                            <select class="form-control" disabled name="questions[q_{{$q_index}}][answers][a_{{$a_index}}][referral_info]" id="referralInfo">
                                                                <option value=""></option>
                                                                @foreach($referral_info as $ref)
                                                                    <option value="{{$ref->id}}" {{$ref->id == $answer->referral_info ? 'selected' : ''}}>{{$ref->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            @if (count($parents) > 1)
                                                                集団
                                                                <select class="form-control mt-1" name="questions[q_{{$q_index}}][answers][a_{{$a_index}}][parent_id]">
                                                                    @foreach($parents as $item)
                                                                        <option value="{{ $item['id'] }}" {{$item['id'] == $answer->parent_id ? 'selected' : ''}}>{{ $item['title'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            @endif
                                                            @if (count($next_questions) > 1)
                                                                次の問題
                                                                <select class="form-control mt-1" name="questions[q_{{$q_index}}][answers][a_{{$a_index}}][next_question_id]">
                                                                    @foreach($next_questions as $n_item)
                                                                        <option value="{{ $n_item['id'] }}" {{$n_item['id'] == $answer->next_question_id ? 'selected' : ''}}>{{ $n_item['title'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            @endif
                                                        </div>

                                                @php $a_index++;
                                                @endphp
                                                @endif
                                            @endforeach
                                            @if ($hasRadio)
                                                @php $hasRadio = false;
                                                @endphp
                                                    </div>
                                                    <button class="btn btn-primary" onclick="onNewField({{$q_index}}, {{$answer->type}}, {{$q_index}})"><i class="fa fa-plus"></i></button>
                                                </div>
                                                @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @endforeach
                    @endif
                </div>

            </form>
            <div>
                <button class="btn btn-primary" id="btn-add-survey" data-toggle="modal" data-target="#modalAddQuestion">
                    +
                </button>
            </div>
        </div>
        <?php
        $clientHost = \Illuminate\Support\Facades\Config::get('constants.clientHost');
        ?>
        <div class="col-4">

            <div class="card">
                <h6 class="form-control-label m-3">
                    URL: <a href="<?php echo $clientHost ?>?id={{ $survey['token'] }}" target="_blank"><?php echo $clientHost ?>?id={{ $survey['token'] }}</a>
                    <br><br>
                    <?php $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->generate($clientHost.'?id='.$survey['token'])?>
                    {!! $qrCode !!}
                </h6>
                <h5 class="card-header">
                    プレビュー
                </h5>
                <div class="card-body preview" id="preview"
                     style="background: {{ isset($survey['background_color']) ? $survey['background_color'] : '#eeebff' }}">
                    <div class="row">
                        <div class="col-1">
                            @if (isset($survey['profile_path']))
                                <div>
                                    <img src="{{asset($survey['profile_path'])}}" class="fs-profile-image small">
                                </div>
                            @endif
                        </div>
                        <div class="col-10">
                            <div id="preview-text" class="ml-4"
                                 style="border: 1px solid {{ isset($survey['border_color']) ? $survey['border_color'] : '#785cff' }}; background: {{ isset($survey['callout_color']) ? $survey['callout_color'] : '#785cff' }}; color: {{ isset($survey['char_color']) ? $survey['char_color'] : '#785cff' }}">
                                テキストプレビュー
                            </div>
                            <div class="card" id="preview-img"
                                 style="border: 4px solid {{ isset($survey['border_color']) ? $survey['border_color'] : '#785cff' }}">
                                <img class="card-img-top" src="{{asset('img/preview_img.png')}}">
                                <div class="card-body preview-gradient" style="background: {{ isset($survey['gradient_color']) ? GRADIENT_COLOR[$survey['gradient_color']][0] : 'white' }};">
                                    テキストプレビュー
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div>
                    下記のHTMLをコピーして、他のサイトにペーストできます。
                </div>
                <div class="embbed-html">
                    <?php

                    if (isset($survey['token'])){
                        echo htmlspecialchars('
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="'. $clientHost .'assets/css/style.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mathjs/5.0.0/math.js"></script>
                       <header id="header" class="header">
                            <div class="site-header">
                                <div class="site-header-inner">
                                    <div class="brand-wrapper">
                                        <div id="brand" class="brand"><img src="" alt="" /></div>
                                        <p id="brand-name" class="brand-name"></p>
                                    </div>
                                    <div id="brand-desc" class="brand-desc"></div>
                                    <div id="title-desc" class="title-desc">
                                        <h1 id="title" class="title"><span></span></h1>
                                        <p id="description" class="description"></p>
                                    </div>
                                    <div id="btn-start" class="btn-start">START</div>
                                    <div id="progress-row" class="progress-row">
                                        <div class="point"></div>
                                        <div id="progress-inner" class="progress-inner"></div>
                                        <div class="point"></div>
                                    </div>
                                </div>
                            </div>
                        </header>
                        <div id="content" class="content">
                            <form action="http://formstylee-admin.com/api/v1/client/save" method="POST">
                                <input type="hidden" name="survey_id" value="0Sc40khVPXyFbzW5h9XG" />
                                <div id="survey" class="survey">
                                </div>

                            </form>
                        </div>
                        <div id="loading-area">
                            <div class="loader-wrapper">
                                <div class="loader">Loading...</div>
                            </div>
                        </div>
                        <script type="text/javascript">
                            var survey_id = "'. $survey['token'].'";
                        </script>
                        <script src="'. $clientHost .'/assets/js/script.js"></script>
                        <script type="text/javascript">
                            $(document).ready(function(){
                                $(\'[data-toggle="popover"]\').popover();
                            });
                        </script>

                    ');
                    }

                    ?>
                </div>
            </div>
        </div>
    </div>
{{--    @include('admin/survey/modal', ['title' => '質問追加', 'modal_id' => 'AddQuestion', 'items' => $question_types])--}}
    @include('admin/survey/question', ['title' => '質問追加', 'questionTypes' => $question_types, 'answerTypes' => $answer_types, 'referral_info' => $referral_info])
    @include('admin/survey/modal', ['title' => '回答追加', 'modal_id' => 'AddAnswer', 'items' => $answer_types])
    @include('admin/survey/modal', ['title' => '回答追加', 'modal_id' => 'AddSubQuestion', 'items' => $answer_types])
    {{--    @include('admin/survey/subQue_Modal', ['title' => 'サーブ質問追加', 'modal_id' => 'AddSubQuestion'])--}}

@endsection
