import { Component, View, NgFor, NgIf, NgClass, Inject, Observable, FORM_DIRECTIVES} from 'angular2/angular2';
import { Router, RouteParams, RouterLink } from "angular2/router";
import { MessengerConversation } from "./messenger-conversation";
import { MessengerSetup } from "./messenger-setup";
import { Storage } from 'src/services/storage';
import { Client } from 'src/services/api';
import { SessionFactory } from 'src/services/session';
import { Material } from 'src/directives/material';
import { InfiniteScroll } from '../../directives/infinite-scroll';

@Component({
  selector: 'minds-gatherings',
  viewBindings: [ Client ]
})
@View({
  templateUrl: 'templates/plugins/gatherings/gatherings.html',
  directives: [ NgFor, NgIf, NgClass, Material, RouterLink, MessengerConversation, MessengerSetup, InfiniteScroll ]
})

export class Gatherings {
  activity : any;
  session = SessionFactory.build();
  conversations : [];
  next : string =  "";
  setup : boolean = false;
  hasMoreData : boolean =  true;
  inprogress : boolean = false;
  cb : Date = new Date();
  search : {};
  storage: Storage;
  minds: {};

  constructor(public client: Client,
    @Inject(Router) public router: Router,
    @Inject(RouteParams) public params: RouteParams
  ){
    console.log("lalalala");
    this.storage = new Storage();
    this.checkSetup();
    this.load(true, true);
    this.minds = window.Minds;
    this.minds.cdn_url = "https://d3ae0shxev0cb7.cloudfront.net";
  }

  showConversation (guid: string, name: string){

  }

  checkSetup(){
    var self = this;
    var key = this.storage.get('private-key');
    if (key){
      this.setup = true;
    }
  }
  
  load(refresh: boolean, fakeData: boolean) {
    var self = this;
    if (this.inprogress || !this.storage.get('private-key')){
      return false;
    }
    this.inprogress = true;
    console.log("load " + refresh);
    this.client.get('api/v1/conversations',
    {	limit: 12,offset: this.next, cb: this.cb
    })
    .then(function(data) {
      if (!fakeData){
        if (!data.conversations) {
          self.hasMoreData = false;
          self.inprogress = false;
          return false;
        } else {
          self.hasMoreData = true;
        };

        if (refresh) {
          self.conversations = data.conversations;
        } else {
          for (var _i = 0, _a = data.conversations; _i < _a.length; _i++) {
            var conversation = _a[_i];
            self.conversations.push(conversation);
          }
        }
      }
      else {
        self.getFakeConversations();
      }

      self.next = data['load-next'];
      //this.$broadcast('scroll.infiniteScrollComplete');
      //this.$broadcast('scroll.refreshComplete');
      self.inprogress = false;
    })
    .catch( function(error) {
      console.log("got error" + error);
      self.inprogress = true;
    });
  };

  doSearch(query: string) {
    var self = this;
    if (!query){
      console.log("clearing");
      this.load(true);
      return true;
    }
    console.log("searching " + query);
    this.client.get('api/v1/gatherings/search', {q: query,type: 'user',view: 'json'})
    .then(function(success) {
      self.conversations = success.user[0];
    })
    .catch(function(error){
      console.log(error);
    });
  };


  doneTyping($event) {
    console.log("typing " + $event.target.value);
    if($event.which === 13) {
      this.doSearch($event.target.value)
      $event.target.value = null;
    }
  };
  refresh() {
    this.search = {};
    this.inprogress = false;
    this.next = "";
    this.previous = "";
    this.cb = new Date();
    this.hasMoreData = true;
    this.load(true);
  };


