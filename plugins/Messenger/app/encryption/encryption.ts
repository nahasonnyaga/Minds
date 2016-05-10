import { Component, View, ElementRef } from 'angular2/core';
import { Router, RouteParams, RouterLink } from "angular2/router";

import { SocketsService } from '../../../services/sockets';
import { Client } from '../../../services/api';
import { SessionFactory } from '../../../services/session';
import { Storage } from '../../../services/storage';
import { AutoGrow } from '../../../directives/autogrow';
import { InfiniteScroll } from '../../../directives/infinite-scroll';

import { MessengerEncryptionFactory } from './service';

@Component({
  selector: 'minds-messenger-encryption',
  host: {
    'class': 'm-messenger-encryption'
  },
  templateUrl: 'src/plugins/messenger/encryption/encryption.html',
  directives: [ InfiniteScroll, RouterLink, AutoGrow ]
})

export class MessengerEncryption {

  minds: Minds;
  session = SessionFactory.build();

  encryption = MessengerEncryptionFactory.build(); //ideally we want this loaded from bootstrap func.

  constructor(public client : Client){

  }

  unlock(password){
    this.encryption.unlock(password.value);
    password.value = '';
  }

  setup(password, password2){
    if(password.value != password2.value){
      return;
    }
    this.encryption.doSetup(password);
  }

}
