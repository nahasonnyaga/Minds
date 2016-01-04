import { Component, View } from 'angular2/core';
import { CORE_DIRECTIVES, FORM_DIRECTIVES } from 'angular2/common';
import { Router, RouterLink } from "angular2/router";

import { Client, Upload } from '../../../services/api';
import { MindsTitle } from '../../../services/ux/title';
import { SessionFactory } from '../../../services/session';
import { Material } from '../../../directives/material';
import { MindsBanner } from '../../../components/banner';


@Component({
  selector: 'minds-groups-create',
  viewBindings: [ Client, Upload ],
  bindings: [ MindsTitle ]
})
@View({
  templateUrl: 'src/plugins/groups/create/create.html',
  directives: [ CORE_DIRECTIVES, Material, RouterLink, FORM_DIRECTIVES, MindsBanner ]
})

export class GroupsCreator {

  session = SessionFactory.build();
  banner;
  group : any = {
    name: '',
    description: '',
    membership: 2
  };

  constructor(public client: Client, public upload: Upload, public router: Router, public title: MindsTitle){
    this.title.setTitle("Create Group");
  }

  addBanner(banner : any){
    this.banner = banner.file;
    this.group.banner_position = banner.top;
  }

  membershipChange(value){
    console.log(value);
    this.group.membership = value;
  }

  save(){
    var self = this;
    this.upload.post('api/v1/groups/group', [this.banner], this.group)
      .then((response : any) => {
        self.router.navigate(['/Groups-Profile', {guid: response.guid, filter: ''}]);
      })
      .catch((e)=>{

      });
  }

}
