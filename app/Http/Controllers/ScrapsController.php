<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use http\Env;

class ScrapsController extends SiteController
{
    public function __construct() {
        parent::__construct();
        $this->middleware('auth');
        $this->template = 'admin.scraps';
    }

    /**
     ** Display a listing of the resource.
     * @return ScrapsController|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Throwable
     */
    public function index(){
        $scraps = $this->getScrapsByUser()->all();
        $this->title = 'My notes';

        if (is_array($scraps) && !empty($scraps['error'])){
            return redirect('/admin/scraps')->with($scraps);
        }

        $this->content = view('admin.layouts.scrapsContent')->with('scraps', $scraps)->render();
        return $this->renderOutput();
    }

    /**
     ** Show the form for creating a new resource.
     * @return ScrapsController
     * @throws \Throwable
     */
    public function create(){
        $this->title = 'Create new note';
        $this->content = view('admin.layouts.scrapCreate')->render();
        return $this->renderOutput();
    }

    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $rules = array(
            'title' => 'required|max:255',
            'text' => 'required'
        );

        if ($request['private']) {
            $rules['emails'] = 'required';
        } else { $request['emails'] = null;}

        $result = $this->validator($request, $rules, false);

        if (is_array($result) && !empty($result['error'])){
            return redirect('/admin/scraps/create')->with($result);
        }
        return redirect('/admin/scraps')->with($result);
    }

    /**
     * Display the specified resource.
     * @param $id
     * @return ScrapsController
     * @throws \Throwable
     */
    public function show(Request $request, $id) {
        $access = $edit = false;
        $scrap = $this->getScrapContent($id);
        if (is_array($scrap) && !empty($scrap['error'])){
            return redirect('/scrap/'. $id)->with($scrap);
        }

        if ($scrap->user_id === $this->auth::id()) {$edit = $access = true;}
        if ($scrap->user_id) { $scrap->user_id = $this->user::findOrFail($scrap->user_id); }


        if ($scrap->private) {
            if ($scrap->share_user_ids){
                $emails = $this->scraps->getShareAttribute('share_user_ids', $scrap->share_user_ids);
                if (is_array($emails)){
                    foreach ($emails as $email) {
                        if ($this->auth::check()){
                            if ($email === $this->auth::user()->email) {
                                $access = true;
                                continue;
                            }
                        }
                        if(isset($request->email) && $email === $request->email) {
                            $access = true;
                            continue;
                        }
                    }
                }
            }
        } else {$access = true;}


        return view('layouts.content')->with('scrap', $scrap)->with('access', $access)->with('edit', $edit)->render();
    }

    /**
     ** Show the form for editing the specified resource.
     * @param $id
     * @return ScrapsController
     * @throws \Throwable
     */
    public function edit($id) {
        $scrap = $this->getScrapByID($id);
        if ($scrap === null) abort(403);
        if (is_array($scrap) && !empty($scrap['error'])){
            return redirect('/admin/scraps/create')->with($scrap);
        }
        $this->title = 'Edit note - ' . $scrap->title;

        $emails = array();
        if ($scrap->private) {
            $scrap->share_user_ids = json_decode($scrap->share_user_ids);
            if (is_array($scrap->share_user_ids)) {
                foreach ($scrap->share_user_ids as $email) {
                    $emails[$email] = $email;
                }
            }
        }

        $this->content = view('admin.layouts.scrapCreate')->with('scrap', $scrap)->with('emails', $emails)->render();
        return $this->renderOutput();
    }

    /**
     ** Update the specified resource in storage.
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id){
        $rules = array(
            'title' => 'required|max:255',
            'text' => 'required'
        );
        if ($request['private']) {
            $rules['emails.*'] = 'required|email';
        } else { $request['emails'] = null;}

        $result = $this->validator($request, $rules, $id);

        if (is_array($result) && !empty($result['error'])){
            return redirect('/admin/scraps/'. $id .'/edit')->with($result);
        }
        return redirect('/admin/scraps')->with($result);
    }

    /**
     ** Remove the specified resource from storage.
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id){
        $result = $this->deleteScrapByID($id);

        if(is_array($result) && !empty($result['error'])) {
            return redirect('/admin/scraps')->with($result);
        }
        return redirect('/admin/scraps')->with($result);
    }

    /**
     * Validate of fields
     * @param $request
     * @param $rules
     * @param $id
     * @return array
     */
    public function validator($request, $rules, $id){
        $validator = Validator::make($request->all(), $rules, Lang::get('validation'), Lang::get('validation.attributes'));
        if ($validator->fails()) {
            return [
                'error' => $validator->errors()->all(),
                'class' => 'alert-danger'
            ];
        } else {
            return $this->actionScrap($request, $id);
        }
    }

    /**
     * Create and update scrap in storage
     * @param $request
     * @param $id
     * @return array
     */
    public function actionScrap($request, $id){
        $scrap = '';
        if ($id){
            $scrap = $this->scraps::where('id', $id)->first();
        }

        $data = $request->except('_token');

        $collection = collect($data);
        $data = $collection->filter(function ($value, $key) {
            return $value !== null;
        })->toArray();

        if (!$this->auth::check()) {
            return ['error' => 'Error! User not login!'];
        }

        $data['user_id'] = $this->auth::user()->getAuthIdentifier();
        if ($data['private']){
            $data['share_user_ids'] = $this->scraps->setShareAttribute('share_user_ids', $data['emails']);
        }

        if(empty($data)) {
            return ['error' => 'No data'];
        }

        if ($id){
            try {
                unset($data['emails']);
                $scrap->fill($data);
                if ($scrap->update()) {
                    if ($scrap->private){
                        $this->getEmails($scrap);
                    }
                    return ['status' => 'Note updated', 'class' => 'alert-success'];
                }
            } catch (\Exception $e) {
                return ['error' => "Error! Note not updated \n" . $e];
            }

        } else {
            try {
                if($newData = $this->scraps::create($data)) {
                    if ($newData->private){
                        $this->getEmails($newData);
                    }
                    return ['status' => 'Note added', 'class' => 'alert-success'];
                }
            } catch (\Exception $e) {
                return ['error' => "Error! Note not added \n" . $e];
            }
        }
    }

    /**
     * Delete by id from storage
     * @param $id
     * @return array
     */
    public function deleteScrapByID($id){
        try {
            $scrap = $this->scraps::where([['id', $id],['user_id', $this->auth::user()->getAuthIdentifier()]])->first();
            if($scrap->delete()) {
                return ['status' => 'Note '. $scrap->title .' deleted',
                    'class' => 'alert-success'];
            }
        } catch (\Exception $e) {
            return ['error' => "Error! Note not deleted \n" . $e];
        }
    }

    /**
     * Get Notes by User form storage
     * @return array
     */
    public function getScrapsByUser(){
        if (!$this->auth::check()){
            return [
                'error' => 'Error! User not login!',
            ];
        }
        try {
            return $this->scraps::where('user_id', $this->auth::user()->getAuthIdentifier())->get();
        } catch (\Exception $e) {
            return ['error' => "Error! Note not found \n" . $e];
        }

    }

    /**
     * Get Note by ID, UserID from storage
     * @param $id
     * @return mixed
     */
    public function getScrapByID($id){
        try {
            return $this->scraps::where([['id', $id], ['user_id', $this->auth::user()->getAuthIdentifier()]])->first();
        } catch (\Exception $e) {
            return ['error' => "Error! Note not found \n" . $e];
        }
    }

    /**
     * Get Note by ID from storage
     * @param $id
     * @return mixed
     */
    public function getScrapContent($id) {
        try {
            return $this->scraps::where('id', $id)->first();
        } catch (\Exception $e) {
            return ['error' => "Error! Note not found \n" . $e];
        }
    }

    /**
     * Search emails form storage
     * @param Request $request
     */
    public function searchEmails(Request $request) {
        $data = array();
        $emails = $this->user->queryLike($request['search']);
        if (!isset($emails['error']) && $emails){
            foreach ($emails as $key => $item){
                $data[$key]['value'] = $item->email;
                $data[$key]['text'] = $item->email;
            }
            echo json_encode($data);
        } elseif (isset($emails['error'])){
            echo $data['error'] = $emails['error'];
        }
    }

    /**
     * Get Emails form storage after create and update
     * @param array $data
     * @throws \Exception
     */
    public function getEmails($data) {
        $emails = $this->scraps->getShareAttribute('share_user_ids', $data->share_user_ids);
        if (is_array($emails)) {
            foreach ($emails as $email) {
                if (!$this->checkUserByEmail($email)){
                    $this->sendToCustomEmail($email, $data);
                }
            }
        }
    }

    /**
     * Check user by email
     * @param string $email
     * @return bool
     */
    public function checkUserByEmail(string $email) {
        if ($this->user::where('email', $email)->first()) {
            return true;
        } else {return false;}
    }

    /**
     * Send to custom email
     * @param string $email
     * @param array $data
     * @throws \Exception
     */
    public function sendToCustomEmail(string $email, $data = []){
        try {
            $data['user'] = $this->user::findOrFail($data['user_id']);
            $data['link'] = route('scrap', ['id' => $data->id, 'email' => $email]);
            $data['url'] = URL::to('/');

            Mail::send('layouts.email', ['data' => $data], function($message) use ($email) {
                $message->from($email, 'Scraps', $email);
                $message->to($email, 'Scraps')->subject('Note for you');
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
