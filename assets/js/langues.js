$('.flag').on("click",function () {
    var langue = $(this).attr("value");
     $.ajax({
        type: 'POST',
        url: $('#path-to-langue').data("href"),
        data: {langue: langue},
        success: function (data) {location.reload();}
            });
})




