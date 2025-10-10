<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coa;

class CoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coas = Coa::orderBy('kode_akun', 'asc')->get();
        return view('coa.index', compact('coas'));
    }
}
