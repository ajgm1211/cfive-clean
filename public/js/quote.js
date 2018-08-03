
$('.m-select2-general').select2({
  placeholder: "Select an option"
});

function schedules(id){
  var elemento = $("#detail"+id);
  var origin = $("#origin"+id);
  var destination = $("#destination"+id);
  var global = $("#global"+id);
  var inlands = $("#inlands"+id);
  var schedule = $("#schedules"+id);
  if(schedule.attr('hidden')){
    schedule.removeAttr('hidden');
    elemento.attr('hidden','true');
    origin.attr('hidden','true');
    destination.attr('hidden','true');
    global.attr('hidden','true');
    inlands.attr('hidden','true');
  }else{
    schedule.attr('hidden','true');

  }
}


function display(id){
  var elemento = $("#detail"+id);
  var origin = $("#origin"+id);
  var destination = $("#destination"+id);
  var global = $("#global"+id);
  var inlands = $("#inlands"+id);
  var schedule = $("#schedules"+id);

  if(elemento.attr('hidden')){

    $("#detail"+id).removeAttr('hidden');
    schedule.attr('hidden','true');

  }else{
    $("#detail"+id).attr('hidden','true');
  }


  if(origin.attr('hidden')){

    $("#origin"+id).removeAttr('hidden');

  }else{
    $("#origin"+id).attr('hidden','true');
  }

  if(destination.attr('hidden')){

    $("#destination"+id).removeAttr('hidden');

  }else{
    $("#destination"+id).attr('hidden','true');
  }
  if(global.attr('hidden')){

    $("#global"+id).removeAttr('hidden');

  }else{
    $("#global"+id).attr('hidden','true');
  }
  if(inlands.attr('hidden')){

    $("#inlands"+id).removeAttr('hidden');

  }else{
    $("#inlands"+id).attr('hidden','true');
  }

}




