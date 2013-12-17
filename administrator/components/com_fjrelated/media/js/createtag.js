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
			var token = $('#createtags-token').attr('name') + '=1';
			$.ajax
			({
				url : "index.php?option=com_fjrelated&task=batch.processbatch&format=json&" + token,
				type : "POST",
				dataType : "json",
				success : onDataReceived,
				error: onError
			});

			function onDataReceived(data) 
			{
				// Need to update some counters
				if (data.articlesProcessed < data.totalArticles) 
				{
					createTags()
				}
				else
				{
					// Need to close things out with success message
				}
			}
			
			function onError()
			{
				// Need error handling here
			}
				
		};
		createTags();
	}
})(jQuery);
