import { Component, View, NgFor, NgIf, Inject, formDirectives, CSSClass} from 'angular2/angular2';
import { Router, RouteParams, RouterLink } from 'angular2/router';
import { Client } from 'src/services/api';
import { Material } from 'src/directives/material';
import { SessionFactory } from '../../services/session';
import { InfiniteScroll } from '../../directives/infinite-scroll';
import { Activity } from 'src/controllers/newsfeed/activity';

@Component({
  selector: 'minds-discovery',
  viewInjector: [ Client ]
})
@View({
  templateUrl: 'templates/discovery/discovery.html',
  directives: [ RouterLink, NgFor, NgIf, Material, formDirectives, InfiniteScroll, CSSClass ]
})

export class Discovery {
  _filter : string = "featured";
  _type : string = "all";

  constructor(public client: Client,
    @Inject(Router) public router: Router,
    @Inject(RouteParams) public params: RouteParams
    ){
    this._filter = params.params['filter'];
    if(params.params['type'])
      this._type = params.params['type'];
    this.load();
  }

  load(){
    console.log("loading " + this._filter + ' (' + this._type + ')');
    this.client.get('api/v1/entities/'+this._filter+'/'+this._type, {limit:12, offset:""})
      .then((data : any) => {
        console.log(data);
        });
  }

}
