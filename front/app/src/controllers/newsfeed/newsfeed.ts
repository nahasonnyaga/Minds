import { Component, View, NgFor, NgIf, formDirectives} from 'angular2/angular2';
import { Client } from 'src/services/api';
import { Material } from 'src/directives/material';
import { InfiniteScroll } from '../../directives/infinite-scroll';
import { Activity } from './activity';

@Component({
  selector: 'minds-newsfeed',
  viewInjector: [ Client ]
})
@View({
  templateUrl: 'templates/newsfeed/list.html',
  directives: [ Activity, NgFor, NgIf, Material, formDirectives, InfiniteScroll ]
})

export class Newsfeed {

	newsfeed : Array<Object> = [];
	offset : string = "";
  inProgress : boolean = false;
  moreData : boolean = true;

  postMeta = {
    title: "",
    description: "",
    thumbnail: "",
    url: "",
    active: false
  }

	constructor(public client: Client){
		this.load();
	}

	/**
	 * Load newsfeed
	 */
	load(refresh : boolean = false){
		var self = this;
    if(this.inProgress){
      //console.log('already loading more..');
      return false;
    }

    if(refresh){
      this.offset = "";
    }

    this.inProgress = true;

		this.client.get('api/v1/newsfeed', {limit:12, offset: this.offset}, {cache: true})
				.then((data : MindsActivityObject) => {
					if(!data.activity){
            self.moreData = false;
            self.inProgress = false;
						return false;
					}
          if(self.newsfeed && !refresh){
            for(let activity of data.activity)
              self.newsfeed.push(activity);
          } else {
					     self.newsfeed = data.activity;
          }
					self.offset = data['load-next'];
          self.inProgress = false;
				})
				.catch(function(e){
					console.log(e);
				});
	}

	/**
	 * Post to the newsfeed
	 */
	post(){
		var self = this;
		this.client.post('api/v1/newsfeed', this.postMeta)
				.then(function(data){
					self.load(true);
          console.log(data);
          //reset
          self.postMeta = {
            message: "",
            title: "",
            description: "",
            thumbnail: "",
            url: "",
            active: false
          }
				})
				.catch(function(e){
					console.log(e);
				});
	}

  /**
   * Get rich embed data
   */
  timeout;
  getPostPreview(message){
    var self = this;

    var match = message.value.match(/(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig);
		if (!match) return;
    var url;

		if ( match instanceof Array) {
			url = match[0];
		} else {
			url = match;
		}

		if (!url.length) return;

		url = url.replace("http://", '');
		url = url.replace("https://", '');
    console.log('found url was ' + url)

    self.postMeta.active = true;

    if(this.timeout)
      clearTimeout(this.timeout);

    this.timeout = setTimeout(()=>{
      this.client.get('api/v1/newsfeed/preview', {url: url})
        .then((data : any) => {
          console.log(data);
          self.postMeta.title = data.meta.title;
          self.postMeta.url = data.meta.canonical;
          self.postMeta.description = data.meta.description;
          for (var link of data.links) {
              if (link.rel.indexOf('thumbnail') > -1) {
                  self.postMeta.thumbnail = link.href;
              }
          }
        });
    }, 600);
  }



	/**
	 * A temporary hack, because pipes don't seem to work
	 */
	toDate(timestamp){
		return new Date(timestamp*1000);
	}
}
