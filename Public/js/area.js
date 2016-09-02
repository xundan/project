function Area(selector){ //构造体
    if(!Area.ALL_AREAS){
        throw new Error('areas not init！');
    }

    this.selector = selector;
    $(this.selector).html('<select name="province" id="province"/> <select name="city" id="city"/> <select name="county" id="county"/>');
    var $province = this._getElement(Area.type.PROVINCE);
    var $city = this._getElement(Area.type.CITY);
    var $county = this._getElement(Area.type.COUNTY);

    var self = this;
    $province.change(function(){
        var province = this.value;
        $city.html(self._getOptions(province));
        var city = $city.val();
        $county.html(self._getOptions(city));
    });
    $city.change(function(){
        var city = this.value;
        $county.html(self._getOptions(city));
    });
    $province.html(self._getOptions());
    this.select();
}

$.extend(Area, { //静态方法
    ALL_AREAS : null,
    type : {
        PROVINCE : 0,
        CITY : 1,
        COUNTY : 2
    },
    init : function(url){
        if(Area.ALL_AREAS) return;
        Area.ALL_AREAS = $.parseJSON($.ajax({
            url : url,
            async : false
        }).responseText);
    },
    getAddressByCode : function(code){
        var codePath = Area._toCodePath(code);
        var address = [];
        var areas = Area.ALL_AREAS;
        for(var i=0; i<codePath.length; i++){
            var area = areas[codePath[i]];
            if(!area) break;
            address.push(Area._getAreaName(area));
            areas = area[Area._getAreaName(area)];
        }
        return address;
    },
    _toCodePath : function(code){
        if(code == null) return [];

        var provinceCode = parseInt(code / 10000) * 10000;
        var cityCode = parseInt(code / 100) * 100;

        if(provinceCode == code) return [code];
        if(cityCode == code) return [provinceCode, code];
        return [provinceCode, cityCode, code];
    },
    _getAreaName : function(area){
        if(typeof(area) == 'string') return area;
        for(var o in area){
            return o;
        }
    }
});

Area.prototype = {
    _getElement : function(type){
        return $(this.selector).find('select').eq(type);
    },

    select : function(code){
        var $province = this._getElement(Area.type.PROVINCE);
        var $city = this._getElement(Area.type.CITY);
        var $county = this._getElement(Area.type.COUNTY);

        var codePath = Area._toCodePath(code);
        var province = codePath.length < 1 ? '' : codePath[0];
        $province.val(province);
        $city.html(this._getOptions(province));

        var city = codePath.length < 2 ? '' : codePath[1];
        $city.val(city);
        $county.html(this._getOptions(city));

        var county = codePath.length < 3 ? '' : codePath[2];
        $county.val(county);
    },

    getAddress : function(){
        var $province = this._getElement(Area.type.PROVINCE);
        var $city = this._getElement(Area.type.CITY);
        var $county = this._getElement(Area.type.COUNTY);

        if($county.val()) return $county.val();
        if($city.val()) return $city.val();
        if($province.val()) return $province.val();
        return null;
    },

    _getAreas : function(code, superAreas){
        var area = superAreas[code];
        if(!area) return {};
        return area[Area._getAreaName(area)] || {};
    },

    _getAreasByCode : function(code){
        var areas = Area.ALL_AREAS;
        var codePath = Area._toCodePath(code);

        for(var i=0; i<codePath.length; i++){
            areas = this._getAreas(codePath[i], areas);
        }
        return areas;
    },

    _getOrderAreas : function(code){
        var areas = this._getAreasByCode(code);
        var codes = [];
        for(var i in areas){
            codes.push(i);
        }
        return {
            codes : codes.sort(),
            areas : areas
        };
    },

    _getOption : function(code, area){
        return '<option value="' + code + '">' + Area._getAreaName(area) + '</option>';
    },

    _getOptions : function(code){
        var orderAreas = this._getOrderAreas(code);
        var codes = orderAreas.codes;
        var areas = orderAreas.areas;
        var options = '<option value="">请选择</option>';

        for(var i=0; i<codes.length; i++){
            options += this._getOption(codes[i], areas[codes[i]]);
        }
        return options;
    }
};
