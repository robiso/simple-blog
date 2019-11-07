function Blog() {
    this.assertSummernote = function() {
        var scripts = [].slice.call(document.getElementsByTagName("script"));
        if(scripts.filter(e => e.src.indexOf("summernote") >= 0).length > 0) {
            console.log("Found summernote editors!");
            // summernote.focus
            $('.editable').on('summernote.blur', function(element) {
              var value = $(element.currentTarget).summernote('code'),
                  key = element.currentTarget.classList[0];

              blog.store(key, value);
            });
        }
    }

    this.save = function(elem) {
        // Let's save this element.
        $("#save").show();

        var key = elem.classList[0],
            value = elem.innerHTML;

        blog.store(key, value);

    };

    this.store = function(key, value) {
        var page = decodeURI(window.location.pathname.split("/").pop());

        $.post( rootURL + "/plugins/simple-blog/save.php", { key: key, value: value, page: page, token: token })
            .done(function( data ) {
                if(data) alert(data);
                $("#save").hide();
            });
    }

    this.new = function() {

        // Ask for name
        var name = prompt("New post title");
        if(name && name.trim() != "") {

            // Let's save this element.
            $("#save").show();

            $.post( rootURL + "/plugins/simple-blog/new.php", { page: name, token: token })
                .done(function( data ) {
                    window.location.href = data;
                    $("#save").hide();
                });

        }

    };

}

window.blog = new Blog();
window.addEventListener('load', blog.assertSummernote);
