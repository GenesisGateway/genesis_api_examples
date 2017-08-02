function PhpRequestCodeRenderer() {
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

    this.getPhpPrefixKeyword = function(klass) {
        switch (klass) {
            case "TransactionType":
                return "add";
            default:
                return "set";
        }
    }

    this.getPhpPropertyName = function(fieldName) {
        return this.convertSnakeToPascalCase(fieldName);
    };

    this.getPhpClassName = function(valueType) {
        switch (valueType) {
            case "shipping_address":
                return "Shipping";
            case "billing_address":
                return "Billing";
            case "transaction_type":
                return "TransactionType";
            case "mpi_params":
                return "Mpi";
            case "risk_params":
                return "Risk";
            case "dynamic_descriptor_params":
                return "Dynamic";
            default:
                return '';
        }
    };

    this.getPhpRequestClassName = function(rootKey, rootValue) {
        if (undefined != rootValue.transaction_type) {
            transaction_type = rootValue.transaction_type
        }
        else {
            transaction_type = rootKey;
        }

        switch(transaction_type) {
            case "authorize":
                return 'Financial\\Authorize';
            case 'authorize3d':
                return 'Financial\\Authorize3D';
            case 'capture':
                return 'Financial\\Capture';
            case 'credit':
                return 'Financial\\Credit';
            case 'payout':
                return 'Financial\\Payout';
            case 'refund':
                return 'Financial\\Refund';
            case 'sale':
                return 'Financial\\Sale';
            case 'sale3d':
                return 'Financial\\Sale3D';
            case 'void':
                return 'Financial\\Void';
            // Recurring
            case 'init_recurring_sale':
                return 'Financial\\Recurring\\InitRecurringSale';
            case 'init_recurring_sale3d':
                return 'Financial\\Recurring\\InitRecurringSale3D';
            case 'recurring_sale':
                return 'Financial\\Recurring\\RecurringSale';
            // AV/AVS
            case 'account_verification':
                return 'NonFinancial\\AccountVerification';
            case 'avs':
                return 'NonFinancial\\AVS';
            // Chargeback
            case 'chargeback_request':
                if (undefined != rootValue.arn || undefined != rootValue.original_transaction_unique_id)
                    return 'FraudRelated\\Chargeback\\Transaction';
                else
                    return 'FraudRelated\\Chargeback\\DateRange';
            // Retrieval
            case 'retrieval_request_request':
                if (undefined != rootValue.arn || undefined != rootValue.original_transaction_unique_id)
                    return 'FraudRelated\\Retrieval\\Transaction';
                else
                    return 'FraudRelated\\Retrieval\\DateRange';
            // Blacklist
            case 'blacklist_request':
                return 'FraudRelated\\Blacklist';
            // WPF
            case 'wpf_payment':
                return 'WPF\\Create';
            case 'wpf_reconcile':
                return 'WPF\\Reconcile';
            // Reconcile
            case 'reconcile':
                if (undefined != rootValue.unique_id)
                    return 'Reconcile\\Transaction';
                else
                    return 'Reconcile\\DateRange';
            default:
                return this.convertSnakeToPascalCase(transaction_type);
        }
    }

    this.getPhpValue = function(type, value) {
        return '"' + value + '"';
    }
}

PhpRequestCodeRenderer.prototype = new RequestCodeRenderer();
PhpRequestCodeRenderer.prototype.getMustacheContext = function(config, request) {
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
        nodeValue.class = that.getPhpClassName(valueType);
        nodeValue.object = [];

        var childrenCount   = itemsCount(value);
        var childNumber     = 0;

        $.each(value, function(index, childValue) {
            childNumber++;

            if (shouldSkip(index)) {
                return;
            }

            var isLast = false; //childNumber === childrenCount;

            var childNode = {};
            if (isNumber(index)) {
                buildRequest(childNode, isLast, level + 1, false, valueType, childValue, nodeValue.class);
            } else if (isEntity(childValue) && isItsSingleElementAnArray(childValue)) {
                var subChild = getLastChild(childValue);
                buildRequest(childNode, isLast, level + 1, index, subChild.index, subChild.value, nodeValue.class);
            } else {
                buildRequest(childNode, isLast, level + 1, index, index, childValue, nodeValue.class);
            }
            nodeValue.object.push(childNode);
        });
    }

    function buildRequest(node, isLastChild, level, key, valueType, value, klass) {
        if (isNonEmptyString(key)) {
            node.name = that.getPhpPropertyName(key);
        }
        else {
            node.name = key;
        }

        node.keyword = that.getPhpPrefixKeyword(klass);

        node.class = klass;

        // Hardcode in order to maintain indentation
        node.indentation = Array(2).join("\t");

        var nodeValue = {};
        nodeValue.is_root   = level === 2;
        nodeValue.is_entity = isEntity(value);
        nodeValue.is_last   = isLastChild;
        nodeValue.is_array  = Array.isArray(value);

        if (!nodeValue.is_entity) {
            nodeValue.value = that.getPhpValue(valueType, value);
        } else {
            buildEntityChild(level, nodeValue, valueType, value);
        }

        node.value = nodeValue;
    };

    var root = {};
    root.config = config;

    var requestName = Object.keys(request)[0];
    var className = that.getPhpRequestClassName(requestName, request[requestName]);
    buildRequest(root, true, 2, requestName, className, request[requestName]);

    // Set the request path
    root.klass = className;

    // Set the last_child "is_last" flag
    lastBranch = root.value.object.last();

    if (undefined !== lastBranch.value.object && undefined !== lastBranch.value.object.last())
        lastBranch.value.object.last().value.is_last = true;
    else
        lastBranch.value.is_last = true;

    return root;
}
PhpRequestCodeRenderer.prototype.getMustacheTemplate = function() {
    return $.when(this.getCodeTemplate("php", "stub"), this.getCodeTemplate("php", "vars"))
        .then(function(request, initialization) {
            return {
                main: request[0],
                partials: {
                    initialization: initialization[0]
                }
            };
        });
}