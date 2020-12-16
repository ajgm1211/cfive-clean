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


    <div class="footer-page" style="background: {{ @$user->companyUser->colors_pdf }}"></div>

</body>

</html>