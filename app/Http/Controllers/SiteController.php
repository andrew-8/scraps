<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\User;
use App\Scraps;
use Illuminate\Support\Facades\Auth;

class SiteController extends Controller
{
    protected $template;
    protected $title;
    protected $content;
    protected $vars;
    protected $auth;
    protected $user;
    protected $scraps;

    public function __construct() {
        $this->auth = new Auth();
        $this->user = new User();
        $this->scraps = new Scraps();
    }

    /**
     * Output parameters to template
     * @return $this
     * @throws \Throwable
     */
    protected function renderOutput(){
        $this->vars = Arr::add($this->vars,'title', $this->title);

        if($this->content) {
            $this->vars = Arr::add($this->vars, 'content', $this->content);
        }
        return view($this->template)->with($this->vars);
    }
}
