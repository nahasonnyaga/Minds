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
var api_1 = require('src/services/api');
var material_1 = require('src/directives/material');
var infinite_scroll_1 = require('../../directives/infinite-scroll');
var cards_1 = require('src/controllers/cards/cards');
var Discovery = (function () {
    function Discovery(client, router, params) {
        this.client = client;
        this.router = router;
        this.params = params;
        this._filter = "featured";
        this._type = "all";
        this.entities = [];
        this.moreData = true;
        this.offset = "";
        this.inProgress = false;
        this._filter = params.params['filter'];
        if (params.params['type'])
            this._type = params.params['type'];
        this.load(true);
    }
    Discovery.prototype.load = function (refresh) {
        if (refresh === void 0) { refresh = false; }
        var self = this;
        if (this.inProgress)
            return false;
        if (refresh)
            this.offset = "";
        this.inProgress = true;
        this.client.get('api/v1/entities/' + this._filter + '/' + this._type, { limit: 12, offset: this.offset })
            .then(function (data) {
            console.log(1);
            if (!data.entities) {
                self.moreData = false;
                self.inProgress = false;
                return false;
            }
            if (refresh) {
                self.entities = data.entities;
            }
            else {
                data.entities.shift();
                for (var _i = 0, _a = data.entities; _i < _a.length; _i++) {
                    var entity = _a[_i];
                    self.entities.push(entity);
                }
            }
            self.offset = data['load-next'];
            self.inProgress = false;
        });
    };
    Discovery = __decorate([
        angular2_1.Component({
            selector: 'minds-discovery',
            viewBindings: [api_1.Client]
        }),
        angular2_1.View({
            templateUrl: 'templates/discovery/discovery.html',
            directives: [router_1.RouterLink, angular2_1.NgFor, angular2_1.NgIf, material_1.Material, infinite_scroll_1.InfiniteScroll, angular2_1.NgClass, cards_1.UserCard, cards_1.VideoCard]
        }),
        __param(1, angular2_1.Inject(router_1.Router)),
        __param(2, angular2_1.Inject(router_1.RouteParams)), 
        __metadata('design:paramtypes', [api_1.Client, router_1.Router, router_1.RouteParams])
    ], Discovery);
    return Discovery;
})();
exports.Discovery = Discovery;

