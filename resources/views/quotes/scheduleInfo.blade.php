

<div class="m-portlet">

  <div class="m-portlet__body">

    <table class="table table-hover">
      <tr class="thead-light">
        <th><span class="portalphacode">Vessel</span></th>
        <th><span class="portalphacode">ETD</span></th>
        <th><span class="portalphacode"><center>Transit Time</center></span>  </th>
        <th><span class="portalphacode">ETA</span></th>
        <th><span class="portalphacode">-</span></th>         
      </tr>                    


      @foreach($schedulesFin as $schedule)
      

      <tr>
        <td width='15%'>{{ $schedule['VesselName'] }}</td>
        <td width='15%'>{{ $schedule['Etd'] }}</td>
        <td width='45%'>
          <div class="row">
            <div class="col-md-4">
              <span class="portcss"> {{ strtoupper($code_orig->name) }} </span><br>            
            </div>
            <div class="col-md-4">
              <center> {{ $schedule['days'] }} Days</center>
              <div class="progress m-progress--sm">    
                <div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <center> {{ $schedule['type'] }} </center>

            </div>
            <div class="col-md-4">
              <span class="portcss">{{ strtoupper($code_dest->name) }} </span><br>

            </div>
          </div>                        
        </td>
        <td width='15%'>{{ $schedule['Eta'] }}</td>
        <td width='10%'>      
          <label class="m-checkbox m-checkbox--state-brand">
            <input class = 'sche' name="schedules[]" type="checkbox" value="{{ json_encode($schedule) }}"> 
            <span></span>
          </label>
        </td>

      </tr>
      @endforeach

    </table>

  </div>
  <br>
  <div class="form-group m-form__group">
    <button id="select-schedule" class="btn btn-info">Select</button>

  </div>

</div>