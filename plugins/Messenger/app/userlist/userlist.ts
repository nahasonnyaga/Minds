import { Component } from '@angular/core';
import { CORE_DIRECTIVES, FORM_DIRECTIVES } from '@angular/common';
import { ROUTER_DIRECTIVES, Router, RouteParams, RouterLink } from "@angular/router-deprecated";

import { SocketsService } from '../../../services/sockets';

import { Storage } from '../../../services/storage';
import { Client } from '../../../services/api';
import { SessionFactory } from '../../../services/session';
import { BUTTON_COMPONENTS } from '../../../components/buttons';
import { Material } from '../../../directives/material';

import { MessengerScrollDirective } from '../scroll';
import { MessengerConversationDockpanes, MessengerConversationDockpanesFactory } from '../conversation-dockpanes/conversation-dockpanes';
import { MessengerEncryptionFactory } from '../encryption/service';
import { MessengerSounds } from '../sounds/service';
import { MessengerEncryption } from '../encryption/encryption';

@Component({
  selector: 'minds-messenger-userlist',
  templateUrl: 'src/plugins/Messenger/userlist/userlist.html',
  directives: [ BUTTON_COMPONENTS, Material, RouterLink, MessengerConversationDockpanes,
    MessengerEncryption, MessengerScrollDirective ]
})

export class MessengerUserlist {

  session = SessionFactory.build();
  encryption = MessengerEncryptionFactory.build(); //ideally we want this loaded from bootstrap func.
  sounds = new MessengerSounds();

  dockpanes = MessengerConversationDockpanesFactory.build();
  conversations : Array<any> = [];
  offset : string =  "";

  setup : boolean = false;
  hasMoreData : boolean =  true;
  inProgress : boolean = false;
  cb: number = Date.now();

  minds: Minds = window.Minds;
  storage: Storage = new Storage();

  socketSubscriptions = {
    touchConversation: null
  };

  userListToggle : boolean = false;

  constructor(public client: Client, public sockets: SocketsService){
  }

  ngOnInit(){
    if(this.session.isLoggedIn()){
      if(this.userListToggle)
        this.load({ refresh: true });
      this.listen();
      this.autoRefresh();
    }
  }

  load(opts) {

    (<any>Object).assign({
      limit: 12,
      offset: '',
      refresh: false
    }, opts);

    if(this.inProgress && !opts.refresh)
      return false;

    this.inProgress = true;

    if(opts.refresh){
      this.offset = "";
      this.cb = Date.now();
    }

    this.client.get('api/v2/conversations', opts)
      .then((response : any) => {
        if (!response.conversations) {
          this.hasMoreData = false;
          this.inProgress = false;
          return false;
        }

        if(opts.refresh){
          this.conversations = response.conversations;
        } else {
          this.conversations = this.conversations.concat(response.conversations);
        }

        this.offset = response['load-next'];
        this.inProgress = false;
      })
      .catch((error) => {
        console.log("got error" + error);
        this.inProgress = false;
      });
  }

  search_timeout;
  search(q : string | HTMLInputElement){
    if(this.search_timeout)
      clearTimeout(this.search_timeout);

    this.conversations = [];

    if (typeof (<HTMLInputElement>q).value !== 'undefined') {
      q = (<HTMLInputElement>q).value;
    }

    if(!q){
      return this.load({ refresh: true });
    }

    this.search_timeout = setTimeout(() => {

      this.inProgress = true;
      this.client.get('api/v2/conversations/search', {
          q,
          limit: 24
        })
        .then((response : any) => {
          if (!response.conversations) {
            this.hasMoreData = false;
            this.inProgress = false;
            return false;
          }

          this.conversations = response.conversations;

          this.offset = response['load-next'];
          this.inProgress = false;
        })
        .catch((error) => {
          console.log("got error" + error);
          this.inProgress = false;
        });
    }, 300);
  }

  openConversation(conversation){
    conversation.open = true;
    this.dockpanes.open(conversation);
  }

  listen(){
    this.socketSubscriptions.touchConversation = this.sockets.subscribe('touchConversation', (guid) => {

      for(var i in this.dockpanes.conversations) {
        if(this.dockpanes.conversations[i].guid == guid) {
          this.dockpanes.conversations[i].unread = true;
          return;
        }
      }

      this.client.get(`api/v2/conversations/${guid}`, {
          password: this.encryption.getEncryptionPassword()
        })
        .then((response) => {
          this.openConversation(response);
        });

    });
  }

  unListen() {
    for (let sub in this.socketSubscriptions) {
      if (this.socketSubscriptions[sub]) {
        this.socketSubscriptions[sub].unsubscribe();
      }
    }
  }

  toggle(){
    this.userListToggle = !this.userListToggle
    if(this.userListToggle)
      this.load({ refresh: true });
  }

  autoRefresh(){
    setInterval(() => {
      if(!this.userListToggle)
        return;
      this.client.get('api/v2/conversations', { limit: 12 })
        .then((response : any) => {
          if (!response.conversations) {
            return false;
          }

          for(let j = 0; j < response.conversations.length; j++){
            for(let i = 0; i < this.conversations.length; i++){
              if(this.conversations[i].guid == response.conversations[j].guid){
                this.conversations[i] = response.conversations[j];
              }
            }
          }

        });
    }, 30000); // refresh 30 seconds
  }

  logout(){
    this.encryption.logout();
    this.dockpanes.closeAll();
  }

  ngOnDestroy(){
    this.unListen();
  }

}
export { MessengerConversation } from '../conversation/conversation';
