function getCookie(param){
    var cookieName = param + "=";
    var cookieLength = document.cookie.length;
    var cookie = document.cookie;
    var result = null;
    if(cookieLength > 0){
        var offset = cookie.indexOf(cookieName);
        if(offset != -1){
            offset += cookieName.length;
            var end = cookie.indexOf(";", offset);
            if(end == -1){
                end = cookie.length;
            }
            result = decodeURI(cookie.substring(offset, end));
        }
    }
    return result;
}

function setCookie(name, value, expires, path, domain, secure){
    if (!expires){
        expires = new Date();
    }
    var cookie = name + "=" + encodeURI(value) + "; expires=" + expires.toGMTString();
    cookie += "; path=" + ((path) ? path : "/");
    cookie += ((domain) ? "; domain=" + domain : "");
    cookie += ((secure) ? "; secure=" + secure : "");
    document.cookie = cookie;
}