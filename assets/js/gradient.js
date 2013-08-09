/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function getPosition(lastguess,ratings,movies,data){
    var a = lastguess;
    var alpha = 0.001;
    var eps = .1;
    var diff = 1;
    var kap = .01;
    var distance = 10;
    var count = 0;
    while (diff > alpha){
        var aplus = new Array();
        for (var i = 0;i<a.length;i++){
            aplus.push(a[i]+kap);
        }
        var dist = new Array();
        var s = new Array();
        var s2 = new Array();
        for (i=0;i<a.length;i++){
            s.push(0);
            s2.push(0);
        }
        for (i=0;i<ratings.length;i++){
            var sum = 0;
            for (var j=3;j<data.getNumberOfColumns();j++){
                //Find movie
                for (var k=0;k<data.getNumberOfRows();k++){
                    if (movies[i] == data.getValue(k,1)){
                        sum += Math.pow(a[j-3]-data.getValue(k,j),2);
                        s[j-3] += Math.abs(a[j-3]-data.getValue(k,j));
                        s2[j-3] += Math.abs(aplus[j-3]-data.getValue(k,j));
                        break;
                    }
                }
            }
            sum = Math.sqrt(sum);
            
            dist.push(Math.abs(5-ratings[i]-sum));
        }
        var aprime = new Array();
        for (var j=0;j<s.length;j++){
            aprime.push((s2[j]-s[j])/kap);
        }
        var last_dist = distance;
        distance = 0;
        for (var i=0; i<dist.length;i++){
            distance += dist[i];
        }
        if (distance > last_dist){
            break;
        }
        diff = Math.abs(distance-last_dist);
        for (var k=0;k<a.length;k++){
            a[k] = a[k] - eps*aprime[k];
        }
        count++;
        if (count > 1000){
            break;
        }
    }
    return a;
}

