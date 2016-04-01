function Translation() {
    this.iso  = ''; // Active language
    this.lang = {}; // Translation storage
}

Translation.prototype = {
    getLanguageCode: function() {
        return this.iso;
    },
    getLocalizedStrings: function() {
        return this.lang[this.iso];
    },
    setLanguageCode: function(iso) {
        this.iso = iso;
    }
};

var translation = new Translation();

// Hard-coded, because we only have one language
translation.setLanguageCode('en_US');