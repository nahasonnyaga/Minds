import { Component, View, Inject, ElementRef } from 'angular2/core';
import { CORE_DIRECTIVES } from 'angular2/common';
import { Router, RouteParams, ROUTER_DIRECTIVES } from "angular2/router";

import { Client } from '../../../services/api';
import { SessionFactory } from '../../../services/session';
import { Material } from '../../../directives/material';
import { GoogleAds } from '../../../components/ads/google-ads';
import { RevContent } from '../../../components/ads/revcontent';
import { MindsTitle } from '../../../services/ux/title';
import { MindsFatBanner } from '../../../components/banner';
import { Comments } from '../../../controllers/comments/comments';
import { BUTTON_COMPONENTS } from '../../../components/buttons';
import { ShareModal } from '../../../components/modal/modal';
import { SocialIcons } from '../../../components/social-icons/social-icons';
import { InfiniteScroll } from '../../../directives/infinite-scroll';
import { ScrollService } from '../../../services/ux/scroll';


import { MindsBlogResponse } from '../../../interfaces/responses';
import { MindsBlogEntity } from '../../../interfaces/entities';


@Component({
  selector: 'm-blog-view',
  inputs: [ 'blog' ],
  host: {
    'class': 'm-blog'
  },
  bindings:[ MindsTitle ],
  templateUrl: 'src/plugins/blog/view/view.html',
  directives: [ CORE_DIRECTIVES, ROUTER_DIRECTIVES, BUTTON_COMPONENTS, Material, Comments, MindsFatBanner,
    GoogleAds, RevContent, ShareModal, SocialIcons, InfiniteScroll ]
})

export class BlogView {

  minds;
  guid : string;
  blog : MindsBlogEntity;
  session = SessionFactory.build();
  sharetoggle : boolean = false;
  element;

  inProgress : boolean = false;
  moreData : boolean = true;
  activeBlog : number = 0;

  constructor(_element : ElementRef,  public scroll: ScrollService){
      this.minds = window.Minds;
      this.element = _element.nativeElement;
      this.isVisible();
  }

  isVisible(){
    //listens every 0.6 seconds
    this.scroll.listen((e) => {
      var bounds = this.element.getBoundingClientRect();
      if(bounds.top < this.scroll.view.clientHeight && bounds.top + bounds.height >= 0){
        window.history.pushState(null, this.blog.title, this.minds.site_url + 'blog/view/' + this.blog.guid);
      }
    }, 0, 600);
  }

}
