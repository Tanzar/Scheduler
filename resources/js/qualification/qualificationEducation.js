/* 
 * This code is free to use, just remember to give credit.
 */

function init() {
    RestApi.getInterfaceNamesPackage(function(language){
        var personsTable = initPersonsTable(language);
        var schoolsTable = initEducationsTable(language, personsTable);
        var coursesTable = initCoursesTable(language, personsTable);
        initPersonsTableControls(language, personsTable, schoolsTable, coursesTable);
        
        $('#searchButton').click(function(){
            personsTable.setDatasource({ 
                method: 'post', 
                address: getRestAddress(), 
                data: { 
                    controller: 'Qualification', 
                    task: 'getPersons',
                    name: document.getElementById('nameSearch').value,
                    surname: document.getElementById('surnameSearch').value,
                } 
            });
        });
    });
}

function initPersonsTable(language){
    var div = document.getElementById('persons');
    
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30},
            { title: language.name_person, variable: 'name', width: 100, minWidth: 100},
            { title: language.surname, variable: 'surname', width: 100, minWidth: 100},
            { title: language.birthplace, variable: 'birthplace', width: 100, minWidth: 100},
            { title: language.birthdate, variable: 'birthday', width: 80, minWidth: 80}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'Qualification', 
                task: 'getPersons',
                name: '---',
                surname: '---'
            } 
        }
    };
    var datatable = new Datatable(div, config);
    return datatable;
}


