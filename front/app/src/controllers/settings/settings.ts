import { Component, View, CORE_DIRECTIVES, FORM_DIRECTIVES} from 'angular2/angular2';
import { RouterLink, RouteParams } from "angular2/router";
import { Client } from 'src/services/api';
import { Material } from 'src/directives/material';

import { SettingsGeneral } from './general';
import { SettingsStatistics } from './statistics';
import { SettingsDisableChannel } from './disable';
import { SettingsTwoFactor } from './twoFactor';

@Component({
  selector: 'minds-settings',
  viewBindings: [ Client ]
})
@View({
  templateUrl: 'templates/settings/settings.html',
  directives: [ CORE_DIRECTIVES, FORM_DIRECTIVES, Material, RouterLink, SettingsGeneral, SettingsStatistics, SettingsDisableChannel, SettingsTwoFactor]
})

export class Settings{

  minds : Minds;
  user : any;
  filter : string;

  constructor(public client: Client, public params: RouteParams){
    this.minds = window.Minds;
    if(params.params['filter'])
      this.filter = params.params['filter'];
    else
      this.filter = 'general';
  }

}
