package  
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.ProgressEvent;
	import flash.net.FileReference;
	import flash.net.URLRequest;
	import flash.net.URLVariables;
	import flash.events.IOErrorEvent;
	import flash.net.URLRequest;
	/**
	 * ...
	 * @author duf
	 */
	public class FileUploader extends Sprite
	{
		private var file:FileReference;
		private var _url:String;
		private var _params:Object;
		private var _directory:String;
		private var urlVars:URLVariables;
		static private var bar_width:int = 268;
		public function FileUploader() 
		{
			super();
			addEventListener(Event.ADDED_TO_STAGE, onReady, false, 0, true);
		
		}
		
		private function onReady(e:Event):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, onReady);
			init();
		}
		private function init():void {
			params = stage.loaderInfo.parameters;
			
			directory = params.directory;
			url = params.uri;
			urlVars = new URLVariables();
			urlVars.directory = directory;
			//params = { foo:"bar", foo2:"bar2" };
			//txtLabel.text = "t-->";
		
			for (var str:String in params) {
				urlVars[str] = params[str];
				//txtLabel.appendText(str+" ");
			}
			file = new FileReference();
			file.addEventListener(Event.SELECT, onSelectFile, false, 0, true);
			file.addEventListener(ProgressEvent.PROGRESS, onUploadProgress, false, 0, true);
			file.addEventListener(Event.COMPLETE, onUploadComplete, false, 0, true);
			file.addEventListener(IOErrorEvent.IO_ERROR, ioErrorHandler,false,0,true);
			
			btn.addEventListener(MouseEvent.CLICK, onBtnClicked, false, 0, true);
		}
		
		private function onUploadComplete(e:Event):void 
		{
			percentage.text = "upload completed.";
			bar.width = 0;
		}
		
		private function ioErrorHandler(e:Event):void 
		{
			
		}
		
		private function onUploadProgress(e:ProgressEvent):void 
		{
			bar.width = (e.bytesLoaded / e.bytesTotal) * bar_width;
			percentage.text = Math.round(e.bytesLoaded/e.bytesTotal)*100+"%";
		}
		private function onSelectFile(e:Event):void 
		{
			var req:URLRequest = new URLRequest(url);
			req.data = urlVars;
			req.method = "post";
			txtLabel.text = file.name;
			bar.width = 0;
			file.upload(req);
			//btn.visible = false;
		}
		
		private function onBtnClicked(e:MouseEvent):void 
		{
			file.cancel();
			file.browse();
		}
		
		public function get url():String { return _url; }
		
		public function set url(value:String):void 
		{
			_url = value;
		}
		
		public function get params():Object { return _params; }
		
		public function set params(value:Object):void 
		{
			_params = value;
		}
		
		public function get directory():String { return _directory; }
		
		public function set directory(value:String):void 
		{
			_directory = value;
		}
		
	}

}