<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Crawl - Backend Challenge</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <style>
            #crawling-loader,.information-container{
                display: none;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h3 class="mt-5">Crawl pages of agencyanalytics.com</h3>
            <button type="button" class="btn btn-primary float-right mt-5" id="crawl-btn">Start Crawling</button>
            <p id="crawling-loader">Crawling...</p>

            <div class="information-container">                
                <h5 class="mt-5">Crawled pages results for <a id="web-url" target="_blank"></a></h5>
                <hr>
                <table class="table">
                    <tbody>
                        <tr>
                            <td>Number of pages crawled</td>
                            <td id="no-of-crawled-pages"></td>
                        </tr>
                        <tr>
                            <td>Number of a unique images</td>
                            <td id="no-of-unique-images"></td>
                        </tr>
                        <tr>
                            <td>Number of unique internal links</td>
                            <td id="no-of-unique-internal-links"></td>
                        </tr>
                        <tr>
                            <td>Number of unique external links</td>
                            <td id="no-of-unique-external-links"></td>
                        </tr>
                        <tr>
                            <td>Average page load in seconds</td>
                            <td id="avg-page-loading-time"></td>
                        </tr>
                        <tr>
                            <td>Average title length</td>
                            <td id="avg-title-length"></td>
                        </tr>
                        <tr>
                            <td>Average word count</td>
                            <td id="avg-word-count"></td>
                        </tr>
                    </tbody>
                </table>
                <h5 class="mt-5">Crawled pages link</h5>
                <hr>                    
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Sr.</th>
                            <th scope="col">Page Link</th>
                            <th scope="col">HTTP Status Code</th>
                        </tr>
                    </thead>    
                    <tbody id="links-container"></tbody>
                </table>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script type="text/javascript">
            $( document ).ready(function() {
                $("#crawl-btn").click(function(){
                    $(this).hide();
                    $(".information-container").hide();
                    $("#crawling-loader").show();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "/api/crawl",
                        type: "GET",
                        dataType: 'json',
                        success: function(result) {
                            $("#crawl-btn,.information-container").show();
                            $("#crawling-loader").hide();
                            
                            $("#web-url").attr("href",result.url).html(result.url);
                            $("#no-of-crawled-pages").text(result.no_of_pages_crawled);
                            $("#no-of-unique-images").text( result.no_of_unique_images );
                            $("#no-of-unique-internal-links").text( result.no_of_unique_internal_links );
                            $("#no-of-unique-external-links").text( result.no_of_unique_external_links );
                            $("#avg-page-loading-time").text( result.avg_page_load_time );
                            $("#avg-title-length").text( result.avg_title_length );
                            $("#avg-word-count").text( result.avg_word_count );
                            
                            $("#links-container").html(result.crawled_pages_data.map((item, index) => { 
                                return `<tr>
                                        <td>${index+1}</td>
                                        <td><a href="${item['page_link']}" target="_blank">${item['page_link']}</a></td>
                                        <td>${item['http_status_code']}</td>
                                    </tr>`;
                            }));
                        }
                    });
                });                
            });
        </script>
    </body>
</html>
