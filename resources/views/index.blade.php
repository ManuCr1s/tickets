@extends('template.template')
@section('container')
        <div class="container-fluid">
                <div class="row mt-5 mx-4">
                        <div class="col-md-8">
                                <x-forms.ticket/>   
                                <x-table.ultimate/>                 
                        </div>
                        <div class="col-md-4">
                                <x-table.origen/>  
                                <x-table.ubicacion/> 
                        </div>                                               
                </div>  
                <div class="row mx-4">
                        <div class="col-md-12">
                                <x-table.detalle/>           
                        </div>                                           
                </div> 
                <div class="row mx-4">
                        <div class="col-md-3 my-3 bg-light p-3 print-report">
                                <x-list.dependecy/>
                        </div>   
                        <div class="col-md-9 bg-light my-3 p-3 print-report">
                                <x-chart.line/>
                        </div>        
                </div>
        </div>
@endsection