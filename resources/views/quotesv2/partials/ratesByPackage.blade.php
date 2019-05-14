                <div class="row">
                  <div class="col-md-12">
                    <div class="m-portlet custom-portlet">
                      <div class="m-portlet__body">

                        @if($quote->total_quantity!='' && $quote->total_quantity>0)
                        <div class="row">
                          <div class="col-md-3">
                            <div id="cargo_details_cargo_type_p"><b>Cargo type:</b> {{$quote->type_cargo == 1 ? 'Pallets' : 'Packages'}}</div>
                          </div>
                          <div class="col-md-3">
                            <div id="cargo_details_total_quantity_p"><b>Total quantity:</b> {{$quote->total_quantity != '' ? $quote->total_quantity : ''}}</div>
                          </div>
                          <div class="col-md-3">
                            <div id="cargo_details_total_weight_p"><b>Total weight: </b> {{$quote->total_weight != '' ? $quote->total_weight.' Kg' : ''}}</div>
                          </div>
                          <div class="col-md-3">
                            <p id="cargo_details_total_volume_p"><b>Total volume: </b> {!!$quote->total_volume != '' ? $quote->total_volume.' m<sup>3</sup>' : ''!!}</p>
                          </div>
                        </div>
                        @endif
                        
                        @if(!empty($package_loads) && count($package_loads)>0)
                        <div class="row">
                          <div class="col-md-12">
                            <table class="table table-sm table-bordered table-hover table color-blue text-center">
                              <thead class="title-quote text-center header-table">
                                <tr>
                                  <td >Cargo type</td>
                                  <td >Quantity</td>
                                  <td >Height</td>
                                  <td >Width</td>
                                  <td >Large</td>
                                  <td >Weight</td>
                                  <td >Total weight</td>
                                  <td >Volume</td>
                                </tr>
                              </thead>
                              <tbody style="background-color: white;">
                                @foreach($package_loads as $package_load)
                                <tr class="text-center">
                                  <td>{{$package_load->type_cargo==1 ? 'Pallets':'Packages'}}</td>
                                  <td>{{$package_load->quantity}}</td>
                                  <td>{{$package_load->height}} cm</td>
                                  <td>{{$package_load->width}} cm</td>
                                  <td>{{$package_load->large}} cm</td>
                                  <td>{{$package_load->weight}} kg</td>
                                  <td>{{$package_load->total_weight}} kg</td>
                                  <td>{{$package_load->volume}} m<sup>3</sup></td>
                                </tr>
                                @endforeach
                              </tbody>
                            </table>
                          </div>
                        </div>
                        <br>
                        <div class="row">
                          <div class="col-md-12 ">
                            <span class="pull-right">
                              <b>Total:</b> {{$package_loads->sum('quantity')}} un {{$package_loads->sum('volume')}} m<sup>3</sup> {{$package_loads->sum('total_weight')}} kg
                            </span>
                          </div>
                        </div>
                        @endif
                        @if($quote->chargeable_weight!='' && $quote->chargeable_weight>0)
                        <div class="row">
                          <div class="col-md-12 ">
                            <br>
                            <b>Chargeable weight:</b> {{$quote->chargeable_weight}} kg
                          </div>
                        </div>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
