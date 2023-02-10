var date  =  new Date;

//date.getTime();

var getTime = function(){
   // return date.getTime();
   document.getElementById('show').innerHTML = new Date().getTime();
},
 fillShow  = function(s){
    
},
x = 1,
 interval = setInterval(function(){
    document.getElementById('show').innerHTML = x;
    x++;
    if(x === 100){
        clearInterval(interval);
    }
 },100);
 
