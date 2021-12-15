var SLIDER = (function(){
    var extend = function (){
        var obj, name, copy,
          target = arguments[0] || {},
          i = 1,
          length = arguments.length;

        for (; i < length; i++) {
            if ((obj = arguments[i]) !== null) {
                for (name in obj) {
                    opy = obj[name];
                    if (target === copy) {
                        continue;
                    } else if (copy !== undefined) {
                        target[name] = copy;
                    }
                }
            }
        }
      return target;
    };
    var toCamel = function(str){
        return str.replace(
            /([-_][a-z])/g,
            (group) => group.toUpperCase()
                            .replace('-', '')
                            .replace('_', '')
        );   
    }
    var merge = function (obj1,obj2){
        var obj3 = {};
        for (var attrname in obj1) { var new_attrname = toCamel(attrname); obj3[new_attrname] = obj1[attrname]; }
        for (var attrname in obj2) { var new_attrname = toCamel(attrname); obj3[new_attrname] = obj2[attrname]; }
        return obj3;
    }
    var sliders = $('.tiny-slider');
    if(sliders.length==0) return;
    for (var i = 0; i < sliders.length; i++) {
        var item = $(sliders[i]);
        var data = item.data();
        var options = merge({
            container: item[0],
            items: 1,
            slideBy: 'page',
            mouseDrag: true,
            autoplay: false,
            controls:false,
            autoplayButtonOutput:false,
            nav:false
          },data||{});
        tns(options);
    }
    
})();