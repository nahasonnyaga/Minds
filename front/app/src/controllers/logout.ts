import { Component, View, Inject } from 'angular2/angular2';
import { Router, ROUTER_DIRECTIVES } from 'angular2/router';
import { Material } from 'src/directives/material';
import { Client } from 'src/services/api';
import { SessionFactory } from 'src/services/session';

@Component({
  viewBindings: [Client]
})
@View({
  templateUrl: 'templates/login.html',
  directives: [ Material, ROUTER_DIRECTIVES ]
})

export class Logout {

	session = SessionFactory.build();

	constructor(public client : Client, @Inject(Router) public router: Router){
		this.logout();
	}

	logout(){
		this.client.delete('api/v1/authenticate');
		this.session.logout();
    this.router.navigate(['/Login', {}]);
	}
}
