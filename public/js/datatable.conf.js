    function loadColumns(json,select = false){
        var columns = [];
        if(select){
            var columns = [{ data: null, render:function(){return "";}}];
        }
        for(i=0; i < json.length;i++){
            columns.push(json[i]);    
        }
        return columns;
    }    