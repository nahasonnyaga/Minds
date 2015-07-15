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
var angular2_1 = require('angular2/angular2');
var Capture = (function () {
    function Capture() {
        console.log("this is the capture");
    }
    Capture = __decorate([
        angular2_1.Component({
            selector: 'minds-capture'
        }),
        angular2_1.View({
            template: 'this is capture'
        }), 
        __metadata('design:paramtypes', [])
    ], Capture);
    return Capture;
})();
exports.Capture = Capture;
//# sourceMappingURL=capture.js.map