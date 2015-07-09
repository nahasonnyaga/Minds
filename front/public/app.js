if (typeof __decorate !== "function") __decorate = function (decorators, target, key, desc) {
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") return Reflect.decorate(decorators, target, key, desc);
    switch (arguments.length) {
        case 2: return decorators.reduceRight(function(o, d) { return (d && d(o)) || o; }, target);
        case 3: return decorators.reduceRight(function(o, d) { return (d && d(target, key)), void 0; }, void 0);
        case 4: return decorators.reduceRight(function(o, d) { return (d && d(target, key, o)) || o; }, desc);
    }
};
if (typeof __metadata !== "function") __metadata = function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};
/// <reference path="../typings/tsd.d.ts" />
var angular2_1 = require('angular2/angular2');
var router_1 = require('angular2/router');
var topbar_1 = require('./src/components/topbar');
var navigation_1 = require('./src/components/navigation');
var login_1 = require('./src/controllers/login');
var logout_1 = require('./src/controllers/logout');
var newsfeed_1 = require('./src/controllers/newsfeed/newsfeed');
var capture_1 = require('./src/controllers/capture/capture');
var Minds = (function () {
    function Minds() {
        this.name = 'Minds';
    }
    Minds = __decorate([
        angular2_1.Component({
            selector: 'minds-app',
        }),
        router_1.RouteConfig([
            { path: '/login', component: login_1.Login, as: 'login' },
            { path: '/logout', component: logout_1.Logout, as: 'logout' },
            { path: '/newsfeed', component: newsfeed_1.Newsfeed, as: 'newsfeed' },
            { path: '/capture', component: capture_1.Capture, as: 'capture' },
            { path: '/:username', redirectTo: '/login' }
        ]),
        angular2_1.View({
            templateUrl: './templates/index.html',
            directives: [topbar_1.Topbar, navigation_1.Navigation, router_1.RouterOutlet, router_1.RouterLink]
        }), 
        __metadata('design:paramtypes', [])
    ], Minds);
    return Minds;
})();
angular2_1.bootstrap(Minds, [router_1.routerInjectables, angular2_1.httpInjectables]);

