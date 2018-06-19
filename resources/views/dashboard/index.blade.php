<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 29/05/2018
 * Time: 10:50 PM
 */
?>
@extends('layouts.app')
@section('title', 'Settings')
@section('content')
     <div class="m-content">
         <div class="row">
             <div class="col-lg-6">
                 <!--begin::Portlet-->
                 <div class="m-portlet m-portlet--mobile">
                     <div class="m-portlet__head">
                         <div class="m-portlet__head-caption">
                             <div class="m-portlet__head-title">
                                 <h3 class="m-portlet__head-text">
                                     Basic Portlet
                                     <small>
                                         portlet sub title
                                     </small>
                                 </h3>
                             </div>
                         </div>
                     </div>
                     <div class="m-portlet__body">
                         Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled.
                     </div>
                 </div>
                 <!--end::Portlet-->
                               </div>
                 <!--end::Portlet-->
         </div>

     </div>
     <div class="row">
         <div class="col-xl-6 col-lg-12">
             <!--Begin::Portlet-->
             <div class="m-portlet  m-portlet--full-height ">
                 <div class="m-portlet__head">
                     <div class="m-portlet__head-caption">
                         <div class="m-portlet__head-title">
                             <h3 class="m-portlet__head-text">
                                 Recent Activities
                             </h3>
                         </div>
                     </div>
                     <div class="m-portlet__head-tools">
                         <ul class="m-portlet__nav">
                             <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" m-dropdown-toggle="hover" aria-expanded="true">
                                 <a href="#" class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl m-dropdown__toggle">
                                     <i class="la la-ellipsis-h m--font-brand"></i>
                                 </a>
                                 <div class="m-dropdown__wrapper">
                                     <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                                     <div class="m-dropdown__inner">
                                         <div class="m-dropdown__body">
                                             <div class="m-dropdown__content">
                                                 <ul class="m-nav">
                                                     <li class="m-nav__section m-nav__section--first">
																			<span class="m-nav__section-text">
																				Quick Actions
																			</span>
                                                     </li>
                                                     <li class="m-nav__item">
                                                         <a href="" class="m-nav__link">
                                                             <i class="m-nav__link-icon flaticon-share"></i>
                                                             <span class="m-nav__link-text">
																					Activity
																				</span>
                                                         </a>
                                                     </li>
                                                     <li class="m-nav__item">
                                                         <a href="" class="m-nav__link">
                                                             <i class="m-nav__link-icon flaticon-chat-1"></i>
                                                             <span class="m-nav__link-text">
																					Messages
																				</span>
                                                         </a>
                                                     </li>
                                                     <li class="m-nav__item">
                                                         <a href="" class="m-nav__link">
                                                             <i class="m-nav__link-icon flaticon-info"></i>
                                                             <span class="m-nav__link-text">
																					FAQ
																				</span>
                                                         </a>
                                                     </li>
                                                     <li class="m-nav__item">
                                                         <a href="" class="m-nav__link">
                                                             <i class="m-nav__link-icon flaticon-lifebuoy"></i>
                                                             <span class="m-nav__link-text">
																					Support
																				</span>
                                                         </a>
                                                     </li>
                                                     <li class="m-nav__separator m-nav__separator--fit"></li>
                                                     <li class="m-nav__item">
                                                         <a href="#" class="btn btn-outline-danger m-btn m-btn--pill m-btn--wide btn-sm">
                                                             Cancel
                                                         </a>
                                                     </li>
                                                 </ul>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </li>
                         </ul>
                     </div>
                 </div>
                 <div class="m-portlet__body">
                     <div class="m-scrollable mCustomScrollbar _mCS_5 mCS-autoHide _mCS_4" data-scrollbar-shown="true" data-scrollable="true" data-max-height="380" style="overflow: visible; height: 380px; max-height: 380px; position: relative;"><div id="mCSB_4" class="mCustomScrollBox mCS-minimal-dark mCSB_vertical mCSB_outside" tabindex="0" style="max-height: none;"><div id="mCSB_4_container" class="mCSB_container" style="position:relative; top:0; left:0;" dir="ltr">
                                 <!--Begin::Timeline 2 -->
                                 <div class="m-timeline-2">
                                     <div class="m-timeline-2__items  m--padding-top-25 m--padding-bottom-30">
                                         <div class="m-timeline-2__item">
														<span class="m-timeline-2__item-time">
															10:00
														</span>
                                             <div class="m-timeline-2__item-cricle">
                                                 <i class="fa fa-genderless m--font-danger"></i>
                                             </div>
                                             <div class="m-timeline-2__item-text  m--padding-top-5">
                                                 Lorem ipsum dolor sit amit,consectetur eiusmdd tempor
                                                 <br>
                                                 incididunt ut labore et dolore magna
                                             </div>
                                         </div>
                                         <div class="m-timeline-2__item m--margin-top-30">
														<span class="m-timeline-2__item-time">
															12:45
														</span>
                                             <div class="m-timeline-2__item-cricle">
                                                 <i class="fa fa-genderless m--font-success"></i>
                                             </div>
                                             <div class="m-timeline-2__item-text m-timeline-2__item-text--bold">
                                                 AEOL Meeting With
                                             </div>
                                             <div class="m-list-pics m-list-pics--sm m--padding-left-20">
                                                 <a href="#">
                                                     <img src="assets/app/media/img/users/100_4.jpg" title="" class="mCS_img_loaded">
                                                 </a>
                                                 <a href="#">
                                                     <img src="assets/app/media/img/users/100_13.jpg" title="" class="mCS_img_loaded">
                                                 </a>
                                                 <a href="#">
                                                     <img src="assets/app/media/img/users/100_11.jpg" title="" class="mCS_img_loaded">
                                                 </a>
                                                 <a href="#">
                                                     <img src="assets/app/media/img/users/100_14.jpg" title="" class="mCS_img_loaded">
                                                 </a>
                                             </div>
                                         </div>
                                         <div class="m-timeline-2__item m--margin-top-30">
														<span class="m-timeline-2__item-time">
															14:00
														</span>
                                             <div class="m-timeline-2__item-cricle">
                                                 <i class="fa fa-genderless m--font-brand"></i>
                                             </div>
                                             <div class="m-timeline-2__item-text m--padding-top-5">
                                                 Make Deposit
                                                 <a href="#" class="m-link m-link--brand m--font-bolder">
                                                     USD 700
                                                 </a>
                                                 To ESL.
                                             </div>
                                         </div>
                                         <div class="m-timeline-2__item m--margin-top-30">
														<span class="m-timeline-2__item-time">
															16:00
														</span>
                                             <div class="m-timeline-2__item-cricle">
                                                 <i class="fa fa-genderless m--font-warning"></i>
                                             </div>
                                             <div class="m-timeline-2__item-text m--padding-top-5">
                                                 Lorem ipsum dolor sit amit,consectetur eiusmdd tempor
                                                 <br>
                                                 incididunt ut labore et dolore magna elit enim at minim
                                                 <br>
                                                 veniam quis nostrud
                                             </div>
                                         </div>
                                         <div class="m-timeline-2__item m--margin-top-30">
														<span class="m-timeline-2__item-time">
															17:00
														</span>
                                             <div class="m-timeline-2__item-cricle">
                                                 <i class="fa fa-genderless m--font-info"></i>
                                             </div>
                                             <div class="m-timeline-2__item-text m--padding-top-5">
                                                 Placed a new order in
                                                 <a href="#" class="m-link m-link--brand m--font-bolder">
                                                     SIGNATURE MOBILE
                                                 </a>
                                                 marketplace.
                                             </div>
                                         </div>
                                         <div class="m-timeline-2__item m--margin-top-30">
														<span class="m-timeline-2__item-time">
															16:00
														</span>
                                             <div class="m-timeline-2__item-cricle">
                                                 <i class="fa fa-genderless m--font-brand"></i>
                                             </div>
                                             <div class="m-timeline-2__item-text m--padding-top-5">
                                                 Lorem ipsum dolor sit amit,consectetur eiusmdd tempor
                                                 <br>
                                                 incididunt ut labore et dolore magna elit enim at minim
                                                 <br>
                                                 veniam quis nostrud
                                             </div>
                                         </div>
                                         <div class="m-timeline-2__item m--margin-top-30">
														<span class="m-timeline-2__item-time">
															17:00
														</span>
                                             <div class="m-timeline-2__item-cricle">
                                                 <i class="fa fa-genderless m--font-danger"></i>
                                             </div>
                                             <div class="m-timeline-2__item-text m--padding-top-5">
                                                 Received a new feedback on
                                                 <a href="#" class="m-link m-link--brand m--font-bolder">
                                                     FinancePro App
                                                 </a>
                                                 product.
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                                 <!--End::Timeline 2 -->
                             </div></div><div id="mCSB_4_scrollbar_vertical" class="mCSB_scrollTools mCSB_4_scrollbar mCS-minimal-dark mCSB_scrollTools_vertical" style="display: block;"><div class="mCSB_draggerContainer"><div id="mCSB_4_dragger_vertical" class="mCSB_dragger" style="position: absolute; min-height: 50px; display: block; height: 221px; max-height: 360px; top: 0px;"><div class="mCSB_dragger_bar" style="line-height: 50px;"></div></div><div class="mCSB_draggerRail"></div></div></div></div>
                 </div>
             </div>
             <!--End::Portlet-->
         </div>
         <div class="col-xl-6 col-lg-12">
             <!--Begin::Portlet-->
             <div class="m-portlet m-portlet--full-height ">
                 <div class="m-portlet__head">
                     <div class="m-portlet__head-caption">
                         <div class="m-portlet__head-title">
                             <h3 class="m-portlet__head-text">
                                 Recent Notifications
                             </h3>
                         </div>
                     </div>
                     <div class="m-portlet__head-tools">
                         <ul class="nav nav-pills nav-pills--brand m-nav-pills--align-right m-nav-pills--btn-pill m-nav-pills--btn-sm" role="tablist">
                             <li class="nav-item m-tabs__item">
                                 <a class="nav-link m-tabs__link active show" data-toggle="tab" href="#m_widget2_tab1_content" role="tab" aria-selected="true">
                                     Today
                                 </a>
                             </li>
                             <li class="nav-item m-tabs__item">
                                 <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_widget2_tab2_content" role="tab" aria-selected="false">
                                     Month
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </div>
                 <div class="m-portlet__body">
                     <div class="tab-content">
                         <div class="tab-pane active" id="m_widget2_tab1_content">
                             <!--Begin::Timeline 3 -->
                             <div class="m-timeline-3">
                                 <div class="m-timeline-3__items">
                                     <div class="m-timeline-3__item m-timeline-3__item--info">
															<span class="m-timeline-3__item-time">
																09:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Lorem ipsum dolor sit amit,consectetur eiusmdd tempor
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By Bob
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--warning">
															<span class="m-timeline-3__item-time">
																10:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Lorem ipsum dolor sit amit
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By Sean
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--brand">
															<span class="m-timeline-3__item-time">
																11:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Lorem ipsum dolor sit amit eiusmdd tempor
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By James
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--success">
															<span class="m-timeline-3__item-time">
																12:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Lorem ipsum dolor
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By James
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--danger">
															<span class="m-timeline-3__item-time">
																14:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Lorem ipsum dolor sit amit,consectetur eiusmdd
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By Derrick
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--info">
															<span class="m-timeline-3__item-time">
																15:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Lorem ipsum dolor sit amit,consectetur
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By Iman
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--brand">
															<span class="m-timeline-3__item-time">
																17:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Lorem ipsum dolor sit consectetur eiusmdd tempor
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By Aziko
																	</a>
																</span>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             <!--End::Timeline 3 -->
                         </div>
                         <div class="tab-pane" id="m_widget2_tab2_content">
                             <!--Begin::Timeline 3 -->
                             <div class="m-timeline-3">
                                 <div class="m-timeline-3__items">
                                     <div class="m-timeline-3__item m-timeline-3__item--info">
															<span class="m-timeline-3__item-time m--font-focus">
																09:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Contrary to popular belief, Lorem Ipsum is not simply random text.
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By Bob
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--warning">
															<span class="m-timeline-3__item-time m--font-warning">
																10:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	There are many variations of passages of Lorem Ipsum available.
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By Sean
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--brand">
															<span class="m-timeline-3__item-time m--font-primary">
																11:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Contrary to popular belief, Lorem Ipsum is not simply random text.
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By James
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--success">
															<span class="m-timeline-3__item-time m--font-success">
																12:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	The standard chunk of Lorem Ipsum used since the 1500s is reproduced.
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By James
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--danger">
															<span class="m-timeline-3__item-time m--font-warning">
																14:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Latin words, combined with a handful of model sentence structures.
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By Derrick
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--info">
															<span class="m-timeline-3__item-time m--font-info">
																15:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Contrary to popular belief, Lorem Ipsum is not simply random text.
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By Iman
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--brand">
															<span class="m-timeline-3__item-time m--font-danger">
																17:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Lorem Ipsum is therefore always free from repetition, injected humour.
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By Aziko
																	</a>
																</span>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             <!--End::Timeline 3 -->
                         </div>
                     </div>
                 </div>
             </div>
             <!--End::Portlet-->
         </div>
     </div>
     </div>
@endsection
