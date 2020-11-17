
function change(id){
    // entidades div
    var divC = document.getElementById("divCompany");
    var divSub = document.getElementById("divSubuser");
    // campos de texto de los diferentes tipos de usuarios
    var txtC = document.getElementById("txtCompany");
    var txtSub = document.getElementById("txtSubuser");





    if(id == '1'){

        divC.style.display = "none"; 
        divSub.style.display = "none"; 

        txtC.required = false;
        txtSub.required = false;
        txtC.value="";
        txtSub.value="";

    }
    if(id == '2'){

        divC.style.display = "block"; 
        divSub.style.display = "none"; 
        txtC.required = true;
        txtSub.value="";


    }
    if(id == '3'){


        divC.style.display = "none"; 
        divSub.style.display = "block";
        txtSub.required = true;
        txtC.value="";

    }




}
