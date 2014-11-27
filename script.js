function randomFromInterval(min,max) {
    return Math.floor(Math.random() * (max - min + 1) + min);
}

function setCodeLang(selectedLang) {
    var currentlySelectedCodeLang = getSelectedCodeLang();
    if (!selectedLang && currentlySelectedCodeLang) {
        selectedLang = currentlySelectedCodeLang;
    }

    $('.lang').each(function() {
        $(this).removeClass('active');
    });
    $('.lang-' + selectedLang).toggleClass('active');

    setCookie('codeLang', selectedLang);
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

    formObj.find('.code-drops').fadeIn(750);

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
    return requestCodeRenderer.render({ lang: selectedCodeLang, config: null, request: jsonRequest});
}

var requestCodeRenderer = (function() {
    return {
        render: function (data) {
            switch (data.lang) {
                case 'cs':
                    return renderCsRequest(data.config, data.request);
                case 'php':
                case 'python':
                case 'perl':
                case 'ruby':
                case 'java':
                case 'node':
                default:
                    break;
            }
        }
    };

    function renderCsRequest(config, request) {
        return getCsMustacheTemplate()
            .then(function(template) {
                var context = getCsMustacheContext(config, request);
                var content = Mustache.render(template.main, context, template.partials);
                return content;
            });
    }

    function getCsMustacheTemplate() {
        return $.when(getCodeTemplate("cs", "request"), getCodeTemplate("cs", "initialization"))
            .then(function (request, initialization) {
                return {
                    main: request[0],
                    partials: {
                        initialization: initialization[0]
                    }
                };
            });
    }

    function getCsMustacheContext(config, jsonRequest) {
        function isEntity(value) {
            return typeof value === "object";
        }

        function isNumber(value) {
            return typeof value === "number";
        }

        function isItsSingleElementAnArray(value) {
            var keys = Object.keys(value);
            if (keys.length != 1) {
                return false;
            }

            var childValue = value[keys[0]];
            if (childValue instanceof Array) {
                return true;
            }
            return false;
        }

        function getLastChild(value) {
            var lastChild = {};
            $.each(value, function(name, value) {
                lastChild.index = name;
                lastChild.value = value;
            });
            return lastChild;
        }

        function build(node, level, key, valueType, value) {
            node.name = key;
            node.indentation = Array(level).join("\t");

            var nodeValue = {};
            nodeValue.is_root = level === 1;
            nodeValue.is_entity = isEntity(value);
            nodeValue.is_last = false;

            if (!nodeValue.is_entity) {
                nodeValue.value = value;
            } else {
                nodeValue.class = valueType;
                nodeValue.object = [];
                $.each(value, function(index, childValue) {
                    var childNode = {};
                    if (isNumber(index)) {
                        build(childNode, level + 1, false, valueType, childValue);
                    } else {
                        if (isEntity(childValue) && isItsSingleElementAnArray(childValue)) {
                            var subChild = getLastChild(childValue);
                            build(childNode, level + 1, index, subChild.index, subChild.value);
                        } else {
                            build(childNode, level + 1, index, index, childValue);
                        }
                    }
                    nodeValue.object.push(childNode);
                });
            }

            node.value = nodeValue;
        };

        var root = {};
        $.each(jsonRequest, function(name, value) {
            build(root, 1, name, name, value);
        });
        root.name = "var request";

        return root;
    }

    function getCodeTemplate(lang, name) {
        var path = 'assets/templates/code/' + lang + '/' + name + '.mustache';
        return $.get(path);
    }
})();

function getFakeData() {
    future_date = faker.date.future();
    return {
        transaction_id: faker.internet.password(),
        usage: faker.hacker.phrase(),
        description: faker.lorem.sentence(),
        remote_ip: faker.internet.ip(),
        amount: randomFromInterval(1337, 2048),
        currency: 'USD',
        card_holder: faker.name.findName(),
        card_number: '4200000000000000',
        cvv: randomFromInterval(100,999),
        expiration_month: future_date.getMonth(),
        expiration_year: future_date.getFullYear(),
        customer_email: faker.internet.email(),
        customer_phone: faker.phone.phoneNumberFormat(),
        billing: {
            first_name: faker.name.firstName(),
            last_name: faker.name.lastName(),
            address1: faker.address.streetAddress(),
            address2: faker.address.secondaryAddress(),
            zip_code: faker.address.zipCode(),
            city: faker.address.city(),
            state: faker.address.stateAbbr(),
            country: 'US',
        },
        shipping: {
            first_name: faker.name.firstName(),
            last_name: faker.name.lastName(),
            address1: faker.address.streetAddress(),
            address2: faker.address.secondaryAddress(),
            zip_code: faker.address.zipCode(),
            city: faker.address.city(),
            state: faker.address.stateAbbr(),
            country: 'US',
        },
        notification_url: 'http://www.dummy.com/notification_url',
        return_success_url: 'http://www.dummy.com/success',
        return_failure_url: 'http://www.dummy.com/failure',
        return_cancel_url: 'http://www.dummy.com/cancel',
        start_date:faker.internet.password()
    }
}

