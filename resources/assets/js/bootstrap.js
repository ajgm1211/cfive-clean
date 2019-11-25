
window._ = require('lodash');
window.Popper = require('popper.js').default;

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
  window.$ = window.jQuery = require('jquery');

  require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
  window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
  console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });



import Echo from 'laravel-echo'
window.Pusher = require('pusher-js');
window.Echo = new Echo({
  broadcaster: 'pusher',
  key: "1a64b121010c1eb64623",
  cluster: "us2",
  encrypted: true
});


var notifications = [];
var notifications_old = [];

$(document).ready(function() {

  if(userId) {
    $.get('/users/notifications', function (data) {


      if(data.length > 0 ){
        addNotifications(data);
        $( ".newNotification" ).removeAttr('hidden');
        $( ".noNotification" ).attr('hidden','true');


      }else{
        $( ".newNotification" ).attr('hidden','true');
        $( ".noNotification" ).removeAttr('hidden');
      } 


    });
    // NOTIFICACIONES LEIDAS 
    $.get('/users/notifications_read', function (read) {
      if(read.length > 0 ){
        addNotifications_old(read);

      }


    });
  }

  // check if there's a logged in user
  if(userId) {
    window.Echo.private('App.User.'+userId)
      .notification((notification) => {
      $( ".newNotification" ).removeAttr('hidden');
      $( ".noNotification" ).attr('hidden','true');

      addNotifications([notification]);
    });
  }

});

function addNotifications(data) {

  notifications = _.concat(notifications, data);

  notifications.map(function (notification) {


    var htmlElements = notifications.map(function (notification) {
      var text = "<div class='m-list-timeline__item'> <span class='m-list-timeline__badge'></span><span class='m-list-timeline__text'>"+notification.data.name_user+" " + notification.data.message + " </span> <span class='m-list-timeline__time'> </span> </div>";
      return text;

    });

    $('.notifications').html(htmlElements);
  });
}


function addNotifications_old(data) {

  notifications_old = _.concat(notifications_old, data);

  notifications_old.map(function (notification_old) {


    var htmlElements = notifications_old.map(function (notification_old) {
      var text = "<div class='m-list-timeline__item'> <span class='m-list-timeline__badge'></span><span class='m-list-timeline__text'>"+notification_old.data.name_user+" " + notification_old.data.message + " </span> <span class='m-list-timeline__time'> </span> </div>";
      return text;

    });

    $('.notifications_old').html(htmlElements);
  });
}

$(document).on('click', '#notifications', function () {

  var geturl = getUrl();
  if(geturl != "")
    geturl = geturl + 'users/updatenot/';
  else
    geturl = '/users/updatenot/';

  var theElement = $(this);
  $.ajax({
    type: 'get',
    url: geturl,
    success: function(data) {  
      $( ".newNotification" ).attr('hidden','true');
      $( ".noNotification" ).removeAttr('hidden');
    }

  });


});

function getUrl(){
  var url ='';
  if (location.hostname == 'app.cargofive.com/')
    url = "https://app.cargofive.com/";
  else
    url = "";

  return  url;

}
