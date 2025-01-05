$('#avancement_corps').change(function () {
            var corpsSelector = $(this); 
    
            // Request the neighborhoods of the selected city.
           $.ajax({
                url: "corps_grades",
                type: "GET",
                dataType: "JSON",
                data: {
                    corpsid: corpsSelector.val()
                },
                success: function (grade) {
                    var gradeSelect = $("#avancement_grade");

                    // Remove current options
                    gradeSelect.html('');
                    
                    // Empty value ...
                    gradeSelect.append('<option value>------Selectionner un Grade------</option>');

                    $.each(grade, function (key, grade) {
                        gradeSelect.append('<option value="' + grade.id + '">' + grade.designationFr + '</option>');
                    });
                },
                error: function (err) {
                    alert("An error ocurred while loading data ...");
                }
            });
        });


$('#avancement_grade').change(function () {
            var gradeSelector = $(this);
            
            // Request the neighborhoods of the selected city.
           $.ajax({
                url: "grades_echelon",
                type: "GET",
                dataType: "JSON",
                data: {
                    gradeid: gradeSelector.val()
                },
                success: function (echelons) {
                    var echelonSelect = $("#avancement_echelon");

                    // Remove current options
                    echelonSelect.html('');
                    
                    // Empty value ...
                    echelonSelect.append('<option value>------Selectionner Indice-Echelon------</option>');

                    $.each(echelons, function (key, echelon) {
                        echelonSelect.append('<option value="' + echelon.id + '">' + echelon.designation + '</option>');
                    });
                },
                error: function (err) {
                    alert("An error ocurred while loading data ...");
                }
            });
        });



$('#personnel_corpsId').change(function () {
            var corpsSelector = $(this); 
    
            // Request the neighborhoods of the selected city.
           $.ajax({
                url: "corps_grades",
                type: "GET",
                dataType: "JSON",
                data: {
                    corpsid: corpsSelector.val()
                },
                success: function (grade) {
                    var gradeSelect = $("#personnel_gradeId");

                    // Remove current options
                    gradeSelect.html('');
                    
                    // Empty value ...
                    gradeSelect.append('<option value>------Selectionner un Grade------</option>');

                    $.each(grade, function (key, grade) {
                        gradeSelect.append('<option value="' + grade.id + '">' + grade.designationFr + '</option>');
                    });
                },
                error: function (err) {
                    alert("An error ocurred while loading data ...");
                }
            });
        });


$('#personnel_gradeId').change(function () {
            var gradeSelector = $(this);
            
            // Request the neighborhoods of the selected city.
           $.ajax({
                url: "grades_echelon",
                type: "GET",
                dataType: "JSON",
                data: {
                    gradeid: gradeSelector.val()
                },
                success: function (echelons) {
                    var echelonSelect = $("#personnel_echelonId");

                    // Remove current options
                    echelonSelect.html('');
                    
                    // Empty value ...
                    echelonSelect.append('<option value>------Selectionner Indice-Echelon------</option>');

                    $.each(echelons, function (key, echelon) {
                        echelonSelect.append('<option value="' + echelon.id + '">' + echelon.designation + '</option>');
                    });
                },
                error: function (err) {
                    alert("An error ocurred while loading data ...");
                }
            });
        });