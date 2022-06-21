/* 
 * This code is free to use, just remember to give credit.
 */

function getRestAddress(){
    var myScript = document.getElementById('RestApi.js');
    var path = myScript.getAttribute('src');
    var index = path.search('resources/js');
    path = path.substring(0, index);
    var address = path + 'sys/scripts/requests/rest.php';
    return address;
}