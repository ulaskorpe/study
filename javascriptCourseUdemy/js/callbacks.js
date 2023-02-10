var app2 = {};

app2.add = function(numbers,callback){
var result = 0;
if(numbers !== undefined && numbers.length){
   // document.getElementById('show').innerHTML= "result";
        for(num in numbers){
            result+=numbers[num];
        }

        if(callback!==undefined){
            callback(result);
        }

        
}
}