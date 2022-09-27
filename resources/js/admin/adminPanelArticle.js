/* 
 * This code is free to use, just remember to give credit.
 */


function init() {
    RestApi.getInterfaceNamesPackage(function(language){
        var articlesTable = new ArticlesTable(language);
        document.getElementById('selectDate').onclick = function(){
            var username = document.getElementById('selectUser').value;
            var year = document.getElementById('selectYear').value;
            articlesTable.refresh(username, year);
        }
        
        articleFormsTable(language);
    });
}

function ArticlesTable(language){
    var div = document.getElementById('articles');
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'AdminPanelArticle',
            task: 'getAllUserArticles',
            username: '',
            year: 0
        }
    };
    var config = {
        columns: [
            {title: 'ID', variable: 'id', width: 30, minWidth: 30},
            {title: language.active, variable: 'active', width: 50, minWidth: 50},
            {title: language.date, variable: 'date', width: 70, minWidth: 70},
            {title: language.art_41_form, variable: 'art_41_form_short', width: 100, minWidth: 100},
            {title: language.position_groups, variable: 'position_group', width: 100, minWidth: 100},
            {title: language.position, variable: 'position', width: 100, minWidth: 100},
            {title: language.outside_company, variable: 'outside_company_text', width: 70, minWidth: 70},
            {title: language.company_name, variable: 'company_name', width: 100, minWidth: 100}
        ],
        dataSource: datasource
    }
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelArticle', 'changeArticleStatus', {id: selected.id},
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    datatable.refresh();
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }
        else{
            alert(language.select_ticket);
        }
    });
    
    this.refresh = function(username, year){
        datatable.setDatasource({
            method: 'get',
            address: getRestAddress(),
            data: {
                controller: 'AdminPanelArticle',
                task: 'getAllUserArticles',
                username: username,
                year: year
            }
        });
    }
}

function articleFormsTable(language){
    var div = document.getElementById('articleForms');
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'AdminPanelArticle',
            task: 'getAllArticleForms'
        }
    };
    var config = {
        columns: [
            {title: 'ID', variable: 'id', width: 30, minWidth: 30},
            {title: language.active, variable: 'active', width: 50, minWidth: 50},
            {title: language.name, variable: 'name', width: 250, minWidth: 250},
            {title: language.short, variable: 'short', width: 150, minWidth: 150},
            {title: language.symbol, variable: 'symbol', width: 50, minWidth: 50},
            {title: language.require_application_info, variable: 'require_application_info', width: 100, minWidth: 100}
        ],
        dataSource: datasource
    }
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        var fields = [
            {type: 'text', title: language.name, variable: 'name', limit: 255, required: true},
            {type: 'text', title: language.short, variable: 'short', limit: 100, required: true},
            {type: 'text', title: language.symbol, variable: 'symbol', limit: 5, required: true},
            {type: 'checkbox', title: language.require_application_info, variable: 'require_application_info'}
        ];
        openModalBox(language.new_art_41_form, fields, language.save, function(data){
            RestApi.post('AdminPanelArticle', 'saveArticleForm', data,
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    datatable.refresh();
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        });
    });
    datatable.addActionButton(language.edit, function(selected){
        if(selected === undefined){
            alert(language.select_art_41_form);
        }
        else{
            var fields = [
                {type: 'text', title: language.name, variable: 'name', limit: 255, required: true},
                {type: 'text', title: language.short, variable: 'short', limit: 100, required: true},
                {type: 'text', title: language.symbol, variable: 'symbol', limit: 5, required: true},
                {type: 'checkbox', title: language.require_application_info, variable: 'require_application_info'}
            ];
            openModalBox(language.edit_art_41_form, fields, language.save, function(data){
                RestApi.post('AdminPanelArticle', 'saveArticleForm', data,
                    function(response){
                        var data = JSON.parse(response);
                        console.log(data);
                        alert(data.message);
                        datatable.refresh();
                    },
                    function(response){
                        console.log(response.responseText);
                        alert(response.responseText);
                });
            }, selected);
        }
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected === undefined){
            alert(language.select_art_41_form);
        }
        else{
            RestApi.post('AdminPanelArticle', 'changeArticleFormStatus', {id: selected.id},
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    datatable.refresh();
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }
    });
}