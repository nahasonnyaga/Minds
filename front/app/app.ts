/// <reference path="../typings/tsd.d.ts" />
import {Component, View, bootstrap, httpInjectables} from 'angular2/angular2';
import {RouteConfig, RouterOutlet, RouterLink, routerInjectables} from 'angular2/router';

import {Topbar} from './src/components/topbar';
import {Navigation} from './src/components/navigation';

import {Login} from './src/controllers/login';
import {Logout} from './src/controllers/logout';
import {Newsfeed} from './src/controllers/newsfeed/newsfeed';
import {Capture} from './src/controllers/capture/capture';

@Component({
  selector: 'minds-app',
})
@RouteConfig([
  { path: '/login', component: Login, as: 'login' },
  { path: '/logout', component: Logout, as: 'logout' },	
  { path: '/newsfeed', component: Newsfeed, as: 'newsfeed' },
  { path: '/capture', component: Capture, as: 'capture' },
  { path: '/discovery', component: Newsfeed, as: 'discovery'},
  { path: '/messenger', component: Newsfeed, as: 'messenger'},
  { path: '/notifications', component: Newsfeed, as: 'notifications'},
  { path: '/groups', component: Newsfeed, as: 'groups'},
  	
  { path: '/:username', redirectTo: '/login' }
])
@View({
  templateUrl: './templates/index.html',
  directives: [Topbar, Navigation, RouterOutlet, RouterLink]
})

class Minds {
  name: string;
  
  constructor() {
    this.name = 'Minds';
  }
}

bootstrap(Minds, [routerInjectables, httpInjectables]);