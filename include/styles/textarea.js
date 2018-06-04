/**
 * Source: http://stackoverflow.com/questions/4771794/default-text-on-textarea-jquery
 * 
 * Use jquery to show default text
 * 
 */

$('textarea').each(function() {
    // Stores the default value for each textarea within each textarea
    $.data(this, 'default', this.value);
}).focus(function() {
    // If the user has NOT edited the text clear it when they gain focus
    if (!$.data(this, 'edited')) {
        this.value = "";
    }
}).change(function() {
    // Fires on blur if the content has been changed by the user
    $.data(this, 'edited', this.value != "");
}).blur(function() {
    // Put the default text back in the textarea if its not been edited
    if (!$.data(this, 'edited')) {
        this.value = $.data(this, 'default');
    }
});