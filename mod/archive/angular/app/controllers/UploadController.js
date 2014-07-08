/**
 * Upload controller.
 *
 * Initializes the jQuery uploader and the Kaltura service.
 */

function UploadCtrl($scope, Elgg, $q, $timeout) {

    $scope.fileInfo = [];
    $scope.queue = [];
    $scope.uploaderElement = '#fileupload';
    $scope.saveEnabled = false;
    $scope.albums = albums;
    
    var config = {
        serviceUrl: serviceUrl
    };

	$scope.newAlbum = function(index){
		console.log(index);
		$scope.newAlbumFromIndex = index; //a tad hacky!
		$.fancybox("#album-create-wrapper");
	};
	
	$scope.createAlbum = function(){
		$.fancybox.close();
		index = $scope.newAlbumFromIndex;
		Elgg.createAlbum({title:$scope.albumName}).then(function(guid){ 
	   		$scope.fileInfo[index]['albumId'] = guid;
	   	
			//we now now add this album to the json array of albums.
			$scope.albums.push({title: $scope.albumName, id: guid});
			//update our list
			$scope.fileInfo[index]['albumId'] == guid;
			$scope.albumName = '';
		});
		
	};
	
    $scope.thumbConfig = {
        serviceUrl: serviceUrl,
        pid: partnerId
    };
    
    /**
     * License options (for dropdown)
     */
   	$scope.licenses = [
   		{value:'not-selected', text:'-- select a license--'},
		{value:'attribution-cc', text:'Attribution CC BY'},
		{value:'attribution-sharealike-cc', text:'Attribution-ShareAlike BY-SA'},
		{value:'attribution-noderivs-cc', text:'Attribution-NoDerivs CC BY-ND'},
		{value:'attribution-noncommercial-cc', text:'Attribution-NonCommerical CC BY-NC'},
		{value:'attribution-noncommercial-sharealike-cc', text:'Attribution-NonCommerical-ShareAlike CC BY-NC-SA'},
		{value:'attribution-noncommercial-noderivs-cc', text:'Attribution-NonCommerical-NoDerivs CC BY-NC-ND'},
		{value:'publicdomaincco', text:'Public Domain CCO "No Rights Reserved'},
		{value:'gnuv3', text:'GNU v3 General Public License'},
		{value:'gnuv1.3', text:'GNU v1.3 Free Documentation License'},
		{value:'gnu-lgpl', text:'GNU Lesser General Public License'},
		{value:'gnu-affero', text: 'GNU Affero General Public License'},
		{value:'apache-v1', text:'Apache License, Version 1.0'},
		{value:'apache-v1.1', text:'Apache License, Version 1.1'},
		{value:'apache-v2', text:'Apache License, Version 2.0'},
		{value:'mozillapublic', text:'Mozilla Public License'},
		{value:'bsd', text:'BSD License'}
	];
	$scope.default_license = 'not-selected';

    /**
     * Access options (for dropdown)
     */
	$scope.access = [
		{value:0, text:'Private'},
		{value:2, text:'Public'}
	];
	
	$scope.default_access = 2;
	
	var saveTimeout;
	$scope.changed = function(guid) {
		if (saveTimeout) $timeout.cancel(saveTimeout);
		
		saveTimeout = $timeout(function(){
			$scope.updateEntry(guid);
		},1000);
		
	};

    /**
     * Gets the uploaded file thumbnail
     * @param entryId
     * @returns {string}
     */
    $scope.getFileThumbnail = function(entry, thumbSecond, guid) {
            if(entry){ //Only applies to video
                var thumbnailUrl = serviceUrl + '/p/' + partnerId + '/thumbnail/entry_id/' + entry.id + '/width/400/vid_sec/' + thumbSecond;
                // return empty string if entryID not set, otherwise return thumbnail URL
                return thumbnailUrl;
            }else if(guid){
                var thumbnailUrl = cdnUrl + '/photos/thumbnail/' + guid +'/large';
                return thumbnailUrl;
            }

            return "";
    };
    
    /**
     * Perform reAlignment of upload items
     */
    $scope.reAlign = function(e){
    	$('.elgg-list.mason').masonry('reloadItems').masonry();
		return true;
    };

    /**
     * Callback for the uploader add method. Creates a token, uploads a file, creates an entry and adds the uploaded
     * content.
     * @param data jQuery uploader data object, contains the file to upload.
     * @param elm the uploader element id (with #).
     */
    $scope.uploadFiles = function(data, elm) {
    	
        var file = data.files[0];
        var index = $scope.fileInfo.length;
        data.index = index;
        
        if(index == 0){
        	if(!confirm('I confirm that I have the rights and permission to use this content')){
        		$scope.fileInfo.pop(index);
        		return false;
        	}
        }
        
        $scope.queue.push(file);

        $scope.saveEnabled = true;
		      console.log(file);
        $scope.fileInfo[index] = {};
        $scope.fileInfo[index]['fileObj'] = file;
        $scope.fileInfo[index]['fileType'] = $scope.detectMediaType(file.type);
        $scope.fileInfo[index]['name'] = file.name;
        $scope.fileInfo[index]['updateResult'] = false;
        $scope.fileInfo[index]['license'] = $scope.default_license;
        $scope.fileInfo[index]['access_id'] = $scope.default_access;
        $scope.fileInfo[index]['tags'] = "";
        $scope.fileInfo[index]['description'] = "";
		
        if($scope.fileInfo[index]['fileType'] == 'image') {
		$scope.fileInfo[index]['albumId'] = $scope.albums[0].id;
	} 
	    
	Elgg.uploadElggFile($scope.fileInfo[index], jQuery(elm), data, $scope).then(function(guid){ 
   		$scope.fileInfo[index]['guid'] = guid;
   	});

    };

    /**
     * Detect the type of a file (audio, video, image).
     * @param type the MIME type.
     * @returns string the file type.
     */
    $scope.detectMediaType = function(type) {
        return type.substring(0, type.indexOf('/'));
    };

	/**
	 * @deprecated
	 */
    $scope.saveAll = function(){
    	return;
        for(var index in $scope.fileInfo) //Saves each file
        {
            $scope.updateEntry(index);
        }
		elgg.system_message('Success!');
    };

    /**
     * Initialize the jQuery uploader.
     * @param elm the uploader element (with #).
     */
    $scope.initializeUploader = function(elm) {
        jQuery(elm).fileupload({
            add: function (e, data) {
                $scope.uploadFiles(data, elm);
            },
            dropZone: $('#dropzone')
        });

        // bind the events
        $scope.bindUploaderEvents(elm);
    };
  

    /**
     * Bind uploader events.
     * @param elm
     */
    $scope.bindUploaderEvents = function(elm) {
        jQuery(elm).bind('fileuploadprogress', function(e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
	console.log(progress, data);
            if($scope.fileInfo[data.index]) //Only if element is found
            {
                $scope.fileInfo[data.index]['progress'] = progress;

                $scope.$apply();
            }
            else{
            }
        });

        jQuery(elm).bind('fileuploaddone', function(e, data) {
	
			$('.elgg-list.mason').imagesLoaded(function(){
					$('.elgg-list.mason').masonry('reloadItems').masonry();
			});

        });
    };
    
     // Initialize the jQuery uploader.
    $scope.initializeUploader($scope.uploaderElement);

    $scope.isImage = function(fileItem){
        if(fileItem['fileType'] == 'image')
        {
            return true;
        }

        return false;
    };

    $scope.rotateImage = function(entryRefresh, rotateLeft, index){
        if(entryRefresh && entryRefresh.duration){
            if(rotateLeft){ //Rotate left (reduce video time)

                $scope.fileInfo[index].thumbSecond = Math.abs(($scope.fileInfo[index].thumbSecond - entryRefresh.duration * 0.2) % entryRefresh.duration);

            }else //Rotate right (increase on video seconds)
            {
                $scope.fileInfo[index].thumbSecond = Math.abs(($scope.fileInfo[index].thumbSecond + entryRefresh.duration * 0.2) % entryRefresh.duration);
            }
        }
    };

    $scope.isShowThumbArrows = function(entry, index){
        if($scope.fileInfo[index].fileType == 'video' || $scope.fileInfo[index].fileType == 'audio')
        {
            if(entry && entry.id){
                if(entry.duration && entry.duration > 0){
                    return true;
               }
                else{
                    $scope.fileInfo[index].entryRefresh = $timeout(function() {
                                return $scope.getEntry(entry.id);
                            }, 20 * 1000);
                    }
                }
        }

        return false;
    };

}
