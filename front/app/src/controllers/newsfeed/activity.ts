import { Component, View, CORE_DIRECTIVES, EventEmitter, Inject, ElementRef} from 'angular2/angular2';
import { RouterLink } from "angular2/router";
import { Client } from 'src/services/api';
import { SessionFactory } from 'src/services/session';
import { Material } from 'src/directives/material';
import { Remind } from './remind';
import { BUTTON_COMPONENTS } from 'src/components/buttons';
import { Boost } from './boost';
import { Comments } from 'src/controllers/comments/comments';
import { TagsPipe } from 'src/pipes/tags';
import { TagsLinks } from 'src/directives/tags';
import { ScrollFactory } from 'src/services/ux/scroll';

@Component({
  selector: 'minds-activity',
  viewBindings: [ Client ],
  properties: ['object'],
  outputs: [ '_delete: delete']
})
@View({
  templateUrl: 'templates/cards/activity.html',
  directives: [ CORE_DIRECTIVES, BUTTON_COMPONENTS, Boost, Comments, Material, Remind, RouterLink, TagsLinks ],
  pipes: [ TagsPipe ]
})

export class Activity {

  activity : any;
  menuToggle : boolean = false;
  commentsToggle : boolean = false;
  session = SessionFactory.build();
  scroll = ScrollFactory.build();
  showBoostOptions : boolean = false;
  type : string;
  element : any;
  visible : boolean = false;

  _delete: EventEmitter = new EventEmitter();

	constructor(public client: Client, @Inject(ElementRef) _element: ElementRef){
    this.element = _element.nativeElement;
    this.isVisible();
	}

  set object(value: any) {
    if(!value)
      return;
    this.activity = value;
  }

  delete(){
    this.client.delete('api/v1/newsfeed/'+this.activity.guid);
    this._delete.next(true);
  }

  openMenu(){
    this.menuToggle = !this.menuToggle;
    console.log(this.menuToggle);
  }

  openComments(){
    this.commentsToggle = !this.commentsToggle;
  }

  showBoost(){
      this.showBoostOptions = !this.showBoostOptions;
  }

  isVisible(){
    var listen = this.scroll.listen((view) => {
      if(this.element.offsetTop - view.height <= view.top && !this.visible){
        //stop listening
        this.scroll.unListen(listen);
        //make visible
        this.visible = true;
        //update the analytics
        this.client.put('api/v1/newsfeed/' + this.activity.guid + '/view');
      }
    });
    this.scroll.fire();
  }
}
