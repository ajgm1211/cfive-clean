        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--left" role="tablist" style="border-bottom: none;">
                    <input type="hidden" id="quote-id" value="<?php echo e($quote->id); ?>"/>
                    <li class="nav-item m-tabs__item size-14px" >
                        <a href="<?php echo e(url('/v2/quotes/search')); ?>">
                            <- Back to search
                        </a>
                    </li>                    
                </ul>                
                <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right" role="tablist" style="border-bottom: none;">
                    <input type="hidden" id="quote-id" value="<?php echo e($quote->id); ?>"/>
                    <li class="nav-item m-tabs__item" >
                        <button class="btn btn-primary-v2" data-toggle="modal" data-target="#SendQuoteModal">
                            Send &nbsp;&nbsp;<i class="fa fa-envelope"></i>
                        </button>
                    </li>
                    <li class="nav-item m-tabs__item" >
                        <a class="btn btn-primary-v2" href="#">
                            PDF &nbsp;&nbsp;<i class="fa fa-download"></i>
                        </a>
                    </li>
                    <li class="nav-item m-tabs__item" >
                        <a class="btn btn-primary-v2" href="<?php echo e(route('quotes-v2.duplicate',setearRouteKey($quote->id))); ?>">
                            Duplicate &nbsp;&nbsp;<i class="fa fa-plus"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Quote details -->
            <div class="col-md-12">
                <div class="m-portlet custom-portlet">
                    <div class="m-portlet__head">
                        <div class="row" style="padding-top: 20px;">
                            <h3 class="title-quote size-14px">Quote info</h3>
                        </div>
                        <div class="m-portlet__head-tools">
                            <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line-danger" role="tablist" style="border-bottom: none;">
                                <li class="nav-item m-tabs__item" id="edit_li">
                                    <a class="btn btn-primary-v2" id="edit-quote" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Edit &nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="tab-content">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" value="<?php echo e($quote->id); ?>" class="form-control id" hidden >
                                    <input type="text" id="currency_id" value="<?php echo e($currency_cfg->alphacode); ?>" class="form-control id" hidden >
                                    <label class="title-quote"><b>Quotation ID:&nbsp;&nbsp;</b></label>
                                    <input type="text" value="<?php echo e($quote->custom_quote_id!='' ? $quote->custom_quote_id:$quote->quote_id); ?>" class="form-control quote_id" hidden >
                                    <span class="quote_id_span"><?php echo e($quote->custom_quote_id!='' ? $quote->custom_quote_id:$quote->quote_id); ?></span>
                                </div>
                                <div class="col-md-4">
                                    <label class="title-quote"><b>Type:&nbsp;&nbsp;</b></label>
                                    <input type="text" value="<?php echo e($quote->quote_id); ?>" class="form-control" hidden >
                                    <?php echo e(Form::select('type',['FCL'=>'FCL','LCL'=>'LCL','AIR'],$quote->type,['class'=>'form-control type select2','hidden','disabled'])); ?>

                                    <span class="type_span"><?php echo e($quote->type); ?></span>
                                </div>
                                <div class="col-md-4">
                                    <label class="title-quote"><b>Company:&nbsp;&nbsp;</b></label>
                                    <?php echo e(Form::select('company_id',$companies,$quote->company_id,['class'=>'form-control company_id select2','hidden'])); ?>

                                    <span class="company_span"><?php echo e($quote->company->business_name); ?></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Status:&nbsp;&nbsp;</b></label>
                                    <?php echo e(Form::select('status',['Draft'=>'Draft','Win'=>'Win','Sent'=>'Sent'],$quote->status,['class'=>'form-control status select2','hidden',''])); ?>

                                    <span class="status_span Status_<?php echo e($quote->status); ?>" style="border-radius: 10px;"><?php echo e($quote->status); ?> <i class="fa fa-check"></i></span>
                                </div>
                                <div class="col-md-4" <?php echo e($quote->type!='AIR' ? '':'hidden'); ?>>
                                    <br>
                                    <label class="title-quote"><b>Destination type:&nbsp;&nbsp;</b></label>
                                    <?php echo e(Form::select('status',[1=>'Port to Port',2=>'Port to Door',3=>'Door to Port',4=>'Door to Door'],$quote->delivery_type,['class'=>'form-control delivery_type select2','hidden',''])); ?>

                                    <span class="delivery_type_span">
                                        <?php if($quote->delivery_type==1): ?>
                                            Port to Port
                                        <?php elseif($quote->delivery_type==2): ?>
                                            Port to Door
                                        <?php elseif($quote->delivery_type==3): ?>
                                            Door to Port
                                        <?php else: ?>
                                            Door to Door
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="col-md-4" <?php echo e($quote->type=='AIR' ? '':'hidden'); ?>>
                                    <br>
                                    <label class="title-quote"><b>Destination type:&nbsp;&nbsp;</b></label>
                                    <?php echo e(Form::select('status',[1=>'Airport to Airport',2=>'Airport to Door',3=>'Door to Airport',4=>'Door to Door'],$quote->delivery_type,['class'=>'form-control delivery_type select2','hidden',''])); ?>

                                    <span class="delivery_type_span">
                                        <?php if($quote->delivery_type==1): ?>
                                            Airport to Airport
                                        <?php elseif($quote->delivery_type==2): ?>
                                            Airport to Door
                                        <?php elseif($quote->delivery_type==3): ?>
                                            Door to Airport
                                        <?php else: ?>
                                            Door to Door
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Contact:&nbsp;&nbsp;</b></label>
                                    <?php echo e(Form::select('contact_id',$contacts,$quote->contact_id,['class'=>'form-control contact_id select2','hidden'])); ?>

                                    <span class="contact_id_span"><?php echo e($quote->contact->first_name); ?> <?php echo e($quote->contact->last_name); ?></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Date issued:&nbsp;&nbsp;</b></label>
                                    <?php
                                        $date = date_create($quote->date_issued);
                                    ?>
                                    <span class="date_issued_span"><?php echo e(date_format($date, 'M d, Y H:i')); ?></span>
                                    <?php echo Form::text('created_at', date_format($date, 'Y-m-d H:i'), ['placeholder' => 'Validity','class' => 'form-control m-input date_issued','readonly'=>true,'required' => 'required','hidden']); ?>

                                </div>                              
                                <div class="col-md-4" <?php echo e($quote->type=='FCL' ? '':'hidden'); ?>>
                                    <br>
                                    <label class="title-quote"><b>Equipment:&nbsp;&nbsp;</b></label>
                                    <span class="equipment_span">
                                        <?php if($quote->equipment!=''): ?>
                                            <?php
                                                $equipment=json_decode($quote->equipment);
                                            ?>
                                            <?php $__currentLoopData = $equipment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php echo e($item); ?><?php if (! ($loop->last)): ?>,<?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </span>
                                    <?php echo e(Form::select('equipment[]',['20' => '20','40' => '40','40HC'=>'40HC','40NOR'=>'40NOR','45'=>'45'],@$equipment,['class'=>'form-control equipment','id'=>'equipment','multiple' => 'multiple','required' => 'true','hidden','disabled'])); ?>

                                </div>
                                <div class="col-md-4" <?php echo e($quote->type!='FCL' ? '':'hidden'); ?>>
                                    <br>
                                    <label class="title-quote"><b>Owner:&nbsp;&nbsp;</b></label>
                                    <?php echo e(Form::select('user_id',$users,$quote->user_id,['class'=>'form-control user_id select2','hidden',''])); ?>

                                    <span class="user_id_span"><?php echo e($quote->user->name); ?> <?php echo e($quote->user->lastname); ?></span>
                                </div>
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Price level:&nbsp;&nbsp;</b></label>
                                    <span class="price_level_span"><?php echo e(@$quote->price->name); ?></span>
                                    <?php echo e(Form::select('price_id',$prices,@$quote->price_id,['class'=>'form-control price_id select2','hidden'])); ?>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Validity:&nbsp;&nbsp;</b></label>
                                    <span class="validity_span"><?php echo e($quote->validity_start); ?> / <?php echo e($quote->validity_end); ?></span>
                                    <?php
                                        $validity = $quote->validity_start ." / ". $quote->validity_end;
                                    ?>
                                    <?php echo Form::text('validity_date', $validity, ['placeholder' => 'Validity','class' => 'form-control m-input validity','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required','hidden']); ?>

                                </div>
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Incoterm:&nbsp;&nbsp;</b></label>
                                    <?php echo e(Form::select('incoterm_id',$incoterms,$quote->incoterm_id,['class'=>'form-control incoterm_id select2','hidden',''])); ?>

                                    <span class="incoterm_id_span"><?php echo e($quote->incoterm->name); ?></span>
                                </div>
                                <?php if($quote->type=='FCL'): ?>
                                    <div class="col-md-4">
                                        <br>
                                        <label class="title-quote"><b>Owner:&nbsp;&nbsp;</b></label>
                                        <?php echo e(Form::select('user_id',$users,$quote->user_id,['class'=>'form-control user_id select2','hidden',''])); ?>

                                        <span class="user_id_span"><?php echo e($quote->user->name); ?> <?php echo e($quote->user->lastname); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center" id="update_buttons" hidden>
                                    <br>
                                    <hr>
                                    <br>
                                    <a class="btn btn-danger" id="cancel" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Cancel&nbsp;&nbsp;<i class="fa fa-close"></i>
                                    </a>
                                    <a class="btn btn-primary" id="update" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Update&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>