//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNyYy9jb250cm9sbGVycy9kaXNjb3ZlcnkvZGlzY292ZXJ5LnRzIl0sIm5hbWVzIjpbIkRpc2NvdmVyeSIsIkRpc2NvdmVyeS5jb25zdHJ1Y3RvciIsIkRpc2NvdmVyeS5sb2FkIl0sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7Ozs7OztBQUFBLHlCQUE2RCxtQkFBbUIsQ0FBQyxDQUFBO0FBQ2pGLHVCQUFnRCxpQkFBaUIsQ0FBQyxDQUFBO0FBQ2xFLG9CQUF1QixrQkFBa0IsQ0FBQyxDQUFBO0FBQzFDLHlCQUF5Qix5QkFBeUIsQ0FBQyxDQUFBO0FBRW5ELGdDQUErQixrQ0FBa0MsQ0FBQyxDQUFBO0FBQ2xFLHNCQUFvQyw2QkFBNkIsQ0FBQyxDQUFBO0FBR2xFO0lBaUJFQSxtQkFBbUJBLE1BQWNBLEVBQ1JBLE1BQWNBLEVBQ1RBLE1BQW1CQTtRQUY5QkMsV0FBTUEsR0FBTkEsTUFBTUEsQ0FBUUE7UUFDUkEsV0FBTUEsR0FBTkEsTUFBTUEsQ0FBUUE7UUFDVEEsV0FBTUEsR0FBTkEsTUFBTUEsQ0FBYUE7UUFUakRBLFlBQU9BLEdBQVlBLFVBQVVBLENBQUNBO1FBQzlCQSxVQUFLQSxHQUFZQSxLQUFLQSxDQUFDQTtRQUN2QkEsYUFBUUEsR0FBbUJBLEVBQUVBLENBQUNBO1FBQzlCQSxhQUFRQSxHQUFhQSxJQUFJQSxDQUFDQTtRQUMxQkEsV0FBTUEsR0FBV0EsRUFBRUEsQ0FBQ0E7UUFDcEJBLGVBQVVBLEdBQWFBLEtBQUtBLENBQUNBO1FBTTNCQSxJQUFJQSxDQUFDQSxPQUFPQSxHQUFHQSxNQUFNQSxDQUFDQSxNQUFNQSxDQUFDQSxRQUFRQSxDQUFDQSxDQUFDQTtRQUN2Q0EsRUFBRUEsQ0FBQUEsQ0FBQ0EsTUFBTUEsQ0FBQ0EsTUFBTUEsQ0FBQ0EsTUFBTUEsQ0FBQ0EsQ0FBQ0E7WUFDdkJBLElBQUlBLENBQUNBLEtBQUtBLEdBQUdBLE1BQU1BLENBQUNBLE1BQU1BLENBQUNBLE1BQU1BLENBQUNBLENBQUNBO1FBQ3JDQSxJQUFJQSxDQUFDQSxJQUFJ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ZXJzL2Rpc2NvdmVyeS9kaXNjb3ZlcnkuanMiLCJzb3VyY2VzQ29udGVudCI6WyJpbXBvcnQgeyBDb21wb25lbnQsIFZpZXcsIE5nRm9yLCBOZ0lmLCBJbmplY3QsIE5nQ2xhc3N9IGZyb20gJ2FuZ3VsYXIyL2FuZ3VsYXIyJztcbmltcG9ydCB7IFJvdXRlciwgUm91dGVQYXJhbXMsIFJvdXRlckxpbmsgfSBmcm9tICdhbmd1bGFyMi9yb3V0ZXInO1xuaW1wb3J0IHsgQ2xpZW50IH0gZnJvbSAnc3JjL3NlcnZpY2VzL2FwaSc7XG5pbXBvcnQgeyBNYXRlcmlhbCB9IGZyb20gJ3NyYy9kaXJlY3RpdmVzL21hdGVyaWFsJztcbmltcG9ydCB7IFNlc3Npb25GYWN0b3J5IH0gZnJvbSAnLi4vLi4vc2VydmljZXMvc2Vzc2lvbic7XG5pbXBvcnQgeyBJbmZpbml0ZVNjcm9sbCB9IGZyb20gJy4uLy4uL2RpcmVjdGl2ZXMvaW5maW5pdGUtc2Nyb2xsJztcbmltcG9ydCB7IFVzZXJDYXJkLCBWaWRlb0NhcmQgfSBmcm9tICdzcmMvY29udHJvbGxlcnMvY2FyZHMvY2FyZHMnO1xuaW1wb3J0IHsgQWN0aXZpdHkgfSBmcm9tICdzcmMvY29udHJvbGxlcnMvbmV3c2ZlZWQvYWN0aXZpdHknO1xuXG5AQ29tcG9uZW50KHtcbiAgc2VsZWN0b3I6ICdtaW5kcy1kaXNjb3ZlcnknLFxuICB2aWV3QmluZGluZ3M6IFsgQ2xpZW50IF1cbn0pXG5AVmlldyh7XG4gIHRlbXBsYXRlVXJsOiAndGVtcGxhdGVzL2Rpc2NvdmVyeS9kaXNjb3ZlcnkuaHRtbCcsXG4gIGRpcmVjdGl2ZXM6IFsgUm91dGVyTGluaywgTmdGb3IsIE5nSWYsIE1hdGVyaWFsLCBJbmZpbml0ZVNjcm9sbCwgTmdDbGFzcywgVXNlckNhcmQsIFZpZGVvQ2FyZCBdXG59KVxuXG5leHBvcnQgY2xhc3MgRGlzY292ZXJ5IHtcbiAgX2ZpbHRlciA6IHN0cmluZyA9IFwiZmVhdHVyZWRcIjtcbiAgX3R5cGUgOiBzdHJpbmcgPSBcImFsbFwiO1xuICBlbnRpdGllcyA6IEFycmF5PE9iamVjdD4gPSBbXTtcbiAgbW9yZURhdGEgOiBib29sZWFuID0gdHJ1ZTtcbiAgb2Zmc2V0OiBzdHJpbmcgPSBcIlwiO1xuICBpblByb2dyZXNzIDogYm9vbGVhbiA9IGZhbHNlO1xuXG4gIGNvbnN0cnVjdG9yKHB1YmxpYyBjbGllbnQ6IENsaWVudCxcbiAgICBASW5qZWN0KFJvdXRlcikgcHVibGljIHJvdXRlcjogUm91dGVyLFxuICAgIEBJbmplY3QoUm91dGVQYXJhbXMpIHB1YmxpYyBwYXJhbXM6IFJvdXRlUGFyYW1zXG4gICAgKXtcbiAgICB0aGlzLl9maWx0ZXIgPSBwYXJhbXMucGFyYW1zWydmaWx0ZXInXTtcbiAgICBpZihwYXJhbXMucGFyYW1zWyd0eXBlJ10pXG4gICAgICB0aGlzLl90eXBlID0gcGFyYW1zLnBhcmFtc1sndHlwZSddO1xuICAgIHRoaXMubG9hZCh0cnVlKTtcbiAgfVxuXG4gIGxvYWQocmVmcmVzaCA6IGJvb2xlYW4gPSBmYWxzZSl7XG4gICAgdmFyIHNlbGYgPSB0aGlzO1xuXG4gICAgaWYodGhpcy5pblByb2dyZXNzKSByZXR1cm4gZmFsc2U7XG5cbiAgICBpZihyZWZyZXNoKVxuICAgICAgdGhpcy5vZmZzZXQgPSBcIlwiO1xuXG4gICAgdGhpcy5pblByb2dyZXNzID0gdHJ1ZTtcblxuICAgIHRoaXMuY2xpZW50LmdldCgnYXBpL3YxL2VudGl0aWVzLycrdGhpcy5fZmlsdGVyKycvJyt0aGlzLl90eXBlLCB7bGltaXQ6MTIsIG9mZnNldDp0aGlzLm9mZnNldH0pXG4gICAgICAudGhlbigoZGF0YSA6IGFueSkgPT4ge1xuICAgICAgICBjb25zb2xlLmxvZygxKTtcbiAgICAgICAgaWYoIWRhdGEuZW50aXRpZXMpe1xuICAgICAgICAgIHNlbGYubW9yZURhdGEgPSBmYWxzZTtcbiAgICAgICAgICBzZWxmLmluUHJvZ3Jlc3MgPSBmYWxzZTtcbiAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgIH1cblxuICAgICAgICBpZihyZWZyZXNoKXtcbiAgICAgICAgICBzZWxmLmVudGl0aWVzID0gZGF0YS5lbnRpdGllcztcbiAgICAgICAgfWVsc2V7XG4gICAgICAgICAgZGF0YS5lbnRpdGllcy5zaGlmdCgpO1xuICAgICAgICAgIGZvcihsZXQgZW50aXR5IG9mIGRhdGEuZW50aXRpZXMpXG4gICAgICAgICAgICBzZWxmLmVudGl0aWVzLnB1c2goZW50aXR5KTtcbiAgICAgICAgfVxuXG4gICAgICAgIHNlbGYub2Zmc2V0ID0gZGF0YVsnbG9hZC1uZXh0J107XG4gICAgICAgIHNlbGYuaW5Qcm9ncmVzcyA9IGZhbHNlO1xuXG4gICAgICB9KTtcbiAgfVxuXG59XG4iXSwic291cmNlUm9vdCI6Ii9zb3VyY2UvIn0=