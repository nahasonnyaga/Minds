import { Component, View } from 'angular2/core';
import { CORE_DIRECTIVES, FORM_DIRECTIVES } from 'angular2/common';
import { Router, RouterLink } from "angular2/router";

import { GroupsService } from '../groups-service';

import { MindsTitle } from '../../../services/ux/title';
import { SessionFactory } from '../../../services/session';
import { Material } from '../../../directives/material';
import { MindsBanner } from '../../../components/banner';
import { MindsAvatar } from '../../../components/avatar';

import { GroupsProfileMembersInvite } from '../profile/members/invite/invite';


@Component({
  selector: 'minds-groups-create',

  bindings: [ MindsTitle, GroupsService ]
})
@View({
  templateUrl: 'src/plugins/Groups/create/create.html',
  directives: [ CORE_DIRECTIVES, Material, RouterLink, FORM_DIRECTIVES, MindsBanner, MindsAvatar, GroupsProfileMembersInvite ]
})

export class GroupsCreator {

  session = SessionFactory.build();
  banner : any = false;
  avatar : any = false;
  group : any = {
    name: '',
    description: '',
    membership: 2,
    tags: '',
    invitees: ''
  };
  invitees : string[] = [];
  editing : boolean = true;
  editDone : boolean = false;
  inProgress : boolean = false;

  constructor(public service: GroupsService, public router: Router, public title: MindsTitle){
    this.title.setTitle("Create Group");
  }

  addBanner(banner : any){
    this.banner = banner.file;
    this.group.banner_position = banner.top;
  }

  addAvatar(avatar : any){
    this.avatar = avatar;
  }

  membershipChange(value){
    this.group.membership = value;
  }

  addInvitee(input, $event = null) {
    if ($event) {
      $event.preventDefault();
      $event.stopPropagation();
    }

    if (!input.value) {
      return;
    }

    let user = input.value;

    input.value = '';

    if (this.invitees.indexOf(user) > -1) {
      return;
    }

    this.service.canInvite(user).then(user => {
      this.invitees.push(user);
    });
  }

  removeInvitee(i) {
    this.invitees.splice(i, 1);
  }

  save(){
    this.editing = false;
    this.editDone = true;
    this.inProgress = true;

    this.group.invitees = this.invitees.join(',');

    this.service.save(this.group)
    .then((guid: any) => {

      this.service.upload({
          guid,
          banner_position: this.group.banner_position
        }, {
          banner: this.banner,
          avatar: this.avatar
        })
        .then(() => {
          this.router.navigate(['/Groups-Profile', { guid, filter: '' }]);
        });

    })
    .catch(e => {
      this.editing = true;
      this.inProgress = false;
    });
  }

}
