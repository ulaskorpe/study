var names = [2,4,5,6,7];
//console.log(typeof names);///object it says

//names.push(6);

///names [2]=666;
// for(i=4;i<44;i++){
//     names[i]=Math.round( Math.random(10,2000)*100);
// }

 names['a'] = 'xxx';

///var removed = names.splice(0,3);/// remove 3 items start from 0 

for(nm in names)  {
    console.log(names[nm]);
}

for(i = 0;i < names.length ;i++) {
    console.log(names[i]);
}