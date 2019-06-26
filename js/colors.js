    let hmin=0, hmax=1, s=1, l=.8;

randomGradient(document.body, 'background-image', 6  , hmax, hmin, s, l)


function randomColor(el, property, h_min, h_max, s, l) {
    let h = randint(h_min, h_max);
    el.style[property] = hsl(h,s,l);
}

function randomGradient(el, property, n, h_min, h_max, s, min_l) {
    let angle = randint(0,360) + 'deg'
    let gradient_params = [angle]
   
    let last_percent = 0;
    let scale = 1/n*100;
    for (var i = 0; i < n; i++) {
        let percent = Math.floor(i*scale + randint(-scale, scale));
        let h = rand(h_min, h_max);
        l = rand(min_l, 1);

       gradient_params.push(hsl(h,s,l)+ ' ' + last_percent + '%')
        gradient_params.push(hsl(h,s,l)+ ' ' + percent + '%')
        last_percent = percent

    }
    console.log(gradient('linear', gradient_params));
    el.style[property] = gradient('linear', gradient_params);
}


function randint(min, max){
    return Math.floor(Math.random()*(max-min) + min);
}

function rand(min, max){
    return (Math.random()*(max-min) + min);
}

function gradient(type, params){
    // type: radial/linear
    return type + "-gradient( "+params.join(', ') +")"
}

function hsl(h,s,l) {
    return 'hsl('+Math.floor(h*360)+','+Math.floor(s*100)+'%,'+Math.floor(l*100)+'%)';
}