<div class="row">
        <div class="col-md-6">
            <div >
                    <h4 class="text-center font-weight-bold m-0">NUEVA CONSULTA DE EXPEDIENTE DE LA PLATAFORMA DIGITAL DE GESTION DOCUMENTAL</h4>
                    <div class="d-flex justify-content-center">
                            <img src="{{asset('assets/img/logo.png')}}" alt="HONORABLE MUNICPALIDAD DE PASCO"  style="width:10%">
                    </div>
                    <h5 class="text-center my-3">Ingrese numero de expediente del ticket que desea hacer seguimiento.</h5>
            </div>
            
        </div>
        <div class="col-md-6">
            <form id="idForm">
                @csrf
                <div class="form-group my-3" id="replace_submit">
                    <label for="ticketInput">Numero de Ticket: Solo numeros</label>
                    <input type="text" class="form-control form-control-lg text-center input-large" placeholder="14-00001-00020-2023-08-000001-0" name="ticket" id="ticketInput">
                </div>
                <div class="form-group d-flex justify-content-center">
                    <button type="button" class="btn btn-danger m-2" id="btn_new">Nuevo Documento</button>
                    <button type="submit" class="btn btn-warning m-2" id="btn-send">Buscar Documento</button>
                </div>
                
            </form>
        </div>
    </div>

