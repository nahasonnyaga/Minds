import { Directive, ViewContainerRef, ProtoViewRef } from 'angular2/angular2';
import { Material as MaterialService } from "src/services/ui";

@Directive({
  selector: '[mdl]',
  properties: ['mdl']
})

export class Material{
  constructor(viewContainer: ViewContainerRef) {
    //MaterialService.rebuild();
    MaterialService.updateElement(viewContainer.element.nativeElement);
  }
}
