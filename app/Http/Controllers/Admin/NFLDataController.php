<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
use Inertia\Response;

class NFLDataController extends Controller
{
    public function index(): Response
    {
        return inertia('admin/nflData/GetNFLData');
    }
}
