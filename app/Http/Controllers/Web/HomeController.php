<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //For security
    public function index(){
        return "";
    }
    //Stripe Webhook needs this
    public function success(){
        return View("success");
    }

    //Stripe Webhook needs this
    public function cancel(){
        return "Yes";
    }
}
