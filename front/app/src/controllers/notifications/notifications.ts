import { Component, View, NgFor, NgIf, NgSwitch, NgSwitchWhen, NgSwitchDefault, Inject, NgClass } from 'angular2/angular2';
import { RouterLink } from 'angular2/router';
import { Client } from 'src/services/api';
import { Material } from 'src/directives/material';
import { InfiniteScroll } from '../../directives/infinite-scroll';

@Component({
  selector: 'minds-notifications',
  viewBindings: [ Client ]
})
@View({
  templateUrl: 'templates/notifications/list.html',
  directives: [ NgFor, NgIf, NgSwitch, NgSwitchWhen, NgSwitchDefault, NgClass, RouterLink, Material, InfiniteScroll ]
})

export class Notifications {

  notificatons : Array<Object> = [];
  moreData : boolean = true;
  offset: string = "";
  inProgress : boolean = false;

  constructor(public client: Client){
    this.load(true);
  }

  load(refresh : boolean = false){
    var self = this;

    if(this.inProgress) return false;

    if(refresh)
      this.offset = "";

    this.inProgress = true;

    this.client.get('api/v1/notifications', {limit:12, offset:this.offset})
      .then((data : any) => {

        if(!data.notifications){
          self.moreData = false;
          self.inProgress = false;
          return false;
        }

        if(refresh){
          self.notifications = data.notifications;
        }else{
          if(self.offset)
            data.notifications.shift();
          for(let entity of data.notifications)
            self.notifications.push(entity);
        }

        self.offset = data['load-next'];
        self.inProgress = false;

      });
  }

}
