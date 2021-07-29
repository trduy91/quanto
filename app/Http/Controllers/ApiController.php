<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\AnswerType;
use App\Models\Client;
use App\Models\ClientAnswer;
use App\Models\Question;
use App\Models\QuestionType;
use App\Models\ReferralInfo;
use App\Models\Status;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\PHPMailer;

class ApiController extends Controller
{

//    public function getSurvey(Request $request, $id) {
//        Log::debug($request);
//        $query = Survey::where('token', $id)->first();
//        $question = Question::where('survey_id', $query->id)->orderBy('ord')->first();
//        $question_count = Question::where('survey_id', $query->id)->count();
//        $query['first_question'] = $question;
//        $query['question_count'] = $question_count;
//        $query['brand_logo_path'] = $query['profile_path'];
//        $user_profile_url = '';
//	    $user_profile_name = '';
//        if($query != null) {
//            $user = User::find($query->user_id);
//            if($user != null) {
//                $user_profile_url = $user->profile_url;
//	            $user_profile_name = $user->full_name;
//            }
//        }
//        $query['user_profile_url'] = $user_profile_url;
//	    $query['user_profile_name'] = $user_profile_name;
//        return response()->json($query);
//    }

    public function getSurvey(Request $request, $id) {
        $query = Survey::where('token', $id)->first();
        $question = Question::where('survey_id', $query->id)->orderBy('ord')->get();
        $answer = Answer::where('survey_id', $query->id)->get();
        $referral = array();
        forEach($question as $q) {
            $refer_id = $q->referral_info;

            if ($refer_id != null && !in_array($refer_id, $referral)){
                $referral[] = (int) $refer_id;
            }
        }
        forEach($answer as $a) {
            $refer_id = $a->referral_info;

            if ($refer_id != null && !in_array($refer_id, $referral)){
                $referral[] = (int) $refer_id;
            }
        }
        $referral_info = ReferralInfo::whereIn('id', $referral)->get();
        Log::debug($referral_info);
        $query['referral'] = $referral_info;
        $query['questions'] = $question;
        $query['answers'] = $answer;
        $query['brand_logo_path'] = $query['profile_path'];
        $user_profile_url = '';
	    $user_profile_name = '';
        if($query != null) {
            $user = User::find($query->user_id);
            if($user != null) {
                $user_profile_url = $user->profile_url;
	            $user_profile_name = $user->full_name;
            }
        }
        $query['user_profile_url'] = $user_profile_url;
	    $query['user_profile_name'] = $user_profile_name;
        return response()->json($query);
    }

    public function getAnswer(Request $request, $id) {
        $query = Answer::find($id);
        return response()->json($query);
    }

    public function getAnswers(Request $request, $qid) {
        $query = Answer::where('question_id', $qid)->get();
        return response()->json($query);
    }

    public function getQuestion(Request $request, $id) {
        $query = Question::find($id);
        $answers = Answer::where('question_id', $id)->get();
        $next_question = Question::where(['survey_id' => $query->survey_id, 'ord' => $query->ord +1])->first();
        $query['answers'] = $answers;
        if($next_question != null) {
            $query['next_question_id'] = $next_question->id;
        } else{
            $query['next_question_id'] = null;
        }
        return response()->json($query);
    }

    public function saveAnswers(Request $request){
        $survey = Survey::where('token', $request->get('survey_id'))->first();

        $client = new Client();
        $client->name = $request->get('name');
        $client->email = $request->get('email');
        $client->full_name = $request->get('name');
        $client->zip_code = $request->get('zip_code');
        $client->address = $request->get('address');
        $client->phone_number = $request->get('phone_number');
        $client->survey_id = $survey->id;
        $total = $request->get('total')  ? $request->get('total') : 0 ;
        $client->total = $total;
        $client->send_mail_status = 0;
        $client->save();

        $survey_settings = json_decode($survey->settings, true);


        $answers = $request->get('answers');
        $answerModels = [];
        foreach ($answers as $question => $answer){
            $clientAnswer = new ClientAnswer();
            $clientAnswer->client_id = $client->id;
            $clientAnswer->answer_id = $answer;
            $clientAnswer->question = str_replace('\'', '', $question);
            $clientAnswer->save();
            $answerModels[] = $clientAnswer;
        }
        if ($survey_settings['autoSendMail']){
            $this->sendMail($client, $answerModels);
        }
        return redirect(Config::get('constants.clientHost', 'http://formstylee-front.com/').'thankyou.php');
    }

    private function sendMail($client, $answers){
        $survey = $client->survey != null ? $client->survey->title : '';
        $content = "<h2>ユーザ設問回答</h2>";
        $content .= "<p><span>ユーザ名</span><span>$client->name</span></p>";
        $content .= "<p><span>メールアドレス</span><span>$client->email</span></p>";
        $content .= "<p><span>名前</span><span>$client->full_name</span></p>";
        $content .= "<p><span>郵便番号</span><span>$client->zip_code</span></p>";
        $content .= "<p><span>住所</span><span>$client->address</span></p>";
        $content .= "<p><span>電話番号</span><span>$client->phone_number</span></p>";
        $content .= "<p><span>設問</span><span>$survey</span></p>";
        $content .= "<table><thead><th>質問</th><th>回答</th></thead><tbody>";
        foreach ($answers as $answer) {
            $answerDetail = [];
            $question = Question::find($answer->question);
            $answerIds = json_decode($answer->answer_id);
            foreach($answerIds as $id) {
                $ans = Answer::find($id);
                $answerDetail[] = $ans;
            }

            $q_item = isset($question) ? $question->title : '';
            $a_item = '';
            foreach($answerDetail as $ans) {
                $a_item .= "<div>".$ans['title'] ."-". $ans['value']."</div>\n";
            }

            $content .= "<tr><td>$q_item</td><td>$a_item</td></tr>";
        }
        $content .= "</tbody></table>";
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->CharSet = "utf-8";
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = "ssl";
            $mail->Host = Config::get('constants.mail.host');
            $mail->Port = Config::get('constants.mail.port');
            $mail->Username = Config::get('constants.mail.username');
            $mail->Password = Config::get('constants.mail.password');
            $mail->setFrom(Config::get('constants.mail.admin_email'), Config::get('constants.mail.admin_name'));
            $mail->Timeout = 30;
            $mail->Subject = "設問回答";
            $mail->MsgHTML($content);
            $mail->addAddress($client->email, $client->full_name);
            $users = User::all();
            foreach ($users as $user){
                $mail->addAddress($user->email, $user->full_name);
            }
            $mail->send();
            $client->send_mail_status = 1;
            $client->save();
        } catch (phpmailerException $e) {
            $client->send_mail_status = 0;
            $client->save();
            dd($e);
        } catch (Exception $e) {
            $client->send_mail_status = 0;
            $client->save();
            dd($e);
        }
    }


}
