<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Scraps;
use Illuminate\Support\Facades\Auth;

class WelcomeController extends SiteController
{
    public function __construct() {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $scraps = $this->getScraps();
        $scrapsPrivate = $this->getScrapsPrivate();

        $privateScraps = array();
        if ($this->auth::check()){
            if ($scrapsPrivate) {
                foreach ($scrapsPrivate as $key => $scrap) {
                    if ($scrap->share_user_ids){
                        $emails = $this->scraps->getShareAttribute('share_user_ids', $scrap->share_user_ids);
                        if (is_array($emails)){
                            foreach ($emails as $email) {
                                if ($email === $this->auth::user()->email){
                                    $privateScraps[$key] = $scrap;
                                    $user = $this->user::findOrFail($scrap->user_id);
                                    $privateScraps[$key]->author = $user;
                                }
                            }
                        }
                    }
                }
            }
        }
        return view('welcome')->with('scraps', $scraps)->with('privateScraps', $privateScraps);
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
    public function store(Request $request)
    {
        //
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

    public function getScrapsPrivate() {
        return $this->scraps->where([['publish', 1], ['private', 1]])->get();
    }

    public function getScraps() {
        return $this->scraps->where([['publish', 1], ['private', 0]])->get();
    }
}
