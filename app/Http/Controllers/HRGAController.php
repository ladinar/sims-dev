<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Barang;
use Auth;
use DB;
use App\Cuti;
use App\Task;
use App\User;
use App\CutiDetil;
// use App\Notifications\CutiKaryawan;
use App\Mail\CutiKaryawan;
use Mail;
// use Notification;
use Excel;
use GuzzleHttp\Client;
use PDF;

class HRGAController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
    	$nik = Auth::User()->nik;
        $territory = DB::table('users')->select('id_territory')->where('nik', $nik)->first();
        $ter = $territory->id_territory;
        $division = DB::table('users')->select('id_division')->where('nik', $nik)->first();
        $div = $division->id_division;
        $position = DB::table('users')->select('id_position')->where('nik', $nik)->first();
        $pos = $position->id_position; 


        if($ter != null){
            $lead = DB::table('sales_lead_register')
                ->join('users', 'users.nik', '=', 'sales_lead_register.nik')
                ->join('tb_contact', 'sales_lead_register.id_customer', '=', 'tb_contact.id_customer')
                ->select('sales_lead_register.lead_id', 'tb_contact.id_customer', 'tb_contact.code', 'sales_lead_register.opp_name','tb_contact.customer_legal_name', 'tb_contact.brand_name','sales_lead_register.created_at', 'sales_lead_register.amount', 'users.name', 'sales_lead_register.result', 'sales_lead_register.status_sho')
                ->where('id_territory', $ter)
                ->get();
        } elseif($div == 'TECHNICAL PRESALES' && $pos == 'STAFF') {
            $lead = DB::table('sales_lead_register')
                ->join('users', 'users.nik', '=', 'sales_lead_register.nik')
                ->join('tb_contact', 'sales_lead_register.id_customer', '=', 'tb_contact.id_customer')
                ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
                ->select('sales_lead_register.lead_id','tb_contact.customer_legal_name', 'tb_contact.brand_name', 'sales_lead_register.opp_name',
                'sales_lead_register.created_at', 'sales_lead_register.amount', 'users.name', 'sales_lead_register.result', 'sales_solution_design.nik', 'sales_lead_register.status_sho')
                ->where('sales_solution_design.nik', $nik)
                ->get();
        }elseif($div == 'PMO' && $pos == 'MANAGER') {
            $lead = DB::table('sales_lead_register')
                ->join('users', 'users.nik', '=', 'sales_lead_register.nik')
                ->join('tb_contact', 'sales_lead_register.id_customer', '=', 'tb_contact.id_customer')
                ->select('sales_lead_register.lead_id','tb_contact.customer_legal_name', 'tb_contact.brand_name', 'sales_lead_register.opp_name',
                'sales_lead_register.created_at', 'sales_lead_register.amount', 'users.name', 'sales_lead_register.result', 'sales_lead_register.status_sho')
                ->where('sales_lead_register.result','WIN')
                ->get();
        }elseif($div == 'PMO' && $pos == 'STAFF') {
            $lead = DB::table('sales_lead_register')
                ->join('users', 'users.nik', '=', 'sales_lead_register.nik')
                ->join('tb_contact', 'sales_lead_register.id_customer', '=', 'tb_contact.id_customer')
                ->join('tb_pmo','sales_lead_register.lead_id','=','tb_pmo.lead_id')
                ->select('sales_lead_register.lead_id','tb_contact.customer_legal_name', 'tb_contact.brand_name', 'sales_lead_register.opp_name',
                'sales_lead_register.created_at', 'sales_lead_register.amount', 'users.name', 'sales_lead_register.result', 'sales_lead_register.status_sho','tb_pmo.pmo_nik')
                ->where('sales_lead_register.result','WIN')
                ->where('tb_pmo.pmo_nik',$nik)
                ->get();
        }
        elseif($div == 'FINANCE' && $pos == 'MANAGER') {
            $lead = DB::table('sales_lead_register')
                ->join('users', 'users.nik', '=', 'sales_lead_register.nik')
                ->join('tb_contact', 'sales_lead_register.id_customer', '=', 'tb_contact.id_customer')
                ->select('sales_lead_register.lead_id','tb_contact.customer_legal_name', 'tb_contact.brand_name', 'sales_lead_register.opp_name','sales_lead_register.created_at', 'sales_lead_register.amount', 'users.name', 'sales_lead_register.result', 'sales_lead_register.status_sho','sales_lead_register.nik')
                ->where('sales_lead_register.result','WIN')
                ->get();
        }
        elseif($div == 'FINANCE' && $pos == 'STAFF') {
            $lead = DB::table('sales_lead_register')
                ->join('users', 'users.nik', '=', 'sales_lead_register.nik')
                ->join('tb_contact', 'sales_lead_register.id_customer', '=', 'tb_contact.id_customer')
                ->select('sales_lead_register.lead_id','tb_contact.customer_legal_name', 'tb_contact.brand_name', 'sales_lead_register.opp_name','sales_lead_register.created_at', 'sales_lead_register.amount', 'users.name', 'sales_lead_register.result', 'sales_lead_register.status_sho','sales_lead_register.nik')
                ->where('sales_lead_register.result','WIN')
                ->get();
        }
        elseif($pos == 'ENGINEER MANAGER') {
            $lead = DB::table('sales_lead_register')
                ->join('users', 'users.nik', '=', 'sales_lead_register.nik')
                ->join('tb_contact', 'sales_lead_register.id_customer', '=', 'tb_contact.id_customer')
                ->select('sales_lead_register.lead_id','tb_contact.customer_legal_name', 'tb_contact.brand_name', 'sales_lead_register.opp_name','sales_lead_register.created_at', 'sales_lead_register.amount', 'users.name', 'sales_lead_register.result', 'sales_lead_register.status_sho','sales_lead_register.nik','sales_lead_register.status_engineer')
                ->where('sales_lead_register.result','WIN')
                ->where('sales_lead_register.status_sho','PMO')
                ->get();
        }
        elseif($pos == 'ENGINEER STAFF') {
            $lead = DB::table('sales_lead_register')
                ->join('users', 'users.nik', '=', 'sales_lead_register.nik')
                ->join('tb_contact', 'sales_lead_register.id_customer', '=', 'tb_contact.id_customer')
                ->join('tb_engineer','sales_lead_register.lead_id','=','tb_engineer.lead_id')
                ->select('sales_lead_register.lead_id','tb_contact.customer_legal_name', 'tb_contact.brand_name', 'sales_lead_register.opp_name','sales_lead_register.created_at', 'sales_lead_register.amount', 'users.name', 'sales_lead_register.result', 'sales_lead_register.status_sho','sales_lead_register.nik','sales_lead_register.status_engineer')
                ->where('sales_lead_register.result','WIN')
                 ->where('tb_engineer.nik',$nik)
                ->get();
        }
        else {
              $lead = DB::table('sales_lead_register')
                ->join('users', 'users.nik', '=', 'sales_lead_register.nik')
                ->join('tb_contact', 'sales_lead_register.id_customer', '=', 'tb_contact.id_customer')
                ->select('sales_lead_register.lead_id', 'tb_contact.id_customer', 'tb_contact.code', 'sales_lead_register.opp_name','tb_contact.customer_legal_name', 'tb_contact.brand_name',
                'sales_lead_register.created_at', 'sales_lead_register.amount', 'users.name', 'sales_lead_register.result', 'sales_lead_register.status_sho','sales_lead_register.nik')
                ->get();
        }

        /*  $presales = DB::table('sales_solution_design')
                    ->join('users','users.nik','=','sales_solution_design.nik')
                    ->select('sales_solution_design.lead_id','sales_solution_design.nik','sales_solution_design.assessment','sales_solution_design.pov','sales_solution_design.pd','sales_solution_design.pb','sales_solution_design.priority','sales_solution_design.project_size','users.name','sales_solution_design.status', 'sales_solution_design.assessment_date', 'sales_solution_design.pd_date', 'sales_solution_design.pov_date')*/
        if ($ter != null) {
            $total_ter = DB::table("sales_lead_register")
                        ->join('users', 'users.nik', '=', 'sales_lead_register.nik')
                        ->where('id_territory', $ter)
                        ->sum('amount');
        }elseif ($div == 'TECHNICAL PRESALES' && $pos == 'STAFF') {
            $total_ter = DB::table("sales_lead_register")
                        ->join('users', 'users.nik', '=', 'sales_lead_register.nik')
                        ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
                        ->where('sales_solution_design.nik', $nik)
                        ->sum('amount');
        }else{
            $total_ter = DB::table("sales_lead_register")
                        ->sum('amount');
        }

        if ($ter != null) {
            $notif = DB::table('sales_lead_register')
            ->select('opp_name','nik','lead_id')
            ->where('result','OPEN')
            ->orderBy('created_at','desc')
            ->get();
        }elseif ($div == 'TECHNICAL PRESALES' && $pos == 'STAFF') {
            $notif = DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik')
            ->where('result','OPEN')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }else{
             $notif = DB::table('sales_lead_register')
            ->select('opp_name','nik')
            ->where('result','OPEN')
            ->orderBy('created_at','desc')
            ->get();
        }

        if ($div == 'TECHNICAL PRESALES' && $pos == 'MANAGER') {
            $notifOpen= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_solution_design.lead_id')
            ->where('result','')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }elseif ($div == 'TECHNICAL PRESALES' && $pos == 'STAFF') {
            $notifOpen= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_solution_design.lead_id')
            ->where('result','')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }elseif ($div == 'SALES' && $pos == 'MANAGER') {
            $notifOpen= DB::table('sales_lead_register')
            ->select('opp_name','nik','lead_id')
            ->where('result','')
            ->orderBy('created_at','desc')
            ->get();
        }elseif ($div == 'SALES' && $pos == 'STAFF') {
            $notifOpen= DB::table('sales_lead_register')
            ->select('opp_name','nik','lead_id')
            ->where('result','')
            ->orderBy('created_at','desc')
            ->get();
        }else{
            $notifOpen= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_solution_design.lead_id')
            ->where('result','')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }

        if ($div == 'TECHNICAL PRESALES' && $pos == 'MANAGER') {
            $notifsd= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_solution_design.lead_id')
            ->where('result','SD')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }elseif ($div == 'TECHNICAL PRESALES' && $pos == 'STAFF') {
            $notifsd= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_solution_design.lead_id')
            ->where('result','SD')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }elseif ($div == 'SALES' && $pos == 'MANAGER') {
            $notifsd= DB::table('sales_lead_register')
            ->select('opp_name','nik','lead_id')
            ->where('result','SD')
            ->orderBy('created_at','desc')
            ->get();
        }elseif ($div == 'SALES' && $pos == 'STAFF') {
            $notifsd= DB::table('sales_lead_register')
            ->select('opp_name','nik','lead_id')
            ->where('result','SD')
            ->orderBy('created_at','desc')
            ->get();
        }else{
            $notifsd= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_solution_design.lead_id')
            ->where('result','SD')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }

        if ($div == 'TECHNICAL PRESALES' && $pos == 'MANAGER') {
            $notiftp= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_solution_design.lead_id')
            ->where('result','TP')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }elseif ($div == 'TECHNICAL PRESALES' && $pos == 'STAFF') {
            $notiftp= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_solution_design.lead_id')
            ->where('result','TP')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }elseif ($div == 'SALES' && $pos == 'MANAGER') {
            $notiftp= DB::table('sales_lead_register')
            ->select('opp_name','nik','lead_id')
            ->where('result','TP')
            ->orderBy('created_at','desc')
            ->get();
        }elseif ($div == 'SALES' && $pos == 'STAFF') {
            $notiftp= DB::table('sales_lead_register')
            ->select('opp_name','nik','lead_id')
            ->where('result','TP')
            ->orderBy('created_at','desc')
            ->get();
        }else{
            $notiftp= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_solution_design.lead_id')
            ->where('result','TP')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }

        $datas = Barang::orderBy('id_item', 'DESC')->paginate(5);

        $tasks = DB::table('tb_task')
                ->select('id_task','task_name','description','task_date')
                ->first();

        return view('HRGA/hrga', compact('lead', 'total_ter','notif','notifOpen','notifsd','notiftp','id_pro','datas','tasks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_delivery_person()
    {
        $nik = Auth::User()->nik;
        $territory = DB::table('users')->select('id_territory')->where('nik', $nik)->first();
        $ter = $territory->id_territory;
        $division = DB::table('users')->select('id_division')->where('nik', $nik)->first();
        $div = $division->id_division;
        $position = DB::table('users')->select('id_position')->where('nik', $nik)->first();
        $pos = $position->id_position; 

         if ($ter != null) {
            $notif = DB::table('sales_lead_register')
            ->select('opp_name','nik','lead_id')
            ->where('result','OPEN')
            ->orderBy('created_at','desc')
            ->get();
        }elseif ($div == 'TECHNICAL PRESALES' && $pos == 'STAFF') {
            $notif = DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik')
            ->where('result','OPEN')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }else{
             $notif = DB::table('sales_lead_register')
            ->select('opp_name','nik')
            ->where('result','OPEN')
            ->orderBy('created_at','desc')
            ->get();
        }

        if ($div == 'TECHNICAL PRESALES' && $pos == 'MANAGER') {
            $notifOpen= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_solution_design.lead_id')
            ->where('result','')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }elseif ($div == 'TECHNICAL PRESALES' && $pos == 'STAFF') {
            $notifOpen= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_solution_design.lead_id')
            ->where('result','')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }elseif ($div == 'SALES' && $pos == 'MANAGER') {
            $notifOpen= DB::table('sales_lead_register')
            ->select('opp_name','nik','lead_id')
            ->where('result','')
            ->orderBy('created_at','desc')
            ->get();
        }elseif ($div == 'SALES' && $pos == 'STAFF') {
            $notifOpen= DB::table('sales_lead_register')
            ->select('opp_name','nik','lead_id')
            ->where('result','')
            ->orderBy('created_at','desc')
            ->get();
        }else{
            $notifOpen= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_solution_design.lead_id')
            ->where('result','')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }

        if ($div == 'TECHNICAL PRESALES' && $pos == 'MANAGER') {
            $notifsd= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_solution_design.lead_id')
            ->where('result','SD')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }elseif ($div == 'TECHNICAL PRESALES' && $pos == 'STAFF') {
            $notifsd= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_solution_design.lead_id')
            ->where('result','SD')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }elseif ($div == 'SALES' && $pos == 'MANAGER') {
            $notifsd= DB::table('sales_lead_register')
            ->select('opp_name','nik','lead_id')
            ->where('result','SD')
            ->orderBy('created_at','desc')
            ->get();
        }elseif ($div == 'SALES' && $pos == 'STAFF') {
            $notifsd= DB::table('sales_lead_register')
            ->select('opp_name','nik','lead_id')
            ->where('result','SD')
            ->orderBy('created_at','desc')
            ->get();
        }else{
            $notifsd= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_solution_design.lead_id')
            ->where('result','SD')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }

        if ($div == 'TECHNICAL PRESALES' && $pos == 'MANAGER') {
            $notiftp= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_solution_design.lead_id')
            ->where('result','TP')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }elseif ($div == 'TECHNICAL PRESALES' && $pos == 'STAFF') {
            $notiftp= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_solution_design.lead_id')
            ->where('result','TP')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }elseif ($div == 'SALES' && $pos == 'MANAGER') {
            $notiftp= DB::table('sales_lead_register')
            ->select('opp_name','nik','lead_id')
            ->where('result','TP')
            ->orderBy('created_at','desc')
            ->get();
        }elseif ($div == 'SALES' && $pos == 'STAFF') {
            $notiftp= DB::table('sales_lead_register')
            ->select('opp_name','nik','lead_id')
            ->where('result','TP')
            ->orderBy('created_at','desc')
            ->get();
        }else{
            $notiftp= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_solution_design.lead_id')
            ->where('result','TP')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }

        return view('delivery/delivery_person',compact('notif','notifOpen','notifsd','notiftp'));
    }

    public function show_cuti(Request $request)
    {
        $nik = Auth::User()->nik;
        $territory = DB::table('users')->select('id_territory')->where('nik', $nik)->first();
        $ter = $territory->id_territory;
        $division = DB::table('users')->select('id_division')->where('nik', $nik)->first();
        $div = $division->id_division;
        $position = DB::table('users')->select('id_position')->where('nik', $nik)->first();
        $pos = $position->id_position;

        $cek = User::join('tb_cuti','tb_cuti.nik','=','users.nik','left')
                ->select('users.nik','cuti','cuti2','status_karyawan','status')->where('users.nik',$nik)->first();

        if ($cek->status == null) {
            $cek_cuti = User::select('users.nik','status_karyawan')->where('users.nik',$nik)->first();
        }else{
            $cek_cuti = User::join('tb_cuti','tb_cuti.nik','=','users.nik','left')
                ->select('users.nik','cuti','cuti2','status_karyawan','status')->where('users.nik',$nik)->orderBy('tb_cuti.id_cuti','desc')->first();
        }

        $total_cuti = $cek->cuti + $cek->cuti2;

        $year = date('Y');

        if ($ter != NULL) {
            if($div == 'SALES' && $pos == 'MANAGER'){
                $cuti = DB::table('tb_cuti')
                ->join('users','users.nik','=','tb_cuti.nik')
                ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                ->join('tb_position','tb_position.id_position','=','users.id_position')
                ->join('tb_division','tb_division.id_division','=','users.id_division')
                ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.id_position','users.id_territory')
                ->orderBy('tb_cuti.date_req','DESC')
                ->where('id_territory', $ter)
                ->groupby('id_cuti')
                ->get();


                $cuti2 = DB::table('tb_cuti')
                    ->join('users','users.nik','=','tb_cuti.nik')
                    ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                    ->join('tb_position','tb_position.id_position','=','users.id_position')
                    ->join('tb_division','tb_division.id_division','=','users.id_division')
                    ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti',DB::raw('COUNT(tb_cuti.id_cuti) as niks'),DB::raw('group_concat(date_off) as dates'),'users.id_position','users.email','users.id_territory')
                    ->orderBy('date_req','DESC')
                    ->groupby('tb_cuti.id_cuti')
                    ->where('id_territory', $ter)
                    ->where('tb_cuti.status','n')
                    ->groupby('nik')
                    ->get();
            } elseif($div == 'TECHNICAL' && $pos == 'ENGINEER MANAGER' && $ter == 'DPG'){
                $cuti = DB::table('tb_cuti')
                    ->join('users','users.nik','=','tb_cuti.nik')
                    ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                    ->join('tb_position','tb_position.id_position','=','users.id_position')
                    ->join('tb_division','tb_division.id_division','=','users.id_division')
                    ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.id_position','users.id_territory')
                    ->groupby('id_cuti')
                    ->orderBy('tb_cuti.date_req','DESC')
                    ->where('users.id_division','TECHNICAL')
                    ->where('users.id_territory','DPG')
                    ->get();

                $cuti2 = DB::table('tb_cuti')
                    ->join('users','users.nik','=','tb_cuti.nik')
                    ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                    ->join('tb_position','tb_position.id_position','=','users.id_position')
                    ->join('tb_division','tb_division.id_division','=','users.id_division')
                    ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti',DB::raw('COUNT(tb_cuti.id_cuti) as niks'),DB::raw('group_concat(date_off) as dates'),'users.id_position','users.email','users.id_territory')
                    ->orderBy('date_req','DESC')
                    ->groupby('tb_cuti.id_cuti')
                    ->where('users.id_division','TECHNICAL')
                    ->where('users.id_territory','DPG')
                    ->where('tb_cuti.status','n')
                    ->groupby('nik')
                    ->get();
            } elseif($div == 'TECHNICAL' && $ter == 'DVG' && $pos == 'MANAGER'){
                $cuti = DB::table('tb_cuti')
                    ->join('users','users.nik','=','tb_cuti.nik')
                    ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                    ->join('tb_position','tb_position.id_position','=','users.id_position')
                    ->join('tb_division','tb_division.id_division','=','users.id_division')
                    ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.id_position','users.id_territory') 
                    ->where('users.id_division','TECHNICAL')
                    ->where('users.id_territory','DVG')
                    ->orderBy('tb_cuti.date_req','DESC')
                    ->groupby('id_cuti')
                    ->orderBy('id_cuti','desc')
                    ->get();

                $cuti2 = DB::table('tb_cuti')
                    ->join('users','users.nik','=','tb_cuti.nik')
                    ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                    ->join('tb_position','tb_position.id_position','=','users.id_position')
                    ->join('tb_division','tb_division.id_division','=','users.id_division')
                    ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti',DB::raw('COUNT(tb_cuti.id_cuti) as niks'),DB::raw('group_concat(date_off) as dates'),'users.id_position','users.email','users.id_territory')
                    ->orderBy('date_req','DESC')
                    ->groupby('tb_cuti.id_cuti')
                    ->where('users.id_division','TECHNICAL')
                    ->where('users.id_territory','DVG')
                    ->where('tb_cuti.status','n')
                    ->groupby('nik')
                    ->get();

                $detail_cuti = DB::table('tb_cuti')
                            ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                            ->select('date_req','tb_cuti_detail.date_off','tb_cuti_detail.id_cuti')
                            ->where('tb_cuti_detail.id_cuti')
                            ->get();
            } elseif ($div == 'PMO' && $ter == 'OPERATION' && $pos == 'MANAGER') {
                $cuti = DB::table('tb_cuti')
                    ->join('users','users.nik','=','tb_cuti.nik')
                    ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                    ->join('tb_position','tb_position.id_position','=','users.id_position')
                    ->join('tb_division','tb_division.id_division','=','users.id_division')
                    ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.id_position','users.id_territory') 
                    ->where('users.id_division','PMO')
                    ->orderBy('tb_cuti.date_req','DESC')
                    ->groupby('id_cuti')
                    ->orderBy('id_cuti','desc')
                    ->get();


                $cuti2 = DB::table('tb_cuti')
                    ->join('users','users.nik','=','tb_cuti.nik')
                    ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                    ->join('tb_position','tb_position.id_position','=','users.id_position')
                    ->join('tb_division','tb_division.id_division','=','users.id_division')
                    ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti',DB::raw('COUNT(tb_cuti.id_cuti) as niks'),DB::raw('group_concat(date_off) as dates'),'users.id_position','users.email','users.id_territory')
                    ->orderBy('date_req','DESC')
                    ->groupby('tb_cuti.id_cuti')
                    ->where('users.id_division','PMO')
                    ->where('tb_cuti.status','n')
                    ->groupby('nik')
                    ->get();
            } elseif ($div == 'MSM' && $ter == 'OPERATION' && $pos == 'MANAGER') {
                $cuti = DB::table('tb_cuti')
                    ->join('users','users.nik','=','tb_cuti.nik')
                    ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                    ->join('tb_position','tb_position.id_position','=','users.id_position')
                    ->join('tb_division','tb_division.id_division','=','users.id_division')
                    ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.id_position','users.id_territory') 
                    ->where('users.id_division','MSM')
                    ->orderBy('tb_cuti.date_req','DESC')
                    ->groupby('id_cuti')
                    ->orderBy('id_cuti','desc')
                    ->get();


                $cuti2 = DB::table('tb_cuti')
                    ->join('users','users.nik','=','tb_cuti.nik')
                    ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                    ->join('tb_position','tb_position.id_position','=','users.id_position')
                    ->join('tb_division','tb_division.id_division','=','users.id_division')
                    ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti',DB::raw('COUNT(tb_cuti.id_cuti) as niks'),DB::raw('group_concat(date_off) as dates'),'users.id_position','users.email','users.id_territory')
                    ->orderBy('date_req','DESC')
                    ->groupby('tb_cuti.id_cuti')
                    ->where('users.id_division','MSM')
                    ->where('tb_cuti.status','n')
                    ->groupby('nik')
                    ->get();
            } elseif ($pos == 'OPERATION DIRECTOR') {
                $cuti = DB::table('tb_cuti')
                    ->join('users','users.nik','=','tb_cuti.nik')
                    ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                    ->join('tb_position','tb_position.id_position','=','users.id_position')
                    ->join('tb_division','tb_division.id_division','=','users.id_division')
                    ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.id_position','users.id_territory') 
                    ->where('users.id_position','MANAGER')
                    ->where('users.id_territory','OPERATION')
                    ->orwhere('users.id_position','OPERATION DIRECTOR')
                    ->orwhere('users.id_division','WAREHOUSE')
                    ->orderBy('tb_cuti.date_req','DESC')
                    ->groupby('id_cuti')
                    ->orderBy('id_cuti','desc')
                    ->get();


                $cuti2 = DB::table('tb_cuti')
                    ->join('users','users.nik','=','tb_cuti.nik')
                    ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                    ->join('tb_position','tb_position.id_position','=','users.id_position')
                    ->join('tb_division','tb_division.id_division','=','users.id_division')
                    ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti',DB::raw('COUNT(tb_cuti.id_cuti) as niks'),DB::raw('group_concat(date_off) as dates'),'users.id_position','users.email','users.id_territory')
                    ->orderBy('date_req','DESC')
                    ->groupby('tb_cuti.id_cuti')
                    ->where('users.id_position','MANAGER')
                    ->where('users.id_territory','OPERATION')
                    ->orwhere('users.id_position','OPERATION DIRECTOR')
                    ->orwhere('users.id_division','WAREHOUSE')
                    ->where('tb_cuti.status','n')
                    ->groupby('nik')
                    ->get();
            } else{
                $cuti = DB::table('tb_cuti')
                ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                ->join('users','users.nik','=','tb_cuti.nik')
                ->join('tb_position','tb_position.id_position','=','users.id_position')
                ->join('tb_division','tb_division.id_division','=','users.id_division')
                ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.id_position','users.id_territory') 
                ->groupby('id_cuti')
                ->orderBy('tb_cuti.date_req','DESC')
                ->where('users.nik',$nik)
                ->orderBy('id_cuti','desc')
                ->get();

                $cuti2 = DB::table('tb_cuti')
                    ->join('users','users.nik','=','tb_cuti.nik')
                    ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                    ->join('tb_position','tb_position.id_position','=','users.id_position')
                    ->join('tb_division','tb_division.id_division','=','users.id_division')
                    ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti',DB::raw('COUNT(tb_cuti.id_cuti) as niks'),DB::raw('group_concat(date_off) as dates'),'users.id_position','users.email','users.id_territory')
                    ->orderBy('date_req','DESC')
                    ->groupby('tb_cuti.id_cuti')
                    ->where('tb_cuti.status','n')
                    ->where('users.nik',$nik)
                    ->groupby('nik')
                    ->get();

                $detail_cuti = DB::table('tb_cuti')
                            ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                            ->select('date_req','tb_cuti_detail.date_off','tb_cuti_detail.id_cuti')
                            ->where('tb_cuti_detail.id_cuti')
                            ->get();
            }
        }elseif($div == 'TECHNICAL PRESALES' && $pos == 'MANAGER'){
        	$cuti = DB::table('tb_cuti')
            ->join('users','users.nik','=','tb_cuti.nik')
            ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
            ->join('tb_position','tb_position.id_position','=','users.id_position')
            ->join('tb_division','tb_division.id_division','=','users.id_division')
            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.id_position')
            ->where('users.id_division','TECHNICAL PRESALES','users.id_territory')
            ->groupby('id_cuti')
            ->orderBy('id_cuti','desc')
            ->get();


            $cuti2 = DB::table('tb_cuti')
                ->join('users','users.nik','=','tb_cuti.nik')
                ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                ->join('tb_position','tb_position.id_position','=','users.id_position')
                ->join('tb_division','tb_division.id_division','=','users.id_division')
                ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti',DB::raw('COUNT(tb_cuti.id_cuti) as niks'),DB::raw('group_concat(date_off) as dates'),'users.id_position','users.email','users.id_territory')
                ->orderBy('date_req','DESC')
                ->groupby('tb_cuti.id_cuti')
                ->where('tb_cuti.status','n')
                ->where('users.id_division','TECHNICAL PRESALES','users.id_territory')
                ->groupby('nik')
                ->get();
        }elseif($div == 'TECHNICAL' && $pos == 'MANAGER'){
            $cuti = DB::table('tb_cuti')
            ->join('users','users.nik','=','tb_cuti.nik')
            ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
            ->join('tb_position','tb_position.id_position','=','users.id_position')
            ->join('tb_division','tb_division.id_division','=','users.id_division')
            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.id_position','users.id_territory')
            ->where('users.id_division','TECHNICAL')
            ->groupby('id_cuti')
            ->orderBy('id_cuti','desc')
            ->get();


            $cuti2 = DB::table('tb_cuti')
                ->join('users','users.nik','=','tb_cuti.nik')
                ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                ->join('tb_position','tb_position.id_position','=','users.id_position')
                ->join('tb_division','tb_division.id_division','=','users.id_division')
                ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti',DB::raw('COUNT(tb_cuti.id_cuti) as niks'),DB::raw('group_concat(date_off) as dates'),'users.id_position','users.email','users.id_territory')
                ->where('users.id_division','TECHNICAL')
                ->orderBy('date_req','DESC')
                ->groupby('tb_cuti.id_cuti')
                ->where('tb_cuti.status','n')
                ->groupby('nik')
                ->get();
        }elseif($div == 'FINANCE' && $pos == 'MANAGER'){
        	$cuti = DB::table('tb_cuti')
            ->join('users','users.nik','=','tb_cuti.nik')
            ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
            ->join('tb_position','tb_position.id_position','=','users.id_position')
            ->join('tb_division','tb_division.id_division','=','users.id_division')
            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.id_position','users.id_territory')
            ->where('users.id_division','FINANCE')
            ->groupby('id_cuti')
            ->orderBy('id_cuti','desc')
            ->get();


            $cuti2 = DB::table('tb_cuti')
                ->join('users','users.nik','=','tb_cuti.nik')
                ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                ->join('tb_position','tb_position.id_position','=','users.id_position')
                ->join('tb_division','tb_division.id_division','=','users.id_division')
                ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti',DB::raw('COUNT(tb_cuti.id_cuti) as niks'),DB::raw('group_concat(date_off) as dates'),'users.id_position','users.email','users.id_territory')
                ->orderBy('date_req','DESC')
                ->groupby('tb_cuti.id_cuti')
                ->where('users.id_division','FINANCE')
                ->groupby('nik')
                ->where('tb_cuti.status','n')
                ->get();
        }elseif($div == 'TECHNICAL DVG' && $pos == 'STAFF' || $div == 'TECHNICAL DPG' && $pos == 'ENGINEER STAFF' || $div == 'TECHNICAL PRESALES' && $pos == 'STAFF' || $div == 'FINANCE' && $pos == 'STAFF' || $div == 'PMO' && $pos == 'STAFF' || $pos == 'ADMIN'){
        	$cuti = DB::table('tb_cuti')
                ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                ->join('users','users.nik','=','tb_cuti.nik')
                ->join('tb_position','tb_position.id_position','=','users.id_position')
                ->join('tb_division','tb_division.id_division','=','users.id_division')
                ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.id_position','users.id_territory') 
                ->groupby('id_cuti')
                ->orderBy('tb_cuti.date_req','DESC')
                ->where('users.nik',$nik)
                ->orderBy('id_cuti','desc')
                ->get();

            $cuti2 = DB::table('tb_cuti')
                ->join('users','users.nik','=','tb_cuti.nik')
                ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                ->join('tb_position','tb_position.id_position','=','users.id_position')
                ->join('tb_division','tb_division.id_division','=','users.id_division')
                ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti',DB::raw('COUNT(tb_cuti.id_cuti) as niks'),DB::raw('group_concat(date_off) as dates'),'users.id_position','users.email','users.id_territory')
                ->orderBy('date_req','DESC')
                ->groupby('tb_cuti.id_cuti')
                ->where('tb_cuti.status','n')
                ->where('users.nik',$nik)
                ->groupby('nik')
                ->get();
        }elseif($div == 'HR' && $pos == 'HR MANAGER'){
	        $cuti = DB::table('tb_cuti')
	            ->join('users','users.nik','=','tb_cuti.nik')
	            ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
	            ->join('tb_position','tb_position.id_position','=','users.id_position')
	            ->join('tb_division','tb_division.id_division','=','users.id_division')
	            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti',DB::raw('COUNT(tb_cuti.id_cuti) as niks'),DB::raw('group_concat(date_off) as dates'),'users.id_position','users.email','users.id_territory')
                ->orderBy('date_req','DESC')
	            ->where('status','v')
                ->orwhere('tb_position.id_position','not like', '%STAFF%')
	            ->groupby('tb_cuti.id_cuti')
	            ->groupby('nik')
	            ->get();

            $cuti2 = DB::table('tb_cuti')
                ->join('users','users.nik','=','tb_cuti.nik')
                ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                ->join('tb_position','tb_position.id_position','=','users.id_position')
                ->join('tb_division','tb_division.id_division','=','users.id_division')
                ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti',DB::raw('COUNT(tb_cuti.id_cuti) as niks'),DB::raw('group_concat(date_off) as dates'),'users.id_position','users.email','users.id_territory')
                ->orderBy('date_req','DESC')
                ->groupby('tb_cuti.id_cuti')
                ->where('tb_cuti.status','n')
                ->groupby('nik')
                ->get();


	        $cuti_index = DB::table('users')
	            ->join('tb_cuti','tb_cuti.nik','=','users.nik')
                ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
	            ->join('tb_position','tb_position.id_position','=','users.id_position')
	            ->join('tb_division','tb_division.id_division','=','users.id_division')
	            ->select('users.nik','users.date_of_entry','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','users.cuti',DB::raw('COUNT(tb_cuti_detail.id_cuti) as niks'),DB::raw('DATEDIFF(NOW(),date_of_entry) AS date_of_entrys'),'users.email','users.cuti2','users.status_karyawan')
	            ->groupby('tb_cuti.nik')
	            ->get();

	        $cuti_list = DB::table('users')
	            ->join('tb_position','tb_position.id_position','=','users.id_position')
	            ->join('tb_division','tb_division.id_division','=','users.id_division')
	            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','users.cuti','users.date_of_entry',DB::raw('DATEDIFF(NOW(),date_of_entry) AS date_of_entrys'),'users.email','users.cuti2','users.status_karyawan')
	            ->whereNotIn('nik',function($query) { 
	            	$query->select('nik')->from('tb_cuti');
	            })
	            ->get();

        }else{
        	$cuti = DB::table('tb_cuti')
                    ->join('users','users.nik','=','tb_cuti.nik')
                    ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                    ->join('tb_position','tb_position.id_position','=','users.id_position')
                    ->join('tb_division','tb_division.id_division','=','users.id_division')
                    ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.id_position','users.id_territory')
                    ->groupby('id_cuti')
                    ->orderBy('id_cuti','desc')
                    ->get();

            $cuti2 = DB::table('tb_cuti')
                    ->join('users','users.nik','=','tb_cuti.nik')
                    ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                    ->join('tb_position','tb_position.id_position','=','users.id_position')
                    ->join('tb_division','tb_division.id_division','=','users.id_division')
                    ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti',DB::raw('COUNT(tb_cuti.id_cuti) as niks'),DB::raw('group_concat(date_off) as dates'),'users.id_position','users.email','users.id_territory')
                    ->orderBy('date_req','DESC')
                    ->groupby('tb_cuti.id_cuti')
                    ->where('tb_cuti.status','n')
                    ->groupby('nik')
                    ->get();
        }

        $client = new Client();
        $api_response = $client->get('https://www.googleapis.com/calendar/v3/calendars/en.indonesian%23holiday%40group.v.calendar.google.com/events?key='.env('GOOGLE_API_YEY'));
        $json = (string)$api_response->getBody();
        $datas_nasional = json_decode($json, true);
       

        $bulan = date('F');
        $tahun_ini = date('Y');
        $tahun_lalu = date('Y') - 1;

        if ($ter != null) {
            $notif = DB::table('sales_lead_register')
            ->select('opp_name','nik','lead_id')
            ->where('result','OPEN')
            ->orderBy('created_at','desc')
            ->get();
        }elseif ($div == 'TECHNICAL PRESALES' && $pos == 'STAFF') {
            $notif = DB::table('sales_lead_register')
            ->select('opp_name','nik','lead_id')
            ->where('result','OPEN')
            ->orderBy('created_at','desc')
            ->get();
        }else{
            $notif = DB::table('sales_lead_register')
            ->select('opp_name','nik','lead_id')
            ->where('result','OPEN')
            ->orderBy('created_at','desc')
            ->get();
        }

        if ($div == 'TECHNICAL PRESALES' && $pos == 'MANAGER') {
            $notifOpen= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_lead_register.lead_id')
            ->where('result','')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }elseif ($div == 'TECHNICAL PRESALES' && $pos == 'STAFF') {
            $notifOpen= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_lead_register.lead_id')
            ->where('result','')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }else{
            $notifOpen= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_lead_register.lead_id')
            ->where('result','')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }

        if ($div == 'TECHNICAL PRESALES' && $pos == 'MANAGER') {
            $notifsd= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_lead_register.lead_id')
            ->where('result','SD')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }elseif ($div == 'TECHNICAL PRESALES' && $pos == 'STAFF') {
            $notifsd= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_lead_register.lead_id')
            ->where('result','SD')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }elseif ($div == 'SALES' && $pos == 'MANAGER') {
            $notifsd= DB::table('sales_lead_register')
            ->select('opp_name','nik','lead_id')
            ->where('result','SD')
            ->orderBy('created_at','desc')
            ->get();
        }elseif ($div == 'SALES' && $pos == 'STAFF') {
            $notifsd= DB::table('sales_lead_register')
            ->select('opp_name','nik','lead_id')
            ->where('result','SD')
            ->orderBy('created_at','desc')
            ->get();
        }else{
            $notifsd= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_lead_register.lead_id')
            ->where('result','SD')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }

        if ($div == 'TECHNICAL PRESALES' && $pos == 'MANAGER') {
            $notiftp= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_solution_design.lead_id')
            ->where('result','TP')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }elseif ($div == 'TECHNICAL PRESALES' && $pos == 'STAFF') {
            $notiftp= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_solution_design.lead_id')
            ->where('result','TP')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }elseif ($div == 'SALES' && $pos == 'MANAGER') {
            $notiftp= DB::table('sales_lead_register')
            ->select('opp_name','nik','lead_id')
            ->where('result','TP')
            ->orderBy('created_at','desc')
            ->get();
        }elseif ($div == 'SALES' && $pos == 'STAFF') {
            $notiftp= DB::table('sales_lead_register')
            ->select('opp_name','nik','lead_id')
            ->where('result','TP')
            ->orderBy('created_at','desc')
            ->get();
        }else{
            $notiftp= DB::table('sales_lead_register')
            ->join('sales_solution_design', 'sales_solution_design.lead_id', '=', 'sales_lead_register.lead_id')
            ->select('sales_lead_register.opp_name','sales_solution_design.nik','sales_solution_design.lead_id')
            ->where('result','TP')
            ->orderBy('sales_lead_register.created_at','desc')
            ->get();
        }

        if (Auth::User()->id_position == 'ADMIN') {
            $notifClaim = DB::table('dvg_esm')
                            ->select('nik_admin', 'personnel', 'type')
                            ->where('status', 'ADMIN')
                            ->get();
        } elseif (Auth::User()->id_position == 'HR MANAGER') {
            $notifClaim = DB::table('dvg_esm')
                            ->select('nik_admin', 'personnel', 'type')
                            ->where('status', 'HRD')
                            ->get();
        } elseif (Auth::User()->id_division == 'FINANCE') {
            $notifClaim = DB::table('dvg_esm')
                            ->select('nik_admin', 'personnel', 'type')
                            ->where('status', 'FINANCE')
                            ->get();
        }

        return view('HR/cuti', compact('notif','notifOpen','notifsd','notiftp','cuti','cuti_index','cuti_list','detail_cuti','notifClaim','cek_cuti','total_cuti','year','datas_nasional','bulan','tahun_ini','tahun_lalu','cuti2','cek'));
    }

    public function detil_cuti(Request $request)
    {
        $cuti = $request->cuti;

        if ($request->pilih == 'date') {
            return array(DB::table('tb_cuti_detail')
                ->join('tb_cuti','tb_cuti.id_cuti','=','tb_cuti_detail.id_cuti')
                ->join('users','users.nik','=','tb_cuti.nik')
                ->select('date_off','reason_leave','date_req','tb_cuti_detail.id_cuti','users.nik')
                ->where('tb_cuti_detail.id_cuti',$cuti)
                ->whereBetween('date_off',array($request->date_start,$request->date_end))
                ->get(),(int)$request->$cuti);
        }else{
            return array(DB::table('tb_cuti_detail')
                ->join('tb_cuti','tb_cuti.id_cuti','=','tb_cuti_detail.id_cuti')
                ->join('users','users.nik','=','tb_cuti.nik')
                ->select('date_off','reason_leave','date_req','tb_cuti_detail.id_cuti','users.nik')
                ->where('tb_cuti_detail.id_cuti',$cuti)
                ->get(),(int)$request->$cuti);
        }
        
    }

    public function store_cuti(Request $request)
    {
        // $hari = DB::table('tb_cuti')
        //             ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
        //             ->select(db::raw('count(tb_cuti_detail.id_cuti) as days'),'tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.status',DB::raw('group_concat(date_off) as dates'))
        //             ->groupby('tb_cuti_detail.id_cuti')
        //             ->first();

        // $array = explode(',',$hari->dates);

        // return $array;

        $nik = Auth::User()->nik;
        $territory = DB::table('users')->select('id_territory')->where('nik', $nik)->first();
        $ter = $territory->id_territory;
        $division = DB::table('users')->select('id_division')->where('nik', $nik)->first();
        $div = $division->id_division;
        $position = DB::table('users')->select('id_position')->where('nik', $nik)->first();
        $pos = $position->id_position; 
        $company = DB::table('users')->select('id_company')->where('nik',$nik)->first();
        $com = $company->id_company;

        $nik = Auth::User()->nik;
        $date_now = date('Y-m-d');
        
        $array =  explode(',', $_POST['date_start']);

        $hitung = sizeof($array);

    	$tambah = new Cuti();
        $tambah->nik = $nik;
        $tambah->date_req = $date_now;
        $tambah->reason_leave = $request['reason'];
        $tambah->jenis_cuti = $request['jenis_cuti'];
        $tambah->status = 'n';
        $tambah->save();

        foreach ($array as $dates) {
            $store = new CutiDetil();
            $store->id_cuti = $tambah->id_cuti;
            $format_start_s = strtotime($dates);
            $store->date_off = date("Y-m-d",$format_start_s);
            $store->save();
        }

        $id_cuti    = $tambah->id_cuti;
        $getStatus  = Cuti::select('status')->where('id_cuti',$id_cuti)->first();
        $status     = $getStatus->status;

        if ($ter != NULL) {
            if ($pos == 'MANAGER' || $pos == 'ENGINEER MANAGER') {
                if ($div == 'PMO') {
                    $nik_kirim = DB::table('users')->select('users.email')->where('email','firman@sinergy.co.id')->where('id_company','1')->first();
                }else if ($div == 'FINANCE' || $div == 'SALES') {
                    $nik_kirim = DB::table('users')->select('users.email')->where('email','rony@sinergy.co.id')->where('id_company','1')->first();
                }else{
                    $nik_kirim = DB::table('users')->select('users.email')->where('email','nabil@sinergy.co.id')->where('id_company','1')->first();
                }
            }else if ($ter == 'DPG') {
                $nik_kirim = DB::table('users')->select('users.email')->where('id_position','ENGINEER MANAGER')->where('id_company','1')->first();
            }else if ($div == 'WAREHOUSE'){
                $nik_kirim = DB::table('users')->select('users.email')->where('email','firman@sinergy.co.id')->where('id_company','1')->first();
            }else{
                $nik_kirim = DB::table('users')->select('users.email')->where('id_territory',Auth::User()->id_territory)->where('id_position','MANAGER')->where('id_division',Auth::User()->id_division)->where('id_company','1')->first();
            }
        	
    		$kirim = User::where('email', $nik_kirim->email)->first()->email;
            // $kirim = User::where('email', 'ladinar@sinergy.co.id')->first()->email;

            $name_cuti = DB::table('tb_cuti')
                ->join('users','users.nik','=','tb_cuti.nik')
                ->select('users.name')
                ->where('id_cuti', $id_cuti)->first();

            $hari = DB::table('tb_cuti')
                    ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                    ->select(db::raw('count(tb_cuti_detail.id_cuti) as days'),'tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.status',DB::raw('group_concat(date_off) as dates'))
                    ->groupby('tb_cuti_detail.id_cuti')
                    ->where('tb_cuti.id_cuti', $id_cuti)
                    ->first();

            $ardetil = explode(',',$hari->dates);

            Mail::to($kirim)->send(new CutiKaryawan($name_cuti,$hari,$ardetil,'[SIMS-App] Permohonan Cuti'));
            
            
        }else{
            if ($div == 'HR') {
                if($pos == 'HR MANAGER'){
                    $nik_kirim = DB::table('users')->select('users.email')->where('email','rony@sinergy.co.id')->where('id_company','1')->first();
                }else{
                    $nik_kirim = DB::table('users')->select('users.email')->where('id_position','HR MANAGER')->where('id_division',Auth::User()->id_division)->where('id_company','1')->first();
                }
            }else if($div == 'MANAGER'){
                $nik_kirim = DB::table('users')->select('users.email')->where('email','rony@sinergy.co.id')->where('id_company','1')->first();
            }else{
                $nik_kirim = DB::table('users')->select('users.email')->where('id_position','MANAGER')->where('id_division',Auth::User()->id_division)->where('id_company','1')->first();
            }
        	
    		// $kirim = User::where('email', 'ladinar@sinergy.co.id')->get();
            //
            $kirim = User::where('email', $nik_kirim->email)->first()->email;

            $name_cuti = DB::table('tb_cuti')
                ->join('users','users.nik','=','tb_cuti.nik')
                ->select('users.name')
                ->where('id_cuti', $id_cuti)->first();

            $hari = DB::table('tb_cuti')
                    ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                    ->select(db::raw('count(tb_cuti_detail.id_cuti) as days'),'tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.status',DB::raw('group_concat(date_off) as dates'))
                    ->groupby('tb_cuti_detail.id_cuti')
                    ->where('tb_cuti.id_cuti', $id_cuti)
                    ->first();

            $ardetil = explode(',',$hari->dates);



            Mail::to($kirim)->send(new CutiKaryawan($name_cuti,$hari,$ardetil,'[SIMS-App] Approve - Permohonan Cuti'));


        	
        }

        return redirect()->back();
    }

    public function approve_cuti(Request $request)
    {
        $nik = Auth::User()->nik;
        $territory = DB::table('users')->select('id_territory')->where('nik', $nik)->first();
        $ter = $territory->id_territory;
        $division = DB::table('users')->select('id_division')->where('nik', $nik)->first();
        $div = $division->id_division;
        $position = DB::table('users')->select('id_position')->where('nik', $nik)->first();
        $pos = $position->id_position; 
        $company = DB::table('users')->select('id_company')->where('nik',$nik)->first();
        $com = $company->id_company;
        
        $id_cuti = $request['id_cuti_detil'];
        $nik = $request['nik_cuti'];

        $update = Cuti::where('id_cuti',$id_cuti)->first();
        $update->decline_reason = null;
        $update->status = 'v';
        $update->update();

        $array =  explode(',', $_POST['cuti_fix']);

        $delete = CutiDetil::where('id_cuti',$id_cuti)->delete();

        foreach ($array as $dates) {
            $update = new CutiDetil();
            $update->id_cuti = $id_cuti;
            $format_start_s = strtotime($dates);
            $update->date_off = date("Y-m-d",$format_start_s);
            $update->save();
        }

        $hitung = sizeof($array);

        $update_cuti = User::where('nik',$nik)->first();
        
        if ($hitung >= $update_cuti->cuti) {
            $ambil2020 = $hitung - $update_cuti->cuti;

            $hasilsisa = $update_cuti->cuti2 - $ambil2020;

            if ($ambil2020 == 0) {
                $update_cuti->cuti = $update_cuti->cuti - $hitung;
            }else{
                $update_cuti->cuti = 0;
                $update_cuti->cuti2 = $hasilsisa;
            }

        }else{
            $update_cuti->cuti = $update_cuti->cuti - $hitung;
        }
        
        $update_cuti->update();

        $getStatus  = Cuti::select('status')->where('id_cuti',$id_cuti)->first();
        $status     = $getStatus->status;

        $nik_kirim = DB::table('tb_cuti')->join('users','users.nik','=','tb_cuti.nik')->select('users.email')->where('id_cuti',$id_cuti)->first();
        $kirim = User::where('email',$nik_kirim->email)
                        ->get();

        $name_cuti = DB::table('tb_cuti')
                ->join('users','users.nik','=','tb_cuti.nik')
                ->select('users.name')
                ->where('id_cuti', $id_cuti)->first();

        $hari = DB::table('tb_cuti')
                ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                ->select(db::raw('count(tb_cuti_detail.id_cuti) as days'),'tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.status',DB::raw('group_concat(date_off) as dates'))
                ->groupby('tb_cuti_detail.id_cuti')
                ->where('tb_cuti.id_cuti', $id_cuti)
                ->first();

        $ardetil = explode(',', $hari->dates); 

        Mail::to($kirim)->cc('yudhi@sinergy.co.id')->send(new CutiKaryawan($name_cuti,$hari,$ardetil,'[SIMS-App] Approve - Permohonan Cuti'));        

        // Notification::send($kirim, new CutiKaryawan($id_cuti,$status));

        return redirect()->back();
    }

    public function decline_cuti(Request $request)
    {
        $id_cuti = $request['id_cuti_decline'];

        $update = Cuti::where('id_cuti',$id_cuti)->first();
        $update->decline_reason = $request['keterangan'];
        $update->status = 'd';
        $update->update();

        // $kirim = User::where('email', 'ladinar@sinergy.co.id')->get();
        $nik_kirim = DB::table('tb_cuti')->join('users','users.nik','=','tb_cuti.nik')->select('users.email')->where('id_cuti',$id_cuti)->first();
            //
        $kirim = User::where('email', $nik_kirim->email)->first()->email;

        $name_cuti = DB::table('tb_cuti')
            ->join('users','users.nik','=','tb_cuti.nik')
            ->select('users.name')
            ->where('id_cuti', $id_cuti)->first();

        $hari = DB::table('tb_cuti')
                ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                ->select(db::raw('count(tb_cuti_detail.id_cuti) as days'),'tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.status','tb_cuti.decline_reason',DB::raw('group_concat(date_off) as dates'))
                ->groupby('tb_cuti_detail.id_cuti')
                ->where('tb_cuti.id_cuti', $id_cuti)
                ->first();

        $ardetil = explode(',', $hari->dates); 

        Mail::to($kirim)->send(new CutiKaryawan($name_cuti,$hari,$ardetil,'[SIMS-App] Decline - Permohonan Cuti'));

        return redirect()->back();
    }

    public function update_cuti(Request $request)
    {
        $id_cuti = $request['id_cuti'];

        $array =  explode(',', $_POST['Dates']);

        $delete = CutiDetil::where('id_cuti',$id_cuti)->delete();

        foreach ($array as $dates) {
            $update = new CutiDetil();
            $update->id_cuti = $id_cuti;
            $format_start_s = strtotime($dates);
            $update->date_off = date("Y-m-d",$format_start_s);
            $update->save();
        }

        $update = Cuti::where('id_cuti',$id_cuti)->first();
        $update->reason_leave = $request['reason_edit'];
        $update->update();

        return redirect()->back();
    }

    public function delete_cuti($id_cuti)
    {
        $hapus = Cuti::find($id_cuti);
        $hapus->delete();

        return redirect()->back();
    }

    public function follow_up($id_cuti)
    {
        $nik = Auth::User()->nik;
        $territory = DB::table('users')->select('id_territory')->where('nik', $nik)->first();
        $ter = $territory->id_territory;
        $division = DB::table('users')->select('id_division')->where('nik', $nik)->first();
        $div = $division->id_division;
        $position = DB::table('users')->select('id_position')->where('nik', $nik)->first();
        $pos = $position->id_position; 
        $company = DB::table('users')->select('id_company')->where('nik',$nik)->first();
        $com = $company->id_company;

        if ($ter != NULL) {
            if ($pos == 'MANAGER' || $pos == 'ENGINEER MANAGER') {
                if ($div == 'PMO') {
                    $nik_kirim = DB::table('users')->select('users.email')->where('email','firman@sinergy.co.id')->where('id_company','1')->first();
                }else if ($div == 'FINANCE' || $div == 'SALES') {
                    $nik_kirim = DB::table('users')->select('users.email')->where('email','rony@sinergy.co.id')->where('id_company','1')->first();
                }else{
                    $nik_kirim = DB::table('users')->select('users.email')->where('email','nabil@sinergy.co.id')->where('id_company','1')->first();
                }
            }else if ($ter == 'DPG') {
                $nik_kirim = DB::table('users')->select('users.email')->where('id_position','ENGINEER MANAGER')->where('id_company','1')->first();
            }else if ($div == 'WAREHOUSE'){
                $nik_kirim = DB::table('users')->select('users.email')->where('email','firman@sinergy.co.id')->where('id_company','1')->first();
            }else{
                $nik_kirim = DB::table('users')->select('users.email')->where('id_territory',Auth::User()->id_territory)->where('id_position','MANAGER')->where('id_division',Auth::User()->id_division)->where('id_company','1')->first();
            }
            
            $kirim = User::where('email', $nik_kirim->email)->first()->email;
            // $kirim = User::where('email', 'ladinar@sinergy.co.id')->first()->email;

            $name_cuti = DB::table('tb_cuti')
                ->join('users','users.nik','=','tb_cuti.nik')
                ->select('users.name')
                ->where('id_cuti', $id_cuti)->first();

            $hari = DB::table('tb_cuti')
                    ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                    ->select(db::raw('count(tb_cuti_detail.id_cuti) as days'),'tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.status',DB::raw('group_concat(date_off) as dates'))
                    ->groupby('tb_cuti_detail.id_cuti')
                    ->where('tb_cuti.id_cuti', $id_cuti)
                    ->first();

            $ardetil = explode(',',$hari->dates);

            Mail::to($kirim)->send(new CutiKaryawan($name_cuti,$hari,$ardetil,'[SIMS-App] Permohonan Cuti (Follow Up)'));
            
            
        }else{
            if ($div == 'HR') {
                if($pos == 'HR MANAGER'){
                    $nik_kirim = DB::table('users')->select('users.email')->where('email','rony@sinergy.co.id')->where('id_company','1')->first();
                }else{
                    $nik_kirim = DB::table('users')->select('users.email')->where('id_position','HR MANAGER')->where('id_division',Auth::User()->id_division)->where('id_company','1')->first();
                }
            }else if($div == 'MANAGER'){
                $nik_kirim = DB::table('users')->select('users.email')->where('email','rony@sinergy.co.id')->where('id_company','1')->first();
            }else{
                $nik_kirim = DB::table('users')->select('users.email')->where('id_position','MANAGER')->where('id_division',Auth::User()->id_division)->where('id_company','1')->first();
            }
            
            // $kirim = User::where('email', 'ladinar@sinergy.co.id')->get();
            //
            $kirim = User::where('email', $nik_kirim->email)->first()->email;

            $name_cuti = DB::table('tb_cuti')
                ->join('users','users.nik','=','tb_cuti.nik')
                ->select('users.name')
                ->where('id_cuti', $id_cuti)->first();

            $hari = DB::table('tb_cuti')
                    ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                    ->select(db::raw('count(tb_cuti_detail.id_cuti) as days'),'tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.status',DB::raw('group_concat(date_off) as dates'))
                    ->groupby('tb_cuti_detail.id_cuti')
                    ->where('tb_cuti.id_cuti', $id_cuti)
                    ->first();

            $ardetil = explode(',',$hari->dates);



            Mail::to($kirim)->send(new CutiKaryawan($name_cuti,$hari,$ardetil,'[SIMS-App] Approve - Permohonan Cuti (Follow Up)'));
        }

        return redirect()->back()->with('success','Cuti Kamu udah di follow up ke Bos! Thanks.');
    }

    public function setting_total_cuti(Request $request)
    {

        if ($request->users == 'all_emp') {
            $nik = User::select('nik','status_karyawan',DB::raw('DATEDIFF(NOW(),date_of_entry) AS date_of_entrys'))->get();

            foreach ($nik as $data) {
                if ($request['pengurangan_cuti'] == NULL) {

                    $update = User::where('nik',$data->nik)->first();
                    if ($data->status_karyawan == 'cuti') {
                        if ($data->date_of_entrys < 365) {
                            $update->cuti = NULL;
                        }else{
                            $update->cuti = $request['set_cuti'];
                        } 
                    }else{
                        $update->cuti = NULL;
                    }
                    $update->update();
                }else{
                    $update = User::where('nik',$data->nik)->first();
                    if ($request['set_cuti'] == NULL) {
                        $update->cuti = $nik->cuti - $request['pengurangan_cuti'];
                    }else{
                        if ($data->status_karyawan == 'cuti') {
                            $update->cuti = $request['set_cuti'] - $request['pengurangan_cuti'];
                        }else{
                            $update->cuti = NULL;
                        }
                    }
                    $update->update();
                }
            }
        }else{
            if ($request['pengurangan_cuti'] == NULL) {
                $update = User::where('nik',$request->users)->first();
                $update->cuti = $request['set_cuti'];
                $update->update();
            }else{
                $update = User::where('nik',$request->users)->first();
                if ($request['set_cuti'] == NULL) {

                    $update->cuti = $nik->cuti - $request['pengurangan_cuti'];
                }else{
                    
                    $update->cuti = $request['set_cuti'] - $request['pengurangan_cuti'];
                }
                
                $update->update();
            }
            
        }

        return redirect()->back();
    }

    public function set_total_cuti(Request $request)
    {

        $nik = User::select('nik','cuti','status_karyawan',DB::raw('DATEDIFF(NOW(),date_of_entry) AS date_of_entrys'))->get();

        foreach ($nik as $data) {
        	$update = User::where('nik',$data->nik)->first();
        	if ($data->status_karyawan == 'cuti') {
		        $update->cuti = $data->cuti - $request['pengurangan_cuti'];
        	}else{
        		$update->cuti = NULL;
        	}
        	$update->update();
        }

        return redirect()->back();
    }

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
        $tambah = new Barang();
        $tambah->id_item = $request['id_item'];
        $tambah->item_name = $request['nama_item'];
        $tambah->quantity = $request['quantity'];
        $tambah->info = $request['info'];
        $tambah->save();

        return view('HRGA/hrga');
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
        $hapus = Barang::find($id);
                $hapus->delete();

                return redirect()->to('/hrga');
    }

    public function getIdTask()
    {
        return array(DB::table('tb_task')
            ->select('id_task','task_name','description','task_date')
            ->get());
        
    }

    public function getCutiUsers(Request $request){

        $getcuti = User::select(
            'nik',
            DB::raw('DATEDIFF(NOW(),date_of_entry) AS date_of_entrys'),
            DB::raw('(CASE WHEN (cuti IS NULL) THEN 0 ELSE cuti END) as cuti'),
            DB::raw('(CASE WHEN (cuti2 IS NULL) THEN 0 ELSE cuti2 END) as cuti2'),
            DB::raw('sum(cuti + cuti2) AS total_cuti'),
            'date_of_entry'
        )->where('nik',$request->nik)
        ->groupby('users.nik')
        ->get();


        $getAllCutiDate = DB::table('tb_cuti_detail')
            ->select('date_off')
            ->whereIn('id_cuti',function($query){
                $query->select('id_cuti')
                    ->from('tb_cuti')
                    ->where('nik','=',Auth::user()->nik);
            })
            ->pluck('date_off');

        return collect(["parameterCuti" => $getcuti[0],"allCutiDate" => $getAllCutiDate]);
    }

    public function getCutiAuth(Request $request){

        $getcuti = User::select('nik','name',DB::raw('DATEDIFF(NOW(),date_of_entry) AS date_of_entrys'),DB::raw('(CASE WHEN (cuti IS NULL) THEN 0 ELSE cuti END) as cuti'),DB::raw('(CASE WHEN (cuti2 IS NULL) THEN 0 ELSE cuti2 END) as cuti2'),DB::raw('sum(cuti + cuti2) AS total_cuti'),'date_of_entry','gambar')->where('nik',Auth::User()->nik)->groupby('users.nik')->get();

        return $getcuti;
    }

    public function CutiExcel(Request $request)
    {
    	$client = new Client();
        $client = $client->get('https://www.googleapis.com/calendar/v3/calendars/en.indonesian%23holiday%40group.v.calendar.google.com/events?key=AIzaSyAf8ww4lC-hR6mDPf4RA4iuhhGI2eEoEiI');
        $variable = json_decode($client->getBody())->items;

        $hitung_cuti_bersama = 0;
        foreach ($variable as $key => $value) {
          if(strpos($value->summary,'Cuti Bersama') === 0){
            if(strpos($value->start->date ,date('Y')) === 0){
              // echo $value->start->date . strpos($value->summary,'Cuti Bersama') . ' - ' . $value->summary . "<br>";
              $hitung_cuti_bersama++;
            }
          }
        }
        $hitung_cuti_bersama;
        // $nama = 'Report Cuti SIP & MSP '. $request->date_start . ' sampai ' . $request->date_end;
        $nama = 'Report Cuti SIP & MSP '. date('F') .' '. date('Y');
        Excel::create($nama, function ($excel) use ($request,$hitung_cuti_bersama) {
            $excel->sheet('Sinergy informasi Pratama', function ($sheet) use ($request,$hitung_cuti_bersama) {
        
                $sheet->mergeCells('A1:H1');
                $sheet->setBorder('A1:H1', 'thin');

                $sheet->row(1, function ($row) {
                    $row->setFontFamily('Calibri');
                    $row->setFontSize(11);
                    $row->setAlignment('center');
                    $row->setFontWeight('bold');
                    $row->setBackground('#fcd703');
                });

                $sheet->row(1, array('Report Cuti SIP'));

                $sheet->row(2, function ($row) {
                    $row->setFontFamily('Calibri');
                    $row->setFontSize(11);
                    $row->setFontWeight('bold');
                });

                	if ($request->filter == 'date') {
                		$cuti_index = User::join('tb_cuti','tb_cuti.nik','=','users.nik')
                        ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
			            ->join('tb_position','tb_position.id_position','=','users.id_position')
			            ->join('tb_division','tb_division.id_division','=','users.id_division')
			            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','users.cuti',DB::raw('COUNT(tb_cuti.id_cuti) as niks'), DB::raw("(CASE WHEN (cuti IS NULL) THEN '0' ELSE cuti END) as cutis"),'status_karyawan','cuti','cuti2')
			            ->groupby('tb_cuti.nik')
                        ->where('id_company','1')
                        ->orderBy('users.id_division')
			            ->where('tb_cuti.status','v')
			            ->where('status_karyawan','!=','dummy')
			            ->get();


			            $cuti_list = User::join('tb_position','tb_position.id_position','=','users.id_position')
			            ->join('tb_division','tb_division.id_division','=','users.id_division')
			            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division',DB::raw("(CASE WHEN (cuti IS NULL) THEN '0' ELSE cuti END) as cutis"),'users.date_of_entry',DB::raw('DATEDIFF(NOW(),date_of_entry) AS date_of_entrys'),'status_karyawan','cuti','cuti2')
			            ->where('status_karyawan','!=','dummy')
                        ->where('id_company','1')
                        ->orderBy('users.id_division')
			            ->whereNotIn('nik',function($query) { 
			            	$query->select('nik')->from('tb_cuti');

			            })->get();
                	}else if($request->filter == 'div'){
                		$cuti_index = User::join('tb_cuti','tb_cuti.nik','=','users.nik')
                        ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
			            ->join('tb_position','tb_position.id_position','=','users.id_position')
			            ->join('tb_division','tb_division.id_division','=','users.id_division')
			            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','users.cuti',DB::raw('COUNT(tb_cuti_detail.id_cuti) as niks'), DB::raw("(CASE WHEN (cuti IS NULL) THEN '0' ELSE cuti END) as cutis"),'status_karyawan','cuti','cuti2')
			            ->groupby('tb_cuti.nik')
			            ->where('tb_cuti.status','v')
                        ->where('id_company','1')
                        ->orderBy('users.id_division')
                        ->whereYear('date_req',date('Y'))
			            ->where('tb_division.id_division',$request->division)
			            ->get();

			             $cuti_list = User::join('tb_position','tb_position.id_position','=','users.id_position')
			            ->join('tb_division','tb_division.id_division','=','users.id_division')
			            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division',DB::raw("(CASE WHEN (cuti IS NULL) THEN '0' ELSE cuti END) as cutis"),'users.date_of_entry',DB::raw('DATEDIFF(NOW(),date_of_entry) AS date_of_entrys'),'status_karyawan','cuti','cuti2')
			            ->where('status_karyawan','!=','dummy')
                        ->where('id_company','1')
                        ->orderBy('users.id_division')
			            ->whereNotIn('nik',function($query) { 
			            	$query->select('nik')->from('tb_cuti');

			            })->get();
                	}else if($request->filter == 'all'){
                        if ($request->division == 'alldeh') {
                            $cuti_index = User::join('tb_cuti','tb_cuti.nik','=','users.nik')
                            ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                            ->join('tb_position','tb_position.id_position','=','users.id_position')
                            ->join('tb_division','tb_division.id_division','=','users.id_division')
                            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','users.cuti',DB::raw('COUNT(tb_cuti.id_cuti) as niks'), DB::raw("(CASE WHEN (cuti IS NULL) THEN '0' ELSE cuti END) as cutis"),'status_karyawan','cuti','cuti2')
                            ->groupby('tb_cuti.nik')
                            ->where('id_company','1')
                            ->whereYear('date_req',date('Y'))
                            ->orderBy('users.id_division')
                            ->where('tb_cuti.status','v')
                            ->get();

                             $cuti_list = User::join('tb_position','tb_position.id_position','=','users.id_position')
				            ->join('tb_division','tb_division.id_division','=','users.id_division')
				            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division',DB::raw("(CASE WHEN (cuti IS NULL) THEN '0' ELSE cuti END) as cutis"),'users.date_of_entry',DB::raw('DATEDIFF(NOW(),date_of_entry) AS date_of_entrys'),'status_karyawan','cuti','cuti2')
				            ->where('status_karyawan','!=','dummy')
                            ->where('id_company','1')
                            ->orderBy('users.id_division')
				            ->whereNotIn('nik',function($query) { 
				            	$query->select('nik')->from('tb_cuti');

				            })->get();
                        }else{
                            $cuti_index = User::join('tb_cuti','tb_cuti.nik','=','users.nik')
                            ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                            ->join('tb_position','tb_position.id_position','=','users.id_position')
                            ->join('tb_division','tb_division.id_division','=','users.id_division')
                            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','users.cuti',DB::raw('COUNT(tb_cuti_detail.id_cuti) as niks'), DB::raw("(CASE WHEN (cuti IS NULL) THEN '0' ELSE cuti END) as cutis"),'status_karyawan','cuti','cuti2')
                            ->groupby('tb_cuti.nik')
                            ->where('tb_cuti.status','v')
                            ->orderBy('users.id_division')
                            ->whereYear('date_req',date('Y'))
                            ->where('tb_division.id_division',$request->division)
                            ->where('id_company','1')
                            ->get();

                            $cuti_list = User::join('tb_position','tb_position.id_position','=','users.id_position')
				            ->join('tb_division','tb_division.id_division','=','users.id_division')
				            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division',DB::raw("(CASE WHEN (cuti IS NULL) THEN '0' ELSE cuti END) as cutis"),'users.date_of_entry',DB::raw('DATEDIFF(NOW(),date_of_entry) AS date_of_entrys'),'status_karyawan','cuti','cuti2')
				            ->where('status_karyawan','!=','dummy')
                            ->where('id_company','1')
                            ->orderBy('users.id_division')
				            ->whereNotIn('nik',function($query) { 
				            	$query->select('nik')->from('tb_cuti');

				            })->get();
                        }
                		
                	}else{
                		$cuti_index = User::join('tb_cuti','tb_cuti.nik','=','users.nik')
                        ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
			            ->join('tb_position','tb_position.id_position','=','users.id_position')
			            ->join('tb_division','tb_division.id_division','=','users.id_division')
			            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','users.cuti',DB::raw('COUNT(tb_cuti_detail.id_cuti) as niks'), DB::raw("(CASE WHEN (cuti IS NULL) THEN '0' ELSE cuti END) as cutis"),'status_karyawan','cuti','cuti2')
			            ->groupby('tb_cuti.nik')
                        ->where('id_company','1')
                        ->orderBy('users.id_division')
			            ->where('tb_cuti.status','v')
			            ->whereYear('tb_cuti.date_req',date('Y'))
			            ->get();

			             $cuti_list = User::join('tb_position','tb_position.id_position','=','users.id_position')
			            ->join('tb_division','tb_division.id_division','=','users.id_division')
			            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division',DB::raw("(CASE WHEN (cuti IS NULL) THEN '0' ELSE cuti END) as cutis"),'users.date_of_entry',DB::raw('DATEDIFF(NOW(),date_of_entry) AS date_of_entrys'),'status_karyawan','cuti','cuti2')
			            ->where('status_karyawan','!=','dummy')
                        ->where('id_company','1')
                        ->orderBy('users.id_division')
			            ->whereNotIn('nik',function($query) { 
			            	$query->select('nik')->from('tb_cuti');

			            })->get();
                	}
	                
                	$tahun_lalu = date('Y') - 1;
                    $datasheetcuti = array();
                    $datasheetcuti[0] = array("No","Nama Karyawan", "Hak Cuti Tahunan","Cuti Bersama", "Cuti Sudah diambil", "Sisa Cuti ".$tahun_lalu , "Sisa Cuti ".date('Y'),"Status Hak Cuti Karyawan");
                    $i=1;

                    foreach ($cuti_index as $data) {
                    	if ($data->status_karyawan == 'belum_cuti') {
                    		$habis = "Belum 1 tahun";
                    	}else{
                    		if ($data->cuti == 0) {
                    			$habis = "Sisa Cuti ".$tahun_lalu." Habis";
                    		}else if($data->cuti2 == 0){
                    			$habis = "Sisa Cuti ".date('Y')." Habis";
                    		}
                    		$habis = "-";
                    	}
                        $datasheetcuti[$i] = array($i,
                                    $data['name'],
                                    $data['']."12 hari",
                                    $hitung_cuti_bersama." hari",
                                    $data['niks']." Hari",
                                    $data['cuti']." Hari",
                                    $data['cuti2']." Hari",
                                    $habis
                                );
                        $i++;
                    }

                    foreach ($cuti_list as $datas ) {
                    	if ($datas->status_karyawan == 'belum_cuti') {
                    		$habis = "Belum 1 tahun";
                    	}else{
                    		if ($datas->cuti == 0) {
                    			$habis = "Sisa Cuti ".$tahun_lalu." Habis";
                    		}else if($datas->cuti2 == 0){
                    			$habis = "Sisa Cuti ".date('Y')." Habis";
                    		}
                    		$habis = "-";
                    	}
                    	$datasheetcuti[$i] = array($i,
                                    $datas['name'],
                                    $datas['']."12 hari",
                                    $hitung_cuti_bersama." hari",
                                    $datas['']."0 Hari",
                                    $datas['cuti']." Hari",
                                    $datas['cuti2']." Hari",
                                    $habis
                                );
                        $i++;
                    }

                    $sheet->fromArray($datasheetcuti);
                    
            });

            $excel->sheet('Multi Solusindo Perkasa', function ($sheet) use ($request,$hitung_cuti_bersama) {
        
                $sheet->mergeCells('A1:H1');
                $sheet->setBorder('A1:h1', 'thin');

                $sheet->row(1, function ($row) {
                    $row->setFontFamily('Calibri');
                    $row->setFontSize(11);
                    $row->setAlignment('center');
                    $row->setFontWeight('bold');
                    $row->setBackground('#fcd703');
                });

                $sheet->row(1, array('Report Cuti MSP'));

                $sheet->row(2, function ($row) {
                    $row->setFontFamily('Calibri');
                    $row->setFontSize(11);
                    $row->setFontWeight('bold');
                });

                    if ($request->filter == 'date') {
                        $cuti_index = User::join('tb_cuti','tb_cuti.nik','=','users.nik')
                        ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                        ->join('tb_position','tb_position.id_position','=','users.id_position')
                        ->join('tb_division','tb_division.id_division','=','users.id_division')
                        ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','users.cuti',DB::raw('COUNT(tb_cuti.id_cuti) as niks'), DB::raw("(CASE WHEN (cuti IS NULL) THEN '0' ELSE cuti END) as cutis"),'status_karyawan','cuti','cuti2')
                        ->groupby('tb_cuti.nik')
                        ->where('tb_cuti.status','v')
                        ->where('status_karyawan','!=','dummy')
                        ->where('id_company','2')
                        ->orderBy('users.id_division')
                        ->get();

                        $cuti_list = User::join('tb_position','tb_position.id_position','=','users.id_position')
                        ->join('tb_division','tb_division.id_division','=','users.id_division')
                        ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division',DB::raw("(CASE WHEN (cuti IS NULL) THEN '0' ELSE cuti END) as cutis"),'users.date_of_entry',DB::raw('DATEDIFF(NOW(),date_of_entry) AS date_of_entrys'),'status_karyawan','cuti','cuti2')
                        ->where('status_karyawan','!=','dummy')
                        ->where('id_company','2')
                        ->orderBy('users.id_division')
                        ->whereNotIn('nik',function($query) { 
                            $query->select('nik')->from('tb_cuti');

                        })->get();
                    }else if($request->filter == 'div'){
                        $cuti_index = User::join('tb_cuti','tb_cuti.nik','=','users.nik')
                        ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                        ->join('tb_position','tb_position.id_position','=','users.id_position')
                        ->join('tb_division','tb_division.id_division','=','users.id_division')
                        ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','users.cuti',DB::raw('COUNT(tb_cuti_detail.id_cuti) as niks'), DB::raw("(CASE WHEN (cuti IS NULL) THEN '0' ELSE cuti END) as cutis"),'status_karyawan','cuti','cuti2')
                        ->groupby('tb_cuti.nik')
                        ->where('tb_cuti.status','v')
                        ->where('id_company','2')
                        ->orderBy('users.id_division')
                        ->whereYear('date_req',date('Y'))
                        ->where('tb_division.id_division',$request->division)
                        ->get();

                         $cuti_list = User::join('tb_position','tb_position.id_position','=','users.id_position')
                        ->join('tb_division','tb_division.id_division','=','users.id_division')
                        ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division',DB::raw("(CASE WHEN (cuti IS NULL) THEN '0' ELSE cuti END) as cutis"),'users.date_of_entry',DB::raw('DATEDIFF(NOW(),date_of_entry) AS date_of_entrys'),'status_karyawan','cuti','cuti2')
                        ->where('status_karyawan','!=','dummy')
                        ->where('id_company','2')
                        ->orderBy('users.id_division')
                        ->whereNotIn('nik',function($query) { 
                            $query->select('nik')->from('tb_cuti');

                        })->get();
                    }else if($request->filter == 'all'){
                        if ($request->division == 'alldeh') {
                            $cuti_index = User::join('tb_cuti','tb_cuti.nik','=','users.nik')
                            ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                            ->join('tb_position','tb_position.id_position','=','users.id_position')
                            ->join('tb_division','tb_division.id_division','=','users.id_division')
                            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','users.cuti',DB::raw('COUNT(tb_cuti.id_cuti) as niks'), DB::raw("(CASE WHEN (cuti IS NULL) THEN '0' ELSE cuti END) as cutis"),'status_karyawan','cuti','cuti2')
                            ->groupby('tb_cuti.nik')
                            ->where('id_company','2')
                            ->orderBy('users.id_division')
                            ->whereYear('date_req',date('Y'))
                            ->where('tb_cuti.status','v')
                            ->get();

                             $cuti_list = User::join('tb_position','tb_position.id_position','=','users.id_position')
                            ->join('tb_division','tb_division.id_division','=','users.id_division')
                            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division',DB::raw("(CASE WHEN (cuti IS NULL) THEN '0' ELSE cuti END) as cutis"),'users.date_of_entry',DB::raw('DATEDIFF(NOW(),date_of_entry) AS date_of_entrys'),'status_karyawan','cuti','cuti2')
                            ->where('status_karyawan','!=','dummy')
                            ->where('id_company','2')
                            ->orderBy('users.id_division')
                            ->whereNotIn('nik',function($query) { 
                                $query->select('nik')->from('tb_cuti');

                            })->get();
                        }else{
                            $cuti_index = User::join('tb_cuti','tb_cuti.nik','=','users.nik')
                            ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                            ->join('tb_position','tb_position.id_position','=','users.id_position')
                            ->join('tb_division','tb_division.id_division','=','users.id_division')
                            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','users.cuti',DB::raw('COUNT(tb_cuti_detail.id_cuti) as niks'), DB::raw("(CASE WHEN (cuti IS NULL) THEN '0' ELSE cuti END) as cutis"),'status_karyawan','cuti','cuti2')
                            ->groupby('tb_cuti.nik')
                            ->where('tb_cuti.status','v')
                            ->where('id_company','2')
                            ->whereYear('date_req',date('Y'))
                            ->orderBy('users.id_division')
                            ->where('tb_division.id_division',$request->division)
                            ->get();

                             $cuti_list = User::join('tb_position','tb_position.id_position','=','users.id_position')
                            ->join('tb_division','tb_division.id_division','=','users.id_division')
                            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division',DB::raw("(CASE WHEN (cuti IS NULL) THEN '0' ELSE cuti END) as cutis"),'users.date_of_entry',DB::raw('DATEDIFF(NOW(),date_of_entry) AS date_of_entrys'),'status_karyawan','cuti','cuti2')
                            ->where('status_karyawan','!=','dummy')
                            ->where('id_company','2')
                            ->orderBy('users.id_division')
                            ->whereNotIn('nik',function($query) { 
                                $query->select('nik')->from('tb_cuti');

                            })->get();
                        }
                        
                    }else{
                        $cuti_index = User::join('tb_cuti','tb_cuti.nik','=','users.nik')
                        ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                        ->join('tb_position','tb_position.id_position','=','users.id_position')
                        ->join('tb_division','tb_division.id_division','=','users.id_division')
                        ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','users.cuti',DB::raw('COUNT(tb_cuti_detail.id_cuti) as niks'), DB::raw("(CASE WHEN (cuti IS NULL) THEN '0' ELSE cuti END) as cutis"),'cuti','cuti2','status_karyawan')
                        ->groupby('tb_cuti.nik')
                        ->orderBy('users.id_division')
                        ->where('tb_cuti.status','v')
                        ->whereYear('date_req',date('Y'))
                        ->where('id_company','2')
                        ->get();

                         $cuti_list = User::join('tb_position','tb_position.id_position','=','users.id_position')
                        ->join('tb_division','tb_division.id_division','=','users.id_division')
                        ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division',DB::raw("(CASE WHEN (cuti IS NULL) THEN '0' ELSE cuti END) as cutis"),'users.date_of_entry',DB::raw('DATEDIFF(NOW(),date_of_entry) AS date_of_entrys'),'cuti2','status_karyawan','cuti')
                        ->where('status_karyawan','!=','dummy')
                        ->where('id_company','2')
                        ->orderBy('users.id_division')
                        ->whereNotIn('nik',function($query) { 
                            $query->select('nik')->from('tb_cuti');

                        })->get();
                    }
                    

                    $tahun_lalu = date('Y') -1;
                    $datasheetcuti = array();
                    $datasheetcuti[0] = array("No","Nama Karyawan", "Hak Cuti Tahunan","Cuti Bersama", "Cuti Sudah diambil", "Sisa Cuti ".$tahun_lalu , "Sisa Cuti".date('Y'), "Status Hak Cuti Karyawan");
                    $i=1;

                    foreach ($cuti_index as $data) {
                    	if ($data->status_karyawan == 'belum_cuti') {
                    		$habis = "Belum 1 tahun";
                    	}else{
                    		if ($data->cuti == 0) {
                    			$habis = "Sisa Cuti ".$tahun_lalu." Habis";
                    		}else if($data->cuti2 == 0){
                    			$habis = "Sisa Cuti ".date('Y')." Habis";
                    		}
                    		$habis = "-";
                    	}

                        $datasheetcuti[$i] = array($i,
                                    $data['name'],
                                    $data['']."12 Hari",
                                    $hitung_cuti_bersama. " Hari",
                                    $data['niks']." Hari",
                                    $data['cuti']." Hari",
                                    $data['cuti2']." Hari",
                                    $habis
                                );
                        $i++;
                    }

                    foreach ($cuti_list as $datas ) {
                    	if ($datas->status_karyawan == 'belum_cuti') {
                    		$habis = "Belum 1 tahun";
                    	}else{
                    		if ($datas->cuti == 0) {
                    			$habis = "Sisa Cuti ".$tahun_lalu." Habis";
                    		}else if($datas->cuti2 == 0){
                    			$habis = "Sisa Cuti ".date('Y')." Habis";
                    		}
                    		$habis = "-";
                    	}
                        $datasheetcuti[$i] = array($i,
                                    $datas['name'],
                                    $datas['']."12 Hari",
                                    $hitung_cuti_bersama. " Hari",
                                    $datas['']."0 Hari",
                                    $datas['cutis']." Hari",
                                    $datas['cuti2']." Hari",
                                    $habis
                                );
                        $i++;
                    }

                    $sheet->fromArray($datasheetcuti);
                    
            });

            $excel->sheet('Detail Report Cuti SIP & MSP', function ($sheet) use ($request) {
        
                $sheet->mergeCells('A1:H1');
                $sheet->setBorder('A1:H1', 'thin');

                $sheet->row(1, function ($row) {
                    $row->setFontFamily('Calibri');
                    $row->setFontSize(11);
                    $row->setAlignment('center');
                    $row->setFontWeight('bold');
                    $row->setBackground('#fcd703');
                });

                $sheet->row(1, array('Detail Report Bulanan Cuti'));

                $sheet->row(2, function ($row) {
                    $row->setFontFamily('Calibri');
                    $row->setFontSize(11);
                    $row->setFontWeight('bold');
                });

                    if ($request->filter == 'date') {
    	                $cuti = Cuti::join('users','users.nik','=','tb_cuti.nik')
    		            ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
    		            ->join('tb_position','tb_position.id_position','=','users.id_position')
    		            ->join('tb_division','tb_division.id_division','=','users.id_division')
                        ->join('tb_company','tb_company.id_company','=','users.id_company')
    		            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti',DB::raw('group_concat(date_off) as date_off'),'code_company','jenis_cuti')
    		            ->where('status','v')
    		            ->whereBetween('date_off',array($request->date_start,$request->date_end))
    		            ->groupby('tb_cuti_detail.id_cuti')
    		            ->get();

                    }else if($request->filter == 'div'){
                        $cuti = Cuti::join('users','users.nik','=','tb_cuti.nik')
                        ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                        ->join('tb_position','tb_position.id_position','=','users.id_position')
                        ->join('tb_division','tb_division.id_division','=','users.id_division')
                        ->join('tb_company','tb_company.id_company','=','users.id_company')
                        ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti',DB::raw('group_concat(date_off) as date_off'),'code_company','jenis_cuti')
                        ->where('status','v')
                        ->where('tb_division.id_division',$request->division)
                        ->groupby('tb_cuti.id_cuti')
                        ->get();

                    }else if($request->filter == 'all'){
                        if ($request->division == 'alldeh') {

                            $cuti = Cuti::join('users','users.nik','=','tb_cuti.nik')
                            ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                            ->join('tb_position','tb_position.id_position','=','users.id_position')
                            ->join('tb_division','tb_division.id_division','=','users.id_division')
                            ->join('tb_company','tb_company.id_company','=','users.id_company')
                            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti',DB::raw('group_concat(date_off) as date_off'),'code_company','jenis_cuti')
                            ->where('status','v')
                            ->whereBetween('date_off',array($request->date_start,$request->date_end))
                            ->groupby('tb_cuti.id_cuti')
                            ->get();
                        }else{
                            $cuti = Cuti::join('users','users.nik','=','tb_cuti.nik')
                            ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                            ->join('tb_position','tb_position.id_position','=','users.id_position')
                            ->join('tb_division','tb_division.id_division','=','users.id_division')
                            ->join('tb_company','tb_company.id_company','=','users.id_company')
                            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti',DB::raw('group_concat(date_off) as date_off'),'code_company','jenis_cuti')
                            ->where('status','v')
                            ->whereBetween('date_off',array($request->date_start,$request->date_end))
                            ->where('tb_division.id_division',$request->division)
                            ->groupby('tb_cuti.id_cuti')
                            ->get();
                        }
                        
                    }else{
                        $cuti = Cuti::join('users','users.nik','=','tb_cuti.nik')
                        ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                        ->join('tb_position','tb_position.id_position','=','users.id_position')
                        ->join('tb_division','tb_division.id_division','=','users.id_division')
                        ->join('tb_company','tb_company.id_company','=','users.id_company')
                        ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti',DB::raw('group_concat(date_off) as date_off'),'code_company','jenis_cuti')
                        ->where('status','v')
                        ->whereMonth('tb_cuti.date_req',date('m'))
                        ->whereYear('tb_cuti.date_req',date('Y'))
                        ->groupby('tb_cuti.id_cuti')
                        ->get();
                    }

                    $datasheetdetail = array();
                    $datasheetdetail[0] = array("No", "Nama Karyawan","Company", "Division", "Request Cuti", "Date Off", "Tanggal Request", "[Jenis Cuti]/[keterangan]");
                    $i=1;

                    foreach ($cuti as $data) {

                        $datasheetdetail[$i] = array($i,
                                    $data['name'],
                                    $data['code_company'],
                                    $data['name_division'],
                                    $data['days']." Hari",
                                    str_replace('-', '/', $data['date_off']),
                                    $data['date_req'],
                                    "[ ".$data['jenis_cuti']." ]/"."[ ".$data['reason_leave']." ]"
                                );
                        $i++;
                    }

                    $sheet->fromArray($datasheetdetail);
                    
            });

        })->export('xls');
    }

    public function filterByDate(Request $request)
    {
    	$cuti = DB::table('tb_cuti')
	            ->join('users','users.nik','=','tb_cuti.nik')
	            ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
	            ->join('tb_position','tb_position.id_position','=','users.id_position')
	            ->join('tb_division','tb_division.id_division','=','users.id_division')
	            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti')
	            ->where('status','v')
	            ->where('date_off', '>=', $request->start)
                ->where('date_off', '<=', $request->end)
	            ->groupby('tb_cuti.id_cuti')
	            ->get();

	    return $cuti;
    }

    public function filterByDateDiv(Request $request)
    {
    	if ($request->division == 'alldeh'){
    		$cuti = DB::table('tb_cuti')
	            ->join('users','users.nik','=','tb_cuti.nik')
	            ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
	            ->join('tb_position','tb_position.id_position','=','users.id_position')
	            ->join('tb_division','tb_division.id_division','=','users.id_division')
	            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti')
	            ->where('status','v')
	            ->orwhere('date_off', '>=', $request->start)
                ->orhere('date_off', '<=', $request->end)
	            ->groupby('tb_cuti.id_cuti')
	            ->get();
    	}else{
    		$cuti = DB::table('tb_cuti')
	            ->join('users','users.nik','=','tb_cuti.nik')
	            ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
	            ->join('tb_position','tb_position.id_position','=','users.id_position')
	            ->join('tb_division','tb_division.id_division','=','users.id_division')
	            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti')
	            ->where('status','v')
	            ->where('tb_division.id_division',$request->division)
	            ->orwhere('date_off', '>=', $request->start)
                ->orwhere('date_off', '<=', $request->end)
	            ->groupby('tb_cuti.id_cuti')
	            ->get();
    	}
    	

	    return $cuti;
    }

    public function filterByDiv(Request $request)
    {

    	if ($request->division == 'alldeh') {
    		$cuti = DB::table('tb_cuti')
	            ->join('users','users.nik','=','tb_cuti.nik')
	            ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
	            ->join('tb_position','tb_position.id_position','=','users.id_position')
	            ->join('tb_division','tb_division.id_division','=','users.id_division')
	            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti')
	            ->where('status','v')
	            ->whereYear('date_req',date('Y'))
	            ->groupby('tb_cuti.id_cuti')
	            ->get();
    	}else{
    		$cuti = DB::table('tb_cuti')
	            ->join('users','users.nik','=','tb_cuti.nik')
	            ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
	            ->join('tb_position','tb_position.id_position','=','users.id_position')
	            ->join('tb_division','tb_division.id_division','=','users.id_division')
	            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','tb_cuti.date_req','tb_cuti.reason_leave','tb_cuti.date_start','tb_cuti.date_end','tb_cuti.id_cuti','tb_cuti.status','tb_cuti.decline_reason',DB::raw('COUNT(tb_cuti_detail.id_cuti) as days'),'users.cuti')
	            ->where('status','v')
	            ->whereYear('date_req',date('Y'))
	            ->where('tb_division.id_division',$request->division)
	            ->groupby('tb_cuti.id_cuti')
	            ->get();
    	}
    	

	    return $cuti;
    }

    public function cutipdf(Request $request)
    {
         $year = date('Y');

        $cuti_index = DB::table('users')
                ->join('tb_cuti','tb_cuti.nik','=','users.nik')
                ->join('tb_cuti_detail','tb_cuti_detail.id_cuti','=','tb_cuti.id_cuti')
                ->join('tb_position','tb_position.id_position','=','users.id_position')
                ->join('tb_division','tb_division.id_division','=','users.id_division')
                ->select('users.nik','users.date_of_entry','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','users.cuti',DB::raw('COUNT(tb_cuti_detail.id_cuti) as niks'),DB::raw('DATEDIFF(NOW(),date_of_entry) AS date_of_entrys'))
                ->groupby('tb_cuti.nik')
                ->get();

        $cuti_list = DB::table('users')
            ->join('tb_position','tb_position.id_position','=','users.id_position')
            ->join('tb_division','tb_division.id_division','=','users.id_division')
            ->select('users.nik','users.name','tb_position.name_position','tb_division.name_division','tb_division.id_division','users.cuti','users.date_of_entry',DB::raw('DATEDIFF(NOW(),date_of_entry) AS date_of_entrys'))
            ->whereNotIn('nik',function($query) { 
                $query->select('nik')->from('tb_cuti');
            })
            ->get();

        /*$pdf = PDF::loadView('HR.cuti_pdf', compact('cuti_index', 'cuti_list', 'year'));
        return $pdf->download('report_cuti-'.date("d-m-Y").'.pdf');*/
        return view('HR.cuti_pdf', compact('cuti_index', 'cuti_list', 'year'));
    }

}
