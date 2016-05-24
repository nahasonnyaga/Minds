import { Component, Inject } from 'angular2/core';
import { CORE_DIRECTIVES } from 'angular2/common';
import { ROUTER_DIRECTIVES, Router, RouteParams } from "angular2/router";

import { Client } from '../../../services/api';
import { SessionFactory } from '../../../services/session';
import { Material } from '../../../directives/material';
import { InfiniteScroll } from '../../../directives/infinite-scroll';
import { MindsBlogListResponse } from '../../../interfaces/responses';
import { BUTTON_COMPONENTS } from '../../../components/buttons';

import { AttachmentService } from '../../../services/attachment';

@Component({
  selector: 'minds-card-blog',

  properties: ['_blog : object'],
  bindings: [AttachmentService],
  templateUrl: 'src/plugins/blog/card/card.html',
  directives: [ CORE_DIRECTIVES, ROUTER_DIRECTIVES, BUTTON_COMPONENTS, Material, InfiniteScroll ]
})

export class BlogCard {

  minds;

  blog;
  session = SessionFactory.build();

  constructor(public attachment: AttachmentService){
      this.minds = window.Minds;
  }

  set _blog(value : any){
    this.blog = value;
  }

}
