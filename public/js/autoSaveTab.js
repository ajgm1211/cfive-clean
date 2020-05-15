
    $('.m-tabs__link').click(function(e){
        nameTab = $(this).attr('href');
        nameTab = nameTab.replace('#','');
    });

    $(".tabMap").each(function() {
        if($(this).hasClass('defaultActive')  ){
            nameTab = $(this).attr('href');  
            if(nameTabCtll == 0){
                $('a[href="'+nameTab+'"]').click();
            }
        }
        if(nameTabCtll != 0){
            $('a[href="#'+nameTabCtll+'"]').click();
        }
    });