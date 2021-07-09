<!-- Modal -->
<div class="modal fade" id="modalAddQuestion" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">質問追加</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-form-label">質問タイプと回答を選んでください:</label>
                </div>
                <div class="dropQuestion">
                    <div class="dropArea" ondrop="dropQuestion(event)" ondragover="allowDrop(event)" id="addQuestionDropArea"></div>
                    <div class="dragArea">
                        <div>質問を選択ください</div>
                        <div class="questionType">

                            @foreach ($questionTypes as $q)
                                <div id="questionType-{{$q->id}}" draggable="true" ondragstart="dragQuestion(event, DRAG_TYPE.QUESTION, {{$q->id}})">{{$q->name}}</div>
                            @endforeach
                        </div>
                        <div>回答を選択ください</div>
                        <div class="answerType">

                            @foreach ($answerTypes as $a)
                                <div id="questionType-{{$a->id}}" draggable="true" ondragstart="dragAnswer(event, DRAG_TYPE.ANSWER, {{$a}})">{{$a->name}}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
                <button type="button" class="btn btn-primary" id="btnAddQuestion">追加</button>
            </div>
            <input type="hidden" id="container-id">
        </div>
    </div>
</div>

