import { Component, View, CORE_DIRECTIVES } from 'angular2/angular2';
import { Router, RouteParams, ROUTER_DIRECTIVES } from "angular2/router";

import { Client } from 'src/services/api';
import { SessionFactory } from 'src/services/session';
import { Material } from 'src/directives/material';
import { InfiniteScroll } from 'src/directives/infinite-scroll';

@Component({
  selector: 'minds-archive-grid',
  viewBindings: [ Client ],
  properties: ['_object: object']
})
@View({
  template: `
    <a *ng-for="#item of items" [router-link]="['/archive-view', {guid: item.guid}]">
      <img src="/archive/thumbnail/{{item.guid}}/large" />
      	<span class="material-icons" [hidden]="item.subtype !='video'">play_circle_outline</span>
    </a>
    <infinite-scroll
        distance="25%"
        (load)="load()"
        *ng-if="moreData"
        style="width:100%"
        />
        <div class="mdl-spinner mdl-js-spinner is-active" [mdl] [hidden]="!inProgress"></div>
    </infinite-scroll>
  `,
  directives: [ CORE_DIRECTIVES, ROUTER_DIRECTIVES, Material, InfiniteScroll ]
})

export class ArchiveGrid {

  object : any = {};
  session = SessionFactory.build();

  items : Array<any> = [];
  inProgress : boolean = false;
  moreData : boolean = true;
  offset : string = "";

  constructor(public client: Client){
  }

  set _object(value : any){
    this.object = value;
    this.load();
  }

  load(){
    var self = this;
    if(this.inProgress)
      return;
    this.inProgress = true;
    this.client.get('api/v1/archive/albums/' + this.object.guid, { offset: this.offset })
      .then((response : any) => {
        if(!response.entities){
          self.inProgress = false
          self.moreData = true;
          return false;
        }

        self.items = self.items.concat(response.entities);
        self.offset = response['load-next'];
        self.inProgress = false;
      })
      .catch((e)=>{

      });
  }

}
