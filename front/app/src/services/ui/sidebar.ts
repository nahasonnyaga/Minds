export class Sidebar{
	open(){
		document.getElementsByClassName('mdl-layout__drawer')[0].style['transform'] = "translateX(0)";
		document.getElementsByClassName('mdl-layout__drawer')[0].style['-webkit-transform'] = "translateX(0)";
		document.getElementsByClassName('mdl-layout__drawer')[0].style['-moz-transform'] = "translateX(0)";
		var self = this;
		//we have a delay so we don't close after click
		setTimeout(() => {
			document.addEventListener("click", (e) => {
				self.close();
				document.removeEventListener('click',  arguments.callee);
			});
		}, 300);
	}
	close(){
		document.getElementsByClassName('mdl-layout__drawer')[0].style['transform'] = null;
		document.getElementsByClassName('mdl-layout__drawer')[0].style['-webkit-transform'] = null;
		document.getElementsByClassName('mdl-layout__drawer')[0].style['-moz-transform'] = null;
	}
}