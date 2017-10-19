/**
 * Author: andrewryantech@gmail.com
 * Created: 19/10/17 9:38 PM
 */
;(function ($, window, document, undefined) {

    var pluginName = 'mwsClickToReveal',
        defaults   = {
            classInProgress: 'in-progress',
            classSuccess: 'success',
            classFailure: 'failure'
        };

    /**
     * @param {dom}    element
     * @param {object} options
     * @constructor
     */
    function MwsClickToReveal(element, options) {
        var plugin          = this;
            plugin.widgetId = null;
            plugin.element  = $(element);
            plugin.captcha  = $('#' + plugin.element.attr('data-recaptcha-id'));
            plugin.options  = $.extend(true, {}, defaults, options);
            plugin.spinner  = $(plugin.element.find('[data-spinner]'));


        // install listeners
        plugin.element.on('click.reveal', function (event) {
            event.preventDefault();
            updateHTML('Generating token...', true);
            plugin.element.addClass(plugin.options.classInProgress);

            if(null === plugin.widgetId){
                /** @var {function} grecaptcha */
                plugin.widgetId = grecaptcha.render(plugin.captcha.get(0), {
                    callback: onGenerateCallback
                });
            } else {
                grecaptcha.reset(plugin.widgetId);
            }

            grecaptcha.execute(plugin.widgetId);
        });


        /**
         * Update the elements text, optionally showing a spinner
         *
         * @param {string} newText
         * @param {boolean} showSpinner
         */
        function updateHTML(newText, showSpinner){
            plugin.element.html(newText);
            if(showSpinner){
                plugin.spinner.show();
                plugin.element.append(plugin.spinner);
            }
        }


        function onGenerateCallback(gRecaptchaResponse){

            updateHTML('Validating token...', true);

            var data = {
                gRecaptchaResponse: gRecaptchaResponse,
                mwsCtrName: plugin.element.attr('data-name')
            };

            $.ajax({
                url: '',
                method: 'GET',
                data: data,
                dataType: 'json',
                success: function(response){
                    if(response.success){
                        /** @var {string} response.hiddenValue */
                        updateHTML(response.hiddenValue, false);
                        plugin.element.addClass(plugin.options.classSuccess);
                        switch(plugin.element.attr('data-format')){
                            case 'email':
                                plugin.element.attr('href', 'mailto:' + response.hiddenValue);
                                break;
                            // Other formats can have format-specific logic here
                        }
                    } else {
                        plugin.element.addClass(plugin.options.classFailure);
                        if(response.message){
                            console.log(response.message);
                        }
                        updateHTML('Authorisation failure', false);
                    }
                },
                failure: function(){
                    plugin.element.addClass(plugin.options.classFailure);
                    updateHTML('System failure', false);
                },
                complete: function(){
                    plugin.element.off('click.reveal');
                    plugin.element.removeAttr('title');
                    plugin.element.removeClass(plugin.options.classInProgress);
                }
            });
        }
    }


    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName, new MwsClickToReveal(this, options));
            }
        });
    }
})(jQuery, window, document);