//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFwcC50cyJdLCJuYW1lcyI6WyJNaW5kcyIsIk1pbmRzLmNvbnN0cnVjdG9yIl0sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7OztBQUFBLDRDQUE0QztBQUM1Qyx5QkFBMEQsbUJBQW1CLENBQUMsQ0FBQTtBQUM5RSx1QkFBdUUsaUJBQWlCLENBQUMsQ0FBQTtBQUV6Rix1QkFBcUIseUJBQXlCLENBQUMsQ0FBQTtBQUMvQywyQkFBeUIsNkJBQTZCLENBQUMsQ0FBQTtBQUV2RCxzQkFBb0IseUJBQXlCLENBQUMsQ0FBQTtBQUM5Qyx1QkFBcUIsMEJBQTBCLENBQUMsQ0FBQTtBQUNoRCx5QkFBdUIscUNBQXFDLENBQUMsQ0FBQTtBQUM3RCx3QkFBc0IsbUNBQW1DLENBQUMsQ0FBQTtBQUUxRDtJQWtCRUE7UUFDRUMsSUFBSUEsQ0FBQ0EsSUFBSUEsR0FBR0EsT0FBT0EsQ0FBQ0E7SUFDdEJBLENBQUNBO0lBcEJIRDtRQUFDQSxvQkFBU0EsQ0FBQ0E7WUFDVEEsUUFBUUEsRUFBRUEsV0FBV0E7U0FDdEJBLENBQUNBO1FBQ0RBLG9CQUFXQSxDQUFDQTtZQUNYQSxFQUFFQSxJQUFJQSxFQUFFQSxRQUFRQSxFQUFFQSxTQUFTQSxFQUFFQSxhQUFLQSxFQUFFQSxFQUFFQSxFQUFFQSxPQUFPQSxFQUFFQTtZQUNqREEsRUFBRUEsSUFBSUEsRUFBRUEsU0FBU0EsRUFBRUEsU0FBU0EsRUFBRUEsZUFBTUEsRUFBRUEsRUFBRUEsRUFBRUEsUUFBUUEsRUFBRUE7WUFDcERBLEVBQUVBLElBQUlBLEVBQUVBLFdBQVdBLEVBQUVBLFNBQVNBLEVBQUVBLG1CQUFRQSxFQUFFQSxFQUFFQSxFQUFFQSxVQUFVQSxFQUFFQTtZQUMxREEsRUFBRUEsSUFBSUEsRUFBRUEsVUFBVUEsRUFBRUEsU0FBU0EsRUFBRUEsaUJBQU9BLEVBQUVBLEVBQUVBLEVBQUVBLFNBQVNBLEVBQUVBO1lBQ3ZEQSxFQUFFQSxJQUFJQSxFQUFFQSxZQUFZQSxFQUFFQSxVQUFVQSxFQUFFQSxRQUFRQSxFQUFFQTtTQUM3Q0EsQ0FBQ0E7UUFDREEsZUFBSUEsQ0FBQ0E7WUFDSkEsV0FBV0EsRUFBRUEsd0JBQXdCQTtZQUNyQ0EsVUFBVUEsRUFBRUEsQ0FBQ0EsZUFBTUEsRUFBRUEsdUJBQVVBLEVBQUVBLHFCQUFZQSxFQUFFQSxtQkFBVUEsQ0FBQ0E7U0FDM0RBLENBQUNBOztjQVFEQTtJQUFEQSxZQUFDQTtBQUFEQSxDQXJCQSxJQXFCQztBQUVELG9CQUFTLENBQUMsS0FBSyxFQUFFLENBQUMsMEJBQWlCLEVBQUUsMEJBQWUsQ0FBQyxDQUFDLENBQUMiLCJmaWxlIjoiYXBwLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4uL3R5cGluZ3MvdHNkLmQudHNcIiAvPlxuaW1wb3J0IHtDb21wb25lbnQsIFZpZXcsIGJvb3RzdHJhcCwgaHR0cEluamVjdGFibGVzfSBmcm9tICdhbmd1bGFyMi9hbmd1bGFyMic7XG5pbXBvcnQge1JvdXRlQ29uZmlnLCBSb3V0ZXJPdXRsZXQsIFJvdXRlckxpbmssIHJvdXRlckluamVjdGFibGVzfSBmcm9tICdhbmd1bGFyMi9yb3V0ZXInO1xuXG5pbXBvcnQge1RvcGJhcn0gZnJvbSAnLi9zcmMvY29tcG9uZW50cy90b3BiYXInO1xuaW1wb3J0IHtOYXZpZ2F0aW9ufSBmcm9tICcuL3NyYy9jb21wb25lbnRzL25hdmlnYXRpb24nO1xuXG5pbXBvcnQge0xvZ2lufSBmcm9tICcuL3NyYy9jb250cm9sbGVycy9sb2dpbic7XG5pbXBvcnQge0xvZ291dH0gZnJvbSAnLi9zcmMvY29udHJvbGxlcnMvbG9nb3V0JztcbmltcG9ydCB7TmV3c2ZlZWR9IGZyb20gJy4vc3JjL2NvbnRyb2xsZXJzL25ld3NmZWVkL25ld3NmZWVkJztcbmltcG9ydCB7Q2FwdHVyZX0gZnJvbSAnLi9zcmMvY29udHJvbGxlcnMvY2FwdHVyZS9jYXB0dXJlJztcblxuQENvbXBvbmVudCh7XG4gIHNlbGVjdG9yOiAnbWluZHMtYXBwJyxcbn0pXG5AUm91dGVDb25maWcoW1xuICB7IHBhdGg6ICcvbG9naW4nLCBjb21wb25lbnQ6IExvZ2luLCBhczogJ2xvZ2luJyB9LFxuICB7IHBhdGg6ICcvbG9nb3V0JywgY29tcG9uZW50OiBMb2dvdXQsIGFzOiAnbG9nb3V0JyB9LFx0XG4gIHsgcGF0aDogJy9uZXdzZmVlZCcsIGNvbXBvbmVudDogTmV3c2ZlZWQsIGFzOiAnbmV3c2ZlZWQnIH0sXG4gIHsgcGF0aDogJy9jYXB0dXJlJywgY29tcG9uZW50OiBDYXB0dXJlLCBhczogJ2NhcHR1cmUnIH0sXG4gIHsgcGF0aDogJy86dXNlcm5hbWUnLCByZWRpcmVjdFRvOiAnL2xvZ2luJyB9XG5dKVxuQFZpZXcoe1xuICB0ZW1wbGF0ZVVybDogJy4vdGVtcGxhdGVzL2luZGV4Lmh0bWwnLFxuICBkaXJlY3RpdmVzOiBbVG9wYmFyLCBOYXZpZ2F0aW9uLCBSb3V0ZXJPdXRsZXQsIFJvdXRlckxpbmtdXG59KVxuXG5jbGFzcyBNaW5kcyB7XG4gIG5hbWU6IHN0cmluZztcbiAgXG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMubmFtZSA9ICdNaW5kcyc7XG4gIH1cbn1cblxuYm9vdHN0cmFwKE1pbmRzLCBbcm91dGVySW5qZWN0YWJsZXMsIGh0dHBJbmplY3RhYmxlc10pOyJdLCJzb3VyY2VSb290IjoiL3NvdXJjZS8ifQ==