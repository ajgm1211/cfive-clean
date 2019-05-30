<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 15/05/2018
 * Time: 03:59 PM
 */
?>

<?php $__env->startSection('title', 'Companies | Details'); ?>
<?php $__env->startSection('css'); ?>
    ##parent-placeholder-2f84417a9e73cead4d5c99e05daff2a534b30132##
    <link rel="stylesheet" type="text/css" href="/assets/plugins/button-dropdown/css/bootstrap.css">
    <script src="/assets/plugins/button-dropdown/js/jquery3.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="/assets/plugins/button-dropdown/js/bootstrap.js"></script>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="m-content">
        <div class="m-portlet--mobile">
            <?php if(Session::has('message.nivel')): ?>
                <div class="col-md-12">
                    <br>
                    <div class="m-alert m-alert--icon m-alert--outline alert alert-<?php echo e(session('message.nivel')); ?> alert-dismissible fade show" role="alert">
                        <div class="m-alert__icon">
                            <i class="la la-warning"></i>
                        </div>
                        <div class="m-alert__text">
                            <strong>
                                <?php echo e(session('message.title')); ?>

                            </strong>
                            <?php echo e(session('message.content')); ?>

                        </div>
                        <div class="m-alert__close">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="m-portlet__body">
                <!--begin: Search Form -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="m-portlet m-portlet--brand m-portlet--head-solid-bg">
                                    <div class="m-portlet__body text-center">
                                        <?php if($company->logo!=''): ?>
                                            <div class="" style="line-height: .5;">
                                                <img src="<?php echo e(Storage::disk('s3_upload')->url($company->logo)); ?>" class="img img-fluid" style="width: 100px; height: auto; margin-bottom:25px">
                                            </div>
                                            <br>
                                        <?php endif; ?>
                                        <h2 class="size-18px color-blue" style="text-transform: uppercase;"><b><?php echo e($company->business_name); ?></b> </h2>
                                        <hr>
                                        <button class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Actions
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="top-start" style="position: absolute; transform: translate3d(0px, -136px, 0px); top: 0px; left: 0px; will-change: transform;">
                                            <a class="dropdown-item" href="#" onclick="AbrirModal('edit',<?php echo e($company->id); ?>)">
                                            <span>
                                                Edit
                                                &nbsp;
                                                <i class="la la-edit"></i>
                                            </span>
                                            </a>
                                            <a id="delete-company-show" href="#" class="dropdown-item" data-company-id="<?php echo e($company->id); ?>" title="Delete">
                                            <span>
                                                Delete
                                                &nbsp;
                                                <i class="la la-trash"></i>
                                            </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="m-portlet m-portlet--brand m-portlet--head-solid-bg">
                                    <div class="m-portlet__body">
                                        <h4 class="size-16px color-blue" data-toggle="collapse" data-target="#about_company" style="cursor: pointer"><i class="fa fa-angle-down"></i> &nbsp;<b>About <?php echo e($company->business_name); ?></b></h4>
                                        <hr>
                                        <div class="collapse show" id="about_company">
                                            <label><b>Name</b></label>
                                            <p class="color-black">
                                                <span id="business_name_span"><?php echo e($company->business_name); ?></span>
                                                <input type="text" class="form-control" id="business_name_input" value="<?php echo e($company->business_name); ?>" hidden>
                                                <a  id='edit_business_name' onclick="display_business_name()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill pull-right"  title="Edit ">
                                                    <i class="la la-edit"></i>
                                                </a>
                                                <a  id='save_business_name' onclick="save_business_name(<?php echo e($company->id); ?>)" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Save" hidden="true">

                                                    <i class="la la-save"></i>
                                                </a>
                                                <a  id='cancel_business_name' onclick="cancel_business_name()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" hidden="true">
                                                    <i  class="la la-reply"></i>
                                                </a>
                                                <br>
                                            </p>
                                            <hr>
                                            <label><b>Phone</b></label>
                                            <p class="color-black">
                                                <span id="phone_span"><?php echo e($company->phone); ?></span>
                                                <input type="text" class="form-control" id="phone_input" value="<?php echo e($company->phone); ?>" hidden>
                                                <a  id='edit_phone' onclick="display_phone()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill pull-right"  title="Edit ">
                                                    <i class="la la-edit"></i>
                                                </a>
                                                <a  id='save_phone' onclick="save_phone(<?php echo e($company->id); ?>)" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Save" hidden="true">
                                                    <i class="la la-save"></i>
                                                </a>
                                                <a  id='cancel_phone' onclick="cancel_phone()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" hidden="true">
                                                    <i  class="la la-reply"></i>
                                                </a>
                                                <br>
                                            </p>
                                            <hr>
                                            <label><b>Email</b></label>
                                            <p class="color-black">
                                                <span id="email_span"><?php echo e($company->email); ?></span>
                                                <input type="email" class="form-control" id="email_input" value="<?php echo e($company->email); ?>" hidden>
                                                <a  id='edit_email' onclick="display_email()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill pull-right"  title="Edit ">
                                                    <i class="la la-edit"></i>
                                                </a>
                                                <a  id='save_email' onclick="save_email(<?php echo e($company->id); ?>)" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Save" hidden="true">
                                                    <i class="la la-save"></i>
                                                </a>
                                                <a  id='cancel_email' onclick="cancel_email()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" hidden="true">
                                                    <i  class="la la-reply"></i>
                                                </a>
                                                <br>
                                            </p>
                                            <hr>
                                            <label><b>Tax number</b></label>
                                            <p class="color-black">
                                                <span id="tax_number_span"><?php echo e($company->tax_number); ?></span>
                                                <input type="text" class="form-control" id="tax_number_input" value="<?php echo e($company->tax_number); ?>" hidden>
                                                <a  id='edit_tax_number' onclick="display_tax_number()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill pull-right"  title="Edit ">
                                                    <i class="la la-edit"></i>
                                                </a>
                                                <a  id='save_tax_number' onclick="save_tax_number(<?php echo e($company->id); ?>)" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Save" hidden="true">
                                                    <i class="la la-save"></i>
                                                </a>
                                                <a  id='cancel_tax_number' onclick="cancel_tax_number()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" hidden="true">
                                                    <i  class="la la-reply"></i>
                                                </a>
                                                <br>
                                            </p>
                                            <hr>
                                            <label><b>PDF language</b></label>
                                            <p class="color-black">
                                            <span id="pdf_language_span">
                                                <?php if($company->pdf_language==1): ?>
                                                    English
                                                <?php elseif($company->pdf_language==2): ?>
                                                    Spanish
                                                <?php else: ?>
                                                    Portuguese
                                                <?php endif; ?>
                                            </span>
                                                <?php echo e(Form::select('pdf_language',['0'=>'Choose a language',1=>'English',2=>'Spanish',3=>'Portuguese'],$company->pdf_language,['class'=>'custom-select form-control','id' => 'pdf_language_select','hidden'=>'true'])); ?>

                                                <a  id='edit_pdf_language' onclick="display_pdf_language()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill pull-right"  title="Edit ">
                                                    <i class="la la-edit"></i>
                                                </a>
                                                <a  id='save_pdf_language' onclick="save_pdf_language(<?php echo e($company->id); ?>)" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Save" hidden="true">
                                                    <i class="la la-save"></i>
                                                </a>
                                                <a  id='cancel_pdf_language' onclick="cancel_pdf_language()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" hidden="true">
                                                    <i  class="la la-reply"></i>
                                                </a>
                                                <br>
                                            </p>
                                            <hr>
                                            <label><b>Address</b></label>
                                            <p class="color-black">
                                                <span id="address_span"><?php echo e($company->address); ?></span>
                                                <textarea class="form-control" id="address_input" hidden>
                                                <?php echo e(trim($company->address)); ?>

                                            </textarea>
                                                <a  id='edit_address' onclick="display_address()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill pull-right"  title="Edit ">
                                                    <i class="la la-edit"></i>
                                                </a>
                                                <a  id='save_address' onclick="save_address(<?php echo e($company->id); ?>)" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Save" hidden>

                                                    <i class="la la-save"></i>
                                                </a>
                                                <a  id='cancel_address' onclick="cancel_address()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" hidden>
                                                    <i  class="la la-reply"></i>
                                                </a>
                                                <br>
                                            </p>
                                            <hr>
                                            <label><b>Price level</b></label>
                                            <?php if(isset($company->price_name) && count($company->price_name)>0): ?>
                                                <div id="price_level_list">
                                                    <ul id="price_level_ul">
                                                        <?php $__currentLoopData = $company->price_name; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $price): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <li style="margin-left: -25px;" class="color-black"><?php echo e($price->name); ?></li>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ul>
                                                </div>

                                            <?php else: ?>
                                                <p class="color-black">
                                                    <span id="price_level_span">There are not associated prices</span>
                                                </p>
                                            <?php endif; ?>
                                            <p>
                                                <?php echo e(Form::select('price_id[]',$prices,$company->price_name,['class'=>'custom-select form-control','id' => 'price_level_select','multiple'=>'true','hidden'=>'false'])); ?>

                                                <a  id='edit_prices' onclick="display_price_level()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill pull-right"  title="Edit ">
                                                    <i class="la la-edit"></i>
                                                </a>
                                                <a  id='save_prices' onclick="save_price_level(<?php echo e($company->id); ?>)" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Save" hidden>
                                                    <i class="la la-save"></i>
                                                </a>
                                                <a  id='cancel_prices' onclick="cancel_price_level()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" hidden>
                                                    <i  class="la la-reply"></i>
                                                </a>
                                                <br>
                                            </p>
                                            <hr>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="m-portlet m-portlet--brand m-portlet--head-solid-bg">
                                    <div class="m-portlet__body">
                                        <h4 class="size-16px color-blue" data-toggle="collapse" data-target="#about_company" style="cursor: pointer"><i class="fa fa-angle-down"></i> &nbsp;<b>Payment conditions</b></h4>
                                        <hr>
                                        <div class="collapse show" id="about_company">
                                            <?php echo Form::open(['route' => 'companies.update.payments','class' => 'form-group m-form__group','type'=>'POST']); ?>

                                            <input type="hidden" name="company_id" value="<?php echo e($company->id); ?>"/>
                                            <?php echo Form::textarea('payment_conditions', $company->payment_conditions, ['placeholder' => 'Please enter payment conditions','class' => 'form-control m-input address_input editor','id'=>'payment_conditions','rows'=>4]); ?>

                                            <br>
                                            <button class="btn btn-primary" type="submit">
                                                Save
                                            </button>
                                            <?php echo Form::close(); ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="m-portlet m-portlet--brand m-portlet--head-solid-bg">
                                    <div class="m-portlet__body">
                                        <h4 class="size-16px color-blue" data-toggle="collapse" data-target="#company_contacts" style="cursor: pointer"><i class="fa fa-angle-down"></i> &nbsp;<b>Contacts</b></h4>
                                        <hr>
                                        <div class="collapse show" id="company_contacts">
                                            <?php if(!$company->contact->isEmpty()): ?>
                                                <?php $__currentLoopData = $company->contact; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <ul>
                                                        <li><?php echo e($contact->first_name); ?> <?php echo e($contact->last_name); ?> <a href="#" data-contact-id="<?php echo e($contact->id); ?>" id="delete-contact"><span class="pull-right"><i class="fa fa-close"></i></span></a></li>
                                                    </ul>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php else: ?>
                                                <p>No contacts</p>
                                            <?php endif; ?>
                                            <br>
                                            <div class="text-center">
                                                <button class="btn btn-default" onclick="AbrirModal('addContact',<?php echo e($company->id); ?>)">
                                                    Add contact
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="m-portlet m-portlet--brand m-portlet--head-solid-bg">
                                    <div class="m-portlet__body">
                                        <h4 class="size-16px color-blue" data-toggle="collapse" data-target="#company_owners" style="cursor: pointer"><i class="fa fa-angle-down"></i> &nbsp;<b>Owners</b></h4>
                                        <hr>
                                        <div class="collapse show" id="company_owners">
                                            <?php if(!$company->groupUserCompanies->isEmpty()): ?>
                                                <?php $__currentLoopData = $company->groupUserCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <ul>
                                                        <li><?php echo e($groupUser->user->name); ?> <?php echo e($groupUser->user->lastname); ?> <a href="#" data-owner-id="<?php echo e($groupUser->user_id); ?>" id="delete-owner"><span class="pull-right"><i class="fa fa-close"></i></span></a></li>
                                                    </ul>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php else: ?>
                                                <p>No Owners</p>
                                            <?php endif; ?>
                                            <br>
                                            <div class="text-center">
                                                <button class="btn btn-default" data-toggle="modal" data-target="#addOwnerModal">
                                                    Add owner
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <table class="m-datatable text-center" id="html_table" >
                            <thead>
                            <tr>
                                <th title="Status">
                                    Status
                                </th>
                                <th title="Created">
                                    Created
                                </th>
                                <th title="Origin">
                                    Origin
                                </th>
                                <th title="Destination">
                                    Destination
                                </th>
                                <th title="Ammount">
                                    Ammount
                                </th>
                                <th title="Options">
                                    Options
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $quotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td ><span class="<?php echo e($quote->status->name); ?>"><?php echo e($quote->status->name); ?></span></td>
                                    <td><?php echo e(date_format($quote->created_at, 'M d, Y H:i')); ?></td>
                                    <?php if($quote->origin_harbor): ?>
                                        <td><?php echo e($quote->origin_harbor->name); ?></td>
                                    <?php else: ?>
                                        <td><?php echo e($quote->origin_address); ?></td>
                                    <?php endif; ?>
                                    <?php if($quote->destination_harbor): ?>
                                        <td><?php echo e($quote->destination_harbor->name); ?></td>
                                    <?php else: ?>
                                        <td><?php echo e($quote->destination_address); ?></td>
                                    <?php endif; ?>
                                    <td><?php echo e($quote->sub_total_origin+$quote->sub_total_freight+$quote->sub_total_destination); ?> <?php echo e($quote->currencies->alphacode); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('quotes.show',setearRouteKey($quote->id))); ?>" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Show ">
                                            <i class="la la-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('quotes.edit',setearRouteKey($quote->id))); ?>" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                                            <i class="la la-edit"></i>
                                        </a>
                                        <a href="<?php echo e(route('quotes.duplicate',setearRouteKey($quote->id))); ?>" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Duplicate ">
                                            <i class="la la-plus"></i>
                                        </a>
                                        <button id="delete-quote" data-quote-id="<?php echo e($quote->id); ?>" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Delete ">
                                            <i class="la la-eraser"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('companies.partials.companiesModal', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php echo $__env->make('companies.partials.deleteCompaniesModal', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php echo $__env->make('companies.partials.addContactModal', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php echo $__env->make('companies.partials.addOwnerModal', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    ##parent-placeholder-93f8bb0eb2c659b85694486c41717eaf0fe23cd4##
    <script src="<?php echo e(asset('js/base.js')); ?>" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/datatables/base/html-table-quotes.js" type="text/javascript"></script>
    <script src="<?php echo e(asset('js/tinymce/jquery.tinymce.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/tinymce/tinymce.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/companies.js')); ?>"></script>
    <script>

        var editor_config = {
            path_absolute : "/",
            selector: "textarea#payment_conditions",
            plugins: ["template"],
            toolbar: "insertfile undo redo | template | bold italic strikethrough | alignleft aligncenter alignright alignjustify | ltr rtl | bullist numlist outdent indent removeformat formatselect| link image media | emoticons charmap | code codesample | forecolor backcolor",
            external_plugins: { "nanospell": "<?php echo e(asset('js/tinymce/plugins/nanospell/plugin.js')); ?>" },
            nanospell_server:"php",
            browser_spellcheck: true,
            relative_urls: false,
            remove_script_host: false,
            file_browser_callback : function(field_name, url, type, win) {
                var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
                var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

                var cmsURL = editor_config.path_absolute + 'laravel-filemanager?field_name=' + field_name;
                if (type == 'image') {
                    cmsURL = cmsURL + "&type=Images";
                } else {
                    cmsURL = cmsURL + "&type=Files";
                }

                tinymce.activeEditor.windowManager.open({
                    file: '<?= route('elfinder.tinymce4') ?>',// use an absolute path!
                    title: 'File manager',
                    width: 900,
                    height: 450,
                    resizable: 'yes'
                }, {
                    setUrl: function (url) {
                        win.document.getElementById(field_name).value = url;
                    }
                });
            }
        };

        tinymce.init(editor_config);

        function AbrirModal(action,id){
            if(action == "edit"){
                var url = '<?php echo e(route("companies.edit", ":id")); ?>';
                url = url.replace(':id', id);
                $('.modal-body').load(url,function(){
                    $('#companyModal').modal({show:true});
                });
            }if(action == "add"){
                var url = '<?php echo e(route("companies.add")); ?>';
                $('.modal-body').load(url,function(){
                    $('#companyModal').modal({show:true});
                });
            }
            if(action == "delete"){
                var url = '<?php echo e(route("companies.delete", ":id")); ?>';
                url = url.replace(':id', id);
                $('.modal-body').load(url,function(){
                    $('#deleteCompanyModal').modal({show:true});
                });
            }
            if(action == "addContact"){
                var url = '<?php echo e(route("contacts.addCMC",":id")); ?>';
                url = url.replace(':id', id);
                $('.modal-body').load(url,function(){
                    $('#addContactModal').modal({show:true});
                });
            }

        }

    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>