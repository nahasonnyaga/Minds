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
var router_1 = require("angular2/router");
var api_1 = require('src/services/api');
var session_1 = require('src/services/session');
var material_1 = require('src/directives/material');
var remind_1 = require('./remind');
var Activity = (function () {
    function Activity(client) {
        this.client = client;
        this.session = session_1.SessionFactory.build();
    }
    Object.defineProperty(Activity.prototype, "object", {
        set: function (value) {
            this.activity = value;
            if (!this.activity['thumbs:up:user_guids'])
                this.activity['thumbs:up:user_guids'] = [];
            if (!this.activity['thumbs:down:user_guids'])
                this.activity['thumbs:down:user_guids'] = [];
        },
        enumerable: true,
        configurable: true
    });
    Activity.prototype.delete = function () {
        this.client.delete('api/v1/newsfeed/' + this.activity.guid);
        delete this.activity;
    };
    Activity.prototype.toDate = function (timestamp) {
        return new Date(timestamp * 1000);
    };
    Activity.prototype.thumbsUp = function () {
        this.client.put('api/v1/thumbs/' + this.activity.guid + '/up', {});
        if (!this.hasThumbedUp()) {
            this.activity['thumbs:up:user_guids'].push(this.session.getLoggedInUser().guid);
        }
        else {
            for (var key in this.activity['thumbs:up:user_guids']) {
                if (this.activity['thumbs:up:user_guids'][key] == this.session.getLoggedInUser().guid)
                    delete this.activity['thumbs:up:user_guids'][key];
            }
        }
    };
    Activity.prototype.thumbsDown = function () {
        this.client.put('api/v1/thumbs/' + this.activity.guid + '/down', {});
        if (!this.hasThumbedDown()) {
            this.activity['thumbs:down:user_guids'].push(this.session.getLoggedInUser().guid);
        }
        else {
            for (var key in this.activity['thumbs:down:user_guids']) {
                if (this.activity['thumbs:down:user_guids'][key] == this.session.getLoggedInUser().guid)
                    delete this.activity['thumbs:down:user_guids'][key];
            }
        }
    };
    Activity.prototype.remind = function () {
        var self = this;
        this.client.post('api/v1/newsfeed/remind/' + this.activity.guid, {})
            .then(function (data) {
        });
    };
    Activity.prototype.hasThumbedUp = function () {
        for (var _i = 0, _a = this.activity['thumbs:up:user_guids']; _i < _a.length; _i++) {
            var guid = _a[_i];
            if (guid == this.session.getLoggedInUser().guid)
                return true;
        }
        return false;
    };
    Activity.prototype.hasThumbedDown = function () {
        for (var _i = 0, _a = this.activity['thumbs:down:user_guids']; _i < _a.length; _i++) {
            var guid = _a[_i];
            if (guid == this.session.getLoggedInUser().guid)
                return true;
        }
        return false;
    };
    Activity.prototype.hasReminded = function () {
        return false;
    };
    Activity = __decorate([
        angular2_1.Component({
            selector: 'minds-activity',
            viewInjector: [api_1.Client],
            properties: ['object']
        }),
        angular2_1.View({
            templateUrl: 'templates/cards/activity.html',
            directives: [angular2_1.NgFor, angular2_1.NgIf, angular2_1.CSSClass, material_1.Material, remind_1.Remind, router_1.RouterLink]
        }), 
        __metadata('design:paramtypes', [api_1.Client])
    ], Activity);
    return Activity;
})();
exports.Activity = Activity;

