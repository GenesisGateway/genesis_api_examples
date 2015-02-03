function randomFromInterval(min,max) {
    return Math.floor(Math.random() * (max - min + 1) + min);
}

function setCodeLang(selectedLang) {
    var currentlySelectedCodeLang = getSelectedCodeLang();
    if (!selectedLang && currentlySelectedCodeLang) {
        selectedLang = currentlySelectedCodeLang;
    }

    // Empty all "code" fields
    $('code').text('');

    // Remove the active flag from language
    $('.lang').each(function() {
        $(this).removeClass('active');
    });
    // Set active flag only to the selected langauge
    $('.lang-' + selectedLang).toggleClass('active');

    // Remove the existing class "language" and set the selected one
    $('code.lang-example').attr('class','lang-example hljs').addClass(selectedLang);

    // Save our choice
    setCookie('codeLang', selectedLang);

    // Re-submit to fetch the new templates
    if ($('.init-notification').length == 0)
        $('form.genesis-api-example').submit();
}

function hasSelectedCodeLang() {
    var selectedCodeLang = getSelectedCodeLang();
    return selectedCodeLang != 'undefined' && !!selectedCodeLang;
}

function getSelectedCodeLang() {
    return getCookie('codeLang');
}

function getCookie(name) {
    var value = "; " + document.cookie;
    var parts = value.split("; " + name + "=");
    if (parts.length == 2) return parts.pop().split(";").shift();
}

function setCookie(c_name, value, exdays) {
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" +
        exdate.toUTCString());
    document.cookie = c_name + "=" + c_value;
}

function showExample(formObj) {
    updateHighlight();

    jQuery('.code-result, .request-result, .response-result').fadeIn(750);

    $('html, body').animate({
        scrollTop: $("body").offset().top - 50
    }, 750);
}

function updateHighlight() {
    $('pre code').each(function(i, block) {
        hljs.highlightBlock(block);
    });
}

function getTransactionCode(selectedCodeLang, xmlRequest) {
    var jsonRequest = new X2JS().xml_str2json(xmlRequest);
    return getConfig()
        .then(function(config) {
            return requestCodeRenderer.render({
                lang:       selectedCodeLang,
                config:     config,
                request:    jsonRequest
            });
        });
}

function getConfig() {
    return $.get("config/default.ini")
        .then(function(content) {
            token = /token = (\w*)/.exec(content)[1];
            username = /username = (\w*)/.exec(content)[1];
            password = /password = (\w*)/.exec(content)[1];

            return { terminal_token: token, api_login: username, api_password: password };
        });
}

function getFakeData() {
    future_date = faker.date.future();
    return {
        transaction_id:     faker.internet.password(),
        usage:              faker.hacker.phrase(),
        description:        faker.lorem.sentence(),
        remote_ip:          faker.internet.ip(),
        amount:             randomFromInterval(1337, 2048),
        currency:           'USD',
        card_holder:        faker.name.findName(),
        card_number:        '4200000000000000',
        cvv:                randomFromInterval(100,999),
        expiration_month:   future_date.getMonth(),
        expiration_year:    future_date.getFullYear(),
        customer_email:     faker.internet.email(),
        customer_phone:     faker.phone.phoneNumberFormat(),

        billing: {
            first_name: faker.name.firstName(),
            last_name:  faker.name.lastName(),
            address1:   faker.address.streetAddress(),
            address2:   faker.address.secondaryAddress(),
            zip_code:   faker.address.zipCode(),
            city:       faker.address.city(),
            state:      faker.address.stateAbbr(),
            country:    'US',
        },

        shipping: {
            first_name: faker.name.firstName(),
            last_name:  faker.name.lastName(),
            address1:   faker.address.streetAddress(),
            address2:   faker.address.secondaryAddress(),
            zip_code:   faker.address.zipCode(),
            city:       faker.address.city(),
            state:      faker.address.stateAbbr(),
            country:    'US',
        },

        start_date:      faker.internet.password(),

        notification_url:   'http://www.dummy.com/url/notification',
        return_success_url: 'http://www.dummy.com/url/success',
        return_failure_url: 'http://www.dummy.com/url/failure',
        return_cancel_url:  'http://www.dummy.com/url/cancel'
    }
}

// Get the last element in Array
if (!Array.prototype.last){
    Array.prototype.last = function(){
        return this[this.length - 1];
    };
};