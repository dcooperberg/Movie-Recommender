/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function stars(){
    $(".movie").hover(function(){
        $(this).addClass("active");
        var rating = 0;
        var rated = $(".active").hasClass("rated");
        var title = $(this).find(".title").text();
        $(".title").hover(function(){
            $(this).html("<div class='span12'><button class='skip btn btn-danger'>Skip</button></div>");
            $(".skip").click(function(){
                $(".active").hide();
                addMovie();
            })
        },
        function(){
            $(this).html(title);
        })
        $(".active .icon-star").hover(function(){
            var star = parseInt($(this).attr('star'));
            $(".active .icon-star").each(function(){
                if (parseInt($(this).attr("star"))<=star){
                    if ($(this).hasClass("icon-white")){
                        $(this).removeClass("icon-white");
                    }
                } else {
                    if ($(this).hasClass("icon-white")==false){
                        $(this).addClass("icon-white");
                    }
                }
            })
            //$(this).off( event );
        },function(){
            var rating = 5;
            if ($(".active").hasClass("rated")){
                for (var i=0; i<ratings.length; i++){
                    if($(".active").attr('mov_id') == movies[i]){
                        rating = ratings[i];
                    }
                }
            }
            $(".active .icon-star").each(function(){
                if (parseInt($(this).attr("star"))<=rating){
                    if ($(this).hasClass("icon-white")){
                        $(this).removeClass("icon-white");
                    }
                } else {
                    if ($(this).hasClass("icon-white")==false){
                        $(this).addClass("icon-white");
                    }
                }
            })
            //$(this).off( event );
        })
        $(".active .icon-star").click(function(){
            if ($(".active").hasClass("rated")){
                for (var i=0; i<ratings.length; i++){                     
                    if($(".active").attr('mov_id') == movies[i]){
                        ratings[i] = parseInt($(this).attr('star'));
                    }
                }
            } else {
                ratings.push(parseInt($(this).attr('star')));
                movies.push($(".active").attr('mov_id'));
                $(".active").addClass("rated");
                addMovie();
            }
            $(this).off( event );
        })

    },function(){
        $(this).removeClass("active");
    })
}
