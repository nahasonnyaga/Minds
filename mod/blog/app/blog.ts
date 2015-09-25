import { Component, View, CORE_DIRECTIVES, NgStyle, Inject, FORM_DIRECTIVES} from 'angular2/angular2';
import { ROUTER_DIRECTIVES, Router, RouteParams } from "angular2/router";

import { Client } from 'src/services/api';
import { SessionFactory } from 'src/services/session';
import { Material } from 'src/directives/material';
import { InfiniteScroll } from 'src/directives/infinite-scroll';
import { MindsBlogListResponse } from 'src/interfaces/responses';

import { BlogCard } from './blog-card';

@Component({
  selector: 'minds-blog',
  viewBindings: [ Client ]
})
@View({
  templateUrl: 'templates/plugins/blog/list.html',
  directives: [ CORE_DIRECTIVES, ROUTER_DIRECTIVES, NgStyle, Material, InfiniteScroll, BlogCard ]
})

export class Blog {

  minds;

  offset : string = "";
  moreData : boolean = true;
  inProgress : boolean = false;
  blogs : Array<any> = [];
  session = SessionFactory.build();
  _filter : string = "featured";

  constructor(public client: Client,
    @Inject(Router) public router: Router,
    @Inject(RouteParams) public params: RouteParams
    ){
      this._filter = params.params['filter'];
      this.minds = window.Minds;
      this.load();
  }

  load(refresh : boolean = false){

    var self = this;
    this.inProgress = true;
    this.client.get('api/v1/blog/' + this._filter, { limit: 12, offset: this.offset})
      .then((response : MindsBlogListResponse) => {

        if(!response.blogs){
          self.moreData = false;
          self.inProgress = false;
          return false;
        }

        if(refresh){
          self.blogs = response.blogs;
        } else {
          if(self.offset)
            response.blogs.shift();
          for(let blog of response.blogs)
            self.blogs.push(blog);
        }

        self.offset = response['load-next'];
        self.inProgress = false;
      })
      .catch((e)=>{

      });
  }
}

export { BlogView } from './blog-view';
export { BlogEdit } from './blog-edit';
