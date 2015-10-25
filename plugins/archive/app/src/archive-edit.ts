import { Component, View, CORE_DIRECTIVES, Inject, FORM_DIRECTIVES } from 'angular2/angular2';
import { Router, RouteParams, ROUTER_DIRECTIVES } from "angular2/router";

import { Client, Upload } from 'src/services/api';
import { SessionFactory } from 'src/services/session';
import { LICENSES, ACCESS } from 'src/services/list-options';

import { Material } from 'src/directives/material';
import { AutoGrow } from 'src/directives/autogrow';
import { MDL_DIRECTIVES } from 'src/directives/material';
import { Comments } from 'src/controllers/comments/comments';
import { BUTTON_COMPONENTS } from 'src/components/buttons';
import { MindsTinymce } from 'src/components/editors/tinymce';
import { ArchiveTheatre } from './views/theatre';
import { ArchiveGrid } from './views/grid';

@Component({
  selector: 'minds-archive-edit',
  viewBindings: [ Client, Upload ]
})
@View({
  templateUrl: 'templates/plugins/archive/edit.html',
  directives: [ MDL_DIRECTIVES, FORM_DIRECTIVES, CORE_DIRECTIVES, ROUTER_DIRECTIVES, BUTTON_COMPONENTS, AutoGrow, MindsTinymce, Material, Comments, ArchiveTheatre, ArchiveGrid ]
})

export class ArchiveEdit {

  minds;
  session = SessionFactory.build();
  guid : string;
  entity : any  = {
    title: "",
    description: "",
    subtype: "",
    license: "all-rights-reserved"
  };
  inProgress : boolean;
  error : string;

  licenses = LICENSES;
  access = ACCESS;

  constructor(public client: Client, public upload: Upload, public router: Router, public params: RouteParams){
      if(params.params['guid'])
        this.guid = params.params['guid'];
      this.minds = window.Minds;
      this.load();
  }

  load(){
    var self = this;
    this.inProgress = true;
    this.client.get('api/v1/entities/entity/' + this.guid, { children: false })
      .then((response : any) => {
        self.inProgress = false;
        console.log(response);
        if(response.entity){
          if (!response.entity.description)
            response.entity.description = "";

          self.entity = response.entity;
        }
      })
      .catch((e) => {

      });
  }

  save(){
    var self = this;
    this.client.post('api/v1/archive/' + this.guid, this.entity)
      .then((response : any) => {
        console.log(response);
        self.router.navigate(['/Archive-View', {guid: self.guid}]);
      })
      .catch((e) => {
        this.error ="There was an error while trying to update";
      });
  }

}
