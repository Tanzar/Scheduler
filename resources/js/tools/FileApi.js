/* 
 * This code is free to use, just remember to give credit.
 */

class FileApi{
    
    static get(controller, task, data, newPage){
        var address = FileApi.getAddress();
        var form = document.createElement('form');
        form.setAttribute('method', 'get');
        form.setAttribute('action', address);
        FileApi.addData(form, controller, task, data);
        if(newPage === undefined || newPage === true){
            form.target = '_blank';
        }
        document.body.appendChild(form);
        form.submit();
    }
    
    static post(controller, task, data, newPage){
        var address = FileApi.getAddress();
        var form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', address);
        FileApi.addData(form, controller, task, data);
        if(newPage === undefined || newPage === true){
            form.target = '_blank';
        }
        document.body.appendChild(form);
        form.submit();
    }
    
    static getAddress(){
        var myScript = document.getElementById('FileApi.js');
        var path = myScript.getAttribute('src');
        var index = path.search('resources/js');
        path = path.substring(0, index);
        var address = path + 'sys/scripts/requests/file.php';
        return address;
    }
    
    static addData(form, controller, task, data){
        var input = FileApi.makeHiddenInput('controller', controller);
        form.appendChild(input);
        input = FileApi.makeHiddenInput('task', task);
        form.appendChild(input);
        var keys = Object.keys(data);
        keys.forEach((element) => {
            input = FileApi.makeHiddenInput(element, data[element]);
            form.appendChild(input);
        });
    }
    
    static makeHiddenInput(name, value){
        var input = document.createElement('input');
        input.setAttribute('type', 'hidden');
        input.setAttribute('name', name);
        input.setAttribute('value', value);
        return input;
    }
}
