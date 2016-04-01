function CsRequestCodeRenderer() {
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
        switch(fieldName) {
            case "transaction_id":
                return "Id";
            default:
                return this.convertSnakeToPascalCase(fieldName);
        }
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
        function toQueryTransactionName(value, partialTransactionName) {
            if (value.start_date !== undefined) {
                return "Multi" + partialTransactionName;
            }
            return "Single" + partialTransactionName;
        }

        switch(rootKey) {
            case "chargeback_request":
                return toQueryTransactionName(rootValue, "Chargeback");
            case "retrieval_request_request":
                return toQueryTransactionName(rootValue, "RetrievalRequest");
                return toQueryTransactionName(rootValue, "Reconcile");
            case "blacklist_request":
                return "Blacklist";
            case "chargeback_request":
                return toQueryTransactionName(rootValue, "Chargeback");
            case "wpf_payment":
                return "WpfCreate";
            case "wpf_reconcile":
            case "reconcile":
                return this.convertSnakeToPascalCase(rootKey);
        }

        function to3dTransactionName(value, partialTransactionName) {
            if (rootValue.mpi_params !== undefined) {
                return partialTransactionName + "Sync";
            }
            return partialTransactionName + "Async";
        }

        switch(rootValue.transaction_type) {
            case "authorize3d":
            case "sale3d":
            case "init_recurring_sale3d":
                return to3dTransactionName(rootValue.transaction_type,
                    this.convertSnakeToPascalCase(rootValue.transaction_type));
            default:
                return this.convertSnakeToPascalCase(rootValue.transaction_type);
        }
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
            case "gaming":
            case "moto":
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

CsRequestCodeRenderer.prototype = new RequestCodeRenderer();

CsRequestCodeRenderer.prototype.getMustacheContext = function(config, request) {
    var that = this;

    function shouldSkip(propertyName) {
        return propertyName === "transaction_type";
    }

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

    function isNonEmptyString(value) {
        return !!value && value.length > 0;
    }

    function buildEntityChild(level, nodeValue, valueType, value) {
        nodeValue.class = that.getCsClassName(valueType);
        nodeValue.object = [];

        var childrenCount = itemsCount(value);
        var childNumber = 0;
        $.each(value, function(index, childValue) {
            childNumber++;
            if (shouldSkip(index)) {
                return;
            }

            var isLast = childNumber === childrenCount;

            var childNode = {};
            if (isNumber(index)) {
                buildRequest(childNode, isLast, level + 1, false, valueType, childValue);
            } else if (isEntity(childValue) && isItsSingleElementAnArray(childValue)) {
                var subChild = getLastChild(childValue);
                buildRequest(childNode, isLast, level + 1, index, subChild.index, subChild.value);
            } else {
                buildRequest(childNode, isLast, level + 1, index, index, childValue);
            }
            nodeValue.object.push(childNode);
        });
    }

    function buildRequest(node, isLastChild, level, key, valueType, value) {
        if (isNonEmptyString(key)) {
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
            buildEntityChild(level, nodeValue, valueType, value);
        }

        node.value = nodeValue;
    };

    var root = {};
    root.config = config;

    var requestName = Object.keys(request)[0];
    var className = that.getCsRequestClassName(requestName, request[requestName]);
    buildRequest(root, true, 1, requestName, className, request[requestName]);
    root.name = "var request";

    return root;
}

CsRequestCodeRenderer.prototype.getMustacheTemplate = function() {
    return $.when(this.getCodeTemplate("cs", "stub"), this.getCodeTemplate("cs", "vars"))
        .then(function(request, initialization) {
            return {
                main: request[0],
                partials: {
                    initialization: initialization[0]
                }
            };
        });
}