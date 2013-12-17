/**
 * Render a bar chart using the jqplot JS library.
 * 
 * @copyright Copyright (C) 2013 Mark Dexter. All rights reserved.
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 */
(function ($) 
{
	$.FJCreateTags = function(start, batchSize) 
	{
		var createTags = function() 
		{
			$.ajax
			({
				url : "index.php?option=com_fjrelated&task=batch.processbatch&format=json",
				type : "POST",
				dataType : "json",
				success : onDataReceived
			});

			function onDataReceived(data) 
			{
				if (data.articlesProcessed < data.totalArticles) 
				{
					createTags()
				}
			}
				
		};
		createTags();
	}
})(jQuery);
