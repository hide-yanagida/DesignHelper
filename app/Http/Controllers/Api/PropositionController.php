<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\EditProgressPost;
use App\Http\Requests\StorePropositionPost;
use App\Http\Requests\StoreMessagePost;
use App\Http\Requests\AttachCreatorPost;
use App\Events\MessageCreated;
use App\Jobs\AssignedProposition;
use App\Jobs\IssueOccurrence;
use App\Jobs\Payment;
use App\Message;
use App\Proposition;
use GuzzleHttp\Client;
use Illuminate\Bus\Dispatcher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\User;
use App\Jobs\AttachedCreator;

class PropositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function getAll()
    {
        $proposition = new Proposition();
        return response()->json($proposition->getAll());
    }

    public function get()
    {
        $user = auth()->user();
        $proposition = new Proposition();
        return response()->json($proposition->get($user));
    }

    public function getOne($id)
    {
        $proposition = new Proposition();
        return response()->json($proposition->getOne($id));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePropositionPost $request, Dispatcher $dispatcher)
    {
        foreach ($request->all()['data'] as $value) {
            Proposition::create(
                [
                    'client_id' => auth()->user()->id,
                    'menu_id' => $value['id'],
                    'amount' => $value['amount']
                ]
            );
        }

        // 管理者にも通知メール
        $managers = User::where('role', 0)->get();
        $managers = $managers->toArray();
        $mail = new IssueOccurrence($managers);
        $dispatcher->dispatch($mail);

        return response()->json();
    }

    public function payment(Request $request, Dispatcher $dispatcher)
    {
        $token = $request->input('token');
        $amount = 0;
        foreach ($request->input('data') as $value) {
            $amount += $value['amount'] * $value['price'];
        }
        $phoneNumber = '0000000'. str_pad(auth()->user()->id, 4, 0, STR_PAD_LEFT);

        $params = [
            "aid" => env('MIX_ROBOTPAYMENT_AID'),
            "jb" => 'CAPTURE',
            "rt" => 0,
            "tkn" => $token,
            "pn" => $phoneNumber,
            "Em" => auth()->user()->email,
            "am" => $amount,
            "tx" => 0,
            "sf" => 0
        ];

        $client = new Client(['base_uri' => 'https://credit.j-payment.co.jp/']);
        $path = 'gateway/gateway_token.aspx';
        $options = [
            'http_errors' => false,
            'form_params' => $params
        ];

        $response = $client->request('POST', $path, $options);
        $responseBody = $response->getBody()->getContents();

        // 発注者に通知メール
        $mail = new Payment(auth()->user()->email, auth()->user()->name);
        $dispatcher->dispatch($mail);

        return response()->json($responseBody);
    }

    public function attachCreator(AttachCreatorPost $request, Dispatcher $dispatcher)
    {
        //dd($request->all());
        $proposition = Proposition::find($request->input('proposition_id'));
        $proposition->designer_id = $request->input('designer_id');
//        $proposition->progress = 1;
        $proposition->save();

        $creator = User::find($proposition->designer_id);
        $client = User::find($proposition->client_id);

        //email送信
        $toClient = new AttachedCreator($client);
        $toCreator = new AssignedProposition($creator);
        $dispatcher->dispatch($toClient);
        $dispatcher->dispatch($toCreator);

        return response()->json();
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getMessages($id)
    {
        $messages = DB::table('messages')
            ->where('messages.proposition_id', '=', $id)
            ->join('users', 'users.id', '=', 'messages.user_id')
            ->select('messages.*', 'users.name')
            ->orderBy('messages.created_at', 'asc')
            ->get();

        foreach ($messages as $key => $value) {
            $messages[$key]->author = ($value->user_id === auth()->user()->id) ? 'me' : $value->user_id;
        }

        return response()->json($messages);
    }

    public function getUsers($id)
    {
        $proposition = Proposition::find($id);

        $users[] = $proposition->client;
        $users[] = $proposition->designer;

        $res = [];
        foreach ($users as $key => $value) {
            if($value->id !== auth()->user()->id) $res[] = $value;
        }

        return response()->json($res);
    }

    public function storeMessage(StoreMessagePost $request)
    {
        DB::beginTransaction();
        try {
            $url = null;
            if($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = Storage::disk('s3')->putFile('message', $file, 'public');
                $url = Storage::disk('s3')->url($filename);
            }

            $message = Message::create([
                'user_id' => auth()->user()->id,
                'proposition_id' => $request->input('proposition_id'),
                'content' => $request->input('content'),
                'type' => $request->input('type'),
                'url' => $url
            ]);

            DB::commit();
            broadcast(new MessageCreated($message))->toOthers();
            return response()->json($message);

        } catch(Exception $e) {
            DB::rollback();
            return null;
        }
    }

    public function editProgress(EditProgressPost $request)
    {
        $proposition = Proposition::find($request->input('proposition_id'));
        $proposition->progress = $request->input('progress');
        $proposition->save();

        return response()->json();
    }

}
