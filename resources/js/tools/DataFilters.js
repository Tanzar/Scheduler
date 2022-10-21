/* 
 * Creates filter for data.
 * Allowing you to set conditions for dataset and filter unwanted data.
 * Variables:
 * - div - HTML element where filter will be set
 * - onChange - (optional) allows to set function called on adding or removing elements form list
 * 
 * Methods to add filters on list (parameters are the same for each):
 * - addEqual() - filters data where column value is equal to passed value
 * - addLess() - filters data where column value is less than passed value
 * - addMore() - filters data where column value is more than passed value
 * - addContains() - filters data where column value contains passed value
 * - addStartsWith() - filters data where column value starts with passed value
 * - addEndsWith() - filters data where column value ends with passed value
 * 
 * Parameters (same for each add method):
 * - column - name of column where value is compared
 * - value - compared value
 * - text - text displayed in filter
 */


function DataFilters(div, onChange){
    this.div = div;
    var filters = [];
    this.div.setAttribute('class', 'datafilters');
    
    if(onChange === undefined){
        onChange = function(filters){
            console.log(filters);
        }
    }
    
    this.addEqual = function (column, value, text) {
        addElement(column, '=', value, text);
    }
    
    this.addLess = function (column, value, text) {
        addElement(column, '<', value, text);
    }
    
    this.addMore = function (column, value, text) {
        addElement(column, '>', value, text);
    }
    
    this.addContains = function (column, value, text) {
        addElement(column, 'contains', value, text);
    }
    
    this.addStartsWith = function (column, value, text) {
        addElement(column, 'starts', value, text);
    }
    
    this.addEndsWith = function (column, value, text) {
        addElement(column, 'ends', value, text);
    }
    
    function addElement(column, operation, value, text) {
        var item = {
            column: column,
            operation: operation,
            value: value
        }
        filters.push(item);
        
        var element = document.createElement('div');
        element.setAttribute('class', 'datafilters-element');
        var textNode = document.createTextNode(text);
        element.appendChild(textNode);
        var close = document.createElement('span');
        close.setAttribute('class', 'datafilters-close');
        close.innerHTML = '&times;';
        element.appendChild(close);
        close.onclick = function() {
            var index = filters.indexOf(item);
            if(index > -1){
                filters.splice(index, 1);
                div.removeChild(element);
                onChange(filters);
            }
        }
        div.appendChild(element);
        onChange(filters);
    }
    
    this.toJSON = function(){
        return filters;
    }
}