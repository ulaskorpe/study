var app = {};

app.countdown = function(settings){
   // console.log(settings);
   var interval , counter = 0 
   ///start-end 
   , startAt = 0 , endAt = 0;

   if(settings === undefined){
    console.log("no setttings");
   }else{
        if(settings.startAt === undefined || settings.endAt === undefined){
            document.getElementById('show').innerHTML = "start-end num req";
        }else{
            startAt = parseInt(settings.startAt,10);
            endAt = parseInt(settings.endAt,10);
            if(!isNaN(startAt) && !isNaN(endAt)){
                counter = startAt;
                interval = setInterval(function(){
                    if(counter< endAt){
                        alert('finished!');
                          clearInterval(interval);
                    }else{
                    document.getElementById('show').innerHTML= counter;
                    }
                    counter--;
                },100)
            }
        }
   }
}