import { Component, View, CORE_DIRECTIVES, Inject } from 'angular2/angular2';
import { Router, RouteParams, ROUTER_DIRECTIVES } from "angular2/router";

import { Client, Upload } from 'src/services/api';
import { SessionFactory } from 'src/services/session';
import { Material } from 'src/directives/material';

import { Comments } from 'src/controllers/comments/comments';
import { BUTTON_COMPONENTS } from 'src/components/buttons';

import { ArchiveTheatre } from './views/theatre';
import { ArchiveGrid } from './views/grid';

@Component({
  selector: 'minds-archive-edit',
  viewBindings: [ Client, Upload ]
})
@View({
  templateUrl: 'templates/plugins/archive/edit.html',
  directives: [ CORE_DIRECTIVES, ROUTER_DIRECTIVES, BUTTON_COMPONENTS, Material, Comments, ArchiveTheatre, ArchiveGrid ]
})

export class ArchiveEdit {

  minds;
  session = SessionFactory.build();
  guid : string;
  entity : any = {};
  attachment : any;
  inProgress : boolean;

  constructor(public client: Client,
    public upload: Upload,
    @Inject(Router) public router: Router,
    @Inject(RouteParams) public params: RouteParams
    ){
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
        if(response.entity)
          self.entity = response.entity;
      })
      .catch((e) => {

      });
  }

  save(){
    var self = this;
    this.client.post('api/v1/archive/' + this.guid, this.entity)
      .then((response : any) => {
        console.log(response);
        if(self.attachment)
          self.uploadAttachment();
        //else
          //self.router.navigate(['/Archive-View', {guid: response.guid}]);
      })
      .catch((e) => {

      });
  }

  addAttachment(file){
    this.attachment = file ? file.files[0] : null;
  }

  uploadAttachment(){
    /**
     * Give a live preview
     */
    var reader  = new FileReader();
    reader.onloadend = () => {
      //this.attachment_preview = reader.result;
    }
    reader.readAsDataURL(this.attachment);

    /**
     * Upload to the archive and return the attachment guid
     */
    this.upload.post('api/v1/archive', [this.attachment], this.entity)
      .then((response : any) => {

      });

  }

}
