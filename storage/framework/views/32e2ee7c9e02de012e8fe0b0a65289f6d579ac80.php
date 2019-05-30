<head>
  <meta charset="utf-8" />
  <title>
    <?php echo $__env->yieldContent('title'); ?>
  </title>
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <meta name="description" content="Latest updates and statistic charts">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <?php $__env->startSection('css'); ?>

  
  <link href="/assets/vendors/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
  <!--end::Page Vendors -->
  <!-- Google Tag Manager -->
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push(

      {'gtm.start': new Date().getTime(),event:'gtm.js'}
    );var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
          'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                              })(window,document,'script','dataLayer','GTM-MT7GKLK');</script>
  <!-- End Google Tag Manager -->
  <!--end::Base Styles -->
  <link rel="shortcut icon" href="/favicon.png" />
  <!--<link href="<?php echo e(asset('css/custom.css')); ?>" rel="stylesheet" />-->
  <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet" />
  <link href="<?php echo e(asset('css/jqueryui-editable.css')); ?>" rel="stylesheet" />
  <!--<link href="<?php echo e(asset('css/header.css')); ?>" rel="stylesheet" />-->

  <?php echo $__env->yieldSection(); ?>
</head>