  getFakeConversations(){
    this.conversations = [{
      "guid": "100000000000000134",
      "type": "user",
      "subtype": false,
      "time_created": "1348444800",
      "time_updated": "1380486497",
      "container_guid": "0",
      "owner_guid": "100000000000000000",
      "site_guid": "1",
      "access_id": "2",
      "name": "John",
      "username": "john",
      "language": "en",
      "icontime": "1437947637",
      "legacy_guid": "134",
      "featured_id": "382261146771525632",
      "website": "",
      "briefdescription": "Information wants to be free",
      "dob": "",
      "gender": "",
      "city": "Wilton, CT",
      "subscribed": true,
      "subscriber": true,
      "subscribers_count": 2583,
      "subscriptions_count": 1628,
      "unread": 0,
      "last_msg": 1441287862
    },
    {
      "guid": "100000000000000599",
      "type": "user",
      "subtype": false,
      "time_created": "1349979579",
      "time_updated": "1378867544",
      "container_guid": "0",
      "owner_guid": "100000000000000000",
      "site_guid": "1",
      "access_id": "2",
      "name": "kram",
      "username": "markna",
      "language": "en",
      "icontime": "1435855391",
      "legacy_guid": "599",
      "featured_id": false,
      "website": "",
      "briefdescription": "test account",
      "dob": "2015-04",
      "gender": "male",
      "city": "NYC",
      "subscribed": true,
      "subscriber": true,
      "subscribers_count": 2600,
      "subscriptions_count": 36,
      "unread": 0,
      "last_msg": 1441277148
    },
    {
      "guid": "481158088242503691",
      "type": "user",
      "subtype": false,
      "time_created": "1440093027",
      "time_updated": false,
      "container_guid": "0",
      "owner_guid": "0",
      "site_guid": false,
      "access_id": "2",
      "name": "3 Feet High and Rising",
      "username": "Fredrik1991",
      "language": "en",
      "icontime": "1440201096",
      "legacy_guid": false,
      "featured_id": false,
      "website": "",
      "briefdescription": "Anti Austerity - Big on Charity",
      "dob": "",
      "gender": "",
      "city": "Stoke-on-Trent",
      "subscribed": true,
      "subscriber": true,
      "subscribers_count": 38,
      "subscriptions_count": 158,
      "unread": 0,
      "last_msg": 1441044753
    },
    {
      "guid": "100000000000000341",
      "type": "user",
      "subtype": false,
      "time_created": "1349106435",
      "time_updated": "1380469351",
      "container_guid": "0",
      "owner_guid": "100000000000000000",
      "site_guid": "1",
      "access_id": "2",
      "name": "Bill Ottman",
      "username": "ottman",
      "language": "en",
      "icontime": "1437409536",
      "legacy_guid": "341",
      "featured_id": "373789547047161856",
      "website": "",
      "briefdescription": "Co-creator, Founder, CEO",
      "dob": "",
      "gender": "",
      "city": "NYC",
      "subscribed": true,
      "subscriber": true,
      "subscribers_count": 10796,
      "subscriptions_count": 4297,
      "unread": 0,
      "last_msg": 1440707171
    },
    {
      "guid": "458569479538880512",
      "type": "user",
      "subtype": false,
      "time_created": "1434707483",
      "time_updated": false,
      "container_guid": "0",
      "owner_guid": "0",
      "site_guid": false,
      "access_id": "2",
      "name": "busssard",
      "username": "busssard",
      "language": "en",
      "icontime": "1434710156",
      "legacy_guid": false,
      "featured_id": "464134655193395213",
      "website": "http:\/\/mundivagus.wordpress.com",
      "briefdescription": "",
      "dob": "",
      "gender": "",
      "city": "",
      "subscribed": true,
      "subscriber": true,
      "subscribers_count": 730,
      "subscriptions_count": 271,
      "unread": 0,
      "last_msg": 1440666401
    },
    {
      "guid": "459039753216471040",
      "type": "user",
      "subtype": false,
      "time_created": "1434819605",
      "time_updated": false,
      "container_guid": "0",
      "owner_guid": "0",
      "site_guid": false,
      "access_id": "2",
      "name": "Paulholtphotography",
      "username": "Paulholtphotography",
      "language": "en",
      "icontime": "1435352220",
      "legacy_guid": false,
      "featured_id": false,
      "website": "",
      "briefdescription": "",
      "dob": "",
      "gender": "male",
      "city": "Bradford",
      "subscribed": true,
      "subscriber": true,
      "subscribers_count": 526,
      "subscriptions_count": 6171,
      "unread": 0,
      "last_msg": 1440377632
    }
  ];
}

}
