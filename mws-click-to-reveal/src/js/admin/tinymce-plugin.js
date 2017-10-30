/**
 * Author: https://github.com/andrewryantech
 * Created: 31/12/16 9:55 PM
 */

(function() {

    tinymce.create('tinymce.plugins.click_to_reveal', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} editor Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(editor, url) {

            editor.addButton('click_to_reveal', {
                title : 'Add Click-to-reveal shortcode',

                icon  : ' dashicons-before dashicons-hidden',
                onclick: function() {

                    // Open window
                    // noinspection JSUnusedGlobalSymbols
                    editor.windowManager.open({
                        title: 'Click-to-reveal Protected Value',
                        body: [
                            {type: 'listbox', name: 'name',        label: 'Name',         values: getHiddenValueNames()},
                            {type: 'textbox', name: 'placeholder', label: 'Placeholder',  placeholder: ''},
                            {type: 'listbox', name: 'format',      label: 'Format',       values: [
                                {text: 'Email',   value: 'email', selected: 'selected'},
                                {text: 'Phone',   value: 'phone', selected: 'selected'},
                                {text: '(none)',  value: 'default'}
                            ]},
                            {type: 'textbox', name: 'title',       label: 'Title',        placeholder: 'Click to reveal'}

                        ],
                        onsubmit: function (e) {
                            // Insert content when the window form is submitted
                            var d           = e.data,
                                format      = ' format="' + d.format + '"',
                                name        = ' name="' + d.name + '"',
                                title       = d.title ? (' title="' + d.title + '"') : '',
                                placeholder = d.placeholder;

                            var shortcode = '[click_to_reveal' + format + name + title + ']' + placeholder + '[/click_to_reveal]';
                            editor.insertContent(shortcode);
                        }
                    });
                }
            });

        },


        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo : function() {
            return {
                longname:  'Click to reveal protected value.',
                author:    'Andrew Ryan',
                authorurl: 'https://github.com/andrewryantech',
                infourl:   'https://github.com/andrewryantech/wp-plugin-recaptcha-click-to-reveal',
                version:   "1.0.0"
            };
        }
    });

    /**
     * Rendered by PHP into the page
     *
     * @return {object[]}
     */
    function getHiddenValueNames() {
        return mws_ctr_protected_value_names;
    }

    // Register plugin
    tinymce.PluginManager.add( 'click_to_reveal', tinymce.plugins.click_to_reveal );
})();
