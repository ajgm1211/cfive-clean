
  var APP_ID = "s9q3w42n";
  var current_user_email = document.currentScript.getAttribute('email'); 
  var current_user_name = document.currentScript.getAttribute('name'); 
  var current_user_id = document.currentScript.getAttribute('id'); 
  var current_company_id = document.currentScript.getAttribute('company'); 
  var current_company_name = document.currentScript.getAttribute('companyName'); 

  window.intercomSettings = {
    app_id: APP_ID,
    name: current_user_name, // Full name
    email: current_user_email, // Email address
    user_id: current_user_id, // current_user_id
    company_id:current_company_id,
    company: {
       id: current_company_id ,
       name: current_company_name,
    },
};

(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',w.intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/s9q3w42n' ;var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);};if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();

