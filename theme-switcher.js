function theme_switcher_switch_theme(stylesheet) {
	var data = {
		action: 'theme_switcher_switch_theme',
		stylesheet: stylesheet
	};
	jQuery.post(theme_switcher_data.ajaxurl, data, function(response) {
		window.location.reload();
	});
}

jQuery(document).ready(function($) {
	$('#wp-admin-bar-theme_switcher_theme_switcher .ab-submenu a').click(function(event) {
		event.preventDefault();
		theme_switcher_switch_theme($(this).data('theme'));
	});
});
