var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") return Reflect.decorate(decorators, target, key, desc);
    switch (arguments.length) {
        case 2: return decorators.reduceRight(function(o, d) { return (d && d(o)) || o; }, target);
        case 3: return decorators.reduceRight(function(o, d) { return (d && d(target, key)), void 0; }, void 0);
        case 4: return decorators.reduceRight(function(o, d) { return (d && d(target, key, o)) || o; }, desc);
    }
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};
var __param = (this && this.__param) || function (paramIndex, decorator) {
    return function (target, key) { decorator(target, key, paramIndex); }
};
var angular2_1 = require('angular2/angular2');
var router_1 = require('angular2/router');
var Navigation = (function () {
    function Navigation(router, location) {
        this.router = router;
        this.location = location;
    }
    Navigation.prototype.getItems = function () {
        var items = window.Minds.navigation;
        if (!items)
            return [];
        var path = this.location.path();
        for (var _i = 0; _i < items.length; _i++) {
            var item = items[_i];
            if (path == item.path || (path && path.indexOf(item.path) > -1))
                item.active = true;
            else
                item.active = false;
            if (item.submenus) {
                for (var _a = 0, _b = item.submenus; _a < _b.length; _a++) {
                    var subitem = _b[_a];
                    var sub_path = subitem.path;
                    for (var p in subitem.params) {
                        if (subitem.params[p])
                            sub_path += '/' + subitem.params[p];
                    }
                    if (path && path.indexOf(sub_path) > -1)
                        subitem.active = true;
                    else
                        subitem.active = false;
                }
            }
        }
        return items;
    };
    Navigation = __decorate([
        __param(0, angular2_1.Inject(router_1.Router)),
        __param(1, angular2_1.Inject(router_1.Location)), 
        __metadata('design:paramtypes', [Router, Location])
    ], Navigation);
    return Navigation;
})();
exports.Navigation = Navigation;
//# sourceMappingURL=navigation.js.map