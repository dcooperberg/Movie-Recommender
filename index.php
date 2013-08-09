<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8">
    <title>Film FREQ!</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
      .movie {
          background-color:#ddddff;
      }
      .unrated {
          border-style:dashed;
          border-width:3px;
          border-color:#ffff33;
          background-color:#aaaaff;
      }
      .text p{
          font-size:11px;
      }
    </style>
    
    <!--<link href="assets/css/bootstrap-responsive.css" rel="stylesheet">-->

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
  <link rel="shortcut icon" href="assets/img/Movies.png">
  </head>
  
  <script type='text/javascript' src='https://www.google.com/jsapi'></script>
  <script type='text/javascript' src="assets/js/jquery.js"></script>
  <script type='text/javascript' src='assets/js/gradient.js'></script>

  <script type="text/javascript">
    google.load('visualization', '1', {packages:['table']});
    google.load("visualization", "1", {packages:["corechart"]});
    
    <?php
        //Combine all PHP into one file
        include 'GetData.php';
        
        //Load Static Data
        $data = getData("MovieCoordinates.csv");
        $comp = getData("components2.csv");
    ?>
    
    function initialize(){
        <?php
        //Convert Table to JavaScript
        googleTable($data,"data");
        googleTable($comp,"comp");
        
        ?>
        //Load JavaScript
        $("#scatter_div").mousemove(function(e){
            mouseXpos = e.pageX 
            mouseYpos = e.pageY
        });
        var dots = 10;
        var page = 0;
        var holdout = 5;
        var ratings = new Array();
        var movies = new Array();
        var movieNo = 0;
        data.sort([{column: 0}]);
        var line1="<div class='well well-small movie unrated' style='min-height:0px'  mov_id='";
        var line2="'><div class='row-fluid'><div class='span6 title'><div class='text'><p style='line-height:15px'>";
        var line3="</p></div><button class='skip btn btn-danger' style='line-height:8px;display:none;vertical-align:top'><p style='font-size:10px'>Skip</p></button></div><div class='span6'><p class='pull-right' style='line-height:12px'><i class='icon-star' star='1'></i><i class='icon-star' star='2'></i><i class='icon-star' star='3'></i><i class='icon-star' star='4'></i><i class='icon-star' star='5'></i> </p></div></div></div>";
        var xax = 0;
        var yax = 1;
        var row = 0;
        var eigen = [1,1,1,1,1,1,1];
        var eigen = [.34736,.14912,.12048,.07985,.06251,.05791,.05419];
        //add Movie to Well;
        $(".go").click(function(){
            $(this).hide();
            $('.start').hide();
            addMovie();
        })
        //Load Filters
        var components = new Array();
        for (var i=3;i<data.getNumberOfColumns();i++){
            components.push(data.getColumnLabel(i));
        }
        $.each(components, function(val, text) {
            $('.xaxis').append( $('<option></option>').val(val).html(text) );
            $('.yaxis').append( $('<option></option>').val(val).html(text) );
        });
        $('.yaxis').find('option:selected', 'select').removeAttr('selected').next('option').attr('selected', 'selected');
        $("select").change(function(){
            var str = "";
            $("select option:selected").each(function () {
                str += $(this).text() + "|";
            });
            var pos = str.indexOf("|");
            var xt = str.substring(0,pos);
            str = str.substring(pos+1);
            pos = str.indexOf("|");
            var yt = str.substring(0,pos);
            xax = switchFunction(xt);
            yax = switchFunction(yt);
            drawVis(xax,yax,page,0);
            page = 0;
        });
        function switchFunction(sel){
            var dummy = 0;
            for (var i=0;i<components.length;i++){
                if (sel == components[i]){
                    dummy = i;
                    break;
                }
            }
            return dummy;
        }
        
        
        function addMovie(rateMe){
            if (ratings.length<holdout){
                $('#counter').html("<h1 style='font-size:250px'>"+(holdout-ratings.length).toString()+"</h1>");
            }

            //if movieNo input
            if (rateMe){
                $(".unrated").hide()
                for (var i=0;i<data.getNumberOfRows();i++){
                    if (rateMe == data.getValue(i,1).toString()){
                        row = i;
                        $("#rateNow").html(line1+data.getValue(i,1).toString()+line2+data.getValue(i,2)+line3);
                    }
                }
            } else {
                $("#rateNow").html(line1+data.getValue(movieNo,1).toString()+line2+data.getValue(movieNo,2)+line3);
                movieNo += 1;
            }
            $(".active").removeClass("active");
            
            var hh = $(".unrated").height();
            $("#container").css('max-height',410-hh);
            //alert(hh+':'+$('#container').height());
            
            //Star Animation
            $(".movie").hover(function(){
                $(this).addClass("active");
                var rating = 0;
                var title = $(this).find(".title").text();
                $(".unrated .title").hover(function(){
                    var hh = $(this).height();
                    $(this).height(hh);
                    $(this).find(".text").hide();
                    $(this).find(".skip").show();
                    $(".skip").click(function(){
                        $(".active").remove();
                        addMovie();
                    })
                },
                function(){
                    $(this).find(".text").show();
                    $(this).find(".skip").hide();
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
                    if ($(".active").hasClass("unrated")==false){
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
                    if ($(".active").hasClass("unrated")==false){
                        for (var i=0; i<ratings.length; i++){                     
                            if($(".active").attr('mov_id') == movies[i]){
                                ratings[i] = parseInt($(this).attr('star'));
                            }
                        }
                    } else {
                        ratings.push(parseInt($(this).attr('star')));
                        data.setValue(row,0,0);
                        data.sort([{column: 0}]);
                        row = 0;
                        movies.push($(".active").attr('mov_id'));
                        $(".active").removeClass("unrated");
                        var exist = $("#container").html();
                        var plus = $("#rateNow").html();
                        $("#container").html(plus+exist);
                        addMovie();
                    }
                    if (ratings.length == holdout){
                        $('.welcome').hide();
                    }
                    if (ratings.length >= holdout){
                        $(".filters").show();
                        drawVis(xax,yax,page,0);
                        page = 0;
                    }
                    $(this).off( event );
                })

            },function(){
                $(this).removeClass("active");
            }) 
        }
        function drawVis(x,y,page,ind){
            var stars = 0;
            for (var i=0;i<ratings.length;i++){
                stars += ratings[i];
            }
            var temp = data.clone();
            var sum = new Array();
            for (var i=3;i<temp.getNumberOfColumns();i++){
                sum.push(0);
            }
            //Get Avg Dist
            for (var i=0; i<ratings.length;i++){
                for (var j=0; j<temp.getNumberOfRows();j++){
                    if (parseInt(movies[i]) == temp.getValue(j,1)){
                        for (var k=3; k<temp.getNumberOfColumns();k++){
                            sum[k-3] += ((ratings[i]-3)/2)*temp.getValue(j,k);
                            //sum[k-3] += ratings[i]*temp.getValue(j,k);
                        }
                    }
                }
            }
            for (var i=0;i<sum.length;i++){
                sum[i] = sum[i]/ratings.length;
                //sum[i] = sum[i]/stars;
            }
            //var temp = data.clone();
            //sum = getPosition(sum,ratings,movies,temp);
            
            var len = temp.getNumberOfColumns();
            temp.addColumn('number','Distance');
            var span = 0;
            for (var j=0; j<temp.getNumberOfRows();j++){
                var dist = 0;
                for (var k=0;k<sum.length;k++){
                    dist += Math.pow(sum[k]-temp.getValue(j,k+3),2)*Math.pow(eigen[k],2);
                }
                dist = Math.sqrt(dist);
                if (dist > span){
                    span = dist;
                }
                temp.setValue(j,len,Math.round(dist*1000)/1000);
            }
            var table = new google.visualization.Table(document.getElementById('table_div'));
            var tableOptions = {
                showRowNumber: true,
                page: 'enable',
                sort:'disable',
                pageSize: 10,
                allowHtml: true
            }
            //Remove Rated Movies
            temp.addColumn('string','Title');
            for (var i=0;i<temp.getNumberOfRows();i++){
                var mov = temp.getValue(i,2);
                temp.setValue(i,2,"<div class='mov_title' style='height:35px;overflow-y:hidden;line-height:12px;padding:0px 0px 0px 0px'><button class='btn btn-mini btn-inverse' style='line-height:12px;vertical-align:bottom' mov_id='"+temp.getValue(i,1)+"'><p style='font-size:10px'>Rate Me!</p></button> <a target='_blank' href='http://www.amazon.com/s/ref=nb_sb_ss_i_0_8?url=search-alias%3Dinstant-video&field-keywords="+mov+"'><img class='img-circle' style='height:22px;top:0px' src='assets/img/play2.png'/></a><p class='tt' style='font-size:10px'>"+mov+"</p></div>");
                temp.setValue(i,temp.getNumberOfColumns()-1,mov);
                
            }
            //fix nulls

            for (var i=0;i<temp.getNumberOfRows();i++){
                var mov = temp.getValue(i,2);
                if (temp.getValue(i,temp.getNumberOfColumns()-1)==null){
                    temp.setValue(i,2,"<div class='mov_title'><button class='btn btn-mini btn-inverse' style='line-height:12px;vertical-align:bottom' mov_id='"+temp.getValue(i,1)+"'>Seen It!</button> <a target='_blank' href='http://www.amazon.com/s/ref=nb_sb_ss_i_0_8?url=search-alias%3Dinstant-video&field-keywords="+mov+"'><img class='img-circle' style='height:22px;top:0px' src='assets/img/play2.png'/></a><p class='tt' style='font-size:10px'>"+mov+"</p></div>");
                    temp.setValue(i,temp.getNumberOfColumns()-1,mov);
                }
                var rated = false;
                for (var k=0;k<movies.length;k++){
                    if (temp.getValue(i,1) == parseInt(movies[k])){
                        rated = true;
                        break;
                    }
                }
                if (rated == true){
                    //temp.removeRow(i);
                    temp.setValue(i,len,999);
                }
            }
            var temp2 = temp.clone();
            temp2.removeColumns(len+1,1);
            temp2.removeColumns(3,len-3);
            temp2.removeColumns(0,2);
            temp2.sort({column: 1});
            temp.sort({column: len});
            if (ind == 0){
                table.draw(temp2, tableOptions);
            }
            $('.btn-inverse').hide();
            $('.mov_title').find('a').hide();
            $('.mov_title').hover(function(){
                var hh = $(this).height();
                $(this).height(hh);
                $(this).find('.tt').hide();
                $(this).find('.btn-inverse').show();
                $(this).find('a').show();
            },function(){
                $(this).find('.btn-inverse').hide();
                $(this).find('a').hide();
                $(this).find('.tt').show();
            })
            $('.btn-inverse').click(function(){
                addMovie($(this).attr('mov_id'));
            })
            google.visualization.events.addListener(table, 'page', function(e){
                page = e["page"];
                $('.btn-inverse').hide();
                $('.mov_title').find('a').hide();
                $('.mov_title').hover(function(){
                    var hh = $(this).height();
                    $(this).height(hh);
                    $(this).find('.tt').hide();
                    $(this).find('.btn-inverse').show();
                    $(this).find('a').show();
                },function(){
                    $(this).find('.btn-inverse').hide();
                    $(this).find('a').hide();
                    $(this).find('.tt').show();
                })
                $('.btn-inverse').click(function(){
                    addMovie($(this).attr('mov_id'));
                })
                drawVis(xax,yax,page,1);
            });
            
            var output = new Array();
            output.push(['Movie',data.getColumnLabel(x+3),data.getColumnLabel(y+3),'ID','Size','Title']);
            if (x!=y){
                output.push(['',sum[x],sum[y],0,3,'You']);
            }else {
                output.push(['',sum[x],0,0,3,'You']);
            }
            var maxx = 0;
            for (var i=dots*(page+1)-1;i>=dots*page;i--){
                if (x!= y){
                    output.push(['',temp.getValue(i,x+3),temp.getValue(i,y+3),temp.getValue(i,len),1,temp.getValue(i,len+1)]);
                } else {
                    output.push(['',temp.getValue(i,x+3),0,temp.getValue(i,len),1,temp.getValue(i,len+1)]);
                }
                if (Math.abs(temp.getValue(i,x+3))>maxx){
                    maxx = Math.abs(temp.getValue(i,x+3));
                }
                if (Math.abs(temp.getValue(i,y+3))>maxx){
                    maxx = Math.abs(temp.getValue(i,y+3));
                }
            }
            //alert(maxx);
            var results = google.visualization.arrayToDataTable(output);
            for (var i=0;i<comp.getNumberOfRows();i++){
                if (results.getColumnLabel(1) == comp.getValue(i,0)){
                    $("#x1").text(comp.getValue(i,1));
                    $("#x2").text(comp.getValue(i,2));
                } else if (results.getColumnLabel(2) == comp.getValue(i,0)){
                    $("#y1").text(comp.getValue(i,1));
                    $("#y2").text(comp.getValue(i,2));
                }
            }
            var chart = new google.visualization.BubbleChart(document.getElementById('scatter_div'));
            console.log(span);
            var scatterOptions = {
                hAxis: {minValue: -maxx, maxValue: maxx, gridlines: {count: Math.round(maxx+.5)*2+1}},
                vAxis: {minValue: -maxx, maxValue: maxx, gridlines: {count: Math.round(maxx+.5)*2+1}},
                bubble: {textStyle:{fontSize:9}},
                theme: 'maximized',
                colorAxis: {minValue:0, maxValue: span/4,colors:['#9ed2ec','green','yellow','red']},
                sizeAxis: {minValue:1,maxSize:16},
                legend: {position:'top'},
                tooltip: {trigger:'none'}
            }
            google.visualization.events.addListener(chart, 'onmouseover', function(e) {
                var row = e['row'];
                //alert(results.getValue(row,5));
                $('#tooltip').html('<h4>'+results.getValue(row,5)+'</h4>');
                $('#tooltip').css('left',mouseXpos + 20);
                $('#tooltip').css('top',mouseYpos-10);
                $('#tooltip').show();
                for (var i=0;i<temp2.getNumberOfRows();i++){
                    var html = temp2.getValue(i,0);
                    var pos = html.indexOf("class='tt'");
                    var pos2 = html.indexOf("</p>",pos+1);
                    var mov = html.substring(pos+34,pos2);
                    if (results.getValue(row,5)==mov){
                        //highlight row in table
                        var newPage = Math.round((i-5)/10);
                        var ct = 0;
                        if (newPage == page){
                        $("tr").each(function(){
                            var rw = i%10+1;
                            if (ct == rw){
                                //assign classes to row
                                $(this).addClass("google-visualization-table-tr-over");
                            }
                            ct++;
                        })
                        }
                        break;
                    }
                }
            });
            google.visualization.events.addListener(chart, 'onmouseout', function(e) {
                $('#tooltip').hide();
                $('tr').removeClass("google-visualization-table-tr-over");
            });
            
            //Update Top Right Text
            if (sum[x] > 0){
                var xpref = 'RIGHT';
                var xnot = 'LEFT';
            } else {
                var xpref = 'LEFT'
                var xnot = 'RIGHT';
            }
            if (sum[y] > 0){
                var ypref = 'TOP';
                var ynot = 'BOTTOM';
            } else {
                var ypref = 'BOTTOM';
                var ynot = 'TOP';
            }
            $('#description').html('<strong>You prefer films that are more '+xpref+' than '+xnot+' and films that are more '+ypref+' than '+ynot+'. Below are the movies we recommend.</strong>');
            $('#scatter_div').css('height','420px');
            chart.draw(results, scatterOptions);
            var left = $("#scatter_div").position().left;
            $("#x2").css('left',left+10);
            $("#x1").css('left',left+445-$("#x1").width());
            $("#y1").css('left',left+240);
            $("#y2").css('left',left+240);
            $(window).resize(function(){
                left = $("#scatter_div").position().left;
                $("#x2").css('left',left+10);
                $("#x1").css('left',left+445-$("#x1").width());
                $("#y1").css('left',left+240);
                $("#y2").css('left',left+240);
            })
            $(".axTitle").show();
        }
    }
</script>
  <body onload="initialize()">
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand reload" href="#"><img class="img-circle" style="height:40px;position:absolute;top:0px" src="assets/img/Movies.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Film FREQ!</a>
        </div>
      </div>
    </div> 
    <div id="main-container" class="container">
        <!--<div class="page-header"><h2>Personalized Movie Recommendations</h2></div>-->
        <div class="row-fluid">
            <div class="span3 well well-small sidebar-nav" id="movie_div" style="background-color:#eeeeff;margin-top:20px">
                <div class="row-fluid">
                    <div class="span12 start" style='margin-bottom:10px'><strong>Start Rating Movies!</strong></div>
                </div>
                <button class="btn btn-success go">Go!</button>
                <div id="rateNow"></div>
                <div id="container" style="overflow-y:scroll"> 
                </div>
            </div>
            <div class="span9 welcome">
                <div><h1 style="font-size:50px">Welcome to <strong>Film Freq!</strong></h1> <p class="lead">
                        Using <strong><em>Analytics</em></strong>, we 
                            interpret your movie preferences on 7 different dimensions. 
                            Start rating movies to see where you fall on each dimension. 
                            Once you've rated 5 movies, you get personalized 
                            list of movie recommendations. Keep rating movies to 
                            make your recommendations more accurate!</p>
                </div>
                <div id="counter" style="text-align:center;margin-top:110px;margin-bottom:110px"></div>
            </div>
            <div class="span6">
                <div class="row-fluid filters" style="display:none">
                    <div class="span6">
                        <ul class="nav nav-list">
                            <li class="nav-header">x-axis</li>
                        </ul>
                        <select class='xaxis'>
                        </select>
                    </div>
                    <div class="span6">
                        <ul class="nav nav-list">
                            <li class="nav-header">y-axis</li>
                        </ul>
                        <select class='yaxis'>
                        </select>
                    </div>
                </div>
                <div class="row-fluid" id="scatter_div">
                </div>
            </div>
            <div class="span3">
                <!--<div class="span12 well well-small" id="description"></div>-->
                <div class="span12" id="table_div" style="margin-top: 20px"></div>
            </div>
            
        </div>
    </div>
      <div id="tooltip" style="display:none;position:absolute;max-width: 150px;text-shadow:-3px -3px 0 #fff,3px -3px 0 #fff,-3px 3px 0 #fff,3px 3px 0 #fff"></div> 
    <!-- /container -->
    <div id="x1" class="axTitle" style="display:none;position:absolute;top:315px"></div>
    <div id="x2" class="axTitle" style="display:none;position:absolute;top:315px"></div>
    <div id="y1" class="axTitle" style="display:none;position:absolute;top:135px"></div>
    <div id="y2" class="axTitle" style="display:none;position:absolute;top:505px"></div>
    <hr>
    <div id="footer">
      <div class="container">
        <p class="muted credit">By 
            <a target="_blank" href="http://www.analytics.northwestern.edu/student/student_profiles/David-Cooperberg-Master-of-Science-in-Analytics-Northwestern-Universityg-.html">David Cooperberg</a>, 
            <a target="_blank" href="http://www.analytics.northwestern.edu/student/student_profiles/Schleder-Austin-Northwestern-University-Master-of-Science-in-Analytics.html">Austin Schleder</a>, 
            <a target="_blank" href="http://www.analytics.northwestern.edu/student/student_profiles/Schweighart-Richard-Northwestern-University-Master-of-Science-in-Analytics.html">Richard Schweighart</a> and 
            <a target="_blank" href="http://www.analytics.northwestern.edu/student/student_profiles/Tebeje-Yared-Northwestern-University-Master-of-Science-in-Analytics.html">Yared Tebeje</a>.</p>
      </div>
    </div>
    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="assets/js/jquery.js"></script>

  </body>
</html>
