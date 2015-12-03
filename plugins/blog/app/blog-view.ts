import { Component, View, CORE_DIRECTIVES, Inject} from 'angular2/angular2';
import { Router, RouteParams, ROUTER_DIRECTIVES } from "angular2/router";

import { Client } from '../../services/api';
import { SessionFactory } from '../../services/session';
import { Material } from '../../directives/material';
import { GoogleAds } from '../../components/ads/google-ads';
import { RevContent } from '../../components/ads/revcontent';
import { MindsTitle } from '../../services/ux/title';
import { MindsBanner } from '../../components/banner';
import { Comments } from '../../controllers/comments/comments';
import { BUTTON_COMPONENTS } from '../../components/buttons';
import { ShareModal } from '../../components/modal/modal';

import { MindsBlogResponse } from '../../interfaces/responses';
import { MindsBlogEntity } from '../../interfaces/entities';

@Component({
  selector: 'minds-blog-view',
  viewBindings: [ Client ],
  bindings:[ MindsTitle ]
})
@View({
  templateUrl: 'src/plugins/blog/templates/view.html',
  directives: [ CORE_DIRECTIVES, ROUTER_DIRECTIVES, BUTTON_COMPONENTS, Material, Comments, MindsBanner, GoogleAds, RevContent, ShareModal ]
})

export class BlogView {

  minds;
  guid : string;
  blog : MindsBlogEntity = {
    guid: '',
    title: '',
    description: '',
    ownerObj: {}
  };
  session = SessionFactory.build();
  sharetoggle : boolean = false;

  constructor(public client: Client,
    @Inject(Router) public router: Router,
    @Inject(RouteParams) public params: RouteParams,
    public title: MindsTitle){
      if(params.params['guid'])
        this.guid = params.params['guid'];
      this.minds = window.Minds;
      this.load();
  }

  load(refresh : boolean = false){
    console.log('grabbing ' + this.guid);
    var self = this;
    this.client.get('api/v1/blog/' + this.guid, {})
      .then((response : MindsBlogResponse) => {
        if(response.blog)
          self.blog = response.blog;
          self.title.setTitle(self.blog.title);
      })
      .catch((e) => {

      });
  }

  delete(){
    var self = this;
    if(confirm("Are you sure?")){
      this.client.delete('api/v1/blog/' + this.guid)
        .then((response : any) => {
          self.router.navigate(['/Blog', {filter: 'owner'}]);
        })
        .catch((e) => {
        });
    }
  }

}
