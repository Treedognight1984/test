jQuery(document).ready(function($) {
    // Attach click event listener to 'Load More' button
    $('#load-more-button').on('click', function(e) {
        e.preventDefault();

        var button = $(this); // Reference to 'Load More' button
        var currentPage = button.data('page'); // Current page number
        var total_pages = button.data('total-pages'); // Total number of pages
        var postTypes = button.data('post-types'); // Post types
        var elementCount = button.data('element-count'); // Number of elements per page
        var searchQuery = button.data('search-query'); // Search query

        // Increment current page number for the next load
        currentPage++;

        // AJAX request to load more posts
        $.ajax({
            url: customSearchData.ajax_url,
            type: 'post',
            data: {
                action: 'custom_search_load_more',
                nonce: customSearchData.nonce,
                page: currentPage,
                total_pages: total_pages,
                post_types: postTypes,
                element_count: elementCount,
                search_query: searchQuery
            },
            success: function(response) {
                // Append new posts to the results container
                $('.custom-search-results').append(response);

                // Update the button data with the new page number
                button.data('page', currentPage);

                // Hide the button if there are no more pages to load
                if (currentPage >= total_pages) {
                    button.hide();
                }
            }
        });
    });
});
