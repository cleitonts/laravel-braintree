<?php

namespace App\Http\Controllers;

use App\Models\Answers;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SatisfactionController extends Controller
{
    use Utils;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application home.
     *
     * @return Renderable
     */
    public function index(): Renderable
    {
        $stored_answers = Answers::where('user_id', Auth::user()->id)->get();
        $answers = [];
        foreach ($stored_answers as $r){
            $answers[$r->question] = $r->answer;
        }

        return view('satisfaction', compact('answers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return false|string
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $valid = self::fastValidade(Answers::$question_arr, $data);
        if($valid !== false){
            return $valid;
        }

        $stored_answer = Answers::where('user_id', Auth::user()->id)->get();

        foreach (Answers::$question_arr as $k=>$r) {
            $skip = false;
            foreach ($stored_answer as $stored){
                if($stored->question == $k){
                    $stored->question = $k;
                    $stored->user_id = Auth::user()->id;
                    $stored->answer = $data[$k];
                    $stored->save();
                    $skip = true;
                }
            }
            if($skip){
                continue;
            }
            Answers::create([
                'question' => $k,
                'user_id' =>Auth::user()->id,
                'answer' => $data[$k],
            ]);
        }

        return json_encode([
            "status" => true,
            "message" => 'Thank you'
        ]);

    }
}
