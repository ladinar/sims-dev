<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ConfigManagement;
use Auth;
use DB;
use PDF;
use Excel;

class CMController extends Controller
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
        $datas = DB::table('dvg_cm')
                    ->join('users','users.nik','=','dvg_cm.nik_pic')
                    ->select('dvg_cm.no','dvg_cm.tgl','dvg_cm.nik_pic','dvg_cm.hostname','dvg_cm.perangkat','dvg_cm.perubahan','dvg_cm.resiko','dvg_cm.downtime','dvg_cm.keterangan','users.name')
                    ->get();

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

        return view('DVG/cm/index',compact('datas', 'notifClaim'));
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
        $tambah = new ConfigManagement();
        // $tambah->tgl = date('Y-m-d');
        $tambah->tgl = $request['tanggal_config'];
        $tambah->hostname = $request['hostname'];
        // $tambah->nik_pic = Auth::User()->nik;
        $tambah->nik_pic = $request['nik_pic'];
        $tambah->perangkat = $request['perangkat'];
        $tambah->perubahan = $request['perubahan'];
        $tambah->resiko = $request['resiko'];
        $tambah->downtime = $request['downtime'];
        $tambah->keterangan = $request['keterangan'];
        $tambah->save();

        return redirect()->to('/config_management');
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
    public function update(Request $request)
    {
        $no = $request['no'];

        $update = ConfigManagement::where('no', $no)->first();
        $update->no = $request['no'];
        $update->tgl = $request['tanggal_config'];
        $update->nik_pic = $request['nik_pic'];
        $update->hostname = $request['hostname'];
        $update->perangkat = $request['perangkat'];
        $update->perubahan = $request['perubahan'];
        $update->resiko = $request['resiko'];
        $update->downtime = $request['downtime'];
        $update->keterangan = $request['keterangan'];
        $update->update();

        return redirect('config_management');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($no)
    {
        $hapus = ConfigManagement::find($no);
        $hapus->delete();

        return redirect()->back()->with('alert', 'Deleted!');
    }

    public function exportExcel(Request $request)
    {
        $nama = 'Config Management '.date('Y-m-d');
        Excel::create($nama, function ($excel) use ($request) {
        $excel->sheet('Daftar Perubahan Konfigurasi', function ($sheet) use ($request) {
        
        $sheet->mergeCells('A1:H1');

       // $sheet->setAllBorders('thin');
        $sheet->row(1, function ($row) {
            $row->setFontFamily('Calibri');
            $row->setFontSize(11);
            $row->setAlignment('center');
            $row->setFontWeight('bold');
        });

        $sheet->row(1, array('DAFTAR PERUBAHAN KONFIGURASI'));

        $sheet->row(2, function ($row) {
            $row->setFontFamily('Calibri');
            $row->setFontSize(11);
            $row->setFontWeight('bold');
        });

        $datas = ConfigManagement::join('users','users.nik','=','dvg_cm.nik_pic')
                    ->select('dvg_cm.no','dvg_cm.tgl','nik_pic','dvg_cm.perangkat','dvg_cm.perubahan','dvg_cm.resiko','dvg_cm.downtime','dvg_cm.keterangan','users.name')
                    ->get();

       // $sheet->appendRow(array_keys($datas[0]));
            $sheet->row($sheet->getHighestRow(), function ($row) {
                $row->setFontWeight('bold');
            });

             $datasheet = array();
             $datasheet[0]  =   array("NO", "TANGGAL", "PIC", "PERANGKAT", "PERUBAHAN",  "RESIKO", "DOWNTIME", "KETERANGAN");
             $i=1;

            foreach ($datas as $data) {

               // $sheet->appendrow($data);
              $datasheet[$i] = array($i,
                            $data['tgl'],
                            $data['name'],
                            $data['perangkat'],
                            $data['perubahan'],
                            $data['resiko'],
                            $data['downtime'],
                            $data['keterangan']
                        );
              
              $i++;
            }

            $sheet->fromArray($datasheet);
        });

        })->export('xls');
    }

    public function downloadPDF()
    {
        $datas = DB::table('dvg_cm')
                    ->join('users','users.nik','=','dvg_cm.nik_pic')
                    ->select('dvg_cm.no','dvg_cm.tgl','nik_pic','dvg_cm.perangkat','dvg_cm.perubahan','dvg_cm.resiko','dvg_cm.downtime','dvg_cm.keterangan','users.name')
                    ->get();

        $pdf = PDF::loadView('DVG.cm.cm_pdf', compact('datas'));
        return $pdf->download('report_config_management'.date("d-m-Y").'.pdf');
    }

}
