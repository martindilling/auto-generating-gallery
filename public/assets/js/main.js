// Tiny jQuery Plugin
// by Chris Goodchild
$.fn.exists = function(callback)
{
	var args = [].slice.call(arguments, 1);

	if (this.length) {
		callback.call(this, args);
	}

	return this;
};

function fit_image()
{
	// Calculate the new height for the image container
	var newheight = $(window).height() - $('[rel=fitimage]').offset().top - 5;

	// Set the new max-height for the image container
	$('[rel=fitimage]').attr('style', 'max-height:' + newheight + 'px;');
	$('[rel=fitimage] img').attr('style', 'max-height:' + newheight + 'px;');

	// If we're loading, set fixed height to the new height
	$('[rel=fitimage].loading').exists(function()
	{
		this.attr('style', 'height:' + newheight + 'px;');
	});
}


!function ($)
{
	// If #imagenav exist, where on the image page
	$('#imagenav').exists(function()
	{
		// Fit image container
		fit_image();

		// When the window is resized, fit image container
		$(window).bind('resize', function() { fit_image(); });

		// Checks for keyup event on the document
		$(document).keyup(function(e)
		{
			// Redirect when left and right arrow key is clicked
			switch(e.which)
			{
				// Left arrow
				case 37:
					window.location.href = $('#prev-img').attr("href");
				break;

				// Right arrow
				case 39:
					window.location.href = $('#next-img').attr("href");
				break;

				// Just return if it's other keys than left and right arrow
				default: return;
			}
			e.preventDefault();
		});

		// Create a new image instance
		var img = new Image();

		$(img)
			// Try loading the image
			.load(function () {
				// Set the image hidden by default
				$(this).hide();

				// Remove loading class from the #loader and insert the image
				$('#loader')
					.removeClass('loading')
					.append(this);

				// Fit image container
				fit_image();

				// Fade our image in
				$(this).fadeIn();
			})
			// If there was an error loading the image
			.error(function () {
				// Fade the #loader out, add loaderror class and fade in again
				$('#loader')
					.fadeTo('fast', 0, function () {
						$('#loader')
							.addClass('loaderror')
							.fadeTo('slow', 1);
					});
			})
			.attr('src', $('#loader').attr('imgfile'))
			.attr('class', 'img-responsive img-full-height');
	});

	FB.XFBML.parse();

}(window.jQuery);