function initEducationsTable(language, personTable){
    var div = document.getElementById('schools');
    
    var config = {
        columns : [
            { title: language.degree, variable: 'degree', width: 100, minWidth: 100},
            { title: language.specialization, variable: 'specialization', width: 150, minWidth: 150},
            { title: language.year, variable: 'year', width: 50, minWidth: 50},
            { title: language.school, variable: 'school', width: 200, minWidth: 200}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'Qualification', 
                task: 'getPersonEducations',
                id_person: 0
            } 
        }
    };
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        var person = personTable.getSelected();
        if(person !== undefined){
            var fields = [
                {type: 'text', title: language.degree, variable: 'degree', limit: 50, required: true},
                {type: 'textarea', title: language.specialization, variable: 'specialization', limit: 255, width: 30, height: 3, required: true},
                {type: 'number', title: language.year, variable: 'year', min: 0, required: true},        
                {type: 'textarea', title: language.school, variable: 'school', limit: 255, width: 30, height: 3, required: true},
            ];
            openModalBox(language.new_school, fields, language.save, function(data){
                data.id_person = person.id;
                RestApi.post('Qualification', 'saveEducation', data,
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
        }
    });
    datatable.addActionButton(language.edit, function(selected){
        if(selected !== undefined){
            var person = personTable.getSelected();
            if(person !== undefined){
                var fields = [
                    {type: 'text', title: language.degree, variable: 'degree', limit: 50, required: true},
                    {type: 'textarea', title: language.specialization, variable: 'specialization', limit: 255, width: 30, height: 3, required: true},
                    {type: 'number', title: language.year, variable: 'year', min: 0, required: true},        
                    {type: 'textarea', title: language.school, variable: 'school', limit: 255, width: 30, height: 3, required: true},
                ];
                openModalBox(language.edit_school, fields, language.save, function(data){
                    data.id_person = person.id;
                    RestApi.post('Qualification', 'saveEducation', data,
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
        }
        else{
            alert(language.select_school);
        }
    });
    datatable.addActionButton(language.remove, function(selected){
        if(selected !== undefined){
            RestApi.post('Qualification', 'removeEducation', {id: selected.id},
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
            alert(language.select_school)
        }
    });
    return datatable;
}

function initCoursesTable(language, personTable){
    var div = document.getElementById('courses');
    
    var config = {
        columns : [
            { title: language.name, variable: 'name', width: 200, minWidth: 200}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'Qualification', 
                task: 'getPersonCourses',
                id_person: 0
            } 
        }
    };
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        var person = personTable.getSelected();
        if(person !== undefined){
            var fields = [
                {type: 'textarea', title: language.name, variable: 'name', limit: 255, width: 30, height: 3, required: true}
            ];
            openModalBox(language.new_course, fields, language.save, function(data){
                data.id_person = person.id;
                RestApi.post('Qualification', 'saveCourse', data,
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
        }
    });
    datatable.addActionButton(language.edit, function(selected){
        if(selected !== undefined){
            var person = personTable.getSelected();
            if(person !== undefined){
                var fields = [
                    {type: 'textarea', title: language.name, variable: 'name', limit: 255, width: 30, height: 3, required: true}
                ];
                openModalBox(language.edit_course, fields, language.save, function(data){
                    data.id_person = person.id;
                    RestApi.post('Qualification', 'saveCourse', data,
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
        }
        else{
            alert(language.select_course);
        }
    });
    datatable.addActionButton(language.remove, function(selected){
        if(selected !== undefined){
            RestApi.post('Qualification', 'removeCourse', {id: selected.id},
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
            alert(language.select_course)
        }
    });
    return datatable;
}

function initPersonsTableControls(language, datatable, schoolsTable, coursesTable) {
    datatable.addActionButton(language.add, function(){
        RestApi.post('Qualification', 'getEducationLevels', {},
            function(response){
                var data = JSON.parse(response);
                var fields = [
                    {type: 'text', title: language.name_person, variable: 'name', limit: 50, required: true},
                    {type: 'text', title: language.surname, variable: 'surname', limit: 50, required: true},
                    {type: 'text', title: language.birthplace, variable: 'birthplace', limit: 150},
                    {type: 'date', title: language.birthdate, variable: 'birthday'},
                    {type: 'select', title: language.select_education_level, variable: 'education_level', options: data, required: true},
                    {type: 'text', title: language.home_town, variable: 'home_town', limit: 50},
                    {type: 'text', title: language.post_code, variable: 'post_code', limit: 10},
                    {type: 'text', title: language.street, variable: 'street', limit: 50},
                    {type: 'text', title: language.home_number, variable: 'home_number', limit: 15}
                ];
                openModalBox(language.new_person, fields, language.save, function(data){
                    RestApi.post('Qualification', 'savePerson', data,
                        function(response){
                            var data = JSON.parse(response);
                            console.log(data);
                            alert(data.message);
                            datatable.refresh();
                            schoolsTable.setDatasource({ 
                                method: 'post', 
                                address: getRestAddress(), 
                                data: { 
                                    controller: 'Qualification', 
                                    task: 'getPersonEducations',
                                    id_person: 0
                                } 
                            });
                            coursesTable.setDatasource({ 
                                method: 'post', 
                                address: getRestAddress(), 
                                data: { 
                                    controller: 'Qualification', 
                                    task: 'getPersonCourses',
                                    id_person: 0
                                } 
                            });
                        },
                        function(response){
                            console.log(response.responseText);
                            alert(response.responseText);
                    });
                });
            });
    });
    datatable.addActionButton(language.edit, function(selected){
        if(selected !== undefined){
            RestApi.post('Qualification', 'getEducationLevels', {},
                function(response){
                    var data = JSON.parse(response);
                    var fields = [
                        {type: 'text', title: language.name_person, variable: 'name', limit: 50, required: true},
                        {type: 'text', title: language.surname, variable: 'surname', limit: 50, required: true},
                        {type: 'text', title: language.birthplace, variable: 'birthplace', limit: 150},
                        {type: 'date', title: language.birthdate, variable: 'birthday'},
                        {type: 'select', title: language.select_education_level, variable: 'education_level', options: data, required: true},
                        {type: 'text', title: language.home_town, variable: 'home_town', limit: 50},
                        {type: 'text', title: language.post_code, variable: 'post_code', limit: 10},
                        {type: 'text', title: language.street, variable: 'street', limit: 50},
                        {type: 'text', title: language.home_number, variable: 'home_number', limit: 15}
                    ];
                    openModalBox(language.edit_person, fields, language.save, function(data){
                        RestApi.post('Qualification', 'savePerson', data,
                            function(response){
                                var data = JSON.parse(response);
                                console.log(data);
                                alert(data.message);
                                datatable.refresh();
                                schoolsTable.setDatasource({ 
                                    method: 'post', 
                                    address: getRestAddress(), 
                                    data: { 
                                        controller: 'Qualification', 
                                        task: 'getPersonEducations',
                                        id_person: 0
                                    } 
                                });
                                coursesTable.setDatasource({ 
                                    method: 'post', 
                                    address: getRestAddress(), 
                                    data: { 
                                        controller: 'Qualification', 
                                        task: 'getPersonCourses',
                                        id_person: 0
                                    } 
                                });
                            },
                            function(response){
                                console.log(response.responseText);
                                alert(response.responseText);
                        });
                    }, selected);
                });
        }
        else{
            alert(language.select_person);
        }
    });
    datatable.addActionButton(language.remove, function(selected){
        if(selected !== undefined){
            RestApi.post('Qualification', 'removePerson', {id: selected.id},
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    datatable.refresh();
                    schoolsTable.setDatasource({ 
                        method: 'post', 
                        address: getRestAddress(), 
                        data: { 
                            controller: 'Qualification', 
                            task: 'getPersonEducations',
                            id_person: 0
                        } 
                    });
                    coursesTable.setDatasource({ 
                        method: 'post', 
                        address: getRestAddress(), 
                        data: { 
                            controller: 'Qualification', 
                            task: 'getPersonCourses',
                            id_person: 0
                        } 
                    });
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }
        else{
            alert(language.select_person)
        }
    });
    datatable.setOnSelect(function(selected){
        if(selected !== undefined){
            schoolsTable.setDatasource({ 
                method: 'post', 
                address: getRestAddress(), 
                data: { 
                    controller: 'Qualification', 
                    task: 'getPersonEducations',
                    id_person: selected.id
                } 
            });
            coursesTable.setDatasource({ 
                method: 'post', 
                address: getRestAddress(), 
                data: { 
                    controller: 'Qualification', 
                    task: 'getPersonCourses',
                    id_person: selected.id
                } 
            });
        }
    });
    datatable.setOnUnselect(function(selected){
        schoolsTable.setDatasource({ 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'Qualification', 
                task: 'getPersonEducations',
                id_person: selected.id
            } 
        });
        coursesTable.setDatasource({ 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'Qualification', 
                task: 'getPersonCourses',
                id_person: selected.id
            } 
        });
    });
}