/* 
 * This code is free to use, just remember to give credit.
 */

class RestApi {
    
    static get(controller, task, data, onSuccess, onError, onComplete){
        var address = RestApi.getAddress();
        var response = '';
        data.controller = controller;
        data.task = task;
        var body = document.body;
        var div = RestApi.blockPage(body);
        $.ajax({
                url: address,
                type: 'GET',
                data: data,
                success: function (data) {
                    if(onSuccess === undefined){
                        try{
                            response = JSON.parse(data); 
                            console.log(response);
                        }
                        catch(e){
                            alert(e);
                        }
                    }
                    else{
                        onSuccess(data);
                    }
                },
                error: function (data) {
                    if(onError === undefined){
                        console.log(data);
                    }
                    else{
                        onError(data);
                    }
                },
                complete: function (data) {
                    RestApi.unblockPage(body, div);
                    if(onComplete !== undefined){
                        onComplete(data);
                    }
                }
        });
        return response;
    }
    
    static post(controller, task, data, onSuccess, onError, onComplete){
        var address = RestApi.getAddress();
        var response = '';
        data.controller = controller;
        data.task = task;
        var body = document.body;
        var div = RestApi.blockPage(body);
        $.ajax({
                url: address,
                type: 'POST',
                data: data,
                success: function (data) {
                    if(onSuccess === undefined){
                        try{
                            response = JSON.parse(data); 
                            console.log(response);
                        }
                        catch(e){
                            alert(e);
                        }
                    }
                    else{
                        onSuccess(data);
                    }
                },
                error: function (data) {
                    if(onError === undefined){
                        console.log(data);
                    }
                    else{
                        onError(data);
                    }
                },
                complete: function (data) {
                    RestApi.unblockPage(body, div);
                    if(onComplete !== undefined){
                        onComplete(data);
                    }
                }
        });
        return response;
    }
    
    static getAddress(){
        var myScript = document.getElementById('RestApi.js');
        var path = myScript.getAttribute('src');
        var index = path.search('resources/js');
        path = path.substring(0, index);
        var address = path + 'sys/scripts/requests/rest.php';
        return address;
    }
    
    static getLanguagePackage(onSuccess){
        RestApi.get('LanguageSelection', 'getPackage', {}, 
        function(response){
            var data = JSON.parse(response);
            onSuccess(data);
        },
        function(response){
            console.log(response.responseText);
        });
    }
    
    static getInterfaceNamesPackage(onSuccess){
        RestApi.get('LanguageSelection', 'getInterfaceNames', {}, 
        function(response){
            var data = JSON.parse(response);
            onSuccess(data);
        },
        function(response){
            console.log(response.responseText);
        });
    }
    
    static upload(controller, task, fileInput, onSuccess, onError, onComplete){
        var file = fileInput.files[0];
        var formData = new FormData();
        formData.append('file', file);
        formData.append('task', task);
        formData.append('controller', controller);
        var address = RestApi.getAddress();
        var response = '';
        var body = document.body;
        var div = RestApi.blockPage(body);
        $.ajax({
                url: address,
                type: 'POST',
                data: formData,
                processData: false,  // tell jQuery not to process the data
                contentType: false,  // tell jQuery not to set contentType
                success: function (data) {
                    if(onSuccess === undefined){
                        try{
                            response = JSON.parse(data); 
                            console.log(response);
                        }
                        catch(e){
                            alert(e);
                        }
                    }
                    else{
                        onSuccess(data);
                    }
                },
                error: function (data) {
                    if(onError === undefined){
                        console.log(data);
                    }
                    else{
                        onError(data);
                    }
                },
                complete: function (data) {
                    RestApi.unblockPage(body, div);
                    if(onComplete !== undefined){
                        onComplete(data);
                    }
                }
        });
        return response;
    }
    
    static blockPage(body){
        var div = document.createElement('div');
        div.setAttribute('class', 'page-blocker');
        var modal = document.createElement('div');
        modal.setAttribute('class', 'page-blocker-modal');
        modal.textContent = 'Processing please wait....';
        div.appendChild(modal);
        body.appendChild(div);
        return div;
    }
    
    static unblockPage(body, div){
        if(body.contains(div)){
            body.removeChild(div);
        }
    }
}
