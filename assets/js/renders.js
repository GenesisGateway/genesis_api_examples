var requestCodeRenderer = (function() {
    return {
        render: function(data) {
            switch (data.lang) {
                case 'cs':
                    return new CsRequestCodeRenderer().renderRequest(
                        data.config, data.request);
                    break;
                case 'php':
                    return new PhpRequestCodeRenderer().renderRequest(
                        data.config, data.request);
                    break;
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

function RequestCodeRenderer() {}

RequestCodeRenderer.prototype.renderRequest = function(config, request) {
    var that = this;
    return that.getMustacheTemplate()
        .then(function(template) {
            var context = that.getMustacheContext(config, request);
            var content = Mustache.render(template.main, context, template.partials);
            return content;
        });
};

RequestCodeRenderer.prototype.getCodeTemplate = function(lang, name) {
    var path = 'assets/templates/renders/' + lang + '/' + name + '.mustache';
    return $.get(path);
};