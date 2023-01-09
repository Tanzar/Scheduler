/* 
 * This code is free to use, just remember to give credit.
 */



function init() {
    RestApi.getInterfaceNamesPackage(function(language){
        
        $('#fix').click(function(){
            var fields = [
                {type: 'display', title: language.warning_fix},
                {type: 'checkbox', title: language.continue_questionmark, variable: 'confirm', required: true}
            ];
            openModalBox(language.warning, fields, language.confirm, function(data){
                if(data.confirm){
                    RestApi.post('AdminPanelSystem', 'runFix', data,
                        function(response){
                            var data = JSON.parse(response);
                            console.log(data);
                            alert(data.message);
                        },
                        function(response){
                            console.log(response.responseText);
                            alert(response.responseText);
                    });
                }
            });
        });
        
        $('#backup').click(function(){
            var fields = [
                {type: 'display', title: language.warning_backup},
                {type: 'checkbox', title: language.continue_questionmark, variable: 'confirm', required: true}
            ];
            openModalBox(language.warning, fields, language.confirm, function(data){
                if(data.confirm){
                    RestApi.post('AdminPanelSystem', 'runBackup', data,
                        function(response){
                            var data = JSON.parse(response);
                            console.log(data);
                            alert(data.message);
                        },
                        function(response){
                            console.log(response.responseText);
                            alert(response.responseText);
                    });
                }
            });
        });
        
        $('#clear').click(function(){
            var fields = [
                {type: 'display', title: language.warning_clear},
                {type: 'checkbox', title: language.continue_questionmark, variable: 'confirm', required: true}
            ];
            openModalBox(language.warning, fields, language.confirm, function(data){
                if(data.confirm){
                    RestApi.post('AdminPanelSystem', 'runCleaner', data,
                        function(response){
                            var data = JSON.parse(response);
                            console.log(data);
                            alert(data.message);
                        },
                        function(response){
                            console.log(response.responseText);
                            alert(response.responseText);
                    });
                }
            });
        });
        
    });
}
