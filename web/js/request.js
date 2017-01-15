$(document).ready(function () {
    $("#send").submit(function (e) {
        e.preventDefault();
        $("#btnSubmit").prop("disabled", true);
        $.ajax({
            type: "POST",
            url: "/send",
            data: JSON.stringify({"color": $("#color").val(), "searchQuery": $("#searchQuery").val()}),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (data) {

                $("#table").html("");
                var imgElements = data.images.map(function (image) {
                    var img = document.createElement("img");
                    var a = document.createElement('a');
                    var label = document.createElement('label');
                    img.src = "data:image/jpg;base64," + image.image;
                    a.href = image.url;
                    label.append(image.coff);
                    a.append(img);
                    a.append(label);
                    return a;
                });

                $("#table").append(imgElements);
                $("#btnSubmit").prop('disabled', false);
            },
            failure: function (errMsg) {
                $("#btnSubmit").prop('disabled', false);
                alert(errMsg);
            }
        });
    });
});
