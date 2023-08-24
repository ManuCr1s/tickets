import { event } from 'jquery';
import route from './routes';
import {validate,onlyNumbers} from './function';
import DataTable from 'datatables';
import 'datatables.net-responsive';
import Chart from 'chart.js';
import Cleave from 'cleave.js';
import { parse } from 'postcss';
import pdfmake from 'pdfmake';
import 'datatables.net-buttons-dt';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs'; 
import pdfFonts from "pdfmake/build/vfs_fonts";
import jsPDF from 'jspdf';
pdfMake.vfs = pdfFonts.pdfMake.vfs;
import swal from 'sweetalert';
$(document).ready(function(){
    $("#preloader").hide();
    //Asigancion de id de tablas a variables
    let form = $('#idForm'),
        table = $('#detail_table'),
        ubicacion = $('#ubicacion'),
        ultimate = $('#ultimate'),
        ultimos=[], 
        ctx = $('#myChart'); 
    //Obtener datos del localstorage de los 10  
        for (var i = localStorage.length-1; i >= Math.max(0, localStorage.length - 10); i--) {
            var value = localStorage.getItem(i);
            ultimos.push(JSON.parse(value));
        }

    //Impresion de la tabla de los ultimos expedientes
console.log(ultimos)
    ultimate.DataTable({
        responsive: true,
        searching:false,
        paging:false,
        data:ultimos,
        columns:[
            {data:'ticket'},
            {data:'fecha'},
            {data:'asunto'}
        ],
        order:[[1,'desc']]
    });  
 
    //Formato del input para formatear los datos ingresados
    let cleave = new Cleave('#ticketInput',{
        delimiter:'-',
        blocks: [2, 5, 5, 4, 2, 6, 1],
        uppercase:true
    })
    
    //Envio de formulario de entrada
    form.on('submit',function(event){
        event.preventDefault();
        $("#preloader").show();
        $('#ticketInput').on("keypress", onlyNumbers);
        $('#btn_new').on("click",function(){window.location.reload();})
        if(!validate(31,$('#ticketInput').val())){
            swal({ 
                title: "Upps, al parecer hubo un error",
                text: 'Ingrese un numero de Ticket de 25 caracteres',
                icon: "warning"
            }).then(()=>{
                window.location.reload(); 
            })
        }else{
            let formData = $(this).serialize(),
            url = route.date_ticket;
                $.ajax({
                    url:url,
                    type:'POST',
                    data:formData,
                    success:function(response){
                        console.log(response);
                        if("ticket" in response){
                            swal({ 
                                title: "Upps, al parecer hubo un error",
                                text: response.ticket.toString(),
                                icon: "warning"
                            }).then(()=>{
                                window.location.reload(); 
                            })
                        }else{
                                
                                let block = `<div>
                                        <h4 class="text-green">¡DOCUMENTO ENCONTRADO!</h4>
                                        <p class="text-green">${$('#ticketInput').val()}</p>
                                        <p class="text-center">Visualice la informacion de origen en "DATOS DEL EXPEDIENTE", la ubicacion en "UBICACION DE EXPEDIENTE" y descargue o visualice
                                        los pasos del expediente en "DETALLE DE EXPEDIENTE"</p>
                                    </div>`,
                                tipoente = $('#tipoente'),
                                numiden = $('#numiden'),
                                nomente = $('#nomente'),
                                tipdoc = $('#tipdoc'),
                                numdoc = $('#numdoc'),
                                asunto = $('#asunto'),
                                areacrea = $('#areacrea'),
                                usercrea = $('#usercrea'),
                                fecsol = $('#fecsol'),
                                dias=[],dependencia=[],pasos=[],pendiente=[],
                                objeto = JSON.stringify({
                                    'ticket':$('#ticketInput').val(),
                                    'fecha':new Date(),
                                    'asunto':response.origen.asunto
                                });
                                localStorage.setItem(localStorage.length,objeto);
                                $('#replace_submit').replaceWith(block);
                                tipoente.text(response.origen.tipoente);
                                numiden.text(response.origen.numiden);
                                nomente.text(response.origen.nomente);
                                tipdoc.text(response.origen.tipdoc);
                                numdoc.text(response.origen.numdoc);
                                asunto.text(response.origen.asunto);
                                areacrea.text(response.origen.areacrea);
                                usercrea.text(response.origen.usercrea);
                                fecsol.text(response.origen.fecsol);

                                response.pasos.forEach(function(objeto){
                                    if(objeto.fec_aten && objeto.area_rec=='ARCHIVO VIRTUAL'){
                                        pendiente.push({'unidad':objeto.area_rec,'estado':false,'fecha':objeto.fec_env_rec})
                                    }else if(!objeto.fec_aten){
                                        pendiente.push({'unidad':objeto.area_rec,'estado':true,'fecha':objeto.fec_env_rec})
                                    }
                                })
                
                
                                let t = table.DataTable({
                                    destroy: true,
                                    responsive: true,
                                    data:response.pasos,
                                    dom:'lftBp',
                                    buttons: [
                                        {
                                            extend: 'pdfHtml5',
                                            className:'btn btn-danger',
                                            orientation: 'landscape',
                                            text:'DESCARGAR PDF',
                                            title:'Reporte del Detalle de Expediente',
                                            filename: 'Reporte_Detalle_Expediente',
                                            customize: function (doc) {
                                                doc.defaultStyle.font = 'Roboto'; // Utiliza el nombre de fuente definido
                                            }
                                        },
                                        {
                                            extend:'excel',
                                            className:'btn btn-success',
                                            text:'DESCARGAR EXCEL',
                                            title:'Reporte del Detalle de Expediente',
                                            filename: 'Reporte_Detalle_Expediente'
                                        },
                                        {
                                            extend:'print',
                                            className:'btn btn-primary',
                                            text:'IMPRIMIR DOCUMENTO',
                                            title:'Reporte del Detalle de Expediente',
                                            orientation: 'landscape',
                                            filename: 'Reporte_Detalle_Expediente'
                                        }
                                    ],
                                    columnDefs:[
                                        {
                                            "searchable": false,
                                            "orderable": true,
                                            "targets": 0
                                        } 
                                    ],
                                    columns:[
                                        {
                                            data: null, 
                                            searchable: false,
                                            orderable: true,
                                            targets: 0,
                                            className:'bg-column-send',
                                            render: function (data, type, row, meta) {
                                                return meta.row + 1;
                                            }
                                        },
                                        {data:'forma',className:'bg-column-send'},
                                        {data:'area_env',className:'bg-column-send'},
                                        {data:'user_env',className:'bg-column-send'},
                                        {data:'accion',className:'bg-column-action'},
                                        {data:'asunto_nota',width:"250px",className:'bg-column-action'},
                                        {data:'fec_env_rec',className:'bg-column-action'},
                                        {data:'area_rec',className:'bg-column-recep'},
                                        {data:'user_rec',className:'bg-column-recep'},
                                        {data:'fec_lec',className:'bg-column-recep'},
                                
                                    ],
                                    order: [[ 1, 'asc' ]]
                                });
                        
                                t.on('order.dt search.dt', function () {
                                    let i = 1;
                                    t.cells(null, 0, { search: 'applied', order: 'applied' }).every(function (cell) {
                                            this.data(i++);
                                    });
                                }).draw();
                            
                                ubicacion.DataTable({
                                    destroy: true,
                                    responsive: true,
                                    data:pendiente,
                                    searching:false,
                                    paging:false,
                                    columns:[
                                        {data:'unidad', orderable: false},
                                        {data:'fecha', orderable: false},
                                        {
                                            'defaultContent':'<p></p>'
                                        }
                                    ],
                                    rowCallback:function(row,data,index){
                                        var estado = data.unidad; 
                                        var accionCell = $('td', row).eq(2);
                                        if (estado === 'ARCHIVO VIRTUAL') {
                                            $('td',row).eq(2).css({'background-color':'#E74C3C'});
                                            accionCell.text('ARCHIVADO');
                                        } else{
                                            $('td',row).eq(2).css({'background-color':'#52BE80 '});
                                            accionCell.text('PENDIENTE');
                                        }
                                    }
                                })
                        
                            response.dias.forEach(function(objeto) {
                                dias.push(objeto.dia);
                                dependencia.push(objeto.dependencia);
                                pasos.push(objeto.paso);
                            })
                            var myChart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    datasets: [{
                                        label: 'DIAS TRANSCURRIDOS',
                                        data: dias,
                                        backgroundColor: [
                                            'rgba(236, 112, 99, 0.1)'
                                        ],
                                        borderColor: [
                                            'rgba(236, 112, 99)'
                                        ],
                                    }, {
                                        label: 'PASOS',
                                        data: pasos,
                                        backgroundColor: [
                                            'rgba(84, 153, 199 , 0.1)'
                                        ],
                                        borderColor: [
                                            'rgba(84, 153, 199 )'
                                        ],
                                        // Changes this dataset to become a line
                                        type: 'line'
                                    }],
                                    labels: dependencia
                                },
                                options: {
                                    scales: {
                                        yAxes: [{
                                            ticks: {
                                                beginAtZero: true
                                            }
                                        }]
                                    },
                                    legend: {
                                        display: true,
                                        labels: {
                                            fontColor: 'rgb(0, 99, 132)'
                                        },
                                        position:'top'
                                    }
                                }
                            });
/*
                            var myChart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    datasets: [{
                                        label: 'Bar Dataset',
                                        data: dias,
                                        // this dataset is drawn below
                                        order: 1
                                    }, {
                                        label: 'Line Dataset',
                                        data: [10, 10, 10, 10],
                                        type: 'line',
                                        // this dataset is drawn on top
                                        order: 2
                                    }],
                                    datasets: [{
                                        label: 'Dias Transcurridos',
                                        data: pasos,
                                        backgroundColor: [
                                            'rgba(0, 99, 132, 0.2)'
                                        ],
                                        borderColor: [
                                            'rgba(0, 99, 132, 1)'
                                        ],
                                        borderWidth: 1
                                    }],
                                    labels:dependencia
                                },
                                options: {
                                    scales: {
                                        yAxes: [{
                                            ticks: {
                                                beginAtZero: true
                                            }
                                        }]
                                    },
                                    legend: {
                                        display: true,
                                        labels: {
                                            fontColor: 'rgb(0, 99, 132)'
                                        },
                                        position:'top'
                                    }
                                }
                            });*/

                            $('#downloadPdf').click(function(){
                                var reportPageHeight = $('#reportImage').innerHeight();
                                var reportPageWidth = $('#reportImage').innerWidth();
                                var pdfCanvas = $('<canvas />').attr({
                                    id: "canvaspdf",
                                    width: reportPageWidth,
                                    height: reportPageHeight
                                });
                                var pdfctx = $(pdfCanvas)[0].getContext('2d');
                                var pdfctxX = 0;
                                var pdfctxY = 0;
                                var buffer = 100;
                                $("canvas").each(function(index) {
                                    // get the chart height/width
                                    var canvasHeight = $(this).innerHeight();
                                    var canvasWidth = $(this).innerWidth();
                                    
                                    // draw the chart into the new canvas
                                    pdfctx.drawImage($(this)[0], pdfctxX, pdfctxY, canvasWidth, canvasHeight);
                                    pdfctxX += canvasWidth + buffer;
                                    
                                    // our report page is in a grid pattern so replicate that in the new canvas
                                    if (index % 2 === 1) {
                                        pdfctxX = 0;
                                        pdfctxY += canvasHeight + buffer;
                                    }
                                })
                                // create new pdf and add our new canvas as an image
                                var pdf = new jsPDF('l', 'pt', [reportPageWidth, reportPageHeight]);
                                pdf.addImage($(pdfCanvas)[0], 'PNG', 0, 0);
                                pdf.save('Reporte_Grafico.pdf');                      
                            })
                        }
                        $("#preloader").hide();
                        $("#btn-send").hide();
                    }
                });
        }
      
    })

});