import { Component, View, NgIf } from 'angular2/angular2';
import { RouterLink } from 'angular2/router';
import { Storage } from 'src/services/storage';
import {Sidebar} from 'src/services/ui';

@Component({
  selector: 'minds-topbar',
  viewInjector: [Storage, Sidebar]
})
@View({
  templateUrl: 'templates/components/topbar.html',
  directives: [NgIf, RouterLink]
})

export class Topbar { 
	constructor(public storage: Storage, public sidebar : Sidebar){ }
	
	/**
	 * Determine if login button should be shown
	 */
	showLogin(){
		window.componentHandler.upgradeDom();
		return !window.LoggedIn;
	}
	
	/**
	 * Open the navigation
	 */
	openNav(){
		console.log('opening nav');
		document.getElementsByClassName('mdl-layout__drawer')[0].style['transform'] = "translateX(0)";
		console.log(document.getElementsByClassName('mdl-layout__drawer'));
	}
}