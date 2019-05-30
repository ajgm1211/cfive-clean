<!DOCTYPE html>
<html lang="en" >
<?php echo $__env->make('includes.head', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<body class="m-page--fluid"  >
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MT7GKLK"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<div class="m-grid m-grid--hor m-grid--root m-page">

    <?php echo $__env->make('includes.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop l-grid--desktop m-body">

            <?php $__env->startSection('content'); ?>
            <?php echo $__env->yieldSection(); ?>

    </div>
</div>


<div class="m-scroll-top m-scroll-top--skin-top" data-toggle="m-scroll-top" data-scroll-offset="500" data-scroll-speed="300">
    <i class="la la-arrow-up"></i>
</div>
<?php echo $__env->make('includes.scriptsbottombody', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</body>
</html>
