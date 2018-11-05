<template>
  <li class="m-nav__item m-topbar__notifications m-topbar__notifications--img m-dropdown m-dropdown--large m-dropdown--header-bg-fill m-dropdown--arrow m-dropdown--align-center 	m-dropdown--mobile-full-width" data-dropdown-toggle="click" data-dropdown-persistent="true">
    <a href="#" class="m-nav__link m-dropdown__toggle" id="m_topbar_notification_icon">
      <span class="m-nav__link-badge m-badge m-badge--dot m-badge--dot-small m-badge--danger"></span>
      <span class="m-nav__link-icon">
        <i class="flaticon-music-2"></i>
      </span>
    </a>
    <div class="m-dropdown__wrapper">
      <span class="m-dropdown__arrow m-dropdown__arrow--center"></span>
      <div class="m-dropdown__inner">
        <div class="m-dropdown__header m--align-center" style="background: url(../../assets/app/media/img/misc/notification_bg.jpg); background-size: cover;">
          <span class="m-dropdown__header-title">
            1 New
          </span>
          <span class="m-dropdown__header-subtitle">
            User Notifications
          </span>
        </div>
        <div class="m-dropdown__body">
          <div class="m-dropdown__content">
            <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--brand" role="tablist">
              <li class="nav-item m-tabs__item">
                <a class="nav-link m-tabs__link active" data-toggle="tab" href="#topbar_notifications_notifications" role="tab">
                  Alerts
                </a>
              </li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="topbar_notifications_notifications" role="tabpanel">
                <div class="m-scrollable" data-scrollable="true" data-max-height="250" data-mobile-max-height="200">
                  <div class="m-list-timeline m-list-timeline--skin-light">
                    <div class="m-list-timeline__items notifications">
                      <div class="m-list-timeline__item">
                        <span class="m-list-timeline__badge"></span>
                        <span class="m-list-timeline__text">
                          System error -
                          <a href="#" class="m-link">
                            Check
                          </a>
                        </span>
                        <span class="m-list-timeline__time">
                          2 hrs
                        </span>
                      </div>
                      <div class="m-list-timeline__item m-list-timeline__item--read">
                        <span class="m-list-timeline__badge"></span>
                        <span href="" class="m-list-timeline__text">
                          New order received
                          <span class="m-badge m-badge--danger m-badge--wide">
                            urgent
                          </span>
                        </span>
                        <span class="m-list-timeline__time">
                          7 hrs
                        </span>
                      </div>

                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </li>
</template>

<script>
  export default {
    mounted() {
      console.log('Component mounted.')
    }
  }
</script>
