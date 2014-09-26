$(document).ready(function(e) {

    $("#contactForm").submit(function() {
        $.post("getcontact.php", $("#contactForm").serialize()) //Serialize looks good name=textInNameInput&&telefon=textInPhoneInput---etc
            .done(function(data) {
                if (data.trim().length >0)
                {
                    $("#sent").text("Error");
                }
                else {
                    $("#sent").text("Success");
                }
            });
        return false;
    })
});