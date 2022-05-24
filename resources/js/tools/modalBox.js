/* 
 * This code is free to use, just remember to give credit.
 */


function openModalBox(title, fields, buttonText, onAccept, item){
//no cleancode here, at least for now
    if(item === undefined){
        item = {};
    }
    
    var blocker = document.createElement('div');
    blocker.setAttribute('class', 'modal-box-blocker');
    
    var modal = document.createElement('div');
    modal.setAttribute('class', 'modal-box');
    
    var close = document.createElement('span');
    close.setAttribute('class', 'modal-box-close');
    close.innerHTML = '&times;';
    
    var header = document.createElement('div');
    header.setAttribute('class', 'modal-box-header');
    
    var headerText = document.createElement('h2');
    headerText.textContent = title;
    
    var modalBody = document.createElement('div');
    for (var i = 0; i < fields.length; i++){
        var field = fields[i];
        var container = document.createElement('div');
        container.setAttribute('class', 'modal-box-field');
        switch(field.type){
            case 'text':
                var input = document.createElement('input');
                input.setAttribute('type', 'text');
                input.setAttribute('class', 'modal-box-input');
                input.setAttribute('placeholder', field.title)
                container.appendChild(input);
                input.variable = field.variable;
                input.onchange = function(){
                    item[this.variable] = this.value;
                }
                if(item[field.variable] !== undefined){
                    input.value = item[field.variable];
                }
                break;
            case 'number':
                var input = document.createElement('input');
                input.setAttribute('type', 'number');
                input.setAttribute('class', 'modal-box-input');
                input.setAttribute('placeholder', field.title)
                container.appendChild(input);
                input.variable = field.variable;
                input.onchange = function(){
                    item[this.variable] = this.value;
                }
                if(item[field.variable] !== undefined){
                    input.value = item[field.variable];
                }
                break;
            case 'select':
                var select = document.createElement('select');
                select.setAttribute('class', 'modal-box-input');
                var placeholder = document.createElement('option');
                placeholder.setAttribute('value', '');
                placeholder.disabled = true;
                placeholder.selected = true;
                placeholder.textContent = field.title;
                select.appendChild(placeholder);
                for(var k = 0; k < field.options.length; k++){
                    var optionItem = field.options[k];
                    var option = document.createElement('option');
                    option.setAttribute('value', optionItem.value);
                    option.textContent = optionItem.title;
                    select.appendChild(option);
                }
                container.appendChild(select);
                select.variable = field.variable;
                select.onchange = function(){
                    item[this.variable] = this.value;
                }
                if(item[field.variable] !== undefined){
                    select.value = item[field.variable];
                }
                break;
            case 'checkbox':
                var check = document.createElement('input');
                check.setAttribute('type', 'checkbox');
                var label = document.createElement('label');
                label.textContent = field.title;
                container.appendChild(check);
                container.appendChild(label);
                check.variable = field.variable;
                check.onchange = function(){
                    if(this.checked){
                        item[this.variable] = 1;
                    }
                    else{
                        item[this.variable] = 0;
                    }
                }
                if(item[field.variable] !== undefined){
                    check.checked = item[field.variable];
                }
                break;
            case 'display':
                var text = document.createTextNode(field.title);
                container.appendChild(text);
                break;
        }
        modalBody.appendChild(container);
    }
    
    
    
    var footer = document.createElement('div');
    footer.setAttribute('class', 'modal-box-footer');
    
    header.appendChild(close);
    header.appendChild(headerText);
    modal.appendChild(header);
    modal.appendChild(modalBody);
    
    modal.appendChild(footer);
    blocker.appendChild(modal);
    var body = document.body;
    body.appendChild(blocker);
    
    window.onclick = function(event) {
        if (event.target === blocker) {
            body.removeChild(blocker);
        }
    }
    
    close.onclick = function(){
        body.removeChild(blocker);
    }
    
    
    if(onAccept instanceof Function){
        var button = document.createElement('button');
        button.textContent = buttonText;
        button.setAttribute('class', 'modal-box-button');
        footer.appendChild(button);
        button.onclick = function(){
            body.removeChild(blocker);
            onAccept(item);
        }
    }
}