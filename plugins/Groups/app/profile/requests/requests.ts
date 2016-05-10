import { Component, Inject } from 'angular2/core';
import { CORE_DIRECTIVES } from 'angular2/common';
import { RouterLink, RouteParams } from "angular2/router";

import { GroupsService } from '../../groups-service';

import { Client } from '../../../../services/api';
import { SessionFactory } from '../../../../services/session';
import { Material } from '../../../../directives/material';
import { InfiniteScroll } from '../../../../directives/infinite-scroll';
import { UserCard } from '../../../../controllers/cards/cards';


@Component({
  selector: 'minds-groups-profile-requests',
  bindings: [ GroupsService ],
  properties: ['_group : group'],
  templateUrl: 'src/plugins/Groups/profile/requests/requests.html',
  directives: [ CORE_DIRECTIVES, Material, RouterLink, InfiniteScroll, UserCard ]
})

export class GroupsProfileRequests {

  minds;
  group : any;
  session = SessionFactory.build();

  users : Array<any> = [];
  offset : string = "";
  inProgress : boolean = false;
  moreData : boolean = true;

	constructor(public client : Client, public service: GroupsService){

	}

  set _group(value : any){
    this.group = value;
    this.load();
    this.minds = window.Minds;
  }

  load(refresh : boolean = false){
    this.inProgress = true;
    this.client.get('api/v1/groups/membership/' + this.group.guid + '/requests', { limit: 12, offset: this.offset })
      .then((response : any) => {

        if(!response.users || response.users.length == 0){
          this.moreData = false;
          this.inProgress = false;
          return false;
        }

        if(this.users && !refresh){
          for(let user of response.users)
            this.users.push(user);
        } else {
             this.users = response.users;
        }
        this.offset = response['load-next'];
        this.inProgress = false;

      })
      .catch((e)=>{

      });
  }

  accept(user : any, index: number){
    this.service.acceptRequest(this.group, user.guid)
    .then(() => {
      this.users.splice(index, 1);
      this.changeCounter('members:count', +1);
      this.changeCounter('requests:count', -1);
    });
  }

  reject(user : any, index: number){
    this.service.rejectRequest(this.group, user.guid)
    .then(() => {
      this.users.splice(index, 1);
      this.changeCounter('requests:count', -1);
    });

  }

  private changeCounter(counter: string, val = 0) {
    if (typeof this.group[counter] !== 'undefined') {
      this.group[counter] = parseInt(this.group[counter], 10) + val;
    }
  }

}
