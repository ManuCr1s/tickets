<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Origen;
use App\Models\Pasos;
use App\Models\Dias;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TicketRequest;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('index');
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
    public function store(TicketRequest $request)
    {
        $origen = Origen::where('numticket',$request->input('ticket'))->orWhere('numticket',str_replace("-","",$request->input('ticket')))->first();
        if(!$origen)return $errors = ['ticket'=> 'No se encontro el expediente, ingrese nuevamente'];
        $pasos = $this->show($origen->idsol);
        $dias = $this->days($origen->idsol);
        $response = ['origen'=>$origen,'pasos'=>$pasos,'dias'=>$dias];
        return $response;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $response = Pasos::where('idsol',$id)->get();
        if(!$response){
            $errors = ['Not_Found'=> 'No se encontro el expediente'];
            return  $errors = $errors->toJson();
        }
        return $response;
    }
    
    public function days($id){
        $response = DB::table(DB::raw('(
            SELECT
                dep.maed_v_referencia AS dependencia,
                di.deas_n_sequenciapaso AS  paso,
                EXTRACT(DAY FROM (di.deas_d_fecderivacion - di.deas_d_fecrecepcion)) AS dia
            FROM
                dbo.m_workflow_maedependencia dep
            INNER JOIN
                dbo.i_workflow_detactor_sol di ON di.deas_n_id_dependencia = dep.maed_n_id_dependencia
            WHERE
                di.deas_n_id_solicitud = '.$id.' AND di.deas_n_sequenciaactor = 2
        ) AS subquery'))
            ->select('subquery.dependencia', 'subquery.paso', 'subquery.dia')
            ->where('subquery.dia', '>', 0)
            ->orderBy('subquery.paso', 'asc')
            ->get();
        /*
        $response = Dias::join('dbo.m_workflow_maedependencia','dbo.m_workflow_maedependencia.maed_n_id_dependencia','=','deas_n_id_dependencia')
                    ->where('deas_n_id_solicitud','=',$id)
                    ->where('deas_n_sequenciaactor','=',2)
                    ->whereNotNull('deas_d_fecderivacion')
                    ->selectRaw('dbo.m_workflow_maedependencia.maed_v_referencia as dependencia,EXTRACT(DAY FROM (dbo.i_workflow_detactor_sol.deas_d_fecderivacion - dbo.i_workflow_detactor_sol.deas_d_fecrecepcion)) as dia')
                    ->get();*/
        return $response;
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
}
