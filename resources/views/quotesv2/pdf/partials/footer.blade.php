            <footer>
                @if($user->companyUser->footer_type=='Image')
                    @if($user->companyUser->footer_image!='')

                        <div class="clearfix">
asdasd
                            <img src="{{Storage::disk('s3_upload')->url($user->companyUser->footer_image)}}" class="img img-fluid" style="max-width:100%;">

                        </div>

                    @endif
                @else
                asdsad
                    {!!$user->companyUser->footer_text!!}

                @endif
            </footer>