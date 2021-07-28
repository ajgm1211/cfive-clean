<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Quote #{{$quote->quote_id}}</title>
    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}" media="all" />
    <link rel="stylesheet" href="{{asset('css/style-pdf.css')}}" media="all" />
    <style>

    </style>
</head>

<body style="background-color: white; font-size: 11px; margin: 50px 50px 50px 50px">


    <main>
        @switch(@$user->companyUser->pdf_template_id)
            @case(1)
                @include('quote.pdf.templates.FCL.freight')
            @break

            @case(2)
                @include('quote.pdf.templates.FCL.origin')
            @break

            @default
                @include('quote.pdf.templates.FCL.freight')

        @endswitch
    </main>

    @if($user->companyUser->footer_type=='Image' || $user->companyUser->footer_type=='Text')
        @include('quote.pdf.partials.footer')
    @else
        <div class="footer-page" style="background: {{ @$user->companyUser->colors_pdf }}"></div>
    @endif

</body>

</html>