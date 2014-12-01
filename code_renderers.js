var requestCodeRenderer = (function() {
    return {
        render: function(data) {
            switch (data.lang) {
                case 'cs':
                    return new CsRequestCodeRenderer().renderRequest(
                        data.config, data.request);
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
})();

RequestCodeRenderer = {
    renderRequest: function(config, request) {
        var that = this;
        return that.getMustacheTemplate()
            .then(function(template) {
                var context = that.getMustacheContext(config, request);
                var content = Mustache.render(template.main, context, template.partials);
                return content;
            });
    },
    getCodeTemplate: function(lang, name) {
        var path = 'assets/templates/code/' + lang + '/' + name + '.mustache';
        return $.get(path);
    }
}

function CsRequestCodeRenderer() {
    this.getMockJsonRequest = getMockJsonRequest = function() {
        return {
            "wpf_payment": {
                "transaction_id": "10000",
                "amount": "10000",
                "currency": "USD",
                "usage": "jhljlkj",
                "description": "kljlkjlkjk",
                "customer_email": "hello@test.com",
                "customer_phone": "0888888888",
                "notification_url": "http://example.com",
                "return_success_url": "http://example.com/success",
                "return_failure_url": "http://example.com/fail",
                "return_cancel_url": "http://example.com/cancel",
                "billing_address": {
                    "first_name": "Test",
                    "last_name": "Test",
                    "address1": "Testing street",
                    "zip_code": "1000",
                    "city": "Testing town",
                    "country": "Tesing land"
                },
                "transaction_types": {
                    "transaction_type": [
                    "auth",
                    "auth3d",
                    "sale",
                    "sale3d"
                    ]
                }
            }
        };
    };

    this.convertSnakeToPascalCase = function(snakeCase) {
        var pascalCase = snakeCase.replace(
            /(\_\w)/g,
            function(matches) {
                return matches[1].toUpperCase();
            }
        );
        return this.capitaliseFirstLetter(pascalCase);
    };

    this.capitaliseFirstLetter = function(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    };

    this.getCsPropertyName = function(fieldName) {
        return this.convertSnakeToPascalCase(fieldName);
    };

    this.getCsClassName = function(valueType) {
        switch (valueType) {
            case "shipping_address":
            case "billing_address":
                return "Address";
            case "transaction_type":
            case "type":
                return "TransactionTypes";
            default:
                return this.convertSnakeToPascalCase(valueType);
        }
    };

    this.getCsRequestClassName = function(rootKey, rootValue) {
        if (rootKey === "chargeback_request")
            if (rootValue.start_date != undefined)
                return "MultiChargeback";
            else
                return "SingleChargeback";

        if (rootKey === "retrieval_request_request")
            if (rootValue.start_date != undefined)
                return "MultiRetrievalRequest";
            else
                return "SingleRetrievalRequest";

        if (rootKey === "reconcile")
            if (rootValue.start_date != undefined)
                return "MultiReconcile";
            else
                return "SingleReconcile";

        if (rootKey === "blacklist_request")
            return "Blacklist";

        if (rootKey === "chargeback_request")
            if (rootValue.start_date != undefined)
                return "MultiChargeback";
            else
                return "SingleChargeback";

        if (rootKey === "wpf_payment")
            return "WpfCreate";

        if (rootKey === "wpf_reconcile")
            return "WpfReconcile";

        if (rootKey === "payment_transaction")
            switch (rootValue.transaction_type) {
                case "authorize":
                    return "Authorize";
                case "authorize3d":
                    if (rootValue.mpi_params != undefined)
                        return "Authorize3DSync";
                    else
                        return "Authorize3DAsync";
                case "capture":
                    return "Capture";
                case "sale":
                    return "Sale";
                case "sale3d":
                    if (rootValue.mpi_params != undefined)
                        return "Sale3DSync";
                    else
                        return "Sale3DAsync";
                case "init_recurring_sale":
                    return "InitRecuringSale";
                case "init_recurring_sale3d":
                    if (rootValue.mpi_params != undefined)
                        return "InitRecurringSale3DSync";
                    else
                        return "InitRecurringSale3DAsync";
                case "recurring_sale":
                    return "RecurringSale";
                case "refund":
                    return "Refund";
                case "void":
                    return "Void";
                case "credit":
                    return "Credit";
                case "account_verification":
                    return "AccountVeriication";
                case "avs":
                    return "Avs";
            }

        return null;
    }

    this.getCsValue = function(type, value) {
        switch(type) {
            case "transaction_type":
            case "type":
                return "TransactionTypes." + this.convertSnakeToPascalCase(value);
            case "start_date":
            case "end_date":
            case "post_date":
            case "time":
                return "DateTime.Parse(\"" + value + "\")";
            case "amount":
                return value;
            case "currency":
                return "Iso4217CurrencyCodes." + value;
            case "country":
                return "Iso3166CountryCodes." + value;
            default:
                return '"' + value + '"';
        }
    }
}

CsRequestCodeRenderer.prototype = RequestCodeRenderer;

CsRequestCodeRenderer.prototype.getMustacheContext = function(config, request) {
    var that = this;

    request = getMockJsonRequest();

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
        if (Array.isArray(childValue)) {
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

    function itemsCount(object) {
        if (!!object.length) {
            return object.length;
        }
        return Object.keys(object).length;
    }

    function build(node, isLastChild, level, key, valueType, value) {
        if (!!key && key.length > 0) {
            node.name = that.getCsPropertyName(key);
        } else {
            node.name = key;
        }
        node.indentation = Array(level).join("\t");

        var nodeValue = {};
        nodeValue.is_root = level === 1;
        nodeValue.is_entity = isEntity(value);
        nodeValue.is_last = isLastChild;
        nodeValue.is_array = Array.isArray(value);

        if (!nodeValue.is_entity) {
            nodeValue.value = that.getCsValue(valueType, value);
        } else {
            nodeValue.class = that.getCsClassName(valueType);
            nodeValue.object = [];

            var childrenCount = itemsCount(value);
            var childNumber = 0;
            $.each(value, function(index, childValue) {
                childNumber++;
                var isLast = childNumber === childrenCount;

                var childNode = {};
                if (isNumber(index)) {
                    build(childNode, isLast, level + 1, false, valueType, childValue);
                } else {
                    if (isEntity(childValue) && isItsSingleElementAnArray(childValue)) {
                        var subChild = getLastChild(childValue);
                        build(childNode, isLast, level + 1, index, subChild.index, subChild.value);
                    } else {
                        build(childNode, isLast, level + 1, index, index, childValue);
                    }
                }
                nodeValue.object.push(childNode);
            });
        }

        node.value = nodeValue;
    };

    var root = {};
    $.each(request, function(name, value) {
        var className = that.getCsRequestClassName(name, value);
        build(root, true, 1, name, className, value);
    });
    root.name = "var request";

    return root;
}
CsRequestCodeRenderer.prototype.getMustacheTemplate = function() {
    return $.when(this.getCodeTemplate("cs", "request"), this.getCodeTemplate("cs", "initialization"))
        .then(function(request, initialization) {
            return {
                main: request[0],
                partials: {
                    initialization: initialization[0]
                }
            };
        });
}
