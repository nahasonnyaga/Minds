import { Component, View, ElementRef } from 'angular2/core';
import { Router, RouteParams, RouterLink } from "angular2/router";

import { SocketsService } from '../../../services/sockets';
import { Client } from '../../../services/api';
import { SessionFactory } from '../../../services/session';
import { Storage } from '../../../services/storage';
import { AutoGrow } from '../../../directives/autogrow';
import { Emoji } from '../../../directives/emoji';
import { InfiniteScroll } from '../../../directives/infinite-scroll';

import { MessengerEncryptionFactory } from '../encryption/service';
import { MessengerEncryption } from '../encryption/encryption';

import { MessengerScrollDirective } from './scroll';
import { MessengerConversationDockpanesFactory } from '../conversation-dockpanes/service';

@Component({
  selector: 'minds-messenger-conversation',
  properties: [ 'conversation' ],
  templateUrl: 'src/plugins/messenger/conversation/conversation.html',
  directives: [ InfiniteScroll, RouterLink, AutoGrow, MessengerEncryption, MessengerScrollDirective, Emoji ]
})

export class MessengerConversation {

  minds: Minds = window.Minds;
  session = SessionFactory.build();

  encryption = MessengerEncryptionFactory.build(); //ideally we want this loaded from bootstrap func.
  dockpanes = MessengerConversationDockpanesFactory.build();

  guid : string;
  conversation;
  participants : Array<any> = [];
  messages : Array<any> = [];
  open : boolean = false;

  message : string = "";

  constructor(public client : Client){

  }

  ngOnInit(){
    this.load();
  }

  load(){
    this.client.get('api/v1/conversations/' + this.conversation.guid, {
        password: this.encryption.getEncryptionPassword()
      })
      .then((response : any) => {
        this.messages = response.messages;
      })
  }

  send(e){
    e.preventDefault();
    this.client.post('api/v1/conversations/' + this.conversation.guid, {
        message: this.message,
        encrypt: true
      })
      .then((response : any) => {
        this.messages.push(response.message);
      });
    this.message = "";
  }

}