//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNyYy9jb250cm9sbGVycy9uZXdzZmVlZC9hY3Rpdml0eS50cyJdLCJuYW1lcyI6WyJBY3Rpdml0eSIsIkFjdGl2aXR5LmNvbnN0cnVjdG9yIiwiQWN0aXZpdHkub2JqZWN0IiwiQWN0aXZpdHkuZGVsZXRlIiwiQWN0aXZpdHkudG9EYXRlIiwiQWN0aXZpdHkudGh1bWJzVXAiLCJBY3Rpdml0eS50aHVtYnNEb3duIiwiQWN0aXZpdHkucmVtaW5kIiwiQWN0aXZpdHkuaGFzVGh1bWJlZFVwIiwiQWN0aXZpdHkuaGFzVGh1bWJlZERvd24iLCJBY3Rpdml0eS5oYXNSZW1pbmRlZCJdLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7Ozs7QUFBQSx5QkFBa0YsbUJBQW1CLENBQUMsQ0FBQTtBQUN0Ryx1QkFBMkIsaUJBQWlCLENBQUMsQ0FBQTtBQUM3QyxvQkFBdUIsa0JBQWtCLENBQUMsQ0FBQTtBQUMxQyx3QkFBK0Isc0JBQXNCLENBQUMsQ0FBQTtBQUN0RCx5QkFBeUIseUJBQXlCLENBQUMsQ0FBQTtBQUNuRCx1QkFBdUIsVUFBVSxDQUFDLENBQUE7QUFFbEM7SUFjQ0Esa0JBQW1CQSxNQUFjQTtRQUFkQyxXQUFNQSxHQUFOQSxNQUFNQSxDQUFRQTtRQUZoQ0EsWUFBT0EsR0FBR0Esd0JBQWNBLENBQUNBLEtBQUtBLEVBQUVBLENBQUNBO0lBR2xDQSxDQUFDQTtJQUVBRCxzQkFBSUEsNEJBQU1BO2FBQVZBLFVBQVdBLEtBQVVBO1lBQ25CRSxJQUFJQSxDQUFDQSxRQUFRQSxHQUFHQSxLQUFLQSxDQUFDQTtZQUN0QkEsRUFBRUEsQ0FBQUEsQ0FBQ0EsQ0FBQ0EsSUFBSUEsQ0FBQ0EsUUFBUUEsQ0FBQ0Esc0JBQXNCQSxDQUFDQSxDQUFDQTtnQkFDeENBLElBQUlBLENBQUNBLFFBQVFBLENBQUNBLHNCQUFzQkEsQ0FBQ0EsR0FBR0EsRUFBRUEsQ0FBQ0E7WUFDN0NBLEVBQUVBLENBQUFBLENBQUNBLENBQUNBLElBQUlBLENBQUNBLFFBQVFBLENBQUNBLHdCQUF3QkEsQ0FBQ0EsQ0FBQ0E7Z0JBQzFDQSxJQUFJQSxDQUFDQSxRQUFRQSxDQUFDQSx3QkFBd0JBLENBQUNBLEdBQUdBLEVBQUVBLENBQUNBO1FBQ2pEQSxDQUFDQTs7O09BQUFGO0lBRURBLHlCQUFNQSxHQUFOQTtRQUNFRyxJQUFJQSxDQUFDQSxNQUFNQSxDQUFDQSxNQUFNQSxDQUFDQSxrQkFBa0JBLEdBQUNBLElBQUlBLENBQUNBLFFBQVFBLENBQUNBLElBQUlBLENBQUNBLENBQUNBO1FBQzFEQSxPQUFPQSxJQUFJQSxDQUFDQSxRQUFRQSxDQUFDQTtJQUN2QkEsQ0FBQ0E7SUFLRkgseUJBQU1BLEdBQU5BLFVBQU9BLFNBQVNBO1FBQ2ZJLE1BQU1BLENBQUNBLElBQUlBLElBQUlBLENBQUNBLFNBQVNBLEdBQUNBLElBQUlBLENBQUNBLENBQUNBO0lBQ2pDQSxDQUFDQTtJQUVBSiwyQkFBUUEsR0FBUkE7UUFDRUssSUFBSUEsQ0FBQ0EsTUFBTUEsQ0FBQ0EsR0FBR0EsQ0FBQ0EsZ0JBQWdCQSxHQUFHQSxJQUFJQSxDQUFDQSxRQUFRQSxDQUFDQSxJQUFJQSxHQUFHQSxLQUFLQSxFQUFFQSxFQUFFQSxDQUFDQSxDQUFDQTtRQUNuRUEsRUFBRUEsQ0FBQUEsQ0FBQ0EsQ0FBQ0EsSUFBSUEsQ0FBQ0EsWUFBWUEsRUFBRUEsQ0FBQ0EsQ0FBQUEsQ0FBQ0E7WUFDdkJBLElBQUlBLENBQUNBLFFBQVFBLENBQUNBLHNCQUFzQkEsQ0FBQ0EsQ0FBQ0EsSUFBSUEsQ0FBQ0EsSUFBSUEsQ0FBQ0EsT0FBT0EsQ0FBQ0EsZUFBZUEsRUFBRUEsQ0FBQ0EsSUFBSUEsQ0FBQ0EsQ0FBQ0E7UUFDbEZBLENBQUNBO1FBQUNBLElBQUlBLENBQUNBLENBQUNBO1lBQ05BLEdBQUdBLENBQUFBLENBQUNBLEdBQUdBLENBQUNBLEdBQUdBLElBQUlBLElBQUlBLENBQUNBLFFBQVFBLENBQUNBLHNCQUFzQkEsQ0FBQ0EsQ0FBQ0EsQ0FBQUEsQ0FBQ0E7Z0JBQ3BEQSxFQUFFQSxDQUFBQSxDQUFDQSxJQUFJQSxDQUFDQSxRQUFRQSxDQUFDQSxzQkFBc0JBLENBQUNBLENBQUNBLEdBQUdBLENBQUNBLElBQUlBLElBQUlBLENBQUNBLE9BQU9BLENBQUNBLGVBQWVBLEVBQUVBLENBQUNBLElBQUlBLENBQUNBO29CQUNuRkEsT0FBT0EsSUFBSUEsQ0FBQ0EsUUFBUUEsQ0FBQ0Esc0JBQXNCQSxDQUFDQSxDQUFDQSxHQUFHQSxDQUFDQSxDQUFDQTtZQUN0REEsQ0FBQ0E7UUFDSEEsQ0FBQ0E7SUFDSEEsQ0FBQ0E7SUFFREwsNkJBQVVBLEdBQVZBO1FBQ0VNLElBQUlBLENBQUNBLE1BQU1BLENBQUNBLEdBQUdBLENBQUNBLGdCQUFnQkEsR0FBR0EsSUFBSUEsQ0FBQ0EsUUFBUUEsQ0FBQ0EsSUFBSUEsR0FBR0EsT0FBT0EsRUFBRUEsRUFBRUEsQ0FBQ0EsQ0FBQ0E7UUFDckVBLEVBQUVBLENBQUFBLENBQUNBLENBQUNBLElBQUlBLENBQUNBLGNBQWNBLEVBQUVBLENBQUNBLENBQUFBLENBQUNBO1lBQ3pCQSxJQUFJQSxDQUFDQSxRQUFRQSxDQUFDQSx3QkFBd0JBLENBQUNBLENBQUNBLElBQUlBLENBQUNBLElBQUlBLENBQUNBLE9BQU9BLENBQUNBLGVBQWVBLEVBQUVBLENBQUNBLElBQUlBLENBQUNBLENBQUNBO1FBQ3BGQSxDQUFDQTtRQUFDQSxJQUFJQSxDQUFDQSxDQUFDQTtZQUNOQSxHQUFHQSxDQUFBQSxDQUFDQSxHQUFHQSxDQUFDQSxHQUFHQSxJQUFJQSxJQUFJQSxDQUFDQSxRQUFRQSxDQUFDQSx3QkFBd0JBLENBQUNBLENBQUNBLENBQUFBLENBQUNBO2dCQUN0REEsRUFBRUEsQ0FBQUEsQ0FBQ0EsSUFBSUEsQ0FBQ0EsUUFBUUEsQ0FBQ0Esd0JBQXdCQSxDQUFDQSxDQUFDQSxHQUFHQSxDQUFDQSxJQUFJQSxJQUFJQSxDQUFDQSxPQUFPQSxDQUFDQSxlQUFlQSxFQUFFQSxDQUFDQSxJQUFJQSxDQUFDQTtvQkFDckZBLE9BQU9BLElBQUlBLENBQUNBLFFBQVFBLENBQUNBLHdCQUF3QkEsQ0FBQ0EsQ0FBQ0EsR0FBR0EsQ0FBQ0EsQ0FBQ0E7WUFDeERBLENBQUNBO1FBQ0hBLENBQUNBO0lBQ0hBLENBQUNBO0lBRUROLHlCQUFNQSxHQUFOQTtRQUNFTyxJQUFJQSxJQUFJQSxHQUFHQSxJQUFJQSxDQUFDQTtRQUNoQkEsSUFBSUEsQ0FBQ0EsTUFBTUEsQ0FBQ0EsSUFBSUEsQ0FBQ0EseUJBQXlCQSxHQUFHQSxJQUFJQSxDQUFDQSxRQUFRQSxDQUFDQSxJQUFJQSxFQUFFQSxFQUFFQSxDQUFDQTthQUM3REEsSUFBSUEsQ0FBQ0EsVUFBQ0EsSUFBSUE7UUFFWEEsQ0FBQ0EsQ0FBQ0EsQ0FBQ0E7SUFDWEEsQ0FBQ0E7SUFLRFAsK0JBQVlBLEdBQVpBO1FBQ0VRLEdBQUdBLENBQUFBLENBQWFBLFVBQXFDQSxFQUFyQ0EsS0FBQUEsSUFBSUEsQ0FBQ0EsUUFBUUEsQ0FBQ0Esc0JBQXNCQSxDQUFDQSxFQUFqREEsY0FBUUEsRUFBUkEsSUFBaURBLENBQUNBO1lBQWxEQSxJQUFJQSxJQUFJQSxTQUFBQTtZQUNWQSxFQUFFQSxDQUFBQSxDQUFDQSxJQUFJQSxJQUFJQSxJQUFJQSxDQUFDQSxPQUFPQSxDQUFDQSxlQUFlQSxFQUFFQSxDQUFDQSxJQUFJQSxDQUFDQTtnQkFDN0NBLE1BQU1BLENBQUNBLElBQUlBLENBQUNBO1NBQ2ZBO1FBQ0RBLE1BQU1BLENBQUNBLEtBQUtBLENBQUNBO0lBQ2ZBLENBQUNBO0lBRURSLGlDQUFjQSxHQUFkQTtRQUNFUyxHQUFHQSxDQUFBQSxDQUFhQSxVQUF1Q0EsRUFBdkNBLEtBQUFBLElBQUlBLENBQUNBLFFBQVFBLENBQUNBLHdCQUF3QkEsQ0FBQ0EsRUFBbkRBLGNBQVFBLEVBQVJBLElBQW1EQSxDQUFDQTtZQUFwREEsSUFBSUEsSUFBSUEsU0FBQUE7WUFDVkEsRUFBRUEsQ0FBQUEsQ0FBQ0EsSUFBSUEsSUFBSUEsSUFBSUEsQ0FBQ0EsT0FBT0EsQ0FBQ0EsZUFBZUEsRUFBRUEsQ0FBQ0EsSUFBSUEsQ0FBQ0E7Z0JBQzdDQSxNQUFNQSxDQUFDQSxJQUFJQSxDQUFDQTtTQUNmQTtRQUNEQSxNQUFNQSxDQUFDQSxLQUFLQSxDQUFDQTtJQUNmQSxDQUFDQTtJQUVEVCw4QkFBV0EsR0FBWEE7UUFDRVUsTUFBTUEsQ0FBQ0EsS0FBS0EsQ0FBQ0E7SUFDZkEsQ0FBQ0E7SUExRkhWO1FBQUNBLG9CQUFTQSxDQUFDQTtZQUNUQSxRQUFRQSxFQUFFQSxnQkFBZ0JBO1lBQzFCQSxZQUFZQSxFQUFFQSxDQUFFQSxZQUFNQSxDQUFFQTtZQUN4QkEsVUFBVUEsRUFBRUEsQ0FBQ0EsUUFBUUEsQ0FBQ0E7U0FDdkJBLENBQUNBO1FBQ0RBLGVBQUlBLENBQUNBO1lBQ0pBLFdBQVdBLEVBQUVBLCtCQUErQkE7WUFDNUNBLFVBQVVBLEVBQUVBLENBQUVBLGdCQUFLQSxFQUFFQSxlQUFJQSxFQUFFQSxtQkFBUUEsRUFBRUEsbUJBQVFBLEVBQUVBLGVBQU1BLEVBQUVBLG1CQUFVQSxDQUFDQTtTQUNuRUEsQ0FBQ0E7O2lCQW1GREE7SUFBREEsZUFBQ0E7QUFBREEsQ0EzRkEsQUEyRkNBLElBQUE7QUFqRlksZ0JBQVEsV0FpRnBCLENBQUEiLCJmaWxlIjoic3JjL2NvbnRyb2xsZXJzL25ld3NmZWVkL2FjdGl2aXR5LmpzIiwic291cmNlc0NvbnRlbnQiOlsiaW1wb3J0IHsgQ29tcG9uZW50LCBWaWV3LCBOZ0ZvciwgTmdJZiwgQ1NTQ2xhc3MsIE9ic2VydmFibGUsIGZvcm1EaXJlY3RpdmVzfSBmcm9tICdhbmd1bGFyMi9hbmd1bGFyMic7XG5pbXBvcnQgeyBSb3V0ZXJMaW5rIH0gZnJvbSBcImFuZ3VsYXIyL3JvdXRlclwiO1xuaW1wb3J0IHsgQ2xpZW50IH0gZnJvbSAnc3JjL3NlcnZpY2VzL2FwaSc7XG5pbXBvcnQgeyBTZXNzaW9uRmFjdG9yeSB9IGZyb20gJ3NyYy9zZXJ2aWNlcy9zZXNzaW9uJztcbmltcG9ydCB7IE1hdGVyaWFsIH0gZnJvbSAnc3JjL2RpcmVjdGl2ZXMvbWF0ZXJpYWwnO1xuaW1wb3J0IHsgUmVtaW5kIH0gZnJvbSAnLi9yZW1pbmQnO1xuXG5AQ29tcG9uZW50KHtcbiAgc2VsZWN0b3I6ICdtaW5kcy1hY3Rpdml0eScsXG4gIHZpZXdJbmplY3RvcjogWyBDbGllbnQgXSxcbiAgcHJvcGVydGllczogWydvYmplY3QnXVxufSlcbkBWaWV3KHtcbiAgdGVtcGxhdGVVcmw6ICd0ZW1wbGF0ZXMvY2FyZHMvYWN0aXZpdHkuaHRtbCcsXG4gIGRpcmVjdGl2ZXM6IFsgTmdGb3IsIE5nSWYsIENTU0NsYXNzLCBNYXRlcmlhbCwgUmVtaW5kLCBSb3V0ZXJMaW5rXVxufSlcblxuZXhwb3J0IGNsYXNzIEFjdGl2aXR5IHtcbiAgYWN0aXZpdHkgOiBhbnk7XG4gIHNlc3Npb24gPSBTZXNzaW9uRmFjdG9yeS5idWlsZCgpO1xuXG5cdGNvbnN0cnVjdG9yKHB1YmxpYyBjbGllbnQ6IENsaWVudCl7XG5cdH1cblxuICBzZXQgb2JqZWN0KHZhbHVlOiBhbnkpIHtcbiAgICB0aGlzLmFjdGl2aXR5ID0gdmFsdWU7XG4gICAgaWYoIXRoaXMuYWN0aXZpdHlbJ3RodW1iczp1cDp1c2VyX2d1aWRzJ10pXG4gICAgICB0aGlzLmFjdGl2aXR5Wyd0aHVtYnM6dXA6dXNlcl9ndWlkcyddID0gW107XG4gICAgaWYoIXRoaXMuYWN0aXZpdHlbJ3RodW1iczpkb3duOnVzZXJfZ3VpZHMnXSlcbiAgICAgIHRoaXMuYWN0aXZpdHlbJ3RodW1iczpkb3duOnVzZXJfZ3VpZHMnXSA9IFtdO1xuICB9XG5cbiAgZGVsZXRlKCl7XG4gICAgdGhpcy5jbGllbnQuZGVsZXRlKCdhcGkvdjEvbmV3c2ZlZWQvJyt0aGlzLmFjdGl2aXR5Lmd1aWQpO1xuICAgIGRlbGV0ZSB0aGlzLmFjdGl2aXR5O1xuICB9XG5cblx0LyoqXG5cdCAqIEEgdGVtcG9yYXJ5IGhhY2ssIGJlY2F1c2UgcGlwZXMgZG9uJ3Qgc2VlbSB0byB3b3JrXG5cdCAqL1xuXHR0b0RhdGUodGltZXN0YW1wKXtcblx0XHRyZXR1cm4gbmV3IERhdGUodGltZXN0YW1wKjEwMDApO1xuXHR9XG5cbiAgdGh1bWJzVXAoKXtcbiAgICB0aGlzLmNsaWVudC5wdXQoJ2FwaS92MS90aHVtYnMvJyArIHRoaXMuYWN0aXZpdHkuZ3VpZCArICcvdXAnLCB7fSk7XG4gICAgaWYoIXRoaXMuaGFzVGh1bWJlZFVwKCkpe1xuICAgICAgdGhpcy5hY3Rpdml0eVsndGh1bWJzOnVwOnVzZXJfZ3VpZHMnXS5wdXNoKHRoaXMuc2Vzc2lvbi5nZXRMb2dnZWRJblVzZXIoKS5ndWlkKTtcbiAgICB9IGVsc2Uge1xuICAgICAgZm9yKGxldCBrZXkgaW4gdGhpcy5hY3Rpdml0eVsndGh1bWJzOnVwOnVzZXJfZ3VpZHMnXSl7XG4gICAgICAgIGlmKHRoaXMuYWN0aXZpdHlbJ3RodW1iczp1cDp1c2VyX2d1aWRzJ11ba2V5XSA9PSB0aGlzLnNlc3Npb24uZ2V0TG9nZ2VkSW5Vc2VyKCkuZ3VpZClcbiAgICAgICAgICBkZWxldGUgdGhpcy5hY3Rpdml0eVsndGh1bWJzOnVwOnVzZXJfZ3VpZHMnXVtrZXldO1xuICAgICAgfVxuICAgIH1cbiAgfVxuXG4gIHRodW1ic0Rvd24oKXtcbiAgICB0aGlzLmNsaWVudC5wdXQoJ2FwaS92MS90aHVtYnMvJyArIHRoaXMuYWN0aXZpdHkuZ3VpZCArICcvZG93bicsIHt9KTtcbiAgICBpZighdGhpcy5oYXNUaHVtYmVkRG93bigpKXtcbiAgICAgIHRoaXMuYWN0aXZpdHlbJ3RodW1iczpkb3duOnVzZXJfZ3VpZHMnXS5wdXNoKHRoaXMuc2Vzc2lvbi5nZXRMb2dnZWRJblVzZXIoKS5ndWlkKTtcbiAgICB9IGVsc2Uge1xuICAgICAgZm9yKGxldCBrZXkgaW4gdGhpcy5hY3Rpdml0eVsndGh1bWJzOmRvd246dXNlcl9ndWlkcyddKXtcbiAgICAgICAgaWYodGhpcy5hY3Rpdml0eVsndGh1bWJzOmRvd246dXNlcl9ndWlkcyddW2tleV0gPT0gdGhpcy5zZXNzaW9uLmdldExvZ2dlZEluVXNlcigpLmd1aWQpXG4gICAgICAgICAgZGVsZXRlIHRoaXMuYWN0aXZpdHlbJ3RodW1iczpkb3duOnVzZXJfZ3VpZHMnXVtrZXldO1xuICAgICAgfVxuICAgIH1cbiAgfVxuXG4gIHJlbWluZCgpe1xuICAgIGxldCBzZWxmID0gdGhpcztcbiAgICB0aGlzLmNsaWVudC5wb3N0KCdhcGkvdjEvbmV3c2ZlZWQvcmVtaW5kLycgKyB0aGlzLmFjdGl2aXR5Lmd1aWQsIHt9KVxuICAgICAgICAgIC50aGVuKChkYXRhKT0+IHtcblxuICAgICAgICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEhhcyB0aHVtYmVkIHVwXG4gICAqL1xuICBoYXNUaHVtYmVkVXAoKXtcbiAgICBmb3IodmFyIGd1aWQgb2YgdGhpcy5hY3Rpdml0eVsndGh1bWJzOnVwOnVzZXJfZ3VpZHMnXSl7XG4gICAgICBpZihndWlkID09IHRoaXMuc2Vzc2lvbi5nZXRMb2dnZWRJblVzZXIoKS5ndWlkKVxuICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICB9XG4gICAgcmV0dXJuIGZhbHNlO1xuICB9XG5cbiAgaGFzVGh1bWJlZERvd24oKXtcbiAgICBmb3IodmFyIGd1aWQgb2YgdGhpcy5hY3Rpdml0eVsndGh1bWJzOmRvd246dXNlcl9ndWlkcyddKXtcbiAgICAgIGlmKGd1aWQgPT0gdGhpcy5zZXNzaW9uLmdldExvZ2dlZEluVXNlcigpLmd1aWQpXG4gICAgICAgIHJldHVybiB0cnVlO1xuICAgIH1cbiAgICByZXR1cm4gZmFsc2U7XG4gIH1cblxuICBoYXNSZW1pbmRlZCgpe1xuICAgIHJldHVybiBmYWxzZTtcbiAgfVxufVxuIl0sInNvdXJjZVJvb3QiOiIvc291cmNlLyJ9