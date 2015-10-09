import { Component, View, Inject } from 'angular2/angular2';
import { Router } from 'angular2/router';
import { Material } from 'src/directives/material';
import { Client } from 'src/services/api';
import { SessionFactory } from 'src/services/session';

@Component({
  selector: 'minds-register',
  viewBindings: [ Client ]
})
@View({
  templateUrl: 'templates/resetPassword.html',
  directives: [ Material ]
})

export class ResetPassword {

	session = SessionFactory.build();
  errorMessage : string = "";
  inProgress : boolean = false;

	constructor(public client : Client, @Inject(Router) public router: Router){
		window.componentHandler.upgradeDom();
	}

	resetPassword(password){
    this.errorMessage = "";
    this.inProgress = true;
		var self = this;
		this.client.post('/api/v1/password-reset', {password: password.value})
			.then((data : any) => {
				password.value = '';

        this.inProgress = false;
				self.session.login(data.user);
				self.router.navigate(['/Newsfeed', {}]);
			})
			.catch((e) => {
        console.log(e);
        this.inProgress = false;
        if(e.status == 'failed'){
          self.errorMessage = "There was a problem trying to reset your password. Please try again.";
        }

        if(e.status == 'error'){
          self.errorMessage = e.message;
        }

			});
	}

}
