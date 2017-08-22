package  {
	import flash.net.FileReference;
	import flash.net.FileFilter;
	import flash.net.FileReferenceList;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.ProgressEvent;
	import flash.events.IOErrorEvent;
	import flash.events.SecurityErrorEvent;
	import flash.system.*;
	import flash.net.URLRequest;
	import flash.net.URLVariables;
	import flash.net.URLRequestMethod;
	import flash.external.ExternalInterface;
	import flash.system.Security;
	public class FileUploader extends Sprite{
		var mc:file_uploader;
		var fr:FileReference;
		var ftype:FileFilter;
		var sURL:String = "uploader.php";
		var fileID:String = "1001";
		var no:String = "1";
		var isDone:Boolean = false;
		public function FileUploader() {
			// constructor code
			init();
		}
		private function init():void{
			Security.allowDomain("*");
			this.fileID = root.loaderInfo.parameters.fileID;
			this.no = root.loaderInfo.parameters.no;
			//root.loaderInfo.parameters.sURL=this.fileID;
			
			mc = new file_uploader();
			mc.bg_prog.visible=false;
			mc.progress.visible=false;
			mc.batal.visible=false;
			mc.txt_percent.visible=false;
			mc.txt_notify.text = "Ukuran file maksimum adalah 100 KB(Kilobytes)";
			
			fr = new FileReference();
			ftype = new FileFilter("Assets (*.gif, *.jpg, *.swf)","*.gif;*.jpg;*.swf;");
			
			initListeners();
			addChild(mc);
		}
		private function initListeners(){
			
			mc.btn01.addEventListener(MouseEvent.CLICK,onBtn01Clicked,false,0,true);
			mc.batal.addEventListener(MouseEvent.CLICK,onBtnBatal,false,0,true);
			fr.addEventListener(Event.COMPLETE,onUploadComplete,false,0,true);
			fr.addEventListener(IOErrorEvent.IO_ERROR,onIOError,false,0,true);
			fr.addEventListener(Event.SELECT,onFileSelect,false,0,true);
			fr.addEventListener(ProgressEvent.PROGRESS,onUploadProgress,false,0,true);
		}
		private function onBtnBatal(evt:MouseEvent):void{
			fr.cancel();
			mc.btn01.visible=true;
			mc.bg_prog.visible=false;
			mc.progress.width=0;
			mc.progress.visible=false;
			mc.batal.visible=false;
		}
		private function onUploadProgress(evt:ProgressEvent):void{
			mc.progress.width = (evt.bytesLoaded/evt.bytesTotal)*200;
			mc.txt_percent.text = Math.round((evt.bytesLoaded/evt.bytesTotal)*100)+" %";
			mc.txt_percent.visible=true;
			mc.batal.visible=true;
			mc.txt_notify.text = "File "+fr.name+" sedang di upload..";
		}
		private function onFileSelect(evt:Event):void{
			mc.btn01.visible=false;
			mc.bg_prog.visible=true;
			mc.progress.width=0;
			mc.progress.visible=true;
			mc.batal.visible=true;
			//proses upload file.
			var urlVars:URLVariables = new URLVariables();
			urlVars.action="upload";
			urlVars.fileID = this.fileID;
			var urlReq:URLRequest = new URLRequest(this.sURL);
			urlReq.method = URLRequestMethod.POST;
			urlReq.data = urlVars;
			//-->
			fr.upload(urlReq,'asset');
		}
		private function onIOError(evt:IOErrorEvent):void{
			mc.txt_notify.text = "Maaf, gagal mengirimkan file ke server. --> "+sURL;
		}
		private function onUploadComplete(evt:Event):void{
			mc.txt_notify.text = "Upload Berhasil !";
			mc.progress.width = 200;
			mc.txt_percent.text = "100 %";
			mc.batal.visible=false;
			if(ExternalInterface.available){
				if(!isDone){
					ExternalInterface.call('upload_notify',{f_id:this.fileID,no:this.no});
					isDone=true;
				}
			}
		}
		private function onBtn01Clicked(evt:MouseEvent):void{
			fr.browse([ftype]);
		}
	}
	
}
