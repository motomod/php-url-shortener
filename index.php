<html>
    <head>
        <title>URL Shortener</title>
    </head>
    <body>
        <h1>URL Shortener</h1>
        <form id="urlform">
            <input name="url" value="text" placeholder="Enter your URL here..." />
            <input name="submit" type="submit" value="Shorten!" />
        </form>

        <div class="response"></div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script>
            $(function() {
                $('form#urlform').submit(function(e) {
                    e.preventDefault()
                    $this = $(this)

                    $.post('geturl.php', $this.serialize(), function(response) {
                        $('div.response').html("<a href=\""+ response +"\">"+ response +"</a>")
                    });
                });
            });
        </script>
    </body>

</html>
