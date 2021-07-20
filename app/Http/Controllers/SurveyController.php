<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\AnswerType;
use App\Models\Question;
use App\Models\QuestionType;
use App\Models\ReferralInfo;
use App\Models\Status;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SurveyController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

    public function formularSetting(Request $request) {

	    $survey_id = $request->get('id');
	    if ($survey_id) {
            $survey = Survey::find($survey_id);
            $question_list = Question::where('survey_id', $survey->id)->get()->sortBy("ord");
            return view('admin/survey/formularSetting', [
                'survey' => $survey,
                'questions' => $question_list
            ]);
        }
	    return redirect()->route('admin.surveys');

    }

    public function formularSave(Request $request) {
        if($request->get('id') != null) {
            $survey = Survey::find($request->get('id'));

            $survey_settings = json_decode($survey->settings, true);

            $formular = $request->get('formular') ? strip_tags($request->get('formular')) : '' ;
            $survey_settings['formular'] = $formular;

            $survey->settings = json_encode($survey_settings);
            $survey->save();
            return redirect()->route('admin.survey.edit', ["id" => $survey->id]);
        }

        return redirect()->route('admin.surveys');

    }

	public function index(Request $request)
	{
		if(Auth::user()->isAdmin()) {
			$surveys = Survey::simplePaginate(20);
		} else {
			$surveys = Survey::join('users', 'surveys.user_id', 'users.id')
				->where('users.id',Auth::user()->id)
				->select('surveys.*')
				->simplePaginate(20);
		}
		return view('admin/survey/index',['surveys' => $surveys]);
	}

	public function add(Request $request)
	{
		$survey = new Survey();
		$statuses = Status::all();
		$question_types = QuestionType::all();
		$answer_types = AnswerType::all();
		$referral_info = ReferralInfo::all();
		return view('admin/survey/form',
			[
				'survey' => $survey,
				'statuses' =>$statuses,
				'question_types' =>$question_types,
				'answer_types' =>$answer_types,
                'referral_info' => $referral_info
			]);
	}

	public function edit(Request $request, $id)
	{
		$survey = Survey::find($id);
		$statuses = Status::all();
		$question_types = QuestionType::all();
		$answer_types = AnswerType::all();
        $referral_info = ReferralInfo::all();
		$question_list = Question::where('survey_id', $survey->id)->get()->sortBy("ord");
		$answer_list = Answer::where('survey_id', $survey->id)->get();
		return view('admin/survey/form',
			[
				'survey' => $survey,
				'statuses' =>$statuses,
				'question_types' =>$question_types,
				'answer_types' =>$answer_types,
				'questions' => $question_list,
				'answers' => $answer_list,
                'referral_info' => $referral_info
			]);
	}

	public function delete(Request $request, $id)
	{
		$result = Survey::where('id',$id)->delete();
		return redirect()->route('admin.surveys');
	}

	public function save(Request $request)
	{
        $survey_settings = [];

        if($request->get('id') != null) {
			$survey = Survey::find($request->get('id'));
            $survey_settings = json_decode($survey->settings, true);

        } else {
			$survey = new Survey();
		}

		$survey->title = $request->get('title') ? $request->get('title') : '';
		$survey->status = $request->get('status');
		$survey->description = $request->get('description');
		$survey->background_color = $request->get('background_color');
		$survey->char_color = $request->get('char_color');
		$survey->border_color = $request->get('border_color');
		$survey->gradient_color = $request->get('gradient_color');
		$survey->progress_status = $request->get('progress_status') == true ? 1 : 0;
		$survey->brand_description = $request->get('brand_description');
		$survey->brand_name = $request->get('brand_name');
		$survey->callout_color = $request->get('callout_color');
		if($request->get('id') == null)
			$survey->token = Str::random(20);

		$survey->user_id = Auth::user()->id;
		$profile_file = $request->file('profile_path');

		if ($profile_file != null) {
			if (strtolower($profile_file->getClientOriginalExtension()) == 'png'
				|| strtolower($profile_file->getClientOriginalExtension()) == 'jpg'
				|| strtolower($profile_file->getClientOriginalExtension()) == 'gif'
			) {
				$profile_file->move('uploads/surveys', str_replace(' ','_', $profile_file->getClientOriginalName()));
				$survey->profile_path = 'uploads/surveys/' . str_replace(' ','_', $profile_file->getClientOriginalName());
			}
		}


        $survey_settings['prefix'] = $request->get('totalPrefix') ? $request->get('totalPrefix') : '';

        $survey_settings['autoSendMail'] = $request->get('autoSendMail');
        $survey->settings = json_encode($survey_settings);

        $survey->save();

		$questions = $request->get('questions');
		$question_files = $request->file('questions');
		$question_ids = [];
		if($questions != null) {
			$q_keys = array_keys($questions);
			$ord = 0;
			$question_setting = [];
			foreach ($q_keys as $key) {
				$item = $questions[$key];
				if (isset($item['id'])) {
					$question = Question::find($item['id']);
					$question_setting = json_decode($question->settings, true);
				} else {
					$question = new Question();
				}

				$question->title = isset($item['title']) ? $item['title'] : ' ';
				if(isset($item['sub_title']))
					$question->sub_title = $item['sub_title'];
				else
					$question->sub_title = "";
				$question->type = $item['type'];
				$question->survey_id = $survey->id;
				$question->ord = $ord;
				$question->referral_info = $item['referral_info'];
				$questionCode = $item['question_code'] ? $item['question_code'] : '' ;
                $question_setting['question_code'] = $questionCode;

                $question->settings = json_encode($question_setting);


				$question->save();
				$question_ids[]=$question->id;
				if (isset($question_files[$key]['file_url'])) {
					$question_file = $question_files[$key]['file_url'];
					if ($question_file != null) {
						if (strtolower($question_file->getClientOriginalExtension()) == 'png'
							|| strtolower($question_file->getClientOriginalExtension()) == 'jpg'
							|| strtolower($question_file->getClientOriginalExtension()) == 'gif'
						) {
							$question_file->move('uploads/questions/' . $question->id, str_replace(' ', '_', $question_file->getClientOriginalName()));
							$question->file_url = 'uploads/questions/' . $question->id . '/' . str_replace(' ', '_', $question_file->getClientOriginalName());
							$question->save();
						}
					}
				}
//				if(isset($questions[$key]['movie_file_tmp']))
//                	var_dump($questions[$key]['movie_file_tmp']);

				if(isset($questions[$key]['movie_file_tmp'])) {
					if($questions[$key]['movie_file_tmp'] == '-') {
//						if (isset($question_files[$key]['movie_file'])) {
							$movie_file = Question::find($question->id);
							if($movie_file != null) {
								@unlink($movie_file->movie_file);
							}
							$question->movie_file = '';
							$question->save();
//						}
					} else {
						if (isset($question_files[$key]['movie_file'])) {
							$question_file = $question_files[$key]['movie_file'];
							if ($question_file != null) {
								if (strtolower($question_file->getClientOriginalExtension()) == 'mp4'
									|| strtolower($question_file->getClientOriginalExtension()) == 'avi'
									|| strtolower($question_file->getClientOriginalExtension()) == 'gif'
								) {
									$question_file->move('uploads/questions/' . $question->id . '/movie', str_replace(' ', '_', $question_file->getClientOriginalName()));
									$question->movie_file = 'uploads/questions/' . $question->id . '/movie/' . str_replace(' ', '_', $question_file->getClientOriginalName());
									$question->save();
								}
							}
						}
					}
				}

				$question->movie_source = isset($questions[$key]['movie_source']) ? $questions[$key]['movie_source'] : '';
				$question->movie_url = isset($questions[$key]['movie_url']) ? $questions[$key]['movie_url'] : '';
				$question->save();


				$answer_ids = [];
				if(isset($item['answers'])) {
					$answers = $item['answers'];
					$a_keys = array_keys($answers);
					$a_ord = 0;
					foreach ($a_keys as $a_key){
						$answerItem =  $answers[$a_key];
						if (isset($answerItem['id'])) {
							$answerModel = Answer::find($answerItem['id']);
						} else {
							$answerModel = new Answer();
						}
						$answerModel->title = isset($answerItem['title']) ? $answerItem['title'] : '' ;
						$answerModel->type = $answerItem['type'];
						$answerModel->value = isset($answerItem['value']) ? $answerItem['value'] : '';
						if(isset($answerItem['parent_id'])){
							$answerModel->parent_id = $answerItem['parent_id'];
						}
						if(isset($answerItem['next_question_id'])){
							$answerModel->next_question_id = $answerItem['next_question_id'];
						}
						$answerModel->survey_id = $survey->id;
						$answerModel->question_id = $question->id;
						$answerModel->ord = $a_ord;
						$answerModel->referral_info = isset($answerItem['referral_info']) ? $answerItem['referral_info'] : null;
						$answerModel->save();
						if (isset($question_files[$key]['answers'][$a_key]['file_url'])) {
							$answer_file = $question_files[$key]['answers'][$a_key]['file_url'];
							if ($answer_file != null) {
								if (strtolower($answer_file->getClientOriginalExtension()) == 'png'
									|| strtolower($answer_file->getClientOriginalExtension()) == 'jpg'
									|| strtolower($answer_file->getClientOriginalExtension()) == 'gif'
								) {
									$answer_file->move('uploads/answers/' . $answerModel->id, str_replace(' ', '_', $answer_file->getClientOriginalName()));
									$answerModel->file_url = 'uploads/answers/' . $answerModel->id . '/' . str_replace(' ', '_', $answer_file->getClientOriginalName());
									$answerModel->save();
								}
							}
						}
						$answer_ids[] = $answerModel->id;
						$a_ord++;
					}
				}

				if(count($answer_ids)>0){
					Answer::where('question_id',$question->id)->whereNotIn('id', $answer_ids)->delete();
				}else{
					Answer::where('question_id',$question->id)->delete();
				}

				$ord++;
			}
		}
		if(count($question_ids)>0){
			Question::where('survey_id',$survey->id)->whereNotIn('id', $question_ids)->delete();
			Answer::where('survey_id',$survey->id)->whereNotIn('question_id', $question_ids)->delete();
		}else{
			Question::where('survey_id',$survey->id)->delete();
			Answer::where('survey_id',$survey->id)->delete();
		}

		$statuses = Status::all();
		$question_types = QuestionType::all();
		$answer_types = AnswerType::all();
		$referral_info = ReferralInfo::all();
		$question_list = Question::where('survey_id', $survey->id)->get();
		$answer_list = Answer::where('survey_id', $survey->id)->get();



		if ($request->get('surveyRedirect')
            && $request->get('surveyRedirect') !== ''
            && Route::has($request->get('surveyRedirect'))  ){
		    return redirect()->route($request->get('surveyRedirect'), [
                'id' => $survey->id,
            ]);
        }
//		return view('admin/survey/form',
//			[
//				'survey' => $survey,
//				'statuses' =>$statuses,
//				'question_types' =>$question_types,
//				'answer_types' =>$answer_types,
//				'questions' => $question_list,
//				'answers' => $answer_list,
//                'referral_info' => $referral_info
//			]);
        return redirect()->route('admin.survey.edit', [
            'id' => $survey->id,
        ]);
//        return redirect()->route('admin.surveys');
	}

	public function getSurvey(Request $request, $id){
		$query = Survey::find($id);
		return response()->json($query);
	}
}
