  <div class="row">
    <div class="col-md-12">
      <div class="m-portlet custom-portlet no-border">
        <div class="m-portlet__head">
          <div class="row" style="padding-top: 20px;">
            <h3 class="title-quote size-14px">Payment conditions</h3>
          </div>
          <div class="m-portlet__head-tools">
            <div class="btn-open" id="pay"><span class="fa fa-angle-down"></span></div>
          </div>
        </div>
        <div class="m-portlet__body display-none portlet-padding pay">
          <div class="row">
            <div class="col-md-12">
              <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line-danger" role="tablist" style="border-bottom: none; margin-bottom: 10px">
                <li class="nav-item m-tabs__item" id="edit_li">
                  <a class="btn btn-primary-v2 btn-edit" id="edit-payments" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                    Edit&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                  </a>
                </li>
              </ul>
            </div>
          </div>
          <div class="card card-body bg-light">
            <span class="payment_conditions_span">{!! $quote->payment_conditions !!}</span>
            <div class="payment_conditions_textarea" hidden>
              <textarea name="payment_conditions" class="form-control payment_conditions editor" id="payment_conditions">{!!$quote->payment_conditions!!}</textarea>
            </div>
            <div class="row">
              <div class="col-md-12 text-center" id="update_payments" hidden>
                <br>
                <a class="btn btn-danger" id="cancel-payments" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                  Cancel&nbsp;&nbsp;<i class="fa fa-close"></i>
                </a>
                <a class="btn btn-primary" id="update-payments" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                  Update &nbsp;&nbsp;<i class="fa fa-pencil"></i>
                </a>
                <br>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>