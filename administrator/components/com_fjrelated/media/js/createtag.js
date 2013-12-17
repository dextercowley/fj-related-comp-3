/**
 * Render a bar chart using the jqplot JS library.
 * 
 * @copyright Copyright (C) 2013 Mark Dexter. All rights reserved.
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 */
(function ($) 
{
	$.FJCreateTags = function() 
	{
		var createTags = function() 
		{
			// Initialize spinner / progress bar
			$('#createtags-progress-container').show();
			var token = $('#createtags-token').attr('name') + '=1';
			var jsonUrl = "index.php?option=com_fjrelated&task=batch.processbatch&format=json&" + token;
			$.ajax
			({
				url : jsonUrl,
				type : "POST",
				dataType : "json",
				success : onDataReceived,
				error: onError
			});

			function onDataReceived(data) 
			{
				
				if (data.articlesProcessed < data.totalArticles) 
				{
					// Need to update some counters & show progress bar
					$( "#createtags-progress-bar" ).progressbar({
					      value: 100 * (data.articlesProcessed / data.totalArticles)
					    });
					createTags();
				}
				else
				{
					// Need to close things out with success message
					$('#createtags-progress-container').hide();
					$('#createtags-success-container').show();
				}
			}
			
			function onError(request, status, error)
			{
				// Need error handling here
				$('#spinner').hide();
				$('#createtags-error-container').show();
				$('#error-message').text("Error code " + request.status + ": " + request.statusText);
			}
				
		};
		createTags();
	}
})(jQuery);
