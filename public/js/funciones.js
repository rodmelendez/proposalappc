uniqueNumber.previous = 0;

function uniqueNumber() {
    let date = Date.now();

    if (date <= uniqueNumber.previous) {
        date = ++uniqueNumber.previous;
    } else {
        uniqueNumber.previous = date;
    }

    return date;
}


function variarColor(variacion, color, c1, l) {
    let r,g,b,P,f,t,h,i=parseInt,m=Math.round,a=typeof(c1)==="string";
    if(typeof(variacion)!=="number"||variacion<-1||variacion>1||typeof(color)!=="string"||(color[0]!=='r'&&color[0]!=='#')||(c1&&!a))return null;
    if(!this.pSBCr)this.pSBCr=(d)=>{
        let n=d.length,x={};
        if(n>9){
            [r,g,b,a]=d=d.split(','),n=d.length;
            if(n<3||n>4)return null;
            x.r=i(r[3]==='a'?r.slice(5):r.slice(4)),x.g=i(g),x.b=i(b),x.a=a?parseFloat(a):-1
        }else{
            if(n==8||n==6||n<4)return null;
            if(n<6)d='#'+d[1]+d[1]+d[2]+d[2]+d[3]+d[3]+(n>4?d[4]+d[4]:'');
            d=i(d.slice(1),16);
            if(n==9||n==5)x.r=d>>24&255,x.g=d>>16&255,x.b=d>>8&255,x.a=m((d&255)/0.255)/1000;
            else x.r=d>>16,x.g=d>>8&255,x.b=d&255,x.a=-1
        }return x};
    h=color.length>9,h=a?c1.length>9?true:c1==='c'?!h:false:h,f=pSBCr(color),P=variacion<0,t=c1&&c1!=='c'?pSBCr(c1):P?{r:0,g:0,b:0,a:-1}:{r:255,g:255,b:255,a:-1},variacion=P?variacion*-1:variacion,P=1-variacion;
    if(!f||!t)return null;
    if(l)r=m(P*f.r+variacion*t.r),g=m(P*f.g+variacion*t.g),b=m(P*f.b+variacion*t.b);
    else r=m((P*f.r**2+variacion*t.r**2)**0.5),g=m((P*f.g**2+variacion*t.g**2)**0.5),b=m((P*f.b**2+variacion*t.b**2)**0.5);
    a=f.a,t=t.a,f=a>=0||t>=0,a=f?a<0?t:t<0?a:a*P+t*variacion:0;
    if(h)return"rgb"+(f?"a(":"(")+r+","+g+","+b+(f?","+m(a*1000)/1000:"")+")";
    else return"#"+(4294967296+r*16777216+g*65536+b*256+(f?m(a*255):0)).toString(16).slice(1,f?undefined:-2)
}


function copiarAlPortapapeles(el) {
    let selection = window.getSelection();
    //emailLink = document.querySelector('.js-emaillink');

    if (false/*useAsyncApi()*/) {
        //navigator.clipboard.writeText(/*emailLink.textContent*/);
    } else {
        let range = document.createRange();
        selection.removeAllRanges();
        range.selectNode(el);
        selection.addRange(range);

        try {
            /*var successful = */document.execCommand('copy');
            //var msg = successful ? 'successful' : 'unsuccessful';
            //log('Copy email command was ' + msg);
        } catch (err) {
            //log('execCommand Error', err);
        }

        selection.removeAllRanges();
    }
}


function formatoFechaApp(fecha, formato_origen, formato_destino) {
    if (typeof formato_origen === 'undefined') formato_origen = 'YYYY-MM-DD HH:mm:ss';
    if (typeof formato_destino === 'undefined') formato_destino = 'DD/MM/YYYY';

    if (formato_origen === 'fecha') formato_origen = 'YYYY-MM-DD';
    if (formato_origen === 'fecha_hora') formato_origen = 'YYYY-MM-DD HH:mm:ss';

    if (formato_destino === 'fecha') formato_destino = 'DD/MM/YYYY';
    if (formato_destino === 'fecha_hora') formato_destino = 'DD/MM/YYYY h:mm a';

    const f = moment(fecha, formato_origen);

    if (f.isValid()) {
        return f.format(formato_destino);
    }

    return '';
}


function sinAcentos(str) {
    let r = str.toLowerCase();
    r = r.replace(new RegExp("\\s", 'g'),"");
    r = r.replace(new RegExp("[àáâãäå]", 'g'),"a");
    r = r.replace(new RegExp("æ", 'g'),"ae");
    r = r.replace(new RegExp("ç", 'g'),"c");
    r = r.replace(new RegExp("[èéêë]", 'g'),"e");
    r = r.replace(new RegExp("[ìíîï]", 'g'),"i");
    r = r.replace(new RegExp("ñ", 'g'),"n");
    r = r.replace(new RegExp("[òóôõö]", 'g'),"o");
    r = r.replace(new RegExp("œ", 'g'),"oe");
    r = r.replace(new RegExp("[ùúûü]", 'g'),"u");
    r = r.replace(new RegExp("[ýÿ]", 'g'),"y");
    r = r.replace(new RegExp("\\W", 'g'),"");
    return r;
}


