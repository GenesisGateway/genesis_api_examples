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
        scrollTop: 33
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

function getFakeData(trx_type) {
    future_date = faker.date.future();

    return {
        transaction_id:     generateTransactionId(),
        usage:              faker.hacker.phrase(),
        description:        faker.lorem.sentence(),
        remote_ip:          faker.internet.ip(),
        amount:             randomFromInterval(1337, 2048),
        currency:           getTrxCurrency(trx_type),
        card_holder:        faker.name.findName(),
        card_number:        getTrxCardNumber(trx_type),
        cvv:                randomFromInterval(100,999),
        expiration_month:   future_date.getMonth(),
        expiration_year:    future_date.getFullYear() + 1,
        customer_email:     faker.internet.email(),
        customer_phone:     getTrxBillCustomerPhone(),

        billing: {
            first_name: faker.name.firstName(),
            last_name:  faker.name.lastName(),
            address1:   faker.address.streetAddress(),
            address2:   faker.address.secondaryAddress(),
            zip_code:   faker.address.zipCode(),
            city:       faker.address.city(),
            state:      faker.address.stateAbbr(),
            country:    getTrxCountryCode(trx_type),
        },

        shipping: {
            first_name: faker.name.firstName(),
            last_name:  faker.name.lastName(),
            address1:   faker.address.streetAddress(),
            address2:   faker.address.secondaryAddress(),
            zip_code:   faker.address.zipCode(),
            city:       faker.address.city(),
            state:      faker.address.stateAbbr(),
            country:    getTrxCountryCode(trx_type),
        },

        start_date:      faker.internet.password(),

        notification_url:   'http://www.dummy.com/url/notification',
        return_success_url: 'http://www.dummy.com/url/success',
        return_failure_url: 'http://www.dummy.com/url/failure',
        return_cancel_url:  'http://www.dummy.com/url/cancel'
    }
}

function generateTransactionId() {
    var params = {
        length: 25,
        pool: 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
    }

    return chance.string(params);
}

function getTrxBillCustomerPhone() {
    return faker.phone.phoneNumberFormat().replace(new RegExp('-', 'g'), '');
}

function getTrxCountryCode(trx_type) {
    var trx_country = {
        abn_ideal:               'NL',
        inpay:                   'DE',
        poli:                    'AU',
        sdd_init_recurring_sale: 'DE',
        sdd_sale:                'DE',
        sdd_payout:              'DE',
        sofort:                  'DE'
    };

    if (trx_type in trx_country) {
        return trx_country[trx_type];
    }

    return 'US';
}

function getTrxCurrency(trx_type) {
    var trx_currency = {
        abn_ideal:               'EUR',
        ezeewallet:              'EUR',
        inpay:                   'EUR',
        p24:                     'PLN',
        paybyvoucher_sale:       'EUR',
        poli:                    'AUD',
        ppro:                    'EUR',
        sdd_init_recurring_sale: 'EUR',
        sdd_sale:                'EUR',
        sdd_payout:              'EUR',
        sofort:                  'EUR'
    };

    if (trx_type in trx_currency) {
        return trx_currency[trx_type];
    }

    return 'USD';
}

function getTrxCardNumber(trx_type) {
    return isThreeDTransaction(trx_type) ? '4711100000000000' : '4200000000000000';
}

function isThreeDTransaction(trx_type) {
    var threeDTransactions = [
        'authorize3d',
        'sale3d',
        'init_recurring_sale3d'
    ];

    return $.inArray(trx_type, threeDTransactions) > -1;
}

// Get the last element in Array
if (!Array.prototype.last){
    Array.prototype.last = function(){
        return this[this.length - 1];
    };
